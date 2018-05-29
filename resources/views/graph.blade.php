{{--This is the result page for the website of the filled in workflows. The result page shows the result of the Evidencio API call.
This is represented in a chart, the user is able to choose the result to be diaplayed in different graphical chats by clicking on different buttons.
The will be also a text result if any was included in the Evidencio API call.
Users are also able to download a pdf of the results--}}


{{--post request to Evidencio API with the answers of the workflow page.
This will return an array with the result and their parameters.--}}
<?php
use App\EvidencioAPI;
use App\Result;
if (!empty($_POST['answer'])&&!empty($_POST['model'])) {
  $decodeRes = EvidencioAPI::run($_POST['model'],$_POST['answer']);
  $modelDetails = EvidencioAPI::getModel($_POST['model']);
  //$modelDetails = EvidencioAPI::getModel(596);
  $friendly = Result::getResult($_POST['db_id'])->toArray();
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
   <br/><br/><br/>
  <div class"row justify-content-center">
    <table width="100%" style="margin-left:auto; margin-right:auto;">
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
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>
        <th width="4%"></th>

      </tr>
      <tr>
        <?php
        $numSad = $result;
        for($j = 0; $j <4; $j++){
          echo "<tr>";
          for($i = 0; $i < 25; $i++ ){
            if($numSad > 0){
          ?>
              <td><img src="{{ URL::to('/images/highRisk.png') }}" width="100%" /></td>
          <?php
              $numSad = $numSad -1;
            }
            else{
          ?>
            <td><img src="{{ URL::to('/images/lowRisk.png') }}" width="100%"/></td>
          <?php
            }
          }
          echo "</tr>";
        }
        ?>
      </tr></table>
      <br />
      <br />
      <h5>Model results show that among 100 patients with/require <?php echo $decodeRes["title"] ?>, <kbd><?php echo $result?></kbd> have similar response like yours. </h5>
      @if($result > 0)
      <p>You may want to consult your doctor to find out more on <?php echo $decodeRes['title'] ?></p>
      @endif
    </div>
</div>

{{--Javascript for the creating the chat using Chartjs--}}
<script>

  var chartType = '<?php if(!empty($friendly)) echo $friendly[0]['representation_type']; else  echo "pie";?>';
  var resultChart;
  var ctx = document.getElementById("graph").getContext('2d');

  Chart.defaults.global.defaultFontSize = 20;

  init();

  function init(){
    resultChart = new Chart(ctx, {
        type: chartType,
        data: {
            labels: [ <?php if(!empty($friendly)){ echo $friendly[0]['representation_label']; } else { echo "'Positive', 'Negative'"; } ?>],
            datasets: [{
                label: 'Result',
                data: [<?php echo 100-$result ?>, <?php echo $result ?>],
                backgroundColor: [
                  'rgba(54, 162, 235, 0.5)',
                  'rgba(255, 99, 132, 1)',
                ]
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

  function loadData(){
    var canv = document.getElementById("graph");
    document.getElementById("chartdata").value = canv.toDataURL("image/jpg");
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
<div class="container">
  <h3>Actions</h3>
  <form method="post" action="/PDF">
    {{ csrf_field() }}
    <input type="hidden" name="model" value="<?php echo $_POST['model']?>"/>
    <input type="hidden" name="model_name" value="<?php echo $decodeRes['title'] ?>"/>
    <input type="hidden" name="percentage" value="<?php echo $decodeRes['result'] ?>"/>
    <input type="hidden" id="chartdata" name="chartIMG"/>
    <?php
    foreach($_POST['answer'] as $value)
    {
      echo '<input type="hidden" name="answer[]" value="'. $value. '"/>';
    }
    ?>
    <?php
    foreach ($modelDetails['variables'] as $item){
    echo '<input type="hidden" name="qn[]" value="'. $item['title']. '"/>';
    }
    ?>
    <?php
    foreach ($modelDetails['variables'] as $item){
    echo '<input type="hidden" name="desc[]" value="'. $item['description']. '"/>';
    }
    ?>
    <?php
    foreach($decodeRes['additionalResultSet'] as $text){
      echo '<input type="hidden" name="remarks[]" value="'. $text['key'] . '"/>';
    }?>
    <?php
    echo '<input type="hidden" name="remarks[]" value="'. $decodeRes['conditionalResultText'] . '"/>';
    ?>
    <input type="submit" onclick="loadData()" name="generatePDF" id="export" value="Export Result to PDF" class="btn btn-info"/>
  </form>
</div>
@endsection
