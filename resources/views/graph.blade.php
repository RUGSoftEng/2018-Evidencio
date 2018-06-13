{{--This is the result page for the website of the filled in workflows. The result page shows the result of the Evidencio API call.
This is represented in a chart, the user is able to choose the result to be diaplayed in different graphical chats by clicking on different buttons.
The will be also a text result if any was included in the Evidencio API call.
Users are also able to download a pdf of the results--}}

{{--post request to Evidencio API with the answers of the workflow page.
This will return an array with the result and their parameters.--}}
<?php
use App\EvidencioAPI;
use App\Result;
use App\Step;
// if (!empty($_GET['answer'])&&!empty($_GET['model'])) {
  //$decodeRes = EvidencioAPI::run($_GET['model'],$_GET['answer']);
  //$modelDetails = EvidencioAPI::getModel($_GET['model']);
  // $friendly = Result::getResult($_GET['db_id'])->toArray();

  $dbStep = Step::find($_GET["db_id"]);
if ($dbStep != null) {
  $dbResults = $dbStep->ResultStepChartItems()->get();
  $description = $dbStep->description;
  if ($dbResults->isNotEmpty()) {
    $chartNumber = (int)($dbStep->result_step_chart_type);
    $dataLabel = "";
    $bgColor = "";
    $result = "";
    foreach($dbResults as $dbResult){
      $dataLabel = $dataLabel . ',\' ' . htmlspecialchars($dbResult->pivot->item_label) . '\'';
      $bgColor = $bgColor . ',\'' . $dbResult->pivot->item_background_colour . '\'';
      $description = str_replace("$" . $dbResult->result_name . "$", $_GET[$dbResult->result_name], $description);
      if ($dbResult->pivot->item_is_negated)
        $result = $result . "," . (100 - (int)$_GET[$dbResult->result_name]);
      else
        $result = $result . "," . $_GET[$dbResult->result_name];
    }
    $dataLabel = substr($dataLabel, 1);
    $bgColor = substr($bgColor, 1);
    $result = substr($result, 1);

    $chartType = "bar";
    if($chartNumber == 0){
      $chartType = "bar";
    }
    else if($chartNumber  == 1){
      $chartType = "pie";
    }
    else if($chartNumber == 2){
      $chartType = "doughnut";
    }
    else if($chartNumber == 3){
      $chartType = "polarArea";
    }
    $firstResult = $dbResults->first();
    if($dbResults->where("result_name", '!=', $firstResult->result_name)->isEmpty()) {
      if ($firstResult->pivot->item_is_negated)
        $numSad = (100 - (int)$_GET[$dbResults->first()->result_name]);
      else
        $numSad = $_GET[$dbResults->first()->result_name];
    }
  }
}

/*  if(!empty($decodeRes["result"])){
      if(!empty($friendly[1])){
        if($friendly[1]["item_is_negated"])
          $result = $decodeRes["result"] . ', ' . (100- (int)$decodeRes['result']);
        if($friendly[0]["item_is_negated"])
          $result = (100- (int)$decodeRes['result']) . ', ' . $decodeRes["result"];
      }
      else{
          $result = $decodeRes['result'];
      }
      $dataLabel = "";
      $bgColor = "";
      foreach($friendly as $f){
        $dataLabel = $dataLabel . ', \'' . htmlspecialchars($f["item_label"]) . '\'';
        $bgColor = $bgColor . ',\'' . $f["item_background_colour"] . '\'';
      }
      $dataLabel = substr($dataLabel, 1);
      $bgColor = substr($bgColor, 1);
  }
  else {
      $result = "";
      foreach($decodeRes["resultSet"] as $res)
        $result = $result . "," . $res["result"];
      $result = substr($result, 1);
  }
  if(empty($bgColor)){
    $bgColor = "'#ff0000', '#00ffff'";
  }



// }*/
?>
@extends('layouts.app')
@section('content')@include('partials.sidebar')
{{--buttons for changing to the different chart representations--}}
<div class="container">
  <button id="pieButton" class="btn btn-primary btn-sm" value='pie' onclick="changeGraphType(this.value)">Pie Chart</button>
  <button id="barButton" class="btn btn-primary btn-sm" value='bar' onclick="changeGraphType(this.value)">Bar Chart</button>
  <button id="barButton" class="btn btn-primary btn-sm" value='doughnut' onclick="changeGraphType(this.value)">Doughnut Chart</button>
  <button id="barButton" class="btn btn-primary btn-sm" value='polarArea' onclick="changeGraphType(this.value)">Polar Area Chart</button>
</div>

{{--Canvas for where the chart will be displayed--}}
<div class="container">
  <canvas id="graph"></canvas>
   <br/><br/><br/>
   @if(!empty($decodeRes['result']))
  <div class"row justify-content-center">
    <table width="100%" class="mx-auto" style="max-width: 600px">
      <tr>
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>
      </tr>
      <tr>
          @for($j = 0; $j <10; $j++)
            <tr>
            @for($i = 0; $i < 10; $i++ )
              @if($numSad > 0)
                <td><img src="{{ URL::to('/images/highRisk.png') }}" width="100%" /></td>
                <?php  $numSad = $numSad -1; ?>
              @else
                  <td><img src="{{ URL::to('/images/lowRisk.png') }}" width="100%"/></td>
              @endif
            @endfor
            </tr>
          @endfor
      </tr></table>
      <br />
      <br />
      @endif
      <h5>@if(!empty($friendly[0]['desc'])){{ $friendly[0]['desc'] }} @endif</h5>
    </div>
</div>
{{--Javascript for the creating the chat using Chartjs--}}
<script>
  var chartType =@if(!empty($chartType)) '{{ $chartType }}' @else 'pie' @endif; //default is pie if none-specfied
  var resultChart;
  var ctx = document.getElementById("graph").getContext('2d');
  Chart.defaults.global.defaultFontSize = 20;
  init();
  function init(){
    resultChart = new Chart(ctx, {
        type: chartType,
        data: {
            labels: [ @if(!empty($dataLabel)) {!! $dataLabel !!} @else "'Positive', 'Negative'" @endif], // default is 2-value input; positive and negative.
            datasets: [{
                label: [{!! $dataLabel !!}],
                data: [{{ $result }}],
                backgroundColor: [{!! $bgColor !!}]
            }]
        },
        options: {
          title:{
            display:true,
            text:"{{ $dbStep->title }}",
            fontSize:25
          },
          legend:{
            postion:'right',
            display:true
          },
          responsive:true,
          tooltips: {
                mode: 'label',
                callbacks: {
                    label: function(tooltipItem, data) {
                        return data['labels'][tooltipItem['index']]+': '+data['datasets'][0]['data'][tooltipItem['index']];
                    }
                }
            },
      }
    });
  }
  function changeGraphType(typeChange){
    resultChart.destroy();
    this.chartType=typeChange;
    init();
  }
  function loadData(){
    var canv = document.getElementById("graph");
    document.getElementById("chartdata").value = canv.toDataURL("image/jpg");
  }
</script>
<div class="container">
<?php if (!empty($description)) print_r($description) ?>
</div>
@endsection
