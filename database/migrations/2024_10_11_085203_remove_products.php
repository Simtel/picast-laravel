<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::drop('products_urls');
        Schema::drop('products');
        Schema::drop('market_places');
        DB::table('permissions')->where('name', 'edit prices')->delete();
    }


    public function down(): void
    {
        //
    }
};
