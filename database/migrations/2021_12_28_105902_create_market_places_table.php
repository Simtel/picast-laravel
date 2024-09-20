<?php

declare(strict_types=1);

use App\Models\MarketPlaces;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_places', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->timestamps();
        });

        MarketPlaces::create(['name' => 'ozon.ru', 'url' => 'https://ozon.ru']);
        MarketPlaces::create(['name' => 'wb.ru', 'url' => 'https://wb.ru']);
        MarketPlaces::create(['name' => 'kazanexpress.ru', 'url' => 'https://kazanexpress.ru']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_places');
    }
}
