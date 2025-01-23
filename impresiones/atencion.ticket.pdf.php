<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
date_default_timezone_set('America/Lima');
require '../datos/config_facturacion.php';
require "PDF.clase.php";
//use Endroid\QrCode\QrCode;

if (!isset($_POST['p_data'])){  
  echo "No se ha recibido un datos para el reporte.";
  exit;
}

$data = json_decode($_POST["p_data"], true);

$id = $data["id"];
$key = $data["key"];

if ($id == NULL){
    echo "No se ha recibido un ID de comprobante válido.";
    exit;
}

if ($key == NULL){
  echo "No tiene permisos para ver este reporte.";
  exit;
}

$fecha_impresion = date("d/m/Y");
$fecha_qr = date( "Y-m-d" );

try {

  $ruta = F_SERVER_API."ventas-ticket/$id";
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

$empresa = F_RAZON_SOCIAL_IMPRESIONES;
$ruc = "R.U.C.: ".F_RUC;
$direccion = F_DIRECCION;
$direccion_2 = F_DIRECCION_2;
//$lugar = F_URBANIZACION;
$ubigeo = F_DIRECCION_DISTRITO."-".F_DIRECCION_PROVINCIA."-".F_DIRECCION_DEPARTAMENTO;
$telefono = "Telf.: ".F_TELEFONO;

$pdf = new PDF($orientation='P', $unit='mm', array(80,270));
$pdf->AddPage();

$pdf->AddFont('IckyTicketMono','','IckyTicketMono.php');
$pdf->AddFont('IckyTicketMono','B','IckyTicketMono.php');

$FONT = "IckyTicketMono";
$aumento_font = 2.65;

$MARGENES_LATERALES = 5.00;
$pdf->SetMargins($MARGENES_LATERALES, $MARGENES_LATERALES, $MARGENES_LATERALES); 

$ANCHO_TICKET = $pdf->GetPageWidth();
$ALTO_LINEA = 3;
$BORDES = 0;
$SALTO_LINEA = .65;

/*CABECERA*/

$fecha_atencion = $datos["fecha_venta"];
$hora_atencion = $datos["hora_venta"];
$numero_ticket = $datos["serie"]."-".$datos["correlativo"];
$nombre_cliente = utf8_decode($datos["cliente"]["nombres_completos"]);
$numero_documento = $datos["cliente"]["numero_documento"];
$observaciones = $datos["observaciones"];
$descuento_global = $datos["monto_descuento"];

$monto_efectivo = $datos["monto_efectivo"];
$monto_tarjeta = $datos["monto_tarjeta"];
$monto_deposito = $datos["monto_deposito"];
$monto_yape = $datos["monto_yape"];
$monto_plin = $datos["monto_plin"];
$monto_credito = $datos["monto_credito"];
$servicios = $datos["detalle"];

$usuario_impresion = utf8_decode($datos["usuario_impresion"]);
$usuario_registro = utf8_decode($datos["usuario_registro"]);


$ANCHO_TICKET_MENOS_MARGENES = $ANCHO_TICKET - ($MARGENES_LATERALES * 2);

$pdf->Image('../imagenes/logo_mediano_blanco.jpg', 20 , 5, 40, 25);
$pdf->SetY(30 + 5);

$pdf->SetFont($FONT,'B',13 + $aumento_font); 


$pdf->SetFont($FONT,'B', 13.5 + $aumento_font); 
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES,$ALTO_LINEA , F_NOMBRE_COMERCIAL,$BORDES,1,"C");

$pdf->Ln(0.5);

$pdf->SetFont($FONT,'', 10 + $aumento_font); 
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES,$ALTO_LINEA * 1.4 , $ruc ,$BORDES,1,"C");

$pdf->Ln(1.5);

$pdf->SetFont($FONT,'',5.5 + $aumento_font); 

