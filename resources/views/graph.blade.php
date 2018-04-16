<?php
use App\EvidencioAPI;
if (!empty($_POST['answer'])&&!empty($_POST['model'])) {
  $decodeRes = EvidencioAPI::run($_POST['model'],$_POST['answer']);
  $test= $_POST['answer'];
  foreach ($test as $answers) {
    if (is_numeric($answers)) {
      $answers = floatval($answers);
    }
  }
  print_r($test);
  echo json_encode($decodeRes);
}
?>
@extends('layouts.app')

@section('content')
@if(!empty($decodeRes['result']))
<?php

$result=(int)$decodeRes['result'];
$dataPoints = array(
  array("label"=>"", "y"=>(100-$result)),
  array("label"=>$decodeRes['title'], "y"=>$result),
)
?>
<div class="container">
  <button id="pieButton" class="btn btn-primary btn-sm" value='pie' onclick="changeGraphType(this.value)">Pie Chart</button>
  <button id="barButton" class="btn btn-primary btn-sm" value='bar' onclick="changeGraphType(this.value)">Bar Chart</button>
  <button id="barButton" class="btn btn-primary btn-sm" value='doughnut' onclick="changeGraphType(this.value)">Doughnut Chart</button>
  <button id="barButton" class="btn btn-primary btn-sm" value='polarArea' onclick="changeGraphType(this.value)">Polar Area Chart</button>
</div>

<div class="container">
  <canvas id="graph"></canvas>
</div>
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
