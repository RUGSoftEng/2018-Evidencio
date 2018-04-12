<!-- Modal -->
<div class="modal fade" id="modalStep" tabindex="-1" role="dialog" aria-labelledby="modalStepOptions" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelTitleId">Step Options</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <!-- TOP -->
                    <div class="row">
                        <div class="col-md-4">
                            <label for="colorPick">Pick a color:</label>
                            <button id="colorPick" type="button" class="btn btn-colorpick dropdown-toggle outline" data-toggle="dropdown" :style="{'background-color': selectedColor}">@{{ nodeID }}</button>
                            <ul class="dropdown-menu">
                                <li><div id="colorPalette"></div></li>
                            </ul>
                            <div class="form-group">
                                <label for="stepType">Select step-type:</label>
                                <select class="custom-select" name="stepType" id="stepType" :disabled="modalNodeID==0" v-model="stepType">
                                    <option value="input">Input</option>
                                    <option value="result">Result</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-8" v-if="stepType == 'input'">
                            <vue-multiselect v-model="selectedVariables" :options="possibleVariables" :multiple="true" :close-on-select="false" :clear-on-select="false" label="title" track-by="title" :limit=3 :limit-text="multiselectVariablesText" :hide-selected="true" :preserve-search="true" placeholder="Choose variables">
                                <template slot="tag" slot-scope="props"><span class="badge badge-info badge-larger"><span class="badge-maxwidth">@{{ props.option.title }}</span>&nbsp;<span class="custom__remove" @click="props.remove(props.option)">❌</span></span></template> 
                            </vue-multiselect>
                        </div>

                        <div class="col-md-8" v-if="stepType == 'result'">
                            
                        </div>
                    </div>

                    <!-- Middle -->
                    <div class="row">
                        <div class="col">
                            
                            <div class="card" v-if="stepType == 'input'">
                                <div class="card-header">
                                    <nav>
                                        <div class="nav nav-tabs card-header-tabs" id="nav-tab-modal" role="tablist">
                                            <a class="nav-item nav-link active" id="nav-variables-tab" data-toggle="tab" href="#nav-variables" role="tab" aria-controls="nav-variables" aria-selected="true">Variables</a>
                                            <a class="nav-item nav-link" id="nav-logic-tab" data-toggle="tab" href="#nav-logic" role="tab" aria-controls="nav-logic" aria-selected="false">Logic</a>
                                        </div>
                                    </nav>
                                </div>
                                <div class="card-body" id="modalCard">
                                    <div class="tab-content" id="nav-tabContent-modal">

                                        <div class="tab-pane fade show active" id="nav-variables" role="tabpanel" aria-labelledby="nav-variables-tab">
                                            <div id="accModalVars">
                                                <div class="card" v-for="(variable, index) in selectedVariables">
                                                    <div class="card-header collapsed" :id="'collapHeader_' + index" data-toggle="collapse" :data-target="'#collap_' + index" data-parent="#accModalVars" aria-expanded="false" :aria-controls="'collap_' + index">
                                                        <h6 class="mb-0">
                                                                @{{ variable.title }}
                                                        </h6>
                                                    </div>

                                                    <div :id="'collap_' + index" class="collapse" :aria-labelledby="'#collap_' + index" data-parent="#accModalVars">
                                                        <div class="card-body">
                                                            <form onsubmit="return false">
                                                                <div class="form-group">
                                                                    <label :for="'titleVar_' + index">Title: </label>
                                                                    <input type="text" name="" :id="'titleVar_' + index" class="form-control" v-model="variable.title" placeholder="Title" :disabled="!editVariableFlags[variable.ind]">
                                                                    <small :id="'titleVarHelp_' + index" class="form-text text-muted">Title of the variable</small>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label :for="'descriptionVar_' + index">Description: </label>
                                                                    <textarea class="form-control" :id="'descriptionVar_' + index" cols="30" rows="3" v-model="variable.description" :disabled="!editVariableFlags[variable.ind]"></textarea>
                                                                    <small :id="'descriptionVarHelp_' + index" class="form-text text-muted">Description of the variable</small>
                                                                    <input type="image" class="buttonIcon" :src="getImage(editVariableFlags[variable.ind])" @click="editVariable(variable.ind)" alt="Edit">
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>

                                        <div class="tab-pane fade" id="nav-logic" role="tabpanel" aria-labelledby="nav-logic-tab">
                                            <div class="container-fluid">
                                                <div class="row" v-for="(rule, index) in rules">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label :for="'ruleName_' + index">Name:</label>
                                                            <input type="text" class="form-control" name="Name" :id="'ruleName_' + index" :aria-describedby="'helpId_' + index" placeholder="Rule name" v-model="rule.name">
                                                            <small :id="'helpId_' + index" class="form-text text-muted">Name of the rule</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card" v-if="stepType == 'result'">
                                

                            </div>
            
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" @click="saveChanges">Save</button>
            </div>
        </div>
    </div>
</div>