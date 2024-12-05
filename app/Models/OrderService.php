<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    use HasFactory;
    protected $fillable = [
                            'date_created',
                            'date_ending',
                            'branch_id',
                            'user_id',
                            'contract_id',
                            'client_id',
                            'constructionsite_id',
                            'budget_id',
                            'status',
                            'observation'
                        ];
        public function setDateAttribute($value)
    {
        $this->attributes['date_created'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        $this->attributes['date_ending'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
        public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }
        public function contracts()
    {
        return $this->belongsTo('App\Models\Contracts');
    }
        public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }
        public function construction_site()
    {
        return $this->belongsTo('App\Models\ConstructionSite','constructionsite_id');
    }
        public function budget_service()
    {
        return $this->belongsTo('App\Models\BudgetService','budget_service_id','id');
    }
        public function budget_service_detail()
    {
        return $this->hasMany('App\Models\BudgetServiceDetail');
    }
         public function order_service_details()
    {
        return $this->hasMany('App\Models\OrderServiceDetail');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
