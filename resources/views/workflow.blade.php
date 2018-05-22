{{--This page where the user will fill in the variables of a step of the chosen workflow.
The page will create a list of the variables of the step, either a slider for continuous values
or radio buttons for categorical values.--}}

<?php
/**
 * post request to Evidencio model API
 * returns array of all the parameters of the evidence models that was clicked on in the search page.
 */
use App\EvidencioAPI;
if (!empty($_GET['model'])) {
  $decodeRes = EvidencioAPI::getModel($_GET['model']);

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
    @foreach ($decodeRes['variables'] as $item)
      <li class="list-group-item">
        {{--creates input for continuous variable--}}
        @if ($item['type']=='continuous')
          <?php
            echo $item['title'].": ";
            $min = $item['options']['min'];
            $max = $item['options']['max'];
            $step = $item['options']['step'];
           ?>

          <small><?php echo $min ?> - <?php echo $max ?> by <?php echo $step ?></small>
          <br>
          <br>
          <div class="sliderInput">
            <div id="<?php echo $item['id']; ?>"></div>
            <div></div>
            <input type="number" step="<?php echo $step ?>" id="answer[<?php echo $item['id']; ?>]" name="answer[<?php echo $item['id']; ?>]">
          </div>
          {{--javascript for making the sliders--}}
            <script type="text/javascript">
              var slider<?php echo $item['id']; ?> = document.getElementById("<?php echo $item['id']; ?>");

              noUiSlider.create(slider<?php echo $item['id']; ?>, {
                start: [<?php echo $min ?>],
                connect: [true,false],
                step:<?php echo $step ?>,
                range: {
                  'min': <?php echo $min ?>,
                  'max': <?php echo $max ?>
                },
              });
              var input<?php echo $item['id']; ?> = document.getElementById("answer[<?php echo $item['id']; ?>]");

              slider<?php echo $item['id']; ?>.noUiSlider.on('update', function( values, handle ) {
	               input<?php echo $item['id']; ?>.value = values[handle];
               });

               input<?php echo $item['id']; ?>.addEventListener('change', function(){
	                slider<?php echo $item['id']; ?>.noUiSlider.set(this.value);
                });
            </script>
      @endif
      {{--creates input for categorical variable--}}
      @if ($item['type']=='categorical')
        <?php  echo $item['title'].": ";?>
        <br>

        @foreach ($item['options'] as $value)
          <input type="radio" name="answer[<?php echo $item['id']; ?>]" value="<?php echo $value['title']; ?>" >
          <?php echo $value['title']; ?>
          <br>
        @endforeach

      @endif
    </li>
    @endforeach
  </ul>
  <br>
  <button type="submit" class="btn btn-primary btn-sm">submit</button>
  </form>
</div>
@endif
@endsection
