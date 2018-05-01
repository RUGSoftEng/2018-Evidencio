<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public $timestamps = false;

    protected $fillable = ['evidencioModelId','resultName','resultNumber','expectedType','representationLabel','representationType'];

    public function step()
    {
        return $this->belongsTo('App\Step','stepId');
    }
}
