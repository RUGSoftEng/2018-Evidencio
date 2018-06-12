<?php
/**
 * post request to Evidencio search API
 * returns array of all the evidencio models that matched the query entered in the search form
 */
use App\EvidencioAPI;
use App\Workflow;
$numResult = 0;
if (!empty($_GET['search'])) {
  $result = (new Workflow)->search($_GET['search']);
  $numResult = count($result);
}
?>
@extends('layouts.app')
@section('content')@include('partials.sidebar')
<br>
{{--the search bar--}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <form method="GET" action="{{ url('/search') }}">
                <input type="text" name="search" class="form-control" style="width:100%; font-size:x-large; height:50px;" value="<?php echo $_GET['search'] ?>" placeholder="Search for Models..."></input>
            </form>
            <br>
        </div>
    </div>
</div>
{{--provides all the models that was produced by the API call to Evidencio's search API--}}
<div class="container">
  <?php
  if ($numResult > 0): ?>
    <div class="alert alert-info">Search returned: <?php echo $numResult ?> result(s)</div>
    <ul class="list-group">
      <?php foreach ($result as $wflow): ?>
        <li class="list-group-item"><a href="/workflow/{{$wflow->id}}"><h2><?php echo $wflow['title']; ?></h2></a>
          <ul>
            <?php echo $wflow['description'] ?>
          </ul>
          <br />
          Model Creator: <b><?php echo ($wflow['fname'] . ' '. $wflow['lname']) ?></b><br />
        </li>
      <?php endforeach;
    endif;
    if($numResult == 0): ?>
        <div class="alert alert-warning">No results found for : <?php echo $_GET['search']; ?> </div>
  <?php endif; ?>
</div>
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
@endsection
