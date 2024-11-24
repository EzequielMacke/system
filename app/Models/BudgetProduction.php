<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetProduction extends Model 
{
    use HasFactory;

    protected $fillable = [
                            'date',
                            'status',
                            'total_amount',
                            'branch_id',
            	            'user_id',
                            'client_id'
                          ];

    protected $dates = ['date'];

    public function setDateAttribute($value)
    {
        if($value)
        {
            $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }else
        {
            $this->attributes['date'] = NULL;
        }        
    }

    public function budget_production_details()
    {
        return $this->hasMany('App\Models\BudgetProductionDetail');
    }

    

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function deposit()
    {
        return $this->belongsTo('App\Models\Deposit', 'deposit_id');
    } 
    
    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

 
}