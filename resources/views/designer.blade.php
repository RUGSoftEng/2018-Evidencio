@extends('layouts.app')

@section('content')

@include('partials.sidebar')

<div class="container-fluid full-height" id="designerDiv">

    {{-- @include('partials.designer_modal') --}}
    <modal-step :step-id="selectedStepId" :step="steps[selectedStepId]" :used-variables="usedVariables" :possible-variables="possibleVariables" :child-nodes="childrenNodes" :changed="modalChanged" @change="applyChanges($event)"></modal-step>
    <!-- Normal view -->
    <div class="row justify-content-center full-height">
        <div id="variablesDiv" class="col-sm-3 full-height">
            <variable-view-list :all-variables="allVariables" :all-variables-used="timesUsedVariables"></variable-view-list>
        </div> 
        <div id="graphDiv" class="col-sm-9 full-height">
            <div id="graphDivCard" class="card full-height">
                <div class="card-header">
                    Designer
                    <button type="button" class="btn btn-primary" @click='fitView'>Fit</button>
                    <button type="button" class="btn btn-primary" @click='saveWorkflow'>Save</button>
                </div>

                <div class="card-body h-75" id="graphCardBody">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div id="graphContainerForBorder" class="full-height">
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
<script src="{{ asset('js/designerGraph.js') }}"></script>

@endsection
