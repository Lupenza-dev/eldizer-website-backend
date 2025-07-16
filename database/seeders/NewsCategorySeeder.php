<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('news_categories')->truncate();

        $resources = [
            ['id' => 1, 'name' => 'Finance','is_published'=>true,'created_by'=>1],
            ['id' => 2, 'name' => 'Education','is_published'=>true,'created_by'=>1],
        ];

        foreach ($resources as $resource) {
            NewsCategory::query()->create($resource);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
