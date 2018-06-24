<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Fields that are provided in an input step. They can be either categorical
 * (then they contain a number of options and have empty continuous attributes)
 * or continuous (they provide additional attributes)
 *
 * @property string friendly_title Title that is friendly to a patient
 * @property string friendly_description Description that is friendly to a patient
 * @property int continuous_field_max Maximum input value (Applies only to
 * continuous fields)
 * @property int continuous_field_min Minimum input value (Applies only to
 * continuous fields)
 * @property int continuous_field_step_by Interval between possible input values
 * (Applies only to continuous fields)
 * @property int continuous_field_unit Unit of the value (kilograms, years, etc.)
 * (Applies only to continuous fields)
 * @property int evidencio_variable_id Evidencio API variable id associated with
 * this field in the designer page
 */
class Field extends Model
{
    protected $touches = ['inputSteps'];

    public $timestamps = false;

    protected $fillable = [
        'evidencio_variable_id','friendly_title','friendly_description','continuous_field_max','continuous_field_min','continuous_field_step_by','continuous_field_unit'
    ];

    /**
     * Possible options of a field (Applies only to categorical fields)
     */
    public function options()
    {
        return $this->hasMany('App\Option','categorical_field_id');
    }

    /**
     * Input steps that have this field
     * @property int order defines index of a field in a step by which it should
     * be ordered
     */
    public function inputSteps()
    {
        return $this->belongsToMany('App\Step','field_in_input_steps','field_id','input_step_id')->withPivot("order");
    }

    public function usedInRunsInSteps()
    {
        return $this->belongsToMany('App\Step','model_run_field_mappings','field_id','step_id');
    }


    /**
     * Updates the information of a single variable
     *
     * @param Array $field Array containing data of field (variable)
     */
    public function saveSingleField($field)
    {
        $this->friendly_title = $field["title"];
        $this->friendly_description = $field["description"];
        $this->evidencio_variable_id = $field["id"];
        if ($field["type"] == "continuous") {
            $this->continuous_field_max = $field["options"]["max"];
            $this->continuous_field_min = $field["options"]["min"];
            $this->continuous_field_unit = $field["options"]["unit"];
            $this->continuous_field_step_by = $field["options"]["step"];
        }
        $this->save();
    }

    /**
     * Saves/updates the options belonging to a categorical variable.
     *
     * @param Array $options Array of options
     * @return Array Array filled with the database IDs of the saved options.
     */
    public function saveCategoricalOptions($options)
    {
        $optionIds = [];
        $savedOptions = $this->options()->get();
        foreach ($options as $option) {
            if ($savedOptions->isNotEmpty() && ($opt = $savedOptions->where("id", $option["databaseId"]))->isNotEmpty()) {
                $opt = $opt->first();
                $opt->setInformation($option);
                $opt->save();
                $savedOptions = $savedOptions->reject(function ($value) use ($option) {
                    return ($value->id == $option["databaseId"]);
                });
            } else {
                $opt = new Option;
                $opt->setInformation($option);
                $this->options()->save($opt);
            }
            $optionIds[] = $opt->id;
        }
        $savedOptions->map(function ($value) {
            $value->delete();
        });
        return $optionIds;
    }

    /**
     * Removes a field from the database. First detaches and deletes connected options.
     *
     * @return void
     */
    public function removeField()
    {
        $stepsUsing = $this->usedInRunsInSteps()->get();
        $stepsUsing->map(function ($value) {
            $value->modelRunFields()->detach($this);
        });
        $options = $this->options()->get();
        foreach ($options as $option) {
            $option->delete();
        }
        $this->delete();
    }

    /**
     * Loads a single variable. The variables are called `Fields` in the database.
     *
     * @return Array Array containing all the fields of the variable and the options, if available.
     */
    public function loadField()
    {
        $retObj = [];
        $retObj["databaseId"] = $this->id;
        $retObj["title"] = $this->friendly_title;
        $retObj["description"] = $this->friendly_description;
        $retObj["id"] = $this->evidencio_variable_id;
        $options = $this->options()->get();
        if ($options->isEmpty()) {
            $retObj["type"] = "continuous";
            $retObj["options"]["max"] = $this->continuous_field_max;
            $retObj["options"]["min"] = $this->continuous_field_min;
            $retObj["options"]["step"] = $this->continuous_field_step_by;
            $retObj["options"]["unit"] = $this->continuous_field_unit;
        } else {
            $retObj["type"] = 'categorical';
            $retObj["options"] = [];
            foreach ($options as $key => $option) {
                $retObj["options"][$key] = $option->loadOption();
            }
        }
        return $retObj;
    }

    /**
     * Load a field for an API-call variable mapping.
     * Unfortunately some required information for the designer is unknown at this point.
     * The localVariable & evidencioTitle are set at the client side.
     *
     * @return Array Array containing variable information required for api-call variable mapping
     */
    public function loadFieldForApiVariableMapping()
    {
        return [
            "evidencioVariableId" => $this->pivot->evidencio_field_id,
            "fieldId" => $this->id,
            "localVariable" => -1,
            "evidencioTitle" => ""
        ];
    }
}
