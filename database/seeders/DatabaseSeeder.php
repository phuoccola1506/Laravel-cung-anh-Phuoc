<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed categories, brands, products with variants
        $this->call([
            CategoriesTableSeeder::class,
            BrandsTableSeeder::class,
            ProductsTableSeeder::class,
            SettingSeeder::class,
        ]);

        // Create default test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0563184952',
            'role' => 'admin',
            'active' => 1,
        ]);

        // Create additional users for orders
        User::factory(10)->create();

        // Seed discounts
        $this->call([
            DiscountSeeder::class,
        ]);

        // Seed orders and order items
        $this->call([
            OrderSeeder::class,
        ]);
    }
}
