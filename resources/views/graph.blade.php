{{--This is the result page for the website of the filled in workflows. The result page shows the result of the Evidencio API call.
This is represented in a chart, the user is able to choose the result to be diaplayed in different graphical chats by clicking on different buttons.
The will be also a text result if any was included in the Evidencio API call.
Users are also able to download a pdf of the results--}}


{{--post request to Evidencio API with the answers of the workflow page. 
This will return an array with the result and their parameters.--}}
<?php
use App\EvidencioAPI;
if (!empty($_POST['answer'])&&!empty($_POST['model'])) {
  $decodeRes = EvidencioAPI::run($_POST['model'],$_POST['answer']);
}
?>
@extends('layouts.app')

@section('content')
@if(!empty($decodeRes['result']))
<?php

/**
 * making data array for charts
 *result is only one presentage value
 *therefore creating opposite result
 */
$result=(int)$decodeRes['result'];
$dataPoints = array(
  array("label"=>"", "y"=>(100-$result)),
  array("label"=>$decodeRes['title'], "y"=>$result),
)
?>

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
</div>

{{--Javascript for the creating the chat using Chartjs--}}
<script>
  var chartType = 'pie';
  var resultChart;

  var ctx = document.getElementById("graph").getContext('2d');

  Chart.defaults.global.defaultFontSize = 16;

  init();

  function init(){
    resultChart = new Chart(ctx, {
        type: chartType,
        data: {
            labels: ["Not "+"<?php echo $decodeRes['title'] ?>", "<?php echo $decodeRes['title'] ?>"],
            datasets: [{
                label: 'Result',
                data: [<?php echo 100-$result ?>, <?php echo $result ?>],
                backgroundColor: [
                  'rgba(54, 162, 235, 0.5)',
                  'rgba(255, 99, 132, 0.5)',
                ],
                borderWidth:1,
                borderColor: [
                    'rgba(54, 162, 435, 1)',
                    'rgba(455,99,132,1)',
                ],
            }]
        },
        options: {
          title:{
            display:true,
            text:"<?php echo $decodeRes['title'] ?>",
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
                        return data['labels'][tooltipItem['index']]+': '+data['datasets'][0]['data'][tooltipItem['index']] + '%';
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

</script>

@endif
{{--if there is any additional information it will print out here (not complete)--}}
<div class="container">
  @if(!empty($decodeRes['additionalResultSet']))
  @foreach($decodeRes['additionalResultSet'] as $text)
  <?php print_r($text['key']); ?>
  <br><br>
  @endforeach
  @endif
  @if(!empty($decodeRes['conditionalResultText']))
  <?php print_r($decodeRes['conditionalResultText']); ?>
  <br><br>
  @endif

</div>

@endsection
