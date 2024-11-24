<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishProductionDetail extends Model
{
    use HasFactory;
    protected $fillable = [ 
                            'wish_production_id',
                            'articulo_id',
                            'quantity'
                        ];
    public function articulo()
    {
        return $this->belongsTo('App\Models\Articulo');
    }
    public function wish_production()
    {
        return $this->belongsTo('App\Models\WishProduction', 'wish_production_id');
    }
}
