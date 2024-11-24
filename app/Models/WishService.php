<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishService extends Model
{
    use HasFactory;
    protected $fillable = [
                            'date_wish',
                            'client_id',
                            'user_id',
                            'construction_site_id',
                            'observation',
                            'branch_id',
                            'status'
                        ];
    public function setDateAttribute($value)
    {
        $this->attributes['date_wish'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
    public function wish_service_detail()
    {
        return $this->hasMany('App\Models\WishServiceDetail','wish_services_id');
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
        return $this->belongsTo('App\Models\ConstructionSite');
    }

}
