<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderServiceDetail extends Model
{
    use HasFactory;
    protected $fillable = [
                            'order_id',
                            'service_id',
                            'input_id',
                            'input_quantity'
                        ];
        public function order_service()
    {
        return $this->hasMany('App\Models\OrderService');
    }
    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }
    public function input()
    {
        return $this->belongsTo('App\Models\Input');
    }
}
