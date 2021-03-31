<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FkeyWhois extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        \App\Models\Whois::truncate();
        Schema::table('whois', static function (Blueprint $table) {
            $table->bigInteger('domain_id')->unsigned()->change();
            $table->foreign('domain_id')
                ->references('id')
                ->on('domains')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('whois', static function (Blueprint $table) {
            $table->dropForeign('whois_domain_id_foreign');
        });
    }
}
