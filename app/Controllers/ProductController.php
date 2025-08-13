<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\StockMovementModel;

class ProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $stockMovementModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->stockMovementModel = new StockMovementModel();
    }

    /**
     * Display products list
     */
    public function index()
    {
        $this->setPageData('Produk', 'Manajemen produk dan stok');

        // Get search parameters
        $search = $this->request->getGet('search');
        $categoryFilter = $this->request->getGet('category');
        $stockFilter = $this->request->getGet('stock_status');

        // Build query
        $builder = $this->productModel->select("
                    products.*, 
                categories.name as category_name,
                CASE 
                    WHEN products.current_stock = 0 THEN 'out_of_stock'
                    WHEN products.current_stock <= products.min_stock THEN 'low_stock'
                    ELSE 'normal'
                END as stock_status
            ")
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true);

        // Apply filters
        if ($search) {
            $builder->groupStart()
                    ->like('products.name', $search)
                    ->orLike('products.sku', $search)
                    ->orLike('products.description', $search)
                    ->groupEnd();
        }

        if ($categoryFilter) {
            $builder->where('products.category_id', $categoryFilter);
        }

        if ($stockFilter) {
            switch ($stockFilter) {
                case 'out_of_stock':
                    $builder->where('products.current_stock', 0);
                    break;
                case 'low_stock':
                    $builder->where('products.current_stock <=', 'products.min_stock', false)
                            ->where('products.current_stock >', 0);
                    break;
                case 'normal':
                    $builder->where('products.current_stock >', 'products.min_stock', false);
                    break;
            }
        }

        $products = $builder->orderBy('products.name', 'ASC')->findAll();

        // Get categories for filter dropdown
        $categories = $this->categoryModel->getActiveCategories();

        $data = [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'categoryFilter' => $categoryFilter,
            'stockFilter' => $stockFilter,
            'totalProducts' => count($products)
        ];

        return $this->render('products/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->setPageData('Tambah Produk', 'Buat produk baru dengan detail lengkap');

        $categories = $this->categoryModel->getActiveCategories();

        $data = [
            'product' => [
                'name' => '',
                'sku' => '',
                'category_id' => '',
                'description' => '',
                'price' => 0,
                'cost_price' => 0,
                'min_stock' => 10,
                'current_stock' => 0,
                'unit' => 'pcs',
                'is_active' => true
            ],
            'categories' => $categories,
            'validation' => \Config\Services::validation()
        ];

        return $this->render('products/create', $data);
    }

    /**
     * Store new product
     */
    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'sku' => 'required|min_length[3]|max_length[50]|is_unique[products.sku]',
            'category_id' => 'required|integer|is_not_unique[categories.id]',
            'description' => 'permit_empty|max_length[1000]',
            'price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'cost_price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'min_stock' => 'permit_empty|integer|greater_than_equal_to[0]',
            'current_stock' => 'permit_empty|integer|greater_than_equal_to[0]',
            'unit' => 'permit_empty|max_length[20]'
        ];

        $messages = [
            'name' => [
                'required' => 'Nama produk harus diisi',
                'min_length' => 'Nama produk minimal 3 karakter',
                'max_length' => 'Nama produk maksimal 255 karakter'
            ],
            'sku' => [
                'required' => 'SKU harus diisi',
                'min_length' => 'SKU minimal 3 karakter',
                'max_length' => 'SKU maksimal 50 karakter',
                'is_unique' => 'SKU sudah digunakan'
            ],
            'category_id' => [
                'required' => 'Kategori harus dipilih',
                'is_not_unique' => 'Kategori tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'sku' => strtoupper($this->request->getPost('sku')),
            'category_id' => $this->request->getPost('category_id'),
            'description' => $this->request->getPost('description'),
            'price' => (float)$this->request->getPost('price') ?: 0,
            'cost_price' => (float)$this->request->getPost('cost_price') ?: 0,
            'min_stock' => (int)$this->request->getPost('min_stock') ?: 10,
            'current_stock' => (int)$this->request->getPost('current_stock') ?: 0,
            'unit' => $this->request->getPost('unit') ?: 'pcs',
            'is_active' => $this->request->getPost('is_active') ? true : false
        ];

        if ($productId = $this->productModel->insert($data)) {
            // Create initial stock movement if current_stock > 0
            if ($data['current_stock'] > 0) {
                $this->stockMovementModel->createMovement([
                    'product_id' => $productId,
                    'type' => 'IN',
                    'quantity' => $data['current_stock'],
                    'notes' => 'Stok awal produk baru',
                    'created_by' => 'System'
                ]);
            }

            $this->setFlash('success', 'Produk berhasil ditambahkan');
            return redirect()->to('/products');
        } else {
            $this->setFlash('error', 'Gagal menambahkan produk');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show product details
     */
    public function show($id)
    {
        $product = $this->productModel->getProductWithCategory($id);
        
        if (!$product) {
            $this->setFlash('error', 'Produk tidak ditemukan');
            return redirect()->to('/products');
        }

        $this->setPageData('Detail Produk', 'Informasi lengkap produk: ' . $product['name']);

        // Get stock movements history
        $stockMovements = $this->stockMovementModel->getMovementsByProduct($id, 10);

        // Calculate stock statistics
        $stockStats = [
            'total_in' => $this->stockMovementModel->where('product_id', $id)
                                                   ->where('type', 'IN')
                                                   ->selectSum('quantity', 'total')
                                                   ->first()['total'] ?? 0,
            'total_out' => $this->stockMovementModel->where('product_id', $id)
                                                    ->where('type', 'OUT')
                                                    ->selectSum('quantity', 'total')
                                                    ->first()['total'] ?? 0
        ];

        $data = [
            'product' => $product,
            'stockMovements' => $stockMovements,
            'stockStats' => $stockStats
        ];

        return $this->render('products/show', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->setFlash('error', 'Produk tidak ditemukan');
            return redirect()->to('/products');
        }

        $this->setPageData('Edit Produk', 'Edit produk: ' . $product['name']);

        $categories = $this->categoryModel->getActiveCategories();

        $data = [
            'product' => $product,
            'categories' => $categories,
            'validation' => \Config\Services::validation()
        ];

        return $this->render('products/edit', $data);
    }

    /**
     * Update product
     */
    public function update($id)
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->setFlash('error', 'Produk tidak ditemukan');
            return redirect()->to('/products');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'sku' => "required|min_length[3]|max_length[50]|is_unique[products.sku,id,$id]",
            'category_id' => 'required|integer|is_not_unique[categories.id]',
            'description' => 'permit_empty|max_length[1000]',
            'price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'cost_price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'min_stock' => 'permit_empty|integer|greater_than_equal_to[0]',
            'unit' => 'permit_empty|max_length[20]'
        ];

        $messages = [
            'name' => [
                'required' => 'Nama produk harus diisi',
                'min_length' => 'Nama produk minimal 3 karakter',
                'max_length' => 'Nama produk maksimal 255 karakter'
            ],
            'sku' => [
                'required' => 'SKU harus diisi',
                'min_length' => 'SKU minimal 3 karakter',
                'max_length' => 'SKU maksimal 50 karakter',
                'is_unique' => 'SKU sudah digunakan'
            ],
            'category_id' => [
                'required' => 'Kategori harus dipilih',
                'is_not_unique' => 'Kategori tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'sku' => strtoupper($this->request->getPost('sku')),
            'category_id' => $this->request->getPost('category_id'),
            'description' => $this->request->getPost('description'),
            'price' => (float)$this->request->getPost('price') ?: 0,
            'cost_price' => (float)$this->request->getPost('cost_price') ?: 0,
            'min_stock' => (int)$this->request->getPost('min_stock') ?: 10,
            'unit' => $this->request->getPost('unit') ?: 'pcs',
            'is_active' => $this->request->getPost('is_active') ? true : false
        ];

        if ($this->productModel->update($id, $data)) {
            $this->setFlash('success', 'Produk berhasil diupdate');
            return redirect()->to('/products');
        } else {
            $this->setFlash('error', 'Gagal mengupdate produk');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete product
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonResponse(['status' => false, 'message' => 'Invalid request'], 400);
        }

        $product = $this->productModel->find($id);
        
        if (!$product) {
            return $this->jsonResponse(['status' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        // Check if product has stock movements
        $hasMovements = $this->stockMovementModel->where('product_id', $id)->countAllResults() > 0;
        
        if ($hasMovements) {
            return $this->jsonResponse([
                'status' => false, 
                'message' => 'Produk tidak bisa dihapus karena memiliki riwayat pergerakan stok'
            ], 400);
        }

        if ($this->productModel->delete($id)) {
            return $this->jsonResponse([
                'status' => true, 
                'message' => 'Produk berhasil dihapus'
            ]);
        } else {
            return $this->jsonResponse([
                'status' => false, 
                'message' => 'Gagal menghapus produk'
            ], 500);
        }
    }

    /**
     * Generate SKU via AJAX
     */
    public function generateSKU()
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonResponse(['status' => false, 'message' => 'Invalid request'], 400);
        }

        $categoryId = $this->request->getPost('category_id');
        $productName = $this->request->getPost('product_name');

        if (!$categoryId || !$productName) {
            return $this->jsonResponse([
                'status' => false, 
                'message' => 'Category ID dan nama produk diperlukan'
            ], 400);
        }

        $sku = $this->productModel->generateSKU($categoryId, $productName);

        if ($sku) {
            return $this->jsonResponse([
                'status' => true, 
                'sku' => $sku
            ]);
        } else {
            return $this->jsonResponse([
                'status' => false, 
                'message' => 'Gagal generate SKU'
            ], 500);
        }
    }
    public function search()
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonResponse(['error' => 'Invalid request'], 400);
        }

        $keyword = $this->request->getGet('q');
        $limit = (int)$this->request->getGet('limit') ?: 10;

        if (!$keyword || strlen($keyword) < 2) {
            return $this->jsonResponse(['results' => []]);
        }

        $products = $this->productModel->searchProducts($keyword);
        $results = [];

        foreach (array_slice($products, 0, $limit) as $product) {
            $results[] = [
                'id' => $product['id'],
                'text' => $product['name'] . ' (' . $product['sku'] . ')',
                'name' => $product['name'],
                'sku' => $product['sku'],
                'category' => $product['category_name'],
                'current_stock' => $product['current_stock'],
                'price' => $product['price']
            ];
        }

        return $this->jsonResponse(['results' => $results]);
    }

    public function getStockStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonResponse(['error' => 'Invalid request'], 400);
        }

        $product = $this->productModel->find($id);
        
        if (!$product) {
            return $this->jsonResponse(['error' => 'Product not found'], 404);
        }

        $status = 'normal';
        if ($product['current_stock'] == 0) {
            $status = 'out_of_stock';
        } elseif ($product['current_stock'] <= $product['min_stock']) {
            $status = 'low_stock';
        }

        return $this->jsonResponse([
            'product_id' => $product['id'],
            'current_stock' => $product['current_stock'],
            'min_stock' => $product['min_stock'],
            'status' => $status,
            'status_text' => [
                'normal' => 'Stok Normal',
                'low_stock' => 'Stok Rendah',
                'out_of_stock' => 'Stok Habis'
            ][$status]
        ]);
    }
}