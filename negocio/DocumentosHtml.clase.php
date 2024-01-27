<?php 

require '../datos/config_facturacion.php';
require "../sistema_facturacion/plugins/phpqrcode/qrlib.php";

class DocumentosHtml {
	private $ruc_empresa = F_RUC;
	private $nombre_empresa = F_NOMBRE_COMERCIAL;
	private $direccion_empresa = F_DIRECCION;
	private $celular =  F_TELEFONO;
	private $correo = "	";

	public $IGV = .18;

	private function cabeceraComprobante($cabecera){
		$html = '<table >
					<tbody class="cabecera_documento">
						<tr>
							<td>
								<div class="nombre_empresa">
									<div class="nombre_empresa_rotulo">'.$this->nombre_empresa.'</div>
									<img class="logofactura" style="width:130px" src="../imagenes/logo.jpeg">
									<p>'.$this->direccion_empresa.'</p>
									<p><img src="../assets/theme_doc_elect/images/telephone.png" style="width: 12px;">'.$this->celular.'</p>
									<!-- <p><img src="../assets/theme_doc_elect/images/email.svg" style="width: 12px;"> Correo: '.$this->correo.'</p> -->
								</div>
							</td>
							<td>
								<div class="nombre_comprobante"">
									<p>R.U.C. '.$this->ruc_empresa.'</p>                    
									<p>'.($cabecera["tipo_comprobante"] == '03' ? 'BOLETA DE VENTA' : 'FACTURA').'<br>ELECTRÓNICA</p>
									<p>'.$cabecera["serie"].' - '.$cabecera["correlativo"].'</p>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			<table>
				<tbody class="cabecera_cliente" >
					<tr>
						<td>
							<div class="col-izq">
								<label class="">Razón Social:</label>
								<div class="txt-izq text-left">'.$cabecera["nombre_cliente"].'</div>
							</div>
						</td>
						<td>
							<div>
								<label>Fecha de Emisión:</label>
								<div class="txt-der">'.Funciones::fechear($cabecera["fecha_emision"]).'</div>
							</div>
						</td>
					</tr>
					<tr>
						<td >
							<div class="col-izq">
								<label>'.$cabecera["tipo_documento"].' N°:</label>
								<div class="txt-izq text-left"> '.(!isset($cabecera["numero_documento"]) || $cabecera["numero_documento"] == "" ? " - " : $cabecera["numero_documento"]).' </div>
							</div>
						</td>
						<td >
							<div>
								<label>Moneda: </label>
								<div class="txt-der">'.$cabecera["moneda"].'</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="col-izq">
								<label>Dirección:</label>
								<div class="txt-izq text-left">'.(!isset($cabecera["direccion_cliente"]) || $cabecera["direccion_cliente"] == "" ? " - " : $cabecera["direccion_cliente"]).'</div>
							</div>
						</td>
						<td>
							<div>
								<label>Guía de Remisión: </label>
								<div class="txt-der">&nbsp;</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			';

		return $html;
	}

