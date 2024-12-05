<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contracts extends Model
{
    use HasFactory;
    protected $fillable = [
                            'description',
                            'date_created',
                            'date_signed',
                            'constructionsite_id',
                            'term',
                            'budget_service_id',
                            'client_id',
                            'user_id',
                            'placement',
                            'issue',
                            'status'
                        ];

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
    public function budget_service()
    {
        return $this->belongsTo('App\Models\BudgetService','budget_service_id','id');
    }
        public function budget_service_detail()
    {
        return $this->hasMany('App\Models\BudgetServiceDetail');
    }
}
