<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Model loaded in the designer page of the workflow
 *
 * @property int modelId
 */
class LoadedEvidencioModel extends Model
{
    public $timestamps = false;

    protected $fillable = ['modelId'];

    public function workflow()
    {
        return $this->belongsTo('App\Workflow', 'workflowId');
    }
}
