<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sop_steps', function (Blueprint $table) {
            $table->string('cloudinary_id')->nullable()->after('gambar_path');
        });
    }

    public function down()
    {
        Schema::table('sop_steps', function (Blueprint $table) {
            $table->dropColumn('cloudinary_id');
        });
    }
};