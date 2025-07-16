<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Getting Started','is_published'=>true,'created_by'=>1],
            ['id' => 2, 'name' => 'Payment Plans','is_published'=>true,'created_by'=>1],
            ['id' => 3, 'name' => 'Eligibility','is_published'=>true,'created_by'=>1],
            ['id' => 4, 'name' => 'Account Management','is_published'=>true,'created_by'=>1],
        ];

        foreach ($categories as $categories) {
            FaqCategory::query()->create($categories);
        }
    }
}
