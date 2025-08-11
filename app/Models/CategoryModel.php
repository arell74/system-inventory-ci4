<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name,id,{id}]',
        'description' => 'permit_empty|max_length[500]'
    ];
    protected $validationMessages   = [
        'name' => [
            'required'    => 'Nama kategori harus diisi',
            'min_length'  => 'Nama kategori minimal 3 karakter',
            'max_length'  => 'Nama kategori maksimal 100 karakter',
            'is_unique'   => 'Nama kategori sudah ada'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    //custom method

    //Get active categories for dropdown
    public function getActiveCategories()
    {
        return $this->where('is_active', true)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    //Get category with product count
    public function getCategoriesWithProductCount()
    {
        return $this->select('categories.*, COUNT(products.id) as product_count')
                    ->join('products', 'products.category_id = categories.id', 'left')
                    ->groupBy('categories.id')
                    ->orderBy('categories.name', 'ASC')
                    ->findAll();
    }

    public function canDelete($id)
    {
        $productModel = new ProductModel();
        $productCount = $productModel->where('category_id', $id)->countAllResults();
        
        return $productCount == 0;
    }
}
