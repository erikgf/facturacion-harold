<?php 

/** Incluye PHPExcel */
require_once '../util/Classes/PHPExcel.php';
require_once '../negocio/util/Funciones.php';           
require_once '../datos/local_config_web.php';
require_once MODELO . "/Almacen.clase.php";

$objPHPExcel = new PHPExcel();    
$objAlmacen = new Almacen();

  if ( isset($_GET['p_su'])) {  
    try {
        $codSucursal  = isset($_GET['p_su']) ? $_GET["p_su"] : null;
        $codProducto  = isset($_GET['p_r']) ? $_GET["p_r"] : null;
        $empresa = NOMBRE_EMPRESA;

        $dataReporte = $objAlmacen->reporteKardex($codSucursal, $codProducto);

        $data = $dataReporte["data"];

		$objPHPExcel->getProperties()->setCreator($empresa)
									 ->setTitle("Reporte de Kardex")
									 ->setSubject("Reporte de Kardex");

		$objPHPExcel->setActiveSheetIndex(0);

		$tituloStyle = array('font' => array('bold' => true,'size' => 15),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$subtituloStyle = array('font' => array('size' => 10),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$fechaHoraStyle = array('font' => array('bold' => true, 'name' => 'Arial','size' => 8),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));

		$objPHPExcel->getActiveSheet()
					->setCellValue('A1', $empresa)
					->mergeCells('A2:L2')
					->mergeCells('A3:L3')
					->setCellValue('G1', 'Fecha: '.date('d-m-Y').' Hora: '.date('H:i:s'))
					->mergeCells('G1:L1')
					->setCellValue('A2', 'REPORTE DE KARDEX');

		$filaStart = 3;

		$sheet = $objPHPExcel->getActiveSheet();

		$sheet->getStyle('G1')->applyFromArray($fechaHoraStyle);
		$sheet->getStyle('A1')->applyFromArray($fechaHoraStyle);
		$sheet->getStyle('A2:L2')->applyFromArray($tituloStyle);
		$sheet->getStyle('A3:L3')->applyFromArray($subtituloStyle);

		/*Inicio tabla CABECERA: A6-D6*/
		$filaInit = $filaStart + 2;
		$filaI = $filaInit;

			$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$filaI, 'Fecha')
					->setCellValue('B'.$filaI, 'Sucursal')
					->setCellValue('C'.$filaI, 'Producto')
					->setCellValue('D'.$filaI, 'Fecha Venc.')
					->setCellValue('E'.$filaI, 'Lote')
					->setCellValue('F'.$filaI, 'Categoría')
					->setCellValue('G'.$filaI, 'Tipo')
					->setCellValue('H'.$filaI, 'Movimiento')
					->setCellValue('I'.$filaI, 'Precio Entrada')
					->setCellValue('J'.$filaI, 'Precio Salida')
					->setCellValue('K'.$filaI, 'Cantidad')
					->setCellValue('L'.$filaI, 'Totalizado');
			$filaI++;

			$primerFila = $filaI;

			$sheet->getColumnDimension('A')->setWidth(15);
			$sheet->getColumnDimension('B')->setWidth(18);
			$sheet->getColumnDimension('C')->setWidth(50);
			$sheet->getColumnDimension('D')->setWidth(14);
			$sheet->getColumnDimension('E')->setWidth(16);
			$sheet->getColumnDimension('F')->setWidth(17);
			$sheet->getColumnDimension('G')->setWidth(16);
			$sheet->getColumnDimension('H')->setWidth(14);
			$sheet->getColumnDimension('I')->setWidth(16);
			$sheet->getColumnDimension('J')->setWidth(16);
			$sheet->getColumnDimension('K')->setWidth(14);
			$sheet->getColumnDimension('L')->setWidth(18);
		
			foreach ($data as $_ => $value) {
					$objPHPExcel->getActiveSheet()
						->setCellValue('A'.$filaI, $value["fecha_movimiento"] )
						->setCellValue('B'.$filaI, $value["sucursal"] )
						->setCellValue('C'.$filaI, $value["producto"])
						->setCellValue('D'.$filaI, $value["fecha_vencimiento"])
						->setCellValue('E'.$filaI, $value["lote"])
						->setCellValue('F'.$filaI, $value["categoria"])
						->setCellValue('G'.$filaI, $value["tipo"])
						->setCellValue('H'.$filaI, $value["movimiento"])
						->setCellValue('I'.$filaI, $value["precio_entrada"])
						->setCellValue('J'.$filaI, $value["precio_salida"])
						->setCellValue('K'.$filaI, $value["cantidad"])
						->setCellValue('L'.$filaI, $value["totalizado"]);

					$filaI++;
			}

		$ultimaFila = $filaI - 1;

		$objPHPExcel->getActiveSheet()
						->setCellValue('K'.$filaI, '=SUM(K'.$primerFila.':K'.$ultimaFila.')' )
						->setCellValue('L'.$filaI, '=SUM(L'.$primerFila.':L'.$ultimaFila.')' );

		$headerTablaStyle = array('font' => array('bold' => true,),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$sheet->getStyle('A'.$filaInit.':L'.$filaInit)->applyFromArray($headerTablaStyle);
		$sheet->setTitle('Reporte de Kardex');
		 
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="reporte-kardex-'.time().'.xlsx"');
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
    	var_dump($exc->getMessage());
    }   
  } else {
  	var_dump("Faltan parámetros");
  }
    

