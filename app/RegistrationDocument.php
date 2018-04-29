<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistrationDocument extends Model
{
    public $timestamps = false;

    public function registree()
    {
        return $this->belongsTo('App\User','registreeId');
    }
}
