<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistrationDocument extends Model
{
    public $timestamps = false;

    protected $fillable = ['name','URL'];

    public function registree()
    {
        return $this->belongsTo('App\User','registreeId');
    }
}
