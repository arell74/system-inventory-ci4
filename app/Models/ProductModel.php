<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'sku',
        'category_id',
        'description',
        'price',
        'cost_price',
        'min_stock',
        'current_stock',
        'unit',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name'        => 'required|min_length[3]|max_length[255]',
        'sku'         => 'required|min_length[3]|max_length[50]|is_unique[products.sku,id,{id}]',
        'category_id' => 'required|integer|is_not_unique[categories.id]',
        'price'       => 'permit_empty|decimal|greater_than_equal_to[0]',
        'cost_price'  => 'permit_empty|decimal|greater_than_equal_to[0]',
        'min_stock'   => 'permit_empty|integer|greater_than_equal_to[0]',
        'current_stock' => 'permit_empty|integer|greater_than_equal_to[0]',
        'unit'        => 'permit_empty|max_length[20]'
    ];
    protected $validationMessages   = [
        'name' => [
            'required'    => 'Nama produk harus diisi',
            'min_length'  => 'Nama produk minimal 3 karakter',
            'max_length'  => 'Nama produk maksimal 255 karakter'
        ],
        'sku' => [
            'required'    => 'SKU harus diisi',
            'min_length'  => 'SKU minimal 3 karakter',
            'max_length'  => 'SKU maksimal 50 karakter',
            'is_unique'   => 'SKU sudah digunakan'
        ],
        'category_id' => [
            'required'      => 'Kategori harus dipilih',
            'is_not_unique' => 'Kategori tidak valid'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Custom Methods

    //Get products with category info
    public function getProductsWithCategory($limit = null, $offset = null)
    {
        $builder = $this->select('products.*, categories.name as category_name')
                        ->join('categories', 'categories.id = products.category_id')
                        ->where('products.is_active', true)
                        ->orderBy('products.name', 'ASC');
        
        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }

    //Get product by ID with category info
    public function getProductWithCategory($id)
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id')
                    ->where('products.id', $id)
                    ->first();
    }

    //Get Product with low stock
    public function getLowStockProducts($limit = null)
    {
        $builder = $this->select('products.*, categories.name as category_name')
                        ->join('categories', 'categories.id = products.category_id')
                        ->where('products.current_stock <=', 'products.min_stock', false)
                        ->where('products.is_active', true)
                        ->orderBy('products.current_stock', 'ASC');
        
        if ($limit !== null) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    //update stock quantity
    public function updateStock($productId, $newStock)
    {
        return $this->update($productId, ['current_stock' => $newStock]);
    }

    /**
     * Generate SKU automatically
     */
    public function generateSKU($categoryId, $productName)
    {
        $categoryModel = new CategoryModel();
        $category = $categoryModel->find($categoryId);
        
        if (!$category) return null;
        
        // Get first 3 letters of category name
        $categoryCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category['name']), 0, 3));
        
        // Get first 3 letters of product name
        $productCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $productName), 0, 3));
        
        // Get next number for this combination
        $lastProduct = $this->like('sku', $categoryCode . $productCode, 'after')
                            ->orderBy('sku', 'DESC')
                            ->first();
        
        $nextNumber = 1;
        if ($lastProduct) {
            $lastNumber = intval(substr($lastProduct['sku'], -3));
            $nextNumber = $lastNumber + 1;
        }
        
        return $categoryCode . $productCode . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    //Get inventory value
    public function getTotalInventoryValue()
    {
        $result = $this->selectSum('(current_stock * price)', 'total_value')
                       ->where('is_active', true)
                       ->first();
        
        return $result['total_value'] ?? 0;
    }

    //search Products
    public function searchProducts($keyword)
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id')
                    ->groupStart()
                        ->like('products.name', $keyword)
                        ->orLike('products.sku', $keyword)
                        ->orLike('categories.name', $keyword)
                    ->groupEnd()
                    ->where('products.is_active', true)
                    ->orderBy('products.name', 'ASC')
                    ->findAll();
    }
}
