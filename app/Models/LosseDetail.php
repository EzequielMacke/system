<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LosseDetail extends Model
{
    use HasFactory;
    protected $fillable = [
                            'quantity',
                            'reason',
                            'articulo_id',
                            'articulo_id'
                        ];
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function losse()
    {
        return $this->belongsTo('App\Models\Losse');
    }
    public function articulo()
    {
        return $this->belongsTo('App\Models\Articulo','articulo_id');
    }
    public function material()
    {
        return $this->belongsTo('App\Models\RawMaterial','articulo_id');
    }
}
