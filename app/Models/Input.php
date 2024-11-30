<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    use HasFactory;
    protected $table = 'inputs';
    protected $fillable = [
                            'description',
                            'user_id',
                            'price',
                            'measurement',
                            'typeofservice',
                            'status'
                        ];
        public function setDateAttribute($value)
    {
        $this->attributes['date_budgets'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
        public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function service()
    {
        return $this->belongsTo('App\Models\Service','typeofservice');
    }
}
