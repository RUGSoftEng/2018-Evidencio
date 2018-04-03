
<?php
use App\EvidencioAPI;
if (!empty($_GET['answer'])&&!empty($_GET['model'])) {
  $decodeRes = EvidencioAPI::run($_GET['model'],$_GET['answer']);
}
?>

@extends('layouts.app')

@section('content')


<?php
$result=(int)$decodeRes['result'];
$dataPoints = array(
	array("label"=>$decodeRes['title'], "y"=>$result),
  array("label"=>"", "y"=>(100-$result)),
)

?>
<script>
window.onload = function() {


var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	title: {
		text: "<?php echo $decodeRes['title'] ?>"
	},
	subtitles: [{
		text:"Pie Chart"
	}],
	data: [{
		type: "pie",
		yValueFormatString: "#,##0.00\"%\"",
		indexLabel: "{label} ({y})",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();

}
</script>

<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

@endsection

