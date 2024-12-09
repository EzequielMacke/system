<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputUseds extends Model
{
    use HasFactory;
    protected $fillable = [
                            'description',
                            'client_id',
                            'branch_id',
                            'order_id',
                            'user_id',
                            'status',
                            'date_created',
                            'constructionsite_id',
                        ];
        public function setDateAttribute($value)
    {
        $this->attributes['date_created'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
        public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }
        public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }
        public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
        public function construction_site()
    {
        return $this->belongsTo('App\Models\ConstructionSite','constructionsite_id');
    }
        public function order_service()
    {
        return $this->belongsTo('App\Models\OrderService');
    }
    public function order_service_details()
    {
        return $this->belongsTo('App\Models\OrderServiceDetail');
    }

}
