<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
date_default_timezone_set('America/Lima');
require '../datos/config_facturacion.php';
require "PDFCode128.clase.php";


/*
try {

  $ruta = F_SERVER_API."comprobantes-ticket/$id";
  $authorization = "Authorization: Bearer $key";

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', $authorization],
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $ruta,
    CURLOPT_USERAGENT => 'Consulta Data Ticket',
    CURLOPT_CONNECTTIMEOUT => 0,
    CURLOPT_TIMEOUT => 400,
    CURLOPT_FAILONERROR => true
  ));

  $respuesta  = curl_exec($curl);

  $error_msg = NULL;
  if (curl_error($curl)) {
    $error_msg = curl_error($curl);
  }
  curl_close($curl);

  if ($error_msg){
    throw new Exception($error_msg, 1);
    exit;
  }

  $datos = json_decode($respuesta, true);

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}
*/
$dataProductos = [
    [
        "categoria"=>"ABK - BANNITAS",
        "descripcion"=>"VESTIDO BANNITAS ÑA.",
        "talla"=>"18M-36M",
        "precio"=>"80.00",
        "codigo_generado"=>"ABK-KWQIQIEQWIELJK",
        "veces"=>1
    ],
    /*
    [
        "categoria"=>"MJS - BANNITAS",
        "descripcion"=>"VESTIDO BANNITAS ÑA.",
        "talla"=>"18M-36M",
        "precio"=>"80.00",
        "codigo_generado"=>"MJS-KWQIQIEQWIELJK",
        "veces"=>2
    ]
    */
];


$pdf = new PDF_Code128($orientation='L', $unit='mm', array(33.8,22));
$pdf->SetMargins(2.5, 2);
$pdf->SetAutoPageBreak(false);
$pdf->AddFont('ArialBlack','','arial_black.php');

$anchoTope = 45.8;
$BORDES = 0;
foreach ($dataProductos as $key => $producto) {
    $numeroCopias = $producto["veces"];
    for ($i=0; $i < $numeroCopias; $i++) { 
        $pdf->AddPage();
        $pdf->SetFont('Helvetica','', 11);
        $pdf->CellFitScale($anchoTope, 4, utf8_decode($producto["categoria"]), $BORDES, 1,'C');
        /*

        $pdf->SetFont('Helvetica','', 10);
        $pdf->CellFitScale($anchoTope, 4, utf8_decode($producto["descripcion"]), $BORDES, 1,'C');
        $pdf->SetFont('Arial','B', 7);
        $pdf->CellFitScaleForce($anchoTope / 2, 3.5, utf8_decode("TALLA: ".$producto["talla"]), $BORDES, 0);
        $pdf->SetFont('ArialBlack','', 10);
        $pdf->CellFitScale($anchoTope / 2, 3.5, utf8_decode("S/. ".$producto["precio"]), $BORDES, 1,'C');
        $pdf->SetFont('Arial','', 6);
        */
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $w = $anchoTope;
        $h = 15;

        $pdf->Code128($x,$y,$producto["codigo_generado"],$w,$h);
        $pdf->SetXY($x + 2.5, $y + $h + .25);
        $pdf->Ln(1);

        $pdf->CellFitScaleForce($anchoTope - 5, 2, $producto["codigo_generado"], $BORDES, 1,'C');

    }
}

$pdf->output();
ob_end_flush();
exit;