<?php
//This page is for the generating of the PDF. The crux of the file is the HTML document which will be displayed after generating
//As TCPDF do not allow echo commands, POST data is used instead.
if(empty($_POST)){
  echo "No model results available for transferring to PDF.";
}
if(isset($_POST["generatePDF"])){
  require_once('tcpdf/tcpdf.php');


  // Extend the TCPDF class to create custom Header and Footer
  class MYPDF extends TCPDF {

    //Page header
    public function Header() {
      // Logo
      $image_file = K_PATH_IMAGES.'headerLogo.png';
      $this->Image($image_file, 10, 5, 60, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
      // Set font
      $this->SetFont('helvetica', '', 10);
      // Title
      //$pdf->Cell(0, 0, 'TEST CELL STRETCH: no stretch', 1, 1, 'C', 0, '', 0);

    }

    // Page footer
    public function Footer() {
      // Position at 15 mm from bottom
      $this->SetY(-15);
      // Set font
      $this->SetFont('helvetica', 'I', 8);
      // Page number
      $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
  }

  // create new PDF document
  $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

  // set default header data
  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', '');

  // set header and footer fonts
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  // set margins
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  // set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

  // set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

  // set some language-dependent strings (optional)
  if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
  }

  // ---------------------------------------------------------

  // set font
  $pdf->SetFont('helvetica', '', 11);
  $pdf->setImageScale(1.66);
  // add a page
  $pdf->AddPage();

  $questionCount = 1;
  $i = 0;
  $answerArr = $_POST['answer'];
  $desc = $_POST['desc'];
  $today = getdate();

  // set some text to print
  $content = '
  <h3>Evidencio Model Prediction Results</h3>
  <p style="text-align:justify;">The following is a summary of the results that is based on what you have answered earlier on. Please note that the information is not stored on our server, and you should save the PDF and bring it to a medical professional to give you advice.</p>
  <table>
  <tr>
  <th width="15%"></th>
  <th width="85%"> </th>
  </tr>
  <tr>
  <td>Model: </td>
  <td>';
  $content .= $_POST["model_name"] . "  [ID : " . $_POST["model"] . "]";
  $content .='
  <br/>
  </td>
  </tr>
  <tr>
  <td>Date: </td>
  <td>'. date("d M Y") .'
  </td></tr>
  </table>
  <br/><br/>
  <table border="1" cellpadding="5">
  <tr>
  <th width="5%">#</th>
  <th width="50%">Question</th>
  <th width="45%">Response</th>
  </tr>';
  foreach($_POST['qn'] as $q){
    $answerArr[$i] = str_replace('<', ' less than ', $answerArr[$i]);
    $answerArr[$i] = str_replace('≥', ' more than or equal to ', $answerArr[$i]); //known bugs: cannot parse text that contains <, ≥ and ≤
    $answerArr[$i] = str_replace('≤', ' less than or equal to ', $answerArr[$i]);
    $q = str_replace('<', ' less than ', $q);
    $q = str_replace('≥', ' more than or equal to ', $q);
    $q = str_replace('≤', ' less than or equal to ', $q);
    $content.= '<tr><td>' . $questionCount .'</td><td>' . $q . '? <br/><small><i>'. $desc[$i] . '</i></small></td><td> '. $answerArr[$i] . '</td></tr>';
    $i = $i + 1;
    $questionCount = $questionCount + 1;
  };
  $content .='
  </table>
  <br />
  <div><table>
  <tr><th width="20%"></th><th width="5%"></th><th width="75%"></th></tr>
  <tr><td>';
  if(count($_POST['res']) == 1){
  $content .='<img src="';
  if($_POST['resultVal'] < 15){
    $content .= "/images/lowRisk.png";
  }
  elseif ($_POST['resultVal'] < 65) {
    $content .= "/images/normalRisk.jpg";
  }
  else {
    $content.= "/images/highRisk.jpg";
  }
  $content.='" width="200"/><br/>';
  }
  $content .='</td><td></td><td style="cellpadding:50px;"><br/><br/><br/>
  </td></tr>
  </table>
  Description
  <p> '. $_POST["friendlyRes"] . '
  <br pagebreak="true" />
  <h3>Additional Information (if applicable):</h3>
  ';
  $count = 0;
  foreach($_POST['res'] as $x){
    if(!empty($_POST['resText'])){
      $content .=  'Score: '.  $x . $_POST['resText'][$count] . '<br/>';
    }
    else{
      $content .=  'Score: '. $x . '<br/>';
    }
  $count = $count + 1;
  }
  if(!empty($_POST["remarks"])){
    foreach($_POST["remarks"] as $txt){
      $content .= '<p>'. $txt .'</p>';
    }
  }
  else{
    $content .= 'No additional information given. You may want to seek for professional advice.';
  }
  $content.= '

  <br pagebreak="true"/>
  <h1>Chart (if applicable)</h1>
  <img src="';
  $content .= $_POST["chartIMG"];
  $content .='" width="2480" height="1200" /><br />
  </div>
  <br /> <br /> <br />
  <p style="font-size:small;"><i>Calculations alone should never dictate patient care, and are no substitute for professional judgement. See our full disclaimer at <u>https://www.evidencio.com/disclaimer</u></i></p>
  <div align="center">-- End of Document -- </div>
  ';

  $pdf->writeHTML($content, true, false, true, false, '');

  // print a block of text using Write()
  //$pdf->Write(10, $txt, '', 0, 'C', true, 0, false, false, 0);

  // ---------------------------------------------------------
  //Close and output PDF document
  $pdf->Output('example_003.pdf', 'I');
  exit();
}
?>
