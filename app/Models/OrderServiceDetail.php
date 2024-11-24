<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderServiceDetail extends Model
{
    use HasFactory;
    protected $fillable = [
                            'service_id',
                            'order_service_id',
                            'quantity'
                        ];
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
        public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }
        public function order_service()
    {
        return $this->hasMany('App\Models\OrderService');
    }
}
