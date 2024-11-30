<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetServiceDetail extends Model
{
    use HasFactory;
    protected $table = 'budgets_service_details';
    protected $fillable = [
                            'budget_service_id',
                            'service_id',
                            'price',
                            'quantity',
                            'level',
                            'total_price',
                            'quantity_per_meter',
                            'input_id',

                        ];

    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }
    public function budget_service()
    {
        return $this->belongsTo('App\Models\BudgetService','budget_service_id');
    }
    public function input()
    {
        return $this->belongsTo('App\Models\Input');
    }
}
