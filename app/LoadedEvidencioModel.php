<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Model loaded in the designer page of the workflow
 *
 * @property int model_id
 */
class LoadedEvidencioModel extends Model
{
    public $timestamps = false;

    protected $fillable = ['model_id'];

    public function workflow()
    {
        return $this->belongsTo('App\Workflow', 'workflow_id');
    }
}
