<?php
/**
 * post request to Evidencio search API
 * returns array of all the evidencio models that matched the query entered in the search form
 */
use App\EvidencioAPI;
if (!empty($_GET['search'])) {
  $decodeRes = EvidencioAPI::search($_GET['search']);
}

?>

@extends('layouts.app')

@section('content')
<br>
{{--the search bar--}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ url('/search') }}">
                <input type="text" name="search" class="form-control" style="width:100%; font-size:x-large; height:50px;" placeholder="Search for Models..."></input>
            </form>
            <br>
        </div>
    </div>
</div>
{{--provides all the models that was produced by the API call to Evidencio's search API--}}
<div class="container">
  <?php if (!empty($decodeRes)): ?>
    <ul class="list-group">
      <?php foreach ($decodeRes as $model): ?>
        <!--Gives Title of model as link-->
        <li class="list-group-item"><a href="/workflow?model=<?php echo $model['id'] ?>"><h2><?php echo $model['title']; ?></h2></a>
          <ul>
            <!--provides all the requires variable inputs for each model-->
            <?php foreach ($model['variableSet'] as $item): ?>
              <li><?php echo $item['title']; ?></li>
            <?php endforeach; ?>
          </ul>
          <br />
          Author: <b>Evidencio</b><br /> 4.5/5 Stars  |  <i><a href="/feedback">Provide Feedback</a></i>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php endif; ?>







</div>

@endsection