	public function get_html_comprobante($data) {
		//Cabecera, 
		//Detalle
		$cabecera = $data["cabecera"];
		$detalle = $data["detalle"];

		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html lang="es-ES" prefix="og: http://ogp.me/ns#" xmlns="https://www.w3.org/1999/xhtml">
		<head>   
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport" />
			<link href="../assets/font-awesome-4.5.0/css/font-awesome.css" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">  
			<link href="../assets/theme_doc_elect/css/estilo.a4.css" rel="stylesheet">
			<title>'.$cabecera["serie"].'-'.$cabecera["correlativo"].'</title>
		</head>
		<body >
			'.$this->cabeceraComprobante($cabecera).'						
			<div class="tablageneral"> 
			 <small> 
				<table>
					<thead>
						<tr>
							<th class="th-small">Cantidad</th>
							<th class="th-small">UM</th>
							<th>Descripción</th>
							<th class="th-medium">P. Unitario</th>
							<th class="th-medium">V. Unitario</th>
							<!-- <th class="th-medium">Descuento</th>-->
							<th class="th-medium">V. Venta</th>
						</tr>
					</thead>
				<tbody>
						';
		foreach ($detalle as $key => $value) {
			$html .= '<tr class="detalletable">
							<td>'.$value["cantidad_item"].'</td>
							<td>'.$value["unidad_medida"].'</td>
							<td>'.$value["nombre_producto"].'</td>
							<td>'.$value["precio_venta_unitario"].'</td>
							<td>'.number_format($value["valor_unitario"],2).'</td>
							<!-- <td>'.$value["descuento_comprobante"].'</td>-->
							<td>'.$value["valor_venta"].'</td>
						</tr>';

		}

		$total_gravadas = $cabecera["total_gravadas"];
		$total_igv =  $cabecera["sumatoria_igv"];
		$total_descuentos = $cabecera["total_descuentos_comprobante"];
		$descuento_global = $cabecera["descuento_global_comprobante"];
		$importe_total = $cabecera["importe_total_venta"];

		$text_qr = $this->ruc_empresa.'|'.$cabecera["tipo_comprobante"].'|'.$cabecera["serie"].'|'.
					$cabecera["correlativo"].'|'.$total_igv.'|'.$importe_total.'|'.$cabecera["fecha_emision"].'|'.$cabecera["cod_tipo_documento"].'|'.$cabecera["numero_documento"].'|';
		$ruta_qr = "../images/imgqr/qr.png";
		QRcode::png($text_qr, $ruta_qr, 'Q',15, 0);

		$html .= '
						<tr class="detalletable ">
							<td colspan="6" class="importeletras">SON: '.Funciones::numtoletras($importe_total).'</td>
						</tr>';

		$html .= '		
						<tr class="detalletable">                                                                
							<td class="imprentaqr" rowspan="4" colspan="2" >
								<img src="../images/imgqr/qr.png" />
							</td>
							<td class="text-left" style="font-size:10px;" rowspan="4" colspan="1">
						';

		$hash_cpe = $cabecera["hash_cpe"];
		$estado_aceptado_sunat = $cabecera["estado_sunat"] == "A";

		if ($estado_aceptado_sunat){
			/*
			$html .= '		
								Autorizado mediante Resolución de Intendencia N° 032-005-Representación impresa de la Boleta Electrónica. Consulte su documento electrónico en: 
								<br><b style="font-size:8px;">https://www.vapingshopperu.com</b>
								*/
			$html .= '			<br>
								<br>
								HASH:
								<br>
								<b style="font-size:12px;">'.$hash_cpe.'</b>';
		}


		$html .= '		<tr class="detalletable">                                                                
							<td class="text-left" colspan="2"><b>Total Gravadas</b></td>
							<td>'.number_format($total_gravadas,2).'</td>
						</tr>  
						<tr class="detalletable">                                                                
							<td class="text-left" colspan="2"><b>I.G.V. ('.($this->IGV * 100).'%)</b></td>
							<td>'.number_format($total_igv,2).'</td>
						</tr>  		
						<tr class="detalletable">
							<td class="text-left" colspan="2"><b>Importe Total</b></td>
							<td>'.$importe_total.'</td>
						</tr>			
				</tbody>
				</table>
			</div>
		</body>
			<script type="text/javascript">
		      	window.onload = function() { window.print(); };
		 	</script>
		</html>
		';

		$resp['respuesta'] = 'ok';
		$resp['html'] = $html;
		return $resp;
	}

