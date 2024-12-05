<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderOficialDetail extends Model
{
    use HasFactory;
    protected $fillable = [
                            'order_id',
                            'oficial_id',
                        ];
        public function order_service()
    {
        return $this->hasMany('App\Models\OrderService');
    }
    public function oficial()
    {
        return $this->belongsTo('App\Models\Oficial');
    }
}
