<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoadedEvidencioModel extends Model
{
    public $timestamps = false;

    public function workflow()
    {
        return $this->belongsTo('App\Workflow', 'workflowId');
    }
}
