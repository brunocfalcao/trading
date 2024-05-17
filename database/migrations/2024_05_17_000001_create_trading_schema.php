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

        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->string('pair');
            $table->decimal('price_highest', 20, 8);
            $table->decimal('price_lowest', 20, 8);
            $table->decimal('stop_loss', 20, 8);
            $table->decimal('tp1', 20, 8);
            $table->decimal('tp2', 20, 8);
            $table->decimal('tp3', 20, 8);
            $table->decimal('tp4', 20, 8);
            $table->softDeletes();
            $table->timestamps();
        });

        // https://developers.binance.com/docs/derivatives/usds-margined-futures/trade/rest-api/New-Order
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('clientOrderId');
            $table->string('cumQty');
            $table->string('cumQuote');
            $table->string('executedQty');
            $table->string('orderId');
            $table->string('avgPrice');
            $table->string('origQty');
            $table->string('price');
            $table->string('reduceOnly');
            $table->string('side');
            $table->string('positionSide');
            $table->string('status');
            $table->string('stopPrice');
            $table->string('closePosition');
            $table->string('symbol');
            $table->string('timeInForce');
            $table->string('type');
            $table->string('origType');
            $table->string('activatePrice');
            $table->string('priceRate');
            $table->string('updateTime');
            $table->string('workingType');
            $table->string('priceProtect');
            $table->string('priceMatch');
            $table->string('selfTradePreventionMode');
            $table->string('goodTillDate');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
    }
};
