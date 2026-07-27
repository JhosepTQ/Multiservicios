<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user only
        $admin = \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@multiservice.com',
            'password' => bcrypt('password123'),
        ]);

        $this->command->info('Admin user creado. Base de datos vacía lista para usar.');
    }
}
