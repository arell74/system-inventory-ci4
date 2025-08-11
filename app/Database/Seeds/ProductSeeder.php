<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Elektronik
            [
                'name'          => 'iPhone 14 Pro',
                'sku'           => 'ELEIP001',
                'category_id'   => 1,
                'description'   => 'iPhone 14 Pro 128GB Space Black',
                'price'         => 15000000.00,
                'cost_price'    => 13000000.00,
                'min_stock'     => 5,
                'current_stock' => 2,
                'unit'          => 'pcs',
                'created_at'    => date('Y-m-d H:i:s')
            ],
            [
                'name'          => 'Samsung Galaxy S23',
                'sku'           => 'ELESAM001',
                'category_id'   => 1,
                'description'   => 'Samsung Galaxy S23 256GB Phantom Black',
                'price'         => 12000000.00,
                'cost_price'    => 10500000.00,
                'min_stock'     => 5,
                'current_stock' => 8,
                'unit'          => 'pcs',
                'created_at'    => date('Y-m-d H:i:s')
            ],
            
            // Fashion
            [
                'name'          => 'Kemeja Batik Pria',
                'sku'           => 'FASKER001',
                'category_id'   => 2,
                'description'   => 'Kemeja Batik Premium Size M-XL',
                'price'         => 250000.00,
                'cost_price'    => 150000.00,
                'min_stock'     => 20,
                'current_stock' => 8,
                'unit'          => 'pcs',
                'created_at'    => date('Y-m-d H:i:s')
            ],
            
            // Makanan
            [
                'name'          => 'Kopi Arabica Premium',
                'sku'           => 'MAKKOP001',
                'category_id'   => 3,
                'description'   => 'Kopi Arabica Premium 250gr',
                'price'         => 85000.00,
                'cost_price'    => 60000.00,
                'min_stock'     => 50,
                'current_stock' => 12,
                'unit'          => 'pack',
                'created_at'    => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('products')->insertBatch($data);
    }
}
