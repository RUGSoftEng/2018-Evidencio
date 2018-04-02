@extends('layouts.app')

@section('content')
<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/solid.js" integrity="sha384-+Ga2s7YBbhOD6nie0DzrZpJes+b2K1xkpKxTFFcx59QmVPaSA8c7pycsNaFwUK6l" crossorigin="anonymous"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js" integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c" crossorigin="anonymous"></script>

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

<div class="overlay"></div>

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
                                
                            </div>

                            <div class="col-md-8">
                                <div>
                                    <label class="typo__label">Simple select / dropdown</label>
                                    <vue-multiselect v-model="selectedVariables" :options="possibleVariables" :multiple="true" :close-on-select="false" :clear-on-select="false" label="title" track-by="title" :limit=3 :limit-text="multiselectVariablesText" :hide-selected="true" :preserve-search="true" placeholder="Choose variables" :preselect-first="true">
                                        <template slot="tag" slot-scope="props"><span class="badge badge-info badge-larger"><span class="badge-maxwidth">@{{ props.option.title }}</span>&nbsp;<span class="custom__remove" @click="props.remove(props.option)">❌</span></span></template> 
                                    </vue-multiselect>
                                </div>

                                <div id="accordion">
                                    <div class="card" v-for="(variable, index) in selectedVariables">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" data-toggle="collapse" :data-target="'#collap_' + index" aria-expanded="false" :aria-controls="'collap_' + index">
                                                    @{{ variable.title }}
                                                </button>
                                            </h5>
                                        </div>

                                        <div :id="'collap_' + index" class="collapse" :aria-labelledby="'#collap_' + index" data-parent="#accordion">
                                            <div class="card-body">
                                                <pre class="language-json"><code>@{{ variable  }}</code></pre>
                                            </div>
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
        <div class="col-sm-3">
        <div class="card">
            <div class="card-header">Variables <element v-if='!modelLoaded'><input type="number" id="inputModelID" v-model='modelID'><button type="button" class="btn btn-primary" @click='loadModelEvidencio()'>Load Model</button></element> </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center disabled" v-if='!modelLoaded'>
                        No variables added yet...
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center" v-if='modelLoaded' v-for='(variable, index) in model.variables'>
                        @{{ variable.title }}
                        <span class="badge badge-pill" :class="{'badge-danger': variablesUsed[index]==0, 'badge-success': variablesUsed[index]>0}">@{{ variablesUsed[index] }}</span>
                    </li>
                    
                </ul>
            </div>
        </div>
        
        </div>
        <div class="col-sm-9">
            <div class="card">
                <div class="card-header">
                    Designer 
                    <button type="button" class="btn btn-primary" @click='fitView()'>Fit</button>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div id="graph">
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
<link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
<link href="{{ asset('css/designer.css') }}" rel="stylesheet">
<script src="{{ asset('js/designer.js') }}"></script>

@endsection
