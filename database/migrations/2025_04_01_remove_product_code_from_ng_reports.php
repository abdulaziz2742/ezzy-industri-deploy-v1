<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ng_reports', function (Blueprint $table) {
            $table->dropColumn('product_code');
        });
    }

    public function down()
    {
        Schema::table('ng_reports', function (Blueprint $table) {
            $table->string('product_code')->nullable();
        });
    }
};