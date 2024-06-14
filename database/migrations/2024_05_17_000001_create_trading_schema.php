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
        Schema::create('symbols', function (Blueprint $table) {
            $table->id();
            $table->string('pair')
                  ->nullable();

            $table->decimal('last_price', 20, 8)
                  ->nullable();

            $table->decimal('previous_price', 20, 8)
                  ->nullable();

            $table->decimal('older_price', 20, 8)
                  ->nullable();

            $table->string('price_precision')
                  ->nullable();

            $table->string('quantity_precision')
                  ->nullable();

            $table->string('base_asset_precision')
                  ->nullable();

            $table->string('quote_precision')
                  ->nullable();

            $table->decimal('_entry_price', 20, 8)
                  ->nullable();

            $table->decimal('_stop_loss_price', 20, 8)
                  ->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => 'Brunocfalcao\Trading\Database\Seeders\PopulateSchemaSeeder',
            '--force' => true,
        ]);
    }

    public function down(): void
    {
    }
};
