<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotions extends Model
{
    use HasFactory;
    protected $fillable = [
                            'description',
                            'status',
                            'user_id',
                            'start_date',
                            'end_date',
                        ];
        public function setDateAttribute($value)
    {
        $this->attributes['start_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        $this->attributes['end_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
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
}
