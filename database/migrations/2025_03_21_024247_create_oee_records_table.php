<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oee_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_id')->constrained();
            $table->foreignId('shift_id')->constrained();
            $table->date('date');
            
            // Waktu Produksi
            $table->integer('planned_production_time')->default(0);
            $table->integer('operating_time')->default(0);
            $table->integer('downtime_problems')->default(0);
            $table->integer('downtime_maintenance')->default(0);
            $table->integer('total_downtime')->default(0);
            
            // Output Produksi
            $table->integer('total_output')->default(0);
            $table->integer('good_output')->default(0);
            $table->integer('defect_count')->default(0);
            $table->decimal('ideal_cycle_time', 10, 2)->default(0);
            
            // Nilai OEE
            $table->decimal('availability_rate', 5, 2)->default(0);
            $table->decimal('performance_rate', 5, 2)->default(0);
            $table->decimal('quality_rate', 5, 2)->default(0);
            $table->decimal('oee_score', 5, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('oee_records');
    }
};