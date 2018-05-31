@extends('layouts.app') 
@section('content') 
@include('partials.sidebar')

<div class="container-fluid height-100" id="designerDiv">

    {{-- @include('partials.designer_modal') --}}
    <modal-step :step-id="selectedStepId" :step="steps[selectedStepId]" :used-variables="usedVariables" :possible-variables="possibleVariables"
        :child-nodes="childrenNodes" :ancestor-variables="variablesUpToStep" :models="models" :changed="modalChanged" @change="applyChanges($event)"></modal-step>
    <modal-confirm :title="confirmDialog.title" :message="confirmDialog.message" @approval="confirmDialog.approvalFunction"></modal-confirm>
    <!-- Normal view -->
    <div class="row justify-content-center height-100">
        <div class="col-sm-3 column-fitting">
            <div class="row fitting pb-2">
                <div class="col px-2">
                    <div class="card height-100">
                        <div class="card-header align-items-center">
                            Workflow
                            <button type="button" class="btn btn-primary ml-2" @click="saveWorkflow">Save Workflow</button>
                        </div>

                        <div class="card-body full-height">
                            <details-editable :title="title" :description="description" @change="changeWorkflowDetails"></details-editable>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row remainder">
                <div class="col px-2 pt-2">
                    <variable-view-list :all-variables="allVariables" :all-variables-used="timesUsedVariables"></variable-view-list>
                </div>
            </div>
        </div>
        <div id="graphDiv" class="col-sm-9 height-100 px-2">
            <div id="graphDivCard" class="card height-100">
                <div class="card-header">
                    Designer
                    <button type="button" class="btn btn-primary ml-2" @click='fitView'>Fit</button>
                </div>

                <div class="card-body h-75" id="graphCardBody">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div id="graphContainerForBorder" class="height-100">
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