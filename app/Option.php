<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['value'];
    public $timestamps = false;

    public function categoricalField()
    {
        return $this->belongsTo('App\Field','categoricalFieldId');
    }
}