$ALTO_LINEA_LV1 = 3;
$ALTO_LINEA_LV2 = $ALTO_LINEA - 1;
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES , $ALTO_LINEA_LV1,utf8_decode($direccion),$BORDES,1,"C");
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES , $ALTO_LINEA_LV1,utf8_decode($direccion_2),$BORDES,1,"C");
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES , $ALTO_LINEA_LV1,utf8_decode($ubigeo),$BORDES,1,"C");
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES , $ALTO_LINEA_LV1,$telefono,$BORDES,1,"C");

$pdf->Ln(5);

$pdf->SetFont($FONT,'', 7 + $aumento_font); 
$ANCHO_COLS = [19, 2, 0];

$ANCHO_COLS[2] = $ANCHO_TICKET - ($ANCHO_COLS[0] + $ANCHO_COLS[1]) - ($MARGENES_LATERALES * 2);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "N. RECIBO", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5,  str_pad($numero_ticket,6,'0',STR_PAD_LEFT) , $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "Fecha", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5,  $fecha_atencion , $BORDES,0);

$pdf->Cell($ANCHO_COLS[0] / 2, $ALTO_LINEA + .5, "Hora: ", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5, $hora_atencion, $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "Cliente", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->CellFitScale($ANCHO_COLS[2], $ALTO_LINEA + .5,  $nombre_cliente , $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("DNI/RUC/CE"), $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5,  $numero_documento , $BORDES,1);


$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("FORMA PAGO"), $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);

$cadenaTipoPago = "";
$cadenaTipoPagoSolo = "";
$numeroPagos = 0;

if ($monto_efectivo > 0.00){
  $cadenaTipoPagoSolo = "EFECTIVO";
  $cadenaTipoPago .= $cadenaTipoPagoSolo."(".$monto_efectivo.") ";
  $numeroPagos++;
}

if ($monto_tarjeta > 0.00){
  $cadenaTipoPagoSolo = "TARJETA";
  $cadenaTipoPago .= $cadenaTipoPagoSolo."(".$monto_tarjeta.") ";
  $numeroPagos++;
}

if ($monto_deposito > 0.00){
  $cadenaTipoPagoSolo = "DEPÓSITO";
  $cadenaTipoPago .= $cadenaTipoPagoSolo."(".$monto_deposito.") ";
  $numeroPagos++;
}


if ($monto_yape > 0.00){
  $cadenaTipoPagoSolo = "YAPE";
  $cadenaTipoPago .= $cadenaTipoPagoSolo."(".$monto_deposito.") ";
  $numeroPagos++;
}

if ($monto_plin > 0.00){
  $cadenaTipoPagoSolo = "PLIN";
  $cadenaTipoPago .= $cadenaTipoPagoSolo."(".$monto_deposito.") ";
  $numeroPagos++;
}

if ($monto_credito > 0.00){
  $cadenaTipoPagoSolo = "CRÉDITO";
  $cadenaTipoPago .= $cadenaTipoPagoSolo."(".$monto_deposito.") ";
  $numeroPagos++;
}

if ($numeroPagos <= 1){
  $cadenaTipoPago = $cadenaTipoPagoSolo;
}

if ($cadenaTipoPago == ""){
  $cadenaTipoPago = "NINGUNO";
}

$pdf->MultiCell($ANCHO_COLS[2], $ALTO_LINEA + .5,  utf8_decode($cadenaTipoPago), $BORDES,1);

$pdf->Ln($SALTO_LINEA);

if ($observaciones && strlen($observaciones) > 0){
  $pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("Observaciones"), $BORDES,0);
  $pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,1);
  $pdf->Cell($ANCHO_TICKET, $ALTO_LINEA + .5,  $observaciones , $BORDES,1);
  $pdf->Ln($SALTO_LINEA);
}

$TAMAÑO_MAXIMO_NOMBRE_SERVICIO = 40;
$ANCHO_COLS_DETALLE = [5, $TAMAÑO_MAXIMO_NOMBRE_SERVICIO, 12.5, 0];
$ANCHO_COLS_DETALLE[3] = $ANCHO_TICKET - ($ANCHO_COLS_DETALLE[0] + $ANCHO_COLS_DETALLE[1] + $ANCHO_COLS_DETALLE[2] + ($MARGENES_LATERALES * 2));
$ALTO_LINEA = 3.35;

