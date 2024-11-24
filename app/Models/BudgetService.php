<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetService extends Model
{
    use HasFactory;
    protected $table = 'budgets_service';
    protected $fillable = [
                            'description',
                            'user_id',
                            'client_id',
                            'wish_service_id',
                            'constructionsite_id',
                            'date_budgets',
                            'tax',
                            'currency',
                            'status'
                        ];
        public function setDateAttribute($value)
    {
        $this->attributes['date_budgets'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
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
        public function wish_service()
    {
        return $this->belongsTo('App\Models\WishService');
    }
        public function budget_service_detail()
    {
        return $this->hasMany('App\Models\BudgetServiceDetail');
    }
}
