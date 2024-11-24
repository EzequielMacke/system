<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    use HasFactory;
    protected $fillable = [
                            'user_id',
                            'construction_site_id',
                            'budget_id',
                            'client_id',
                            'branch_id',
                            'date',
                            'start_date',
                            'observation',
                            'status'
                        ];
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value;
    }
        public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
        public function construction_site()
    {
        return $this->belongsTo('App\Models\ConstructionSite','construction_site_id');
    }
        public function budget()
    {
        return $this->belongsTo('App\Models\BudgetService');
    }
        public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }
        public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }
        public function order_service_details()
    {
        return $this->hasMany('App\Models\OrderServiceDetail');
    }
}
