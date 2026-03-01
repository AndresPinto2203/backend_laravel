<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('currencies')->count() > 0) {
            return; 
        }

        DB::table('currencies')->insert([
            [
                'name' => 'US Dollar',
                'symbol' => 'USD',
                'exchange_rate' => 1.000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Euro',
                'symbol' => 'EUR',
                'exchange_rate' => 0.920000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bolivar',
                'symbol' => 'VES',
                'exchange_rate' => 450.000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
