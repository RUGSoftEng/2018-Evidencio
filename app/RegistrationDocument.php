<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Document provided by the user during registration to allow their verification
 *
 * @property string name
 * @property string URL
 */
class RegistrationDocument extends Model
{
    public $timestamps = false;

    protected $fillable = ['name','URL'];

    /**
     * User that provided the document during registration
     */
    public function registree()
    {
        return $this->belongsTo('App\User','registreeId');
    }
}
