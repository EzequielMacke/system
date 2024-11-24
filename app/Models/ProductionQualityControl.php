<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionQualityControl extends Model
{
    use HasFactory;
    protected $fillable = [
                            'date',
                            'status',
                            'client_id',
                            'branch_id',
                            'user_id'
                        ];
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
    public function production_quality_control_details()
    {
        return $this->hasMany('App\Models\ProductionQualityControlDetail','production_quality_id');
    }
    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }
    public function Branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
