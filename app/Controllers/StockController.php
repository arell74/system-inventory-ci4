<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\StockMovementModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class StockController extends BaseController
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

    public function stockIn()
    {
        $this->setPageData('Barang Masuk', 'Input stok barang masuk ke inventory');

        $products = $this->productModel->getProductsWithCategory();
        $categories = $this->categoryModel->getActiveCategories();

        $recentMovements = $this->stockMovementModel->select('
            stock_movements.*,
            products.name as product_name,
            products.sku as product_sku
        ')
        ->join('products', 'products.id = stock_movements.product_id')
        ->where('stock_movements.type', 'IN')
        ->orderBy('stock_movements.created_at', 'DESC')
        ->limit(5)
        ->findAll();

    $data = [
        'products' => $products,
        'categories' => $categories,
        'recent_movements' => $recentMovements,
        'selected_product' => $this->request->getGet('product')
    ];

    return $this->render('stock/in', $data);
    }

    public function storeStockIn()
    {
        $rules = [
            'movements' => 'required',
            'movements.*.product_id' => 'required|integer|is_not_unique[products.id]',
            'movements.*.quantity' => 'required|integer|greater_than[0]',
            'movements.*.notes' => 'permit_empty|max_length[500]'
        ];

        $messages = [
            'movements.required' => 'Minimal satu produk harus dipilih',
            'movements.*.product_id.required' => 'Produk harus dipilih',
            'movements.*.product_id.is_not_unique' => 'Produk tidak valid',
            'movements.*.quantity.required' => 'Jumlah harus diisi',
            'movements.*.quantity.greater_than' => 'Jumlah harus lebih dari 0'
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $movements = $this->request->getPost('movements');
        $globalNotes = $this->request->getPost('global_notes');
        $reference = $this->request->getPost('reference_no') ?: $this->stockMovementModel->generateReferenceNo('IN');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $successCount = 0;
            $errors = [];

            foreach ($movements as $index => $movement) {
                if (empty($movement['product_id']) || empty($movement['quantity'])) {
                    continue;
                }

                $movementData = [
                    'product_id' => (int)$movement['product_id'],
                    'type' => 'IN',
                    'quantity' => (int)$movement['quantity'],
                    'reference_no' => $reference . '-' . ($index + 1),
                    'notes' => $movement['notes'] ?: $globalNotes,
                    'created_by' => 'Admin' // TODO: Get from session
                ];

                try {
                    $this->stockMovementModel->createMovement($movementData);
                    $successCount++;
                } catch (\Exception $e) {
                    $product = $this->productModel->find($movement['product_id']);
                    $errors[] = "Gagal memproses {$product['name']}: " . $e->getMessage();
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false || $successCount == 0) {
                throw new \Exception('Tidak ada transaksi yang berhasil diproses');
            }

            if (!empty($errors)) {
                $this->setFlash('warning', "Berhasil memproses {$successCount} item. Errors: " . implode(', ', $errors));
            } else {
                $this->setFlash('success', "Berhasil memproses {$successCount} transaksi barang masuk dengan referensi: {$reference}");
            }

            return redirect()->to('/stock/in');

        } catch (\Exception $e) {
            $db->transRollback();
            $this->setFlash('error', 'Gagal memproses transaksi: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function stockOut()
    {
        $this->setPageData('Barang Keluar', 'Output stok barang dari inventory');

        // Get products with available stock
        $products = $this->productModel->select('
                products.*, 
                categories.name as category_name
            ')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true)
            ->where('products.current_stock >', 0)
            ->orderBy('products.name', 'ASC')
            ->findAll();

        $categories = $this->categoryModel->getActiveCategories();

        // Get recent stock out movements
        $recentMovements = $this->stockMovementModel->select('
                stock_movements.*, 
                products.name as product_name, 
                products.sku as product_sku
            ')
            ->join('products', 'products.id = stock_movements.product_id')
            ->where('stock_movements.type', 'OUT')
            ->orderBy('stock_movements.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'products' => $products,
            'categories' => $categories,
            'recent_movements' => $recentMovements,
            'selected_product' => $this->request->getGet('product')
        ];

        return $this->render('stock/out', $data);
    }

    public function storeStockOut()
    {
        $rules = [
            'movements' => 'required',
            'movements.*.product_id' => 'required|integer|is_not_unique[products.id]',
            'movements.*.quantity' => 'required|integer|greater_than[0]',
            'movements.*.notes' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $movements = $this->request->getPost('movements');
        $globalNotes = $this->request->getPost('global_notes');
        $reference = $this->request->getPost('reference_no') ?: $this->stockMovementModel->generateReferenceNo('OUT');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $successCount = 0;
            $errors = [];

            foreach ($movements as $index => $movement) {
                if (empty($movement['product_id']) || empty($movement['quantity'])) {
                    continue;
                }

                // Check available stock
                $product = $this->productModel->find($movement['product_id']);
                if (!$product) {
                    $errors[] = "Produk tidak ditemukan";
                    continue;
                }

                if ($product['current_stock'] < $movement['quantity']) {
                    $errors[] = "{$product['name']}: Stok tidak mencukupi (Available: {$product['current_stock']})";
                    continue;
                }

                $movementData = [
                    'product_id' => (int)$movement['product_id'],
                    'type' => 'OUT',
                    'quantity' => (int)$movement['quantity'],
                    'reference_no' => $reference . '-' . ($index + 1),
                    'notes' => $movement['notes'] ?: $globalNotes,
                    'created_by' => 'Admin' // TODO: Get from session
                ];

                try {
                    $this->stockMovementModel->createMovement($movementData);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal memproses {$product['name']}: " . $e->getMessage();
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false || $successCount == 0) {
                throw new \Exception('Tidak ada transaksi yang berhasil diproses');
            }

            if (!empty($errors)) {
                $this->setFlash('warning', "Berhasil memproses {$successCount} item. Errors: " . implode(', ', $errors));
            } else {
                $this->setFlash('success', "Berhasil memproses {$successCount} transaksi barang keluar dengan referensi: {$reference}");
            }

            return redirect()->to('/stock/out');

        } catch (\Exception $e) {
            $db->transRollback();
            $this->setFlash('error', 'Gagal memproses transaksi: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function history()
    {
        $this->setPageData('Riwayat Pergerakan Stok', 'History semua pergerakan stok inventory');

        // Get filter parameters
        $productId = $this->request->getGet('product');
        $type = $this->request->getGet('type');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $reference = $this->request->getGet('reference');

        // Build query
        $builder = $this->stockMovementModel->select('
                stock_movements.*, 
                products.name as product_name,
                products.sku as product_sku,
                categories.name as category_name
            ')
            ->join('products', 'products.id = stock_movements.product_id')
            ->join('categories', 'categories.id = products.category_id');

        // Apply filters
        if ($productId) {
            $builder->where('stock_movements.product_id', $productId);
        }

        if ($type) {
            $builder->where('stock_movements.type', $type);
        }

        if ($startDate) {
            $builder->where('DATE(stock_movements.created_at) >=', $startDate);
        }

        if ($endDate) {
            $builder->where('DATE(stock_movements.created_at) <=', $endDate);
        }

        if ($reference) {
            $builder->like('stock_movements.reference_no', $reference);
        }

        $movements = $builder->orderBy('stock_movements.created_at', 'DESC')->findAll();

        // Get summary statistics
        $stats = $this->getMovementStats($productId, $type, $startDate, $endDate);

        // Get products and categories for filters
        $products = $this->productModel->getProductsWithCategory();
        $categories = $this->categoryModel->getActiveCategories();

        $data = [
            'movements' => $movements,
            'stats' => $stats,
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'product' => $productId,
                'type' => $type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reference' => $reference
            ]
        ];

        return $this->render('stock/history', $data);
    }

    public function adjustment()
    {
        $this->setPageData('Penyesuaian Stok', 'Koreksi stok untuk menyesuaikan dengan kondisi fisik');

        $products = $this->productModel->getProductsWithCategory();
        $categories = $this->categoryModel->getActiveCategories();

        $data = [
            'products' => $products,
            'categories' => $categories
        ];

        return $this->render('stock/adjustment', $data);
    }

    /**
     * Store Stock Adjustment
     */
    public function storeAdjustment()
    {
        $rules = [
            'adjustments' => 'required',
            'adjustments.*.product_id' => 'required|integer|is_not_unique[products.id]',
            'adjustments.*.new_stock' => 'required|integer|greater_than_equal_to[0]',
            'adjustments.*.notes' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $adjustments = $this->request->getPost('adjustments');
        $globalNotes = $this->request->getPost('global_notes') ?: 'Stock adjustment';
        $reference = $this->request->getPost('reference_no') ?: $this->stockMovementModel->generateReferenceNo('ADJUSTMENT');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $successCount = 0;
            $errors = [];

            foreach ($adjustments as $index => $adjustment) {
                if (empty($adjustment['product_id']) || !isset($adjustment['new_stock'])) {
                    continue;
                }

                $product = $this->productModel->find($adjustment['product_id']);
                if (!$product) {
                    $errors[] = "Produk tidak ditemukan";
                    continue;
                }

                $newStock = (int)$adjustment['new_stock'];
                $currentStock = $product['current_stock'];

                // Skip if no change
                if ($newStock == $currentStock) {
                    continue;
                }

                $movementData = [
                    'product_id' => (int)$adjustment['product_id'],
                    'type' => 'ADJUSTMENT',
                    'quantity' => $newStock, // For adjustment, quantity is the final stock
                    'reference_no' => $reference . '-' . ($index + 1),
                    'notes' => $adjustment['notes'] ?: $globalNotes . " (from {$currentStock} to {$newStock})",
                    'created_by' => 'Admin'
                ];

                try {
                    $this->stockMovementModel->createMovement($movementData);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal memproses {$product['name']}: " . $e->getMessage();
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false || $successCount == 0) {
                throw new \Exception('Tidak ada penyesuaian yang berhasil diproses');
            }

            if (!empty($errors)) {
                $this->setFlash('warning', "Berhasil memproses {$successCount} penyesuaian. Errors: " . implode(', ', $errors));
            } else {
                $this->setFlash('success', "Berhasil memproses {$successCount} penyesuaian stok dengan referensi: {$reference}");
            }

            return redirect()->to('/stock/adjustment');

        } catch (\Exception $e) {
            $db->transRollback();
            $this->setFlash('error', 'Gagal memproses penyesuaian: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function alerts()
    {
        $this->setPageData('Peringatan Stok', 'Produk dengan stok rendah yang membutuhkan perhatian');

        $lowStockProducts = $this->productModel->getLowStockProducts();
        $outOfStockProducts = $this->productModel->where('current_stock', 0)
                                                 ->where('is_active', true)
                                                 ->findAll();

        $data = [
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts
        ];

        return $this->render('stock/alerts', $data);
    }

    private function getMovementStats($productId = null, $type = null, $startDate = null, $endDate = null)
    {
        $builder = $this->stockMovementModel;

        if ($productId) {
            $builder = $builder->where('product_id', $productId);
        }

        if ($type) {
            $builder = $builder->where('type', $type);
        }

        if ($startDate) {
            $builder = $builder->where('DATE(created_at) >=', $startDate);
        }

        if ($endDate) {
            $builder = $builder->where('DATE(created_at) <=', $endDate);
        }

        $movements = $builder->findAll();

        $stats = [
            'total_movements' => count($movements),
            'total_in' => 0,
            'total_out' => 0,
            'total_adjustments' => 0,
            'in_quantity' => 0,
            'out_quantity' => 0,
            'adjustment_quantity' => 0
        ];

        foreach ($movements as $movement) {
            switch ($movement['type']) {
                case 'IN':
                    $stats['total_in']++;
                    $stats['in_quantity'] += $movement['quantity'];
                    break;
                case 'OUT':
                    $stats['total_out']++;
                    $stats['out_quantity'] += $movement['quantity'];
                    break;
                case 'ADJUSTMENT':
                    $stats['total_adjustments']++;
                    $stats['adjustment_quantity'] += abs($movement['quantity'] - $movement['previous_stock']);
                    break;
            }
        }

        return $stats;
    }

    public function getProductStock($productId)
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonResponse(['status' => false, 'message' => 'Invalid request'], 400);
        }

        $product = $this->productModel->getProductWithCategory($productId);

        if (!$product) {
            return $this->jsonResponse(['status' => false, 'message' => 'Product not found'], 404);
        }

        return $this->jsonResponse([
            'status' => true,
            'product' => [
                'id' => $product['id'],
                'name' => $product['name'],
                'sku' => $product['sku'],
                'category_name' => $product['category_name'],
                'current_stock' => $product['current_stock'],
                'min_stock' => $product['min_stock'],
                'unit' => $product['unit'],
                'price' => $product['price']
            ]
        ]);
    }
}
