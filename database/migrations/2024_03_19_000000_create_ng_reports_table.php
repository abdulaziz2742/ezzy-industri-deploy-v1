<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ng_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('operator_name');
            $table->string('employee_id');
            $table->string('machine_name');
            $table->enum('shift', [1, 2, 3]);
            $table->string('batch_number');
            $table->string('product_name');
            $table->string('product_code');
            $table->integer('total_production');
            $table->integer('total_ng');
            $table->decimal('ng_percentage', 5, 2);
            $table->string('ng_type');
            $table->string('ng_type_other')->nullable();
            $table->text('what');
            $table->text('why');
            $table->text('where');
            $table->text('when');
            $table->text('who');
            $table->text('how');
            $table->text('countermeasure');
            $table->text('preventive_action');
            $table->string('pic');
            $table->string('verified_by')->nullable();
            $table->enum('status', ['diperbaiki', 'scrap', 'rework', 'pending']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ng_reports');
    }
};