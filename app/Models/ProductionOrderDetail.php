<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrderDetail extends Model
{
    protected $fillable = ['production_order_id',
                           'articulo_id',
                           'articulo_id',
                           'quantity',
                           'quantity_material'];

    public function production_order()
    {
        return $this->belongsTo('App\Models\ProductionOrder','production_order_id');
    }

    public function articulo()
    {
        return $this->belongsTo('App\Models\Articulo','articulo_id');
    }

    public function raw_material()
    {
        return $this->belongsTo('App\Models\RawMaterial','articulo_id');
    }

}
