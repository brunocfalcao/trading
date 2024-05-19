<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->string('pair');
            $table->decimal('mark_price', 20, 8);
            $table->decimal('previous_price', 20, 8)
                ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')
                ->nullable();
            $table->string('positionAmt')
                ->nullable();
            $table->string('entryPrice')
                ->nullable();
            $table->string('breakEvenPrice')
                ->nullable();
            $table->string('markPrice')
                ->nullable();
            $table->string('unRealizedProfit')
                ->nullable();
            $table->string('liquidationPrice')
                ->nullable();
            $table->string('leverage')
                ->nullable();
            $table->string('maxNotionalValue')
                ->nullable();
            $table->string('marginType')
                ->nullable();
            $table->string('isolatedMargin')
                ->nullable();
            $table->string('isAutoAddMargin')
                ->nullable();
            $table->string('positionSide')
                ->nullable();
            $table->string('notional')
                ->nullable();
            $table->string('isolatedWallet')
                ->nullable();
            $table->string('updateTime')
                ->nullable();
            $table->string('isolated')
                ->nullable();
            $table->string('adlQuantile')
                ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // https://developers.binance.com/docs/derivatives/usds-margined-futures/trade/rest-api/New-Order
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('clientOrderId')
                ->nullable();
            $table->string('cancelOnMarkPrice')
                ->nullable();
            $table->string('newStopLossMarkPrice')
                ->nullable();
            $table->string('cumQty')
                ->nullable();
            $table->string('cumQuote')
                ->nullable();
            $table->string('executedQty')
                ->nullable();
            $table->string('orderId')
                ->nullable();
            $table->string('avgPrice')
                ->nullable();
            $table->string('origQty')
                ->nullable();
            $table->string('price')
                ->nullable();
            $table->string('reduceOnly')
                ->nullable();
            $table->string('side')
                ->nullable();
            $table->string('positionSide')
                ->nullable();
            $table->string('status')
                ->nullable();
            $table->string('stopPrice')
                ->nullable();
            $table->string('closePosition')
                ->nullable();
            $table->string('symbol')
                ->nullable();
            $table->string('timeInForce')
                ->nullable();
            $table->string('type')
                ->nullable();
            $table->string('origType')
                ->nullable();
            $table->string('activatePrice')
                ->nullable();
            $table->string('priceRate')
                ->nullable();
            $table->string('workingType')
                ->nullable();
            $table->string('priceProtect')
                ->nullable();
            $table->string('priceMatch')
                ->nullable();
            $table->string('selfTradePreventionMode')
                ->nullable();
            $table->string('goodTillDate')
                ->nullable();
            $table->string('updateTime')
                ->nullable();
            $table->string('time')
                ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
    }
};