	private function cabeceraComprobantePDF($cabecera){
		$html = '<table >
					<tbody class="cabecera_documento">
						<tr>
							<td>
								<div class="nombre_empresa">
									<div class="nombre_empresa_rotulo">'.$this->nombre_empresa.'</div>
									<img class="logofactura" style="width:130px" src="../imagenes/logo.jpeg">
									<p>'.$this->direccion_empresa.'</p>
									<p><img src="../assets/theme_doc_elect/images/telephone.png" style="width: 12px;"> Cel.: '.$this->celular.'</p>
									<p><img src="../assets/theme_doc_elect/images/email.svg" style="width: 12px;"> Correo: '.$this->correo.'</p>
								</div>
							</td>
							<td>
								<div class="nombre_comprobante"">
									<p>RUC: '.$this->ruc_empresa.'</p>';


		switch ($cabecera["tipo_comprobante"]) {
		 	case "01":
		 		$rotulo = "FACTURA ELECTRÓNICA";
		 		break;
		 	case "03":
		 		$rotulo = "BOLETA DE VENTA ELECTRÓNICA";
		 		break;
		 	case "CO":	
		 		$rotulo = "COTIZACIÓN";
		 		break;
		 } 

		$html .= '					<p>'.$rotulo.'</p>
									<p>'.$cabecera["serie"].' - '.$cabecera["correlativo"].'</p>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			<table>
				<tbody class="cabecera_cliente" >
					<tr>
						<td>
							<div class="col-izq">
								<label class="">Razón Social:</label>
								<div class="txt-izq text-left">'.$cabecera["nombre_cliente"].'</div>
							</div>
						</td>
						<td>
							<div>
								<label>Fecha de Emisión:</label>
								<div class="txt-der">'.Funciones::fechear($cabecera["fecha_emision"]).'</div>
							</div>
						</td>
					</tr>
					<tr>
						<td >
							<div class="col-izq">
								<label>'.$cabecera["tipo_documento"].' N°:</label>
								<div class="txt-izq text-left"> '.(!isset($cabecera["numero_documento"]) || $cabecera["numero_documento"] == "" ? " - " : $cabecera["numero_documento"]).' </div>
							</div>
						</td>
						<td >
							<div>
								<label>Moneda: </label>
								<div class="txt-der">'.$cabecera["moneda"].'</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="col-izq">
								<label>Dirección:</label>
								<div class="txt-izq text-left">'.(!isset($cabecera["direccion_cliente"]) || $cabecera["direccion_cliente"] == "" ? " - " : $cabecera["direccion_cliente"]).'</div>
							</div>
						</td>
						<td>
							<div>
								<label>Guía de Remisión: </label>
								<div class="txt-der">&nbsp;</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			';

		return $html;
	}

