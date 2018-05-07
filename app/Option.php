<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Possible value of a categorial field
 *
 * @property string value Name of the option
 */
class Option extends Model
{
    protected $fillable = ['value'];
    public $timestamps = false;

    /**
     * Categorical field which has this option
     */
    public function categoricalField()
    {
        return $this->belongsTo('App\Field','categoricalFieldId');
    }
}
