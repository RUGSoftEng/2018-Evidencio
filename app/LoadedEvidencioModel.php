<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoadedEvidencioModel extends Model
{
    public $timestamps = false;

    protected $fillable = ['modelId'];

    public function workflow()
    {
        return $this->belongsTo('App\Workflow', 'workflowId');
    }
}
