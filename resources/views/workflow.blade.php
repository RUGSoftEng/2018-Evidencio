{{--This page where the user will fill in the variables of a step of the chosen workflow.
The page will create a list of the variables of the step, either a slider for continuous values
or radio buttons for categorical values.--}}

<?php
/**
 * post request to Evidencio model API
 * returns array of all the parameters of the evidence models that was clicked on in the search page.
 */
use App\EvidencioAPI;
use App\Step;
use App\Workflow;
if (!empty($_GET['model'])) {
  $decodeRes = (new Workflow)->getWorkflowByID($_GET['model']);
  $result = (new Step)->getModel($_GET['model']);
}
?>


@extends('layouts.app')
@section('content')
{{--makes inputs for all the required variables--}}
@if (!empty($decodeRes))
<div class="container">
  <h3><?php echo $decodeRes['title'] ?></h3>
</div>
<div class="container">
  <form method="POST" action="/graph">
    {{ csrf_field() }}
    <input type="hidden" name="model" value="<?php echo $_GET['model'] ?>">
    <ul class="list-group">
      @foreach($result as $x)
        <li class="list-group-item">
          <b><?php echo $x['title'] ?></b>
          <br />
          <i>&nbsp<?php echo $x['description'] ?></i>
        </li>
      @endforeach
  </ul>
  <br>
  <button type="submit" class="btn btn-primary btn-sm">submit</button>
  </form>
</div>
@endif
@endsection
