<?php 

/** Incluye PHPExcel */
require_once '../util/Classes/PHPExcel.php';
require_once '../negocio/util/Funciones.php';           
require_once '../datos/local_config_web.php';
require_once MODELO . "/Venta.clase.php";

$objPHPExcel = new PHPExcel();    
$objVenta = new Venta();


  $_GET['p_modo'] = 0;
  if ( isset($_GET['p_tipo'])) {  
    try {
        $tipo  = $_GET['p_tipo'];
        $f0 = isset($_GET['p_f0']) ? $_GET["p_f0"] : null;
        $f1 = isset($_GET['p_f1']) ? $_GET["p_f1"] : null;
        $codCliente  = isset($_GET['p_cl']) ? $_GET["p_cl"] : null;
        $codSucursal  = isset($_GET['p_su']) ? $_GET["p_su"] : null;
        $empresa = NOMBRE_EMPRESA;


        $dataReporte = $objVenta->reporteGeneral($f0,$f1, $tipo, $codSucursal, $codCliente);

        $cabecera = $dataReporte["cabecera"];
        $detalle = $dataReporte["data"];

		$objPHPExcel->getProperties()->setCreator($empresa)
									 ->setTitle("Reporte Ventas")
									 ->setSubject("Reporte de Ventas");

		$objPHPExcel->setActiveSheetIndex(0);

		$tituloStyle = array('font' => array('bold' => true,'size' => 15),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$subtituloStyle = array('font' => array('size' => 10),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$fechaHoraStyle = array('font' => array('bold' => true, 'name' => 'Arial','size' => 8),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));

		if ($tipo == "false"){
			$cadenaFecha = 'Reporte de';
			$cadenaFecha .= ($f1 == $f0) ? ' '.Funciones::fechear($f0) : 'l '.Funciones::fechear($f0).' al '.Funciones::fechear($f1);
		} else {
			$cadenaFecha = "Todas las ventas";
		}

		$objPHPExcel->getActiveSheet()
					->setCellValue('A1', $empresa)
					->mergeCells('A2:N2')
					->mergeCells('A3:N3')
					->setCellValue('D1', 'Fecha: '.date('d-m-Y').' Hora: '.date('H:i:s'))
					->mergeCells('D1:N1')
					->setCellValue('A2', 'REPORTE DE VENTAS')
					->setCellValue('A3', $cadenaFecha);

		$filaStart = 5;

		$sheet = $objPHPExcel->getActiveSheet();

		$sheet			
					->setCellValue('F'.$filaStart, !isset($cabecera["total_produccion"]) ? "0.00" : $cabecera["total_descuentos"])
					->setCellValue('G'.$filaStart, 'Importe Efectivo')
					->setCellValue('H'.$filaStart, !isset($cabecera["monto_efectivo"]) ? "0.00" : $cabecera["monto_efectivo"])
					->setCellValue('I'.$filaStart, 'Importe Tarjetas')
					->setCellValue('J'.$filaStart, !isset($cabecera["monto_tarjeta"]) ? "0.00" : $cabecera["monto_tarjeta"])
					->setCellValue('K'.$filaStart, 'Importe Crédito')
					->setCellValue('L'.$filaStart, !isset($cabecera["monto_credito"]) ? "0.00" : $cabecera["monto_credito"]);

		$sheet->getStyle('D1')->applyFromArray($fechaHoraStyle);
		$sheet->getStyle('A1')->applyFromArray($fechaHoraStyle);
		$sheet->getStyle('A2:N2')->applyFromArray($tituloStyle);
		$sheet->getStyle('A3:N3')->applyFromArray($subtituloStyle);

		$sheet->getStyle('B'.$filaStart)->getNumberFormat()->setFormatCode('#,##0.00');
		$sheet->getStyle('F'.$filaStart)->getNumberFormat()->setFormatCode('#,##0.00');
		$sheet->getStyle('H'.$filaStart)->getNumberFormat()->setFormatCode('#,##0.00');
		$sheet->getStyle('J'.$filaStart)->getNumberFormat()->setFormatCode('#,##0.00');
		$sheet->getStyle('L'.$filaStart)->getNumberFormat()->setFormatCode('#,##0.00');

		/*Inicio tabla CABECERA: A6-D6*/
		$filaInit = $filaStart + 2;
		$filaI = $filaInit;

			$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$filaI, 'Código')
					->setCellValue('B'.$filaI, 'Comprobante')
					->setCellValue('C'.$filaI, 'Voucher')
					->setCellValue('D'.$filaI, 'Documento')
					->setCellValue('E'.$filaI, 'Cliente')
					->setCellValue('F'.$filaI, 'Monto Efectivo')
					->setCellValue('G'.$filaI, 'Monto Tarjeta')
					->setCellValue('H'.$filaI, 'Monto Crédito')
					->setCellValue('I'.$filaI, 'Fecha Venta')
					->setCellValue('J'.$filaI, 'Subtotal')
					->setCellValue('K'.$filaI, 'Total descuentos')
					->setCellValue('L'.$filaI, 'Importe total Sin IGV')
					->setCellValue('M'.$filaI, 'IGV')
					->setCellValue('N'.$filaI, 'Importe total')
					->setCellValue('O'.$filaI, 'Sucursal');
			$filaI++;

			$primerFila = $filaI;


			$sheet->getColumnDimension('A')->setWidth(17);
			$sheet->getColumnDimension('B')->setWidth(15);
			$sheet->getColumnDimension('C')->setWidth(15);
			$sheet->getColumnDimension('D')->setWidth(18);
			$sheet->getColumnDimension('E')->setWidth(35);
			$sheet->getColumnDimension('F')->setWidth(15);
			$sheet->getColumnDimension('G')->setWidth(15);
			$sheet->getColumnDimension('H')->setWidth(15);
			$sheet->getColumnDimension('I')->setWidth(16);
			$sheet->getColumnDimension('J')->setWidth(16);
			$sheet->getColumnDimension('K')->setWidth(16);
			$sheet->getColumnDimension('L')->setWidth(16);
			$sheet->getColumnDimension('M')->setWidth(16);
			$sheet->getColumnDimension('N')->setWidth(16);
			$sheet->getColumnDimension('O')->setWidth(16);
	
			foreach ($detalle as $_ => $value) {
					$objPHPExcel->getActiveSheet()
						->setCellValue('A'.$filaI, $value["codigo"] )
						->setCellValue('B'.$filaI, $value["comprobante"] )
						->setCellValue('C'.$filaI, $value["voucher"])
						->setCellValue('D'.$filaI, $value["numero_documento"])
						->setCellValue('E'.$filaI, $value["cliente"])
						->setCellValue('F'.$filaI, number_format($value["monto_efectivo"],2))
						->setCellValue('G'.$filaI, number_format($value["monto_tarjeta"],2))
						->setCellValue('H'.$filaI, number_format($value["monto_tarjeta"],2))
						->setCellValue('I'.$filaI, $value["fecha_venta"])
						->setCellValue('J'.$filaI, number_format($value["subtotal"],2))
						->setCellValue('K'.$filaI, number_format($value["total_descuentos"],2))
						->setCellValue('L'.$filaI, number_format($value["total_gravadas"],2))
						->setCellValue('M'.$filaI, number_format($value["sumatoria_igv"],2))
						->setCellValue('N'.$filaI, number_format($value["importe_total"],2))
						->setCellValue('O'.$filaI, $value["sucursal"]);

					$filaI++;
			}

			$ultimaFila = $filaI;

			$sheet->getStyle('F'.$primerFila.':F'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('G'.$primerFila.':G'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('G'.$primerFila.':H'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('I'.$primerFila.':J'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('J'.$primerFila.':K'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('K'.$primerFila.':L'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('L'.$primerFila.':M'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('M'.$primerFila.':N'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');

		$headerTablaStyle = array('font' => array('bold' => true,),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$sheet->getStyle('A'.$filaInit.':L'.$filaInit)->applyFromArray($headerTablaStyle);
		$sheet->setTitle('Reporte Ventas');
		 
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="reporte-ventas-'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;

    } catch (Exception $exc) {
        $objWebServices = new WebServices(500, "ERROR", $exc->getMessage());
    }   
  } else {
        $objWebServices = new WebServices(400, "Faltan parámetros", "");
  }
    