	public function get_html_comprobante_pdf($data, $hash_cpe) {
		//Cabecera, 
		//Detalle
		$cabecera = $data["cabecera"];
		$detalle = $data["detalle"];

		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html lang="es-ES" prefix="og: http://ogp.me/ns#" xmlns="https://www.w3.org/1999/xhtml">
		<head>   
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport" />
			<link href="../../assets/font-awesome-4.5.0/css/font-awesome.css" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">  
			<link href="../../assets/theme_doc_elect/css/estilo.a4.pdf.css" rel="stylesheet">
			<title>'.$cabecera["serie"].'-'.$cabecera["correlativo"].'</title>
			<style>
			
			</style>
		</head>
		<body >
			'.$this->cabeceraComprobantePDF($cabecera).'						
			<div class="tablageneral"> 
			 <small> 
				<table>
					<thead>
						<tr>
							<th class="th-small">Cantidad</th>
							<th class="th-small">UM</th>
							<th>Descripción</th>
							<th class="th-medium">P. Unitario</th>
							<th class="th-medium">V. Unitario</th>
							<th class="th-medium">Descuento</th>
							<th class="th-medium">V. Venta</th>
						</tr>
					</thead>
				<tbody>
						';
		foreach ($detalle as $key => $value) {
			$html .= '<tr class="detalletable">
							<td>'.$value["cantidad_item"].'</td>
							<td>'.$value["unidad_medida"].'</td>
							<td>'.$value["nombre_producto"].'</td>
							<td>'.$value["precio_venta_unitario"].'</td>
							<td>'.number_format($value["valor_unitario"],2).'</td>
							<td>'.$value["descuento_comprobante"].'</td>
							<td>'.$value["valor_venta"].'</td>
						</tr>';

		}

		$total_gravadas = $cabecera["total_gravadas"];
		$total_igv =  $cabecera["sumatoria_igv"];
		$total_descuentos = $cabecera["total_descuentos_comprobante"];
		$descuento_global = $cabecera["descuento_global_comprobante"];
		$importe_total = $cabecera["importe_total_venta"];

		$text_qr = $this->ruc_empresa.'|'.$cabecera["tipo_comprobante"].'|'.$cabecera["serie"].'|'.
					$cabecera["correlativo"].'|'.$total_igv.'|'.$importe_total.'|'.$cabecera["fecha_emision"].'|'.$cabecera["cod_tipo_documento"].'|'.$cabecera["numero_documento"].'|';
		$ruta_qr = "../../images/imgqr/qr.png";
		QRcode::png($text_qr, $ruta_qr, 'Q',15, 0);

		$html .= '
						<tr class="detalletable ">
							<td colspan="7" class="importeletras">SON: '.Funciones::numtoletras($importe_total).'</td>
						</tr>
						<tr class="detalletable">                                                                
							<td class="imprentaqr" rowspan="4" colspan="2" >
								<img src="../../images/imgqr/qr.png" />
							</td>
							<td class="text-left" style="font-size:10px;" rowspan="4" colspan="2">
								Autorizado mediante Resolución de Intendencia N° 032-005-Representación impresa de la Boleta Electrónica. Consulte su documento electrónico en: 
								<br><b style="font-size:8px;">https://www.vapingshopperu.com</b>
								<br>
								<br>
								HASH:
								<br>
								<b style="font-size:12px;">'.$hash_cpe.'</b>
							</td>
							<td class="text-left" colspan="2"><b>Descuentos Globales</b></td>
							<td>'.number_format($descuento_global,2).'</td>
						</tr>
						<tr class="detalletable">                                                                
							<td class="text-left" colspan="2"><b>Total Gravadas</b></td>
							<td>'.number_format($total_gravadas,2).'</td>
						</tr>  
						<tr class="detalletable">                                                                
							<td class="text-left" colspan="2"><b>I.G.V. ('.($cabecera["porcentaje_igv"]).'%)</b></td>
							<td>'.number_format($total_igv,2).'</td>
						</tr>  		
						<tr class="detalletable">
							<td class="text-left" colspan="2"><b>Importe Total</b></td>
							<td>'.$importe_total.'</td>
						</tr>			
				</tbody>
				</table>
			  </small> 
			</div>
		</body>
		</html>
		';

		$resp['respuesta'] = 'ok';
		$resp['html'] = $html;
		return $resp;
	}


	private function cabeceraCotizacionPDF($cabecera){

		$html .= '<table >
					<tbody class="cabecera_documento">
						<tr>
							<td>
								<div class="nombre_empresa">
									<div class="nombre_empresa_rotulo">'.$this->nombre_empresa.'</div>
									<img class="logofactura" style="width:130px" src="../imagenes/logo.jpeg">
									<p>'.$this->direccion_empresa.'</p>
									<p><img src="../assets/theme_doc_elect/images/telephone.png" style="width: 12px;"> Cel.: '.$this->celular.'</p>
									<p><img src="../assets/theme_doc_elect/images/email.svg" style="width: 12px;"> Correo: '.$this->correo.'</p>
								</div>
							</td>
							<td>
								<div class="nombre_comprobante"">
									<p>RUC: '.$this->ruc_empresa.'</p>';


		switch ($cabecera["tipo_comprobante"]) {
		 	case "01":
		 		$rotulo = "FACTURA ELECTRÓNICA";
		 		break;
		 	case "03":
		 		$rotulo = "BOLETA DE VENTA ELECTRÓNICA";
		 		break;
		 	case "CO":	
		 		$rotulo = "COTIZACIÓN";
		 		break;
		 } 

		$html .= '					<p>'.$rotulo.'</p>
									<p>'.$cabecera["serie"].' - '.$cabecera["correlativo"].'</p>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			<table>
				<tbody class="cabecera_cliente" >
					<tr>
						<td>
							<div class="col-izq">
								<label class="">Razón Social:</label>
								<div class="txt-izq text-left">'.$cabecera["nombre_cliente"].'</div>
							</div>
						</td>
						<td>
							<div>
								<label>Fecha de Emisión:</label>
								<div class="txt-der">'.Funciones::fechear($cabecera["fecha_emision"]).'</div>
							</div>
						</td>
					</tr>
					<tr>
						<td >
							<div class="col-izq">
								<label>'.$cabecera["tipo_documento"].' N°:</label>
								<div class="txt-izq text-left"> '.(!isset($cabecera["numero_documento"]) || $cabecera["numero_documento"] == "" ? " - " : $cabecera["numero_documento"]).' </div>
							</div>
						</td>
						<td >
							<div>
								<label>Moneda: </label>
								<div class="txt-der">'.$cabecera["moneda"].'</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="col-izq">
								<label>Dirección:</label>
								<div class="txt-izq text-left">'.(!isset($cabecera["direccion_cliente"]) || $cabecera["direccion_cliente"] == "" ? " - " : $cabecera["direccion_cliente"]).'</div>
							</div>
						</td>
						<td>
							<div>
								<label>Guía de Remisión: </label>
								<div class="txt-der">&nbsp;</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			';

		return $html;
	}

