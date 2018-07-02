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
    protected $fillable = ['model_id'];
    protected $touches = ['workflow'];

    public $timestamps = false;

    public function workflow()
    {
        return $this->belongsTo('App\Workflow', 'workflow_id');
    }
}
