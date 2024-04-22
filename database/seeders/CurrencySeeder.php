<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$'],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€'],
            ['name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£'],
            ['name' => 'Nigerian Naira', 'code' => 'NGN', 'symbol' => '₦'],
        ];
        DB::beginTransaction();
        try {
            foreach ($currencies as $currency) {
                Currency::firstOrCreate($currency);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report_error($e);
        }
    }
}
