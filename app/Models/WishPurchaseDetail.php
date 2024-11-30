<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishPurchaseDetail extends Model
{
    use HasFactory;
    protected $fillable = [
                            'wish_purchase_id',
                            'articulo_id',
                            'quantity',
                            'level',
                            'deposit_id',
                            'presentation',
                            'description'
                        ];
    public function material()
    {
        return $this->belongsTo('App\Models\RawMaterial');
    }
}
