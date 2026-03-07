<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Machinery Fault'],
            ['category_name' => 'Production Issue'],
            ['category_name' => 'Quality Issue'],
            ['category_name' => 'Electrical'],
            ['category_name' => 'IT Support'],
            ['category_name' => 'Maintenance'],
            ['category_name' => 'Facility'],
            ['category_name' => 'Logistics'],
            ['category_name' => 'Security'],
            ['category_name' => 'Inventory / Material Issue'],
        ];
    
        foreach ($categories as $cat) {
            \App\Models\TicketCategory::updateOrCreate(['category_name' => $cat['category_name']], $cat);
        }
    }
}
