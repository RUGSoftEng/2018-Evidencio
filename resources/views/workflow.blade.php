<?php
use App\EvidencioAPI;
if (!empty($_GET['model'])) {


  $decodeRes = EvidencioAPI::getModel($_GET['model']);

}
?>


@extends('layouts.app')

@section('content')
<?php if (!empty($decodeRes)): ?>
<div class="container">
  <h3><?php echo $decodeRes['title'] ?></h3>
</div>
<div class="container">
  <form  action="/graph">
    <input type="hidden" name="model" value="<?php echo $_GET['model'] ?>">
    <ul class="list-group">
    <?php foreach ($decodeRes['variables'] as $item): ?>
      <li class="list-group-item">
        <?php if ($item['type']=='continuous'): ?>
          <?php
            echo $item['title'].": ";
            $min = $item['options']['min'];
            $max = $item['options']['max'];
            $step = $item['options']['step'];
           ?>

          <small><?php echo $min ?> - <?php echo $max ?> by <?php echo $step ?></small>
          <br>

            <input type="text" name="answer[<?php echo $item['id']; ?>]">


      <?php endif; ?>
      <?php if ($item['type']=='categorical'): ?>
        <?php  echo $item['title'].": ";?>
        <br>

        <?php foreach ($item['options'] as $value): ?>
          <input type="radio" name="answer[<?php echo $item['id']; ?>]" value="<?php echo $value['title']; ?>" >
          <?php echo $value['title']; ?>
          <br>

        <?php endforeach; ?>





      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>

  <button type="submit" class="btn btn-primary btn-sm">submit</button>
  </form>
</div>
<?php endif; ?>


@endsection
