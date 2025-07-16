<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('faq_categories')->truncate();

        $categories = [
            ['id' => 1, 'name' => 'Getting Started','is_published'=>true,'created_by'=>1],
            ['id' => 2, 'name' => 'Payment Plans','is_published'=>true,'created_by'=>1],
            ['id' => 3, 'name' => 'Eligibility','is_published'=>true,'created_by'=>1],
            ['id' => 4, 'name' => 'Account Management','is_published'=>true,'created_by'=>1],
        ];

        foreach ($categories as $categories) {
            FaqCategory::query()->create($categories);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
