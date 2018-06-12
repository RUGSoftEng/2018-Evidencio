{{--This page where the user will fill in the variables of a step of the chosen workflow.
The page will create a list of the variables of the step, either a slider for continuous values
or radio buttons for categorical values.--}}

<?php

 use App\EvidencioAPI;
 use App\Workflow;
?>


@extends('layouts.app')

@section('content')

{{--vue stuff--}}
<div id="workflow">
<workflow-step :workflow-data="{{json_encode($result)}}"></workflow-step>

</div>




<script src="{{ asset('js/bootstrap-colorpalette.js') }}"></script>
<link href="{{ asset('css/designer.css') }}" rel="stylesheet">
<!--<script src="{{ asset('js/designer.js') }}"></script>-->
<script src="{{ asset('js/WorkflowStep.js') }}"></script>
@endsection
