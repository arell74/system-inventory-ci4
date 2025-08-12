<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        
        echo "Database seeded successfully!\n";
        echo "- Categories: " . $this->db->table('categories')->countAll() . " records\n";
        echo "- Products: " . $this->db->table('products')->countAll() . " records\n";
    }
}
