<?php

namespace Database\Seeders;

use App\Models\AboutUs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AboutUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('about_us')->truncate();

        $categories = [
            [
                'vision' => 'To be one of the outstanding financial company that can solve financial challenge from university student, graduate, entrepreneur as well as public servant',
                'mission' => 'To walk along with scholars and public servant in day to day manner, providing services that enrich their livelihood',
                'content' => 'El-dizer financial service is only financial service (fintech) in Tanzania that serves scholar from different higher learning institution as well as public servant to get access over a number of credit facilities so that they can simplify their day to day demands and wants through digital platform such as application, website and social media',
                'values' => 'Digital Accessibility and Convenience,Transparent and Fair Practices,Fast Approval and Disbursement,Customer Support and Relationship Management',
                'is_published'=>true,
                'created_by'=>1],
           
        ];

        foreach ($categories as $categories) {
            AboutUs::query()->create($categories);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
