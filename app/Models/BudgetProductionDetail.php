<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetProductionDetail extends Model
{
    protected $fillable = [	
                            'quantity',
                            'amount',
                            'budget_production_id',                      
    						'articulo_id',
                            'wish_production_id'];
    
    public function budget_production()
    {
        return $this->belongsTo('App\Models\BudgetProduction', 'budget_production_id');
    }

    public function articulo()
    {
        return $this->belongsTo('App\Models\Articulo','articulo_id');
    }

    public function wish_production()
    {
        return $this->belongsTo('App\Models\WishProduction', 'wish_production_id');
    }
    
}