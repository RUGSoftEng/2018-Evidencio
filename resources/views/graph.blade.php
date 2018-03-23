@extends('layouts.app')

@section('content')

<div class="card graphcard">
  <div class="card-body graphcard">
    <h4 class="card-title">Workflow</h4>
    <!--<button onclick="addNode(0,'new')">Add</button>-->
    <div id="cy">







    </div>
    <link href="{{ asset('css/workflow.css') }}" rel="stylesheet">
    <script src="{{ asset('js/workflow.js') }}" type="text/javascript"> </script>
  </div>
</div>




@endsection('content')