	public function get_html_cotizacion($data) {
		//Cabecera, 
		//Detalle
		$cabecera = $data["cabecera"];
		$detalle = $data["detalle"];

		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html lang="es-ES" prefix="og: http://ogp.me/ns#" xmlns="https://www.w3.org/1999/xhtml">
		<head>   
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport" />
			<link href="../assets/font-awesome-4.5.0/css/font-awesome.css" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">  
			<link href="../assets/theme_doc_elect/css/estilo.a4.pdf.css" rel="stylesheet">
			<title>'.$cabecera["serie"].'-'.$cabecera["correlativo"].'</title>
			<style>
			
			</style>
		</head>
		<body >
			<div class="book">
			<a href="#" class="btnimprimir" onclick="print();">Imprimir</a>
				<div class="page">
					'.$this->cabeceraCotizacionPDF($cabecera).'
					<div class="tablageneral"> 
					 <small> 
						<table>
							<thead>
								<tr>
									<th class="th-small">Ítem</th>
									<th class="th-small">Cantidad</th>
									<th class="th-small">UM</th>
									<th>Descripción</th>
									<th class="th-medium">Marca</th>
									<th class="th-medium">P. Unit.</th>
									<th class="th-medium">Monto</th>
								</tr>
							</thead>
						<tbody>
								';
				foreach ($detalle as $key => $value) {
					$html .= '<tr class="detalletable">
									<td>'.$value["cantidad_item"].'</td>
									<td>'.$value["unidad_medida"].'</td>
									<td>'.$value["nombre_producto"].'</td>
									<td>'.$value["marca"].'</td>
									<td>'.$value["precio_unitario"].'</td>
									<td>'.number_format($value["subtotal"],2).'</td>
								</tr>';

				}

				$total_gravadas = $cabecera["subtotal"];
				$total_igv =  $cabecera["sumatoria_igv"];
				$importe_total = $cabecera["total"];

				$html .= '		<tr class="detalletable">                                                                
									<td class="text-left" colspan="2"><b>Total Gravadas</b></td>
									<td>'.number_format($total_gravadas,2).'</td>
								</tr>  
								<tr class="detalletable">                                                                
									<td class="text-left" colspan="2"><b>I.G.V. ('.($cabecera["porcentaje_igv"]).'%)</b></td>
									<td>'.number_format($total_igv,2).'</td>
								</tr>  		
								<tr class="detalletable">
									<td class="text-left" colspan="2"><b>Importe Total</b></td>
									<td>'.$importe_total.'</td>
								</tr>			
						</tbody>
						</table>
					  </small> 
					</div>
				</div>
			</div>
		</body>
		</html>
		';

		$resp['respuesta'] = 'ok';
		$resp['html'] = $html;
		return $resp;
	}

} 