$pdf->Ln($SALTO_LINEA * 5); 
$pdf->SetFont($FONT,'B', 5 + $aumento_font); 
$pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "CNT", $BORDES,0,"C");
$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "SERVICIO", $BORDES,0);
$pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "", $BORDES,0,"C");
$pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, "TOTAL" , $BORDES,1 ,"C");
$pdf->SetFont($FONT,'', 6.5 + $aumento_font); 

$pdf->Cell($ANCHO_TICKET - ($MARGENES_LATERALES * 2), .15, "---------------------------------------------" , $BORDES,1);

$pdf->Ln($SALTO_LINEA * 2.5); 

$total  = 0.00;
foreach ($servicios as $key => $value) {
  $subtotal = round($value["precio_unitario"] * $value["cantidad"],2);
  $pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, $value["cantidad"], $BORDES,0 ,"C");
  $pdf->CellFitScale($ANCHO_COLS_DETALLE[1] + $ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5,  utf8_decode($value["nombre_servicio"]), $BORDES,0);
  $pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, number_format($subtotal,2) , $BORDES,1,"R");

  $total += $subtotal;
}

$total -= $descuento_global;

$pdf->Ln($SALTO_LINEA); 

$pdf->SetFont($FONT,'B', 6.5 + $aumento_font); 

if ($descuento_global > 0.00){
  $pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");
  $pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5,"DSCTO", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "S/", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, "-".number_format($descuento_global, 2) , $BORDES,1, "R");
}

$pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");
$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "TOTAL", $BORDES,0,"R");
$pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "S/", $BORDES,0,"R");
$pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, number_format($total, 2) , $BORDES,1, "R");

$pdf->Ln($SALTO_LINEA * 1.5); 
$pdf->Cell($ANCHO_TICKET - ($MARGENES_LATERALES * 2), .15, "---------------------------------------------" , $BORDES,1);
$pdf->Ln($SALTO_LINEA * 1.5);

/*
if ($total_credito > 0){
  $pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");
  $pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "MTO. PAGADO", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "S/", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, number_format($total - $total_credito, 2) , $BORDES,1, "R");

  $pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");
  $pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "SALDO", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "S/", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, number_format($total_credito, 2) , $BORDES,1, "R");
}

if ($total_vuelto > 0){
  $pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");
  $pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "VUELTO", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "S/", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, number_format($total_vuelto, 2) , $BORDES,1, "R");
}
*/

$pdf->SetFont($FONT,'', 6 + $aumento_font); 

$pdf->Ln($SALTO_LINEA * 6); 

$pdf->SetFont( $FONT, "B", 7.25 + $aumento_font);
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES, $ALTO_LINEA + .5, "GRACIAS POR SU PREFERENCIA", $BORDES,1,"C");

$pdf->Ln($SALTO_LINEA * 2); 


$pdf->SetFont($FONT,'', 6 + $aumento_font); 
$pdf->Ln($SALTO_LINEA * 10); 

$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA, "ATENDIDO POR: ".$usuario_registro, $BORDES,1);
$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA, "IMPRESO POR: ".$usuario_impresion, $BORDES,1);

$pdf->Ln($SALTO_LINEA * 10); 
/*
$pdf->SetFont($FONT,'', 5.5 + $aumento_font); 
$pdf->MultiCell($ANCHO_TICKET - ($MARGENES_LATERALES * 2),$ALTO_LINEA - 1, utf8_decode("Se le recomienda conservar este TICKET. ".F_NOMBRE_COMERCIAL_TICKET." no se hace responsable de la pérdida de este y es de carácter OBLIGATORIO que sea presentado para gestionar devoluciones y/u otros procesos requeridos por el cliente."),$BORDES,"C");
*/

$pdf->AutoPrint();
$pdf->Output();

ob_end_flush();
exit;
