<?php

namespace Brunocfalcao\Trading\Database\Seeders;

use Brunocfalcao\Trading\Futures;
use Brunocfalcao\Trading\Models\Symbol;
use Illuminate\Database\Seeder;

class PopulateSchemaSeeder extends Seeder
{
    public function run()
    {
        $client = new Futures();

        $exchangeInfo = $client->exchangeInfo();

        foreach ($exchangeInfo['symbols'] as $symbol) {
            Symbol::create([
                'pair' => $symbol['pair'],
                'price_precision' => $symbol['pricePrecision'],
                'quantity_precision' => $symbol['quantityPrecision'],
                'base_asset_precision' => $symbol['baseAssetPrecision'],
                'quote_precision' => $symbol['quotePrecision'],
            ]);
        }
    }
}
