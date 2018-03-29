<?php
use App\EvidencioAPI;
if (!empty($_GET['answer'])&&!empty($_GET['model'])) {
  $decodeRes = EvidencioAPI::run($_GET['model'],$_GET['answer']);
  print_r ($decodeRes);
}
?>
