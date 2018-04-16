@extends('layouts.app')

@section('content')

@include('partials.sidebar')

<div class="container-fluid" id="designerDiv">

    @include('partials.designer_modal')

    <!-- Normal view -->
    <div class="row justify-content-center">
        <div id="variablesDiv" class="col-sm-3">
        <div id="variablesDivCard" class="card">
            <div class="card-header">Variables <template v-if='!modelLoaded'><input type="number" id="inputModelID" v-model='modelID'><button type="button" class="btn btn-primary" @click='loadModelEvidencio()'>Load Model</button></template> </div>

            <div class="card-body scrollbarAtProject">

                <div id="accordion1">
                    <div class="card" v-if='!modelLoaded'>
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                No variables added yet...
                            </h5>
                        </div>

                    </div>

                    <template v-if='modelLoaded'>
                        <div class="card" v-for='(variable, index) in model.variables'>
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
                    </template>
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
