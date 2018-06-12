<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Document provided by the user during registration to allow their verification
 *
 * @property string name
 * @property string url
 */
class RegistrationDocument extends Model
{
    protected $fillable = ['name','url'];
    protected $touches = ['registree'];
    
    public $timestamps = false;

    /**
     * User that provided the document during registration
     */
    public function registree()
    {
        return $this->belongsTo('App\User','registree_id');
    }
}
