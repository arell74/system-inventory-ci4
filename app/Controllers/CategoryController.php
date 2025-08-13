<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class CategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $this->setPageData('Kategori', 'Manajemen Kategori Produk');

        //Get Category with product count
        $categories = $this->categoryModel->getCategoriesWithProductCount();

        $data = [
            'categories' => $categories
        ];

        return $this->render('categories/index', $data);
    }

    public function create()
    {
        $this->setPageData('Tambah Kategori', 'Buat Kategori produk baru');

        $data = [
            'category' => [
                'name' => '',
                'description' => '',
                'is_active' => true,
            ],
            'validation' => service('validation')
        ];

        return $this->render('categories/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name]',
            'description' => 'permit_empty|max_length[500]'
        ];

        $messages = [
            'name' => [
                'required' => 'Nama kategori harus diisi',
                'min_length' => 'Nama kategori minimal 3 karakter',
                'max_length' => 'Nama kategori maksimal 100 karakter',
                'is_unique' => 'Nama kategori sudah ada'
            ],
            'description' => [
                'max_length' => 'Deskripsi maksimal 500 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? true : false
        ];

        if ($this->categoryModel->insert($data)) {
            $this->setFlash('success', 'Kategori berhasil ditambahkan');
            return redirect()->to('/categories');
        } else {
            $this->setFlash('error', 'Gagal menambahkan kategori');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            $this->setFlash('errors', 'Kategori Tidak ditemukan');
            return redirect()->to('/categories');
        }

        $this->setPageData('Edit Kategori', 'Edit Kategori: ' . $category['name']);

        $data = [
            'category' => $category,
            'validation' => service('validation')
        ];

        return $this->render('categories/edit', $data);
    }

    public function update($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            $this->setFlash('errors', 'Kategori Tidak ditemukan');
            return redirect()->to('/categories');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name]',
            'description' => 'permit_empty|max_length[500]'
        ];

        $messages = [
            'name' => [
                'required' => 'Nama Kategori harus diisi!',
                'min_length' => 'Nama Kategori minimal 3 karakter',
                'max_length' => 'Nama Kategori Maksimal 100 karakter',
                'is_unique' => 'Nama Kategori sudah ada'
            ],
            'description' => [
                'max_length' => 'Deskripsi Maksimal 500 Karakter'
            ]
        ];

        if(!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? true : false
        ];

        if ($this->categoryModel->update($id, $data)) {
            $this->setFlash('success', 'Kategori berhasil diupdate');
            return redirect()->to('/categories');
        } else {
            $this->setFlash('error', 'Gagal mengupdate kategori');
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonResponse(['status' => false, 'message' => 'Invalid request'], 400);
        }

        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return $this->jsonResponse(['status' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        // Check if category can be deleted
        if (!$this->categoryModel->canDelete($id)) {
            return $this->jsonResponse([
                'status' => false, 
                'message' => 'Kategori tidak bisa dihapus karena masih digunakan oleh produk'
            ], 400);
        }

        if ($this->categoryModel->delete($id)) {
            return $this->jsonResponse([
                'status' => true, 
                'message' => 'Kategori berhasil dihapus'
            ]);
        } else {
            return $this->jsonResponse([
                'status' => false, 
                'message' => 'Gagal menghapus kategori'
            ], 500);
        }
    }
}
