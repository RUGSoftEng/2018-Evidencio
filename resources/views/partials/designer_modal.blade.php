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
                            <button id="colorPick" type="button" class="btn btn-colorpick dropdown-toggle outline" data-toggle="dropdown" :style="{'background-color': modalSelectedColor}">@{{ modalDatabaseStepID }}</button>
                            <ul class="dropdown-menu">
                                <li><div id="colorPalette"></div></li>
                            </ul>
                            <div class="form-group">
                                <label for="stepType">Select step-type:</label>
                                <select class="custom-select" name="stepType" id="stepType" :disabled="modalNodeID==0" v-model="modalStepType">
                                    <option value="input">Input</option>
                                    <option value="result">Result</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-8" v-if="modalStepType == 'input'">
                            <vue-multiselect v-model="modalMultiselectSelectedVariables" :options="possibleVariables" :multiple="true" group-values="variables" group-label="title" :group-select="true" :close-on-select="false" :clear-on-select="false" label="title" track-by="title" :limit=3 :limit-text="multiselectVariablesText" :preserve-search="true" placeholder="Choose variables" @remove="modalRemoveVariables" @select="modalSelectVariables">
                                <template slot="tag" slot-scope="props"><span class="badge badge-info badge-larger"><span class="badge-maxwidth">@{{ props.option.title }}</span>&nbsp;<span class="custom__remove" @click="props.remove(props.option)">❌</span></span></template> 
                            </vue-multiselect>
                        </div>

                        <div class="col-md-8" v-if="modalStepType == 'result'">
                            
                        </div>
                    </div>

                    <!-- Middle -->
                    <div class="row">
                        <div class="col">
                            
                            <div class="card" v-if="modalStepType == 'input'">
                                <div class="card-header">
                                    <nav>
                                        <div class="nav nav-tabs card-header-tabs nav-scroll" id="nav-tab-modal" role="tablist">
                                            <a class="nav-item nav-link active" id="nav-variables-tab" data-toggle="tab" href="#nav-variables" role="tab" aria-controls="nav-variables" aria-selected="true">Variables</a>
                                            <a class="nav-item nav-link" id="nav-logic-tab" data-toggle="tab" href="#nav-logic" role="tab" aria-controls="nav-logic" aria-selected="false">Logic</a>
                                            <a class="nav-item nav-link" id="nav-api-tab" data-toggle="tab" href="#nav-api" role="tab" aria-controls="nav-api" aria-selected="false">Model calculation</a>
                                        </div>
                                    </nav>
                                </div>
                                <div class="card-body" id="modalCard">
                                    <div class="tab-content" id="nav-tabContent-modal">

                                        <div class="tab-pane fade show active" id="nav-variables" role="tabpanel" aria-labelledby="nav-variables-tab">
                                            <div id="accModalVars">
                                                <div class="card" v-for="(variable, index) in modalSelectedVariables">
                                                    <div class="card-header collapsed" :id="'collapHeader_' + index" data-toggle="collapse" :data-target="'#collap_' + index" data-parent="#accModalVars" aria-expanded="false" :aria-controls="'collap_' + index">
                                                        <h6 class="mb-0">
                                                                @{{ modalUsedVariables[variable].title }}
                                                        </h6>
                                                    </div>

                                                    <div :id="'collap_' + index" class="collapse" :aria-labelledby="'#collap_' + index" data-parent="#accModalVars">
                                                        <div class="card-body">
                                                            <form onsubmit="return false">
                                                                <div class="form-group">
                                                                    <label :for="'titleVar_' + index">Title: </label>
                                                                    <input type="text" name="" :id="'titleVar_' + index" class="form-control" v-model="modalUsedVariables[variable].title" placeholder="Title" :disabled="!modalEditVariableFlags[modalUsedVariables[variable].ind]">
                                                                    <small :id="'titleVarHelp_' + index" class="form-text text-muted">Title of the variable</small>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label :for="'descriptionVar_' + index">Description: </label>
                                                                    <textarea class="form-control" :id="'descriptionVar_' + index" cols="30" rows="3" v-model="modalUsedVariables[variable].description" :disabled="!modalEditVariableFlags[modalUsedVariables[variable].ind]"></textarea>
                                                                    <small :id="'descriptionVarHelp_' + index" class="form-text text-muted">Description of the variable</small>
                                                                    <input type="image" class="buttonIcon" :src="getImage(modalEditVariableFlags[modalUsedVariables[variable].ind])" @click="editVariable(modalUsedVariables[variable].ind)" alt="Edit">
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>

                                        <div class="tab-pane fade" id="nav-logic" role="tabpanel" aria-labelledby="nav-logic-tab">
                                            <div class="container-fluid">
                                                <button type="button" class="btn btn-primary" @click="addRule()">Add rule</button>
                                                <table class="table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Condition</th>
                                                            <th>Target</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(rule, index) in modalRules">
                                                            <td data-label ="Name">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" name="Name" :id="'ruleName_' + index" :aria-describedby="'helpId_' + index" placeholder="Rule name" v-model="rule.name">
                                                                    <small :id="'helpId_' + index" class="form-text text-muted">Name of the rule</small>
                                                                </div>
                                                            </td>
                                                            <td data-label ="Condition">Wayne</td>
                                                            <td data-label ="Target">Batman</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="nav-api" role="tabpanel" aria-labelledby="nav-api-tab">
                                            <div class="container-fluid">
                                                <label for="apiCallModelSelect">Select model for calculation:</label>
                                                <vue-multiselect id="apiCallModelSelect" v-model="modalApiCall.model" deselect-label="Cannot be done without a model" track-by="id" label="title" placeholder="Select one" :options="modelChoiceRepresentation" :searchable="true" :allow-empty="false" open-direction="bottom" @select="apiCallModelChangeAction">
                                                </vue-multiselect>  
                                                <small class="form-text text-muted">Model used for calculation</small>
                                                <h6>Set variables used in calculation:</h6>
                                                <div class="form-group" v-for="apiVariable in modalApiCall.variables">
                                                <label :for="'var_' + apiVariable.originalID">@{{ apiVariable.originalTitle }}</label>
                                                    <select class="custom-select" :name="apiVariable.title" :id="'var_' + apiVariable.originalID" v-model="apiVariable.targetID">
                                                        <option v-for="usedVariable in modalUsedVariables" :key="usedVariable.id">@{{ usedVariable.title }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card" v-if="modalStepType == 'result'">
                                

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
