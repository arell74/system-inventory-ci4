<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'Elektronik',
                'description' => 'Produk-produk elektronik dan gadget',
                'is_active'   => true,
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'name'        => 'Fashion',
                'description' => 'Pakaian dan aksesoris fashion',
                'is_active'   => true,
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'name'        => 'Makanan & Minuman',
                'description' => 'Produk makanan dan minuman',
                'is_active'   => true,
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'name'        => 'Kesehatan & Kecantikan',
                'description' => 'Produk kesehatan dan kecantikan',
                'is_active'   => true,
                'created_at'  => date('Y-m-d H:i:s')
            ],
            [
                'name'        => 'Rumah Tangga',
                'description' => 'Peralatan rumah tangga',
                'is_active'   => true,
                'created_at'  => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('categories')->insertBatch($data);
    }
}
