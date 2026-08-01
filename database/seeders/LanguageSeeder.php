<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['name' => 'English', 'code' => 'en'],
            ['name' => 'Uzbek', 'code' => 'uz'],
            ['name' => 'Russian', 'code' => 'ru'],
            ['name' => 'Turkish', 'code' => 'tr'],
            ['name' => 'Arabic', 'code' => 'ar'],
        ];

        foreach ($languages as $language) {
            Language::query()->updateOrCreate(['code' => $language['code']], $language);
        }
    }
}
