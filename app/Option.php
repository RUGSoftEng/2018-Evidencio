<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Possible value of a categorial field
 *
 * @property string title Title of the option as in Evindecio API
 * @property friendly_title Title of the option shown to the patient
 */
class Option extends Model
{
    protected $fillable = ['title', 'friendly_title'];
    public $timestamps = false;

    /**
     * Categorical field which has this option
     */
    public function categoricalField()
    {
        return $this->belongsTo('App\Field','categorical_field_id');
    }
}
