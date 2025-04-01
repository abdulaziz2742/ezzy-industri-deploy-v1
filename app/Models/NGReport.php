<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NGReport extends Model
{
    protected $table = 'ng_reports';
    
    protected $fillable = [
        'production_id',
        'date',
        'operator_name',
        'employee_id',
        'machine_name',
        'shift',
        'batch_number',
        'product_name',
        'product_code',      // Tambahkan kembali ini
        'total_production',
        'total_ng',
        'ng_percentage',
        'ng_type',
        'ng_type_other',
        'what',
        'why',
        'where',
        'when',
        'who',
        'how',
        'countermeasure',
        'preventive_action',
        'pic',
        'verified_by',
        'status'
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }
}