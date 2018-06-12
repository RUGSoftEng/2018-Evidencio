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
  $friendly = Result::getResult($_POST['db_id'])->toArray();
  if(!empty($friendly)){
      $dataLabel = "";
      $bgColor = "";
      foreach($friendly as $f){
        $dataLabel = $dataLabel . ',\' ' . $f["item_label"] . '\'';
        $bgColor = $bgColor . ',\'' . $f["item_background_colour"] . '\'';
      }
      $dataLabel = substr($dataLabel, 1);
      $bgColor = substr($bgColor, 1);
  }
  if(!empty($decodeRes["result"])){
      $result = $decodeRes["result"];
      $dataLabel = "";
      $bgColor = "";
      foreach($friendly as $f){
        $dataLabel = $dataLabel . ', \'' . $f["item_label"] . '\'';
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
  $chartType = "bar";
  if(!empty($friendly[0]["chartType"])){
    if($friendly[0]["chartType"] == 0){
      $chartType = "pie";
    }
    else if($friendly[0]["chartType"]  == 1){
      $chartType = "bar";
    }
    else if($friendly[0]["chartType"] == 2){
      $chartType = "doughnut";
    }
    else if($friendly[0]["chartType"] == 3){
      $chartType = "polarArea";
    }
  }
}
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
          $numSad = $decodeRes["result"];
          for($j = 0; $j <4; $j++){
            echo "<tr>";
            for($i = 0; $i < 25; $i++ ){
              if($numSad > 0){ ?>
                <td><img src="{{ URL::to('/images/highRisk.png') }}" width="100%" /></td>
        <?php  $numSad = $numSad -1;
              } else{   ?>
                  <td><img src="{{ URL::to('/images/lowRisk.png') }}" width="100%"/></td>
        <?php }
            }
            echo "</tr>";
          }
       ?>
      </tr></table>
      <br />
      <br />
      <h5>Model results show that among 100 patients with/require <?php echo $decodeRes["title"] ?>, <kbd><?php echo $result?></kbd> have similar response like yours. </h5>
      @endif
      <p><?php  if(!empty($friendly[0]['desc'])){echo $friendly[0]['desc']; } ?></p>
    </div>
</div>
{{--Javascript for the creating the chat using Chartjs--}}
<script>
  var chartType = '<?php if(!empty($friendly)) echo $chartType; else  echo "pie";?>'; //default is pie if none-specfied
  var resultChart;
  var ctx = document.getElementById("graph").getContext('2d');
  Chart.defaults.global.defaultFontSize = 20;
  init();
  function init(){
    resultChart = new Chart(ctx, {
        type: chartType,
        data: {
            labels: [ <?php if(!empty($dataLabel)){ echo $dataLabel; } else { echo "'Positive', 'Negative'"; } ?>], // default is 2-value input; positive and negative.
            datasets: [{
                label: [<?php echo $dataLabel ?>],
                data: [<?php echo $result ?>],
                backgroundColor: [<?php echo $bgColor ?>]
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
  <?php
  if(!empty($decodeRes['additionalResultSet'])){
    foreach($decodeRes['additionalResultSet'] as $text){
   print_r($text['key']);
    }
  }?>
  <br><br>
  <?php
  if(!empty($decodeRes['conditionalResultText'])){
   print_r($decodeRes['conditionalResultText']);
 }?>
  <br><br>
</div>
<div class="container">
  <h3>Actions</h3>
  <form method="post" action="/PDF">
    {{ csrf_field() }}
    <input type="hidden" name="model" value="<?php echo $_POST['model']?>"/>
    <input type="hidden" name="model_name" value="<?php echo $decodeRes['title'] ?>"/>
    <input type="hidden" name="friendlyRes" value="<?php if(!empty($friendly[0]['desc'])){echo $friendly[0]['desc']; } ?> "/>
    <input type="hidden" id="chartdata" name="chartIMG"/>
    <?php
    if(!empty($decodeRes["resultSet"])){
      foreach($decodeRes["resultSet"] as $item){
        echo '<input type="hidden" name="res[]" value="'. $item["result"] . '"/>';
        echo '<input type="hidden" name="resText[]" value="' . $item["conditionalResultText"] . '"/>';
      }
    }
    else{
      echo '<input type="hidden" name="res[]" value="'. $decodeRes["result"] . '"/>';
    }
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
    echo '<input type="hidden" name="remarks[]" value="'. $decodeRes['conditionalResultText'] . '"/>';
    ?>
    <input type="submit" onclick="loadData()" name="generatePDF" id="export" value="Export Result to PDF" class="btn btn-info"/>
  </form>
</div>
@endsection
