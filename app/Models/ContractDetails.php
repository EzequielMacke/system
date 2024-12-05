<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractDetails extends Model
{
    use HasFactory;
    protected $fillable = [
                            'contract_id',
                            'service_id',
                            'obligation_id',
                            'clause_id',
                        ];

    public function contract()
    {
        return $this->belongsTo('App\Models\Contracts','contract_id');
    }
    public function obligation()
    {
        return $this->belongsTo('App\Models\Obligations','obligation_id');
    }
    public function clauses()
    {
        return $this->belongsTo('App\Models\Clauses','clause_id');
    }
}
