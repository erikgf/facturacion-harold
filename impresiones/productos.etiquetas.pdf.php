<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
date_default_timezone_set('America/Lima');
require '../datos/config_facturacion.php';
require "PDFCode128.clase.php";


if (!isset($_POST['p_data'])){  
  echo "No se ha recibido un datos para el reporte.";
  exit;
}

$data = json_decode($_POST["p_data"], true);

$ids = $data["ids"];
$key = $data["key"];

if ($ids == NULL){
    echo "No se ha recibido un ID de productos válidos.";
    exit;
}

if ($key == NULL){
  echo "No tiene permisos para ver este reporte.";
  exit;
}

try {

  $ruta = F_SERVER_API."productos-tickets";
  $authorization = "Authorization: Bearer $key";
  $requestData = ["items"=>$ids];

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', $authorization],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => json_encode($requestData),
    CURLOPT_URL => $ruta,
    CURLOPT_USERAGENT => 'Consulta Productos Tickets',
    CURLOPT_CONNECTTIMEOUT => 0,
    CURLOPT_TIMEOUT => 4000,
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
  }

  $datos = json_decode($respuesta, true);

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}


/*
$dataProductos = [
    [
        "categoria"=>"ABK - BANNITAS",
        "descripcion"=>"VESTIDO BANNITAS ÑA.",
        "talla"=>"18M-36M",
        "precio"=>"80.00",
        "codigo_generado"=>"ABK-00001-115-3",
        "veces"=>1
    ],
    [
        "categoria"=>"MJS - BANNITAS",
        "descripcion"=>"VESTIDO BANNITAS ÑA.",
        "talla"=>"18M-36M",
        "precio"=>"80.00",
        "codigo_generado"=>"ABK-BNO-423",
        "veces"=>2
    ]
];
*/


$pdf = new PDF_Code128('L', 'mm',  array(25.4, 50.8)); //25.4
$pdf->SetMargins(3, 2.5, 3);
$pdf->SetAutoPageBreak(true, 0);
$pdf->AddFont('ArialBlack','','arial_black.php');
$pdf->AddFont('Helvetica','','helvetica.php');

$anchoTope = 45.8;
$BORDES = 0;
$cantidad = count($datos);
foreach ($datos as $key => $producto) {
    $numeroCopias = $producto["veces"];
    for ($i=0; $i < $numeroCopias; $i++) { 
        $pdf->AddPage('L');
        $pdf->SetFont('Helvetica','B', 10);
        $pdf->CellFitScale($anchoTope, 3.5, $producto["empresa_especial"]." - ".utf8_decode($producto["marca"]["nombre"]), $BORDES, 1,'C');
        $pdf->SetFont('Helvetica','', 8);
        $pdf->CellFitScale($anchoTope, 3.5, utf8_decode($producto["nombre"]), $BORDES, 1,'C');
        $pdf->SetFont('Arial','B', 6.5);
        $pdf->CellFitScale($anchoTope / 2, 3.5, utf8_decode("TALLA: ".@$producto["talla"] ?: ""), $BORDES, 0);
        $pdf->SetFont('ArialBlack','', 8.5);
        $pdf->CellFitScale($anchoTope / 2, 3.5, utf8_decode("S/ ".$producto["precio_unitario"]), $BORDES, 1,'C');
        
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $w = $anchoTope *.75;
        $h = 8;

        $pdf->Code128($x + 5,$y,$producto["codigo_generado"],$w,$h);
        $pdf->SetXY($x + 2.5, $y + $h + .25);
        $pdf->Ln(0);
        
        $pdf->SetX($pdf->GetX()+12.5);
        $pdf->SetFont('Courier','B', 5);
        $pdf->CellFitScale($anchoTope - 25, 2, $producto["codigo_generado"], $BORDES, 1,'C');

    }
}

$pdf->output();
ob_end_flush();
exit;