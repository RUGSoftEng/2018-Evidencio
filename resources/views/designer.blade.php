@extends('layouts.app')

@section('content')
<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/solid.js" integrity="sha384-+Ga2s7YBbhOD6nie0DzrZpJes+b2K1xkpKxTFFcx59QmVPaSA8c7pycsNaFwUK6l" crossorigin="anonymous"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js" integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c" crossorigin="anonymous"></script>

<div class="wrapper">
    <nav id="sidebar">
        <div id="dismiss">
            <i class="fas fa-angle-left"></i>
        </div>

        <div class="sidebar-header">
            <h3>
                {{ Auth::user()->name }}
            </h3>
        </div>

        <ul class="list-unstyled components">
            <p>My Account</p>
            <li>
                <a class="somethingSomething" href="#homeSubmenu" data-toggle="collapse" aria-expanded="false">Workflow</a>
                <ul class="collapse list-unstyled" id="homeSubmenu">
                    <li><a href="#">Approved</a></li>
                    <li><a href="#">Rejected</a></li>
                    <li><a href="#">Drafts</a></li>
                </ul>
            </li>
            <li>
                <a class="somethingSomething" href="#pageSubmenu" data-toggle="collapse" aria-expanded="false">Administrator</a>
                <ul class="collapse list-unstyled" id="pageSubmenu">
                    <li><a href="#">Submitted Workflows</a></li>
                    <li><a href="#">User Questions</a></li>
                    <li><a href="#">User Requests</a></li>
                </ul>
                <a href="#">Edit Account Details</a>
            </li>
                <p class="paragraphInSideMenu" >Help</p>
            <li>
                <a href="#">Instructions</a>
            </li>
            <li>
                <a href="#">Contact Us</a>
            </li>
        </ul>
    </nav>
</div>
<script src="{{ asset('js/ya-simple-scrollbar.js') }}"></script>
<script src="{{ asset('js/sidebar.js') }}"></script>
<link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">

<div class="container-fluid" id="designerDiv">

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
                        <div class="row">
                            <div class="col-md-4">
                                <label for="colorPick">Pick a color:</label>
                                <button id="colorPick" type="button" class="btn btn-colorpick dropdown-toggle outline" data-toggle="dropdown" :style="{'background-color': selectedColor}" v-if="modalNodeID != -1">@{{ steps[modalNodeID].id }}</button>
                                <ul class="dropdown-menu">
                                    <li><div id="colorPalette"></div></li>
                                </ul>
                                <div class="form-group">
                                    <label for="stepType">Select step-type:</label>
                                    <select class="custom-select" name="stepType" id="stepType">
                                        <option value="input" selected>Input</option>
                                        <option value="result">Result</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <vue-multiselect v-model="selectedVariables" :options="possibleVariables" :multiple="true" :close-on-select="false" :clear-on-select="false" label="title" track-by="title" :limit=3 :limit-text="multiselectVariablesText" :hide-selected="true" :preserve-search="true" placeholder="Choose variables" :preselect-first="true">
                                    <template slot="tag" slot-scope="props"><span class="badge badge-info badge-larger"><span class="badge-maxwidth">@{{ props.option.title }}</span>&nbsp;<span class="custom__remove" @click="props.remove(props.option)">❌</span></span></template> 
                                </vue-multiselect>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <!--<div id="accModalSections">
                                    <div class="card">
                                        <div class="card-header" id="headingVars" data-toggle="collapse" data-target="#collapseVars" aria-expanded="true" aria-controls="collapseVars">
                                            <h5 class="mb-0">
                                                Variables
                                            </h5>
                                        </div>

                                        <div id="collapseVars" class="collapse show" aria-labelledby="headingVars" data-parent="#accModalSections">
                                            <div class="card-body">
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
                                                                      <label for="titleText">Title: </label>
                                                                      <input type="text" name="" id="titleText" class="form-control" v-model="variable.title" placeholder="Title" :disabled="!editVariableFlags[variable.ind]">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="descriptionText">Description: </label>
                                                                        <textarea class="form-control" id="descriptionText" cols="30" rows="3" v-model="variable.description" :disabled="!editVariableFlags[variable.ind]"></textarea>
                                                                        &nbsp;&nbsp;
                                                                        <input type="image" class="buttonIcon" :src="getImage(this.editVariableFlags[variable.ind])" @click="editVariable(variable.ind)" alt="Edit">
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                            
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header collapsed" id="headingLogic" data-toggle="collapse" data-target="#collapseLogic" aria-expanded="false" aria-controls="collapseLogic">
                                            <h5 class="mb-0">
                                                Logic
                                            </h5>
                                        </div>
      
                                        <div id="collapseLogic" class="collapse" aria-labelledby="headingLogic" data-parent="#accModalSections">
                                            <div class="card-body">
                                                <div>
                                                    <label class="typo__label">Single select</label>
                                                    <multiselect v-model="value" :options="options" :searchable="false" :close-on-select="false" :show-labels="false" placeholder="Pick a value"></multiselect>
                                                </div>
                                                
                                                <svg width="50" height="50" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="3" y="3" width="40" height="40" rx="5" ry="5" style="stroke:black; stroke-width: 1" :style="{'fill': }"/>
                                                </svg>

                                            </div>
                                        </div>
                                    </div>
                                </div>-->
                                <div class="card">
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
                                                                        <label for="titleText">Title: </label>
                                                                        <input type="text" name="" id="titleText" class="form-control" v-model="variable.title" placeholder="Title" :disabled="!editVariableFlags[variable.ind]">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="descriptionText">Description: </label>
                                                                        <textarea class="form-control" id="descriptionText" cols="30" rows="3" v-model="variable.description" :disabled="!editVariableFlags[variable.ind]"></textarea>
                                                                        &nbsp;&nbsp;
                                                                        <input type="image" class="buttonIcon" :src="getImage(editVariableFlags[variable.ind])" @click="editVariable(variable.ind)" alt="Edit">
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                            
                                            </div>
                                            <div class="tab-pane fade" id="nav-logic" role="tabpanel" aria-labelledby="nav-logic-tab">...</div>
                                        </div>
                                    </div>
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

    <!-- Normal view -->
    <div class="row justify-content-center">
        <div id="variablesDiv" class="col-sm-3">
        <div id="variablesDivCard" class="card">
            <div class="card-header">Variables <element v-if='!modelLoaded'><input type="number" id="inputModelID" v-model='modelID'><button type="button" class="btn btn-primary" @click='loadModelEvidencio()'>Load Model</button></element> </div>

            <div class="card-body scrollbarAtProject">

                <div id="accordion1">
                    <div class="card" v-if='!modelLoaded'>
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                No variables added yet...
                            </h5>
                        </div>

                    </div>

                    <div class="card" v-if='modelLoaded' v-for='(variable, index) in model.variables'>
                        <div class="card-header collapsed" :id="'heading' + index" data-toggle="collapse" :data-target="'#collapse' + index" aria-expanded="true" aria-controls="'collapse' + index"  data-parent="#accordion1">
                            <h5 class="mb-0">
                                @{{ variable.title }}
                                <span class="badge badge-pill" :class="{'badge-danger': variablesUsed[index]==0, 'badge-success': variablesUsed[index]>0}">@{{ variablesUsed[index] }}</span>

                            </h5>
                        </div>

                        <div :id="'collapse' + index" class="collapse" :aria-labelledby="'#heading' + index" data-parent="#accordion1">
                            <pre class="language-json"><code>@{{ variable }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        </div>
        <div id="graphDiv" class="col-sm-9">
            <div id="graphDivCard" class="card" >
                <div class="card-header">
                    Designer 
                    <button type="button" class="btn btn-primary" @click='fitView()'>Fit</button>
                </div>

                <div class="card-body h-75" id="graphCardBody">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div id="graphContainerForBorder">
                        <div id="graph">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
<script src="{{ asset('js/bootstrap-colorpalette.js') }}"></script>
<link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
<link href="{{ asset('css/designer.css') }}" rel="stylesheet">
<script src="{{ asset('js/designer.js') }}"></script>

@endsection
