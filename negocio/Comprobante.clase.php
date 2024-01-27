<?php

require_once '../datos/Conexion.clase.php';
require_once '../datos/config_facturacion.php';
require_once 'util/Funciones.php';
//require_once 'PHPMailer/PHPMailerAutoload.php';
require_once 'PrintPDF.clase.php';


class Comprobante extends Conexion {
    private $cod_transaccion;

    public function getCodTransaccion()
    {
        return $this->cod_transaccion;
    }
    
    
    public function setCodTransaccion($cod_transaccion)
    {
        $this->cod_transaccion = $cod_transaccion;
        return $this;
    }

    public function obtenerComprobanteData()
    {
        try{

            $sql = "SELECT  
                    t.cod_tipo_comprobante as tipo_comprobante,
                    CONCAT(tcomp.abrev,t.serie) as serie,
                    LPAD(t.correlativo,6,'0') as correlativo,
                    CONCAT(c.nombres,' ',COALESCE(c.apellidos,'')) as nombre_cliente,
                    c.direccion as direccion_cliente,
                    t.fecha_transaccion as fecha_emision,
                    tdoc.abrev as tipo_documento,
                    tdoc.cod_tipo_documento as cod_tipo_documento,
                    c.numero_documento as numero_documento,
                    tm.abrev as moneda,
                    v.porcentaje_igv,
                    v.total_descuentos,
                    v.total_descuentos_comprobante,
                    v.descuentos_globales,
                    v.descuento_global_comprobante,
                    v.subtotal,
                    v.sumatoria_igv,
                    v.total_gravadas,
                    v.importe_total_venta,
                    estado_sunat,
                    cdr,
                    hash_cpe
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    INNER JOIN tipo_comprobante tcomp ON tcomp.cod_tipo_comprobante = t.cod_tipo_comprobante
                    INNER JOIN tipo_documento tdoc ON t.cod_tipo_documento = tdoc.cod_tipo_documento
                    INNER JOIN tipo_moneda tm ON tm.cod_tipo_moneda = v.cod_tipo_moneda
                    INNER JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    WHERE t.estado = 1 AND t.cod_transaccion = :0";
            $cabecera = $this->consultarFila($sql, [$this->getCodTransaccion()]);

            $sql = "SELECT 
                    item,
                    p.nombre as nombre_producto ,
                    valor_unitario,
                    valor_venta_bruto,
                    valor_venta,
                    precio_venta_unitario,
                    descuento_comprobante,
                    cantidad_item,
                    um.codigo_ece as unidad_medida,
                    descuentos
                    FROM venta_detalle vd
                    INNER JOIN venta v ON v.cod_venta = vd.cod_venta
                    INNER JOIN unidad_medida um ON um.cod_unidad_medida = vd.cod_unidad_medida
                    INNER JOIN producto p ON p.cod_producto = vd.cod_producto
                    WHERE v.cod_transaccion = :0";

            $detalle = $this->consultarFilas($sql, [$this->getCodTransaccion()]);

            return ["rpt"=>true, "data"=>["cabecera"=>$cabecera, "detalle"=>$detalle]];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDatos(){
        try{

            $USUARIO = $this->lastUsuario;
            /*obtener Sucursales: si rol == 1 => todas las sucursales, ordenadas por id, 
            sino, la sucursal segun su sucursal asignada.*/
            $sql = "SELECT cod_rol, cod_sucursal FROM personal WHERE cod_personal = :0 AND estado_mrcb";
            $objRolSucursal = $this->consultarFila($sql, [$USUARIO]);

            if ($objRolSucursal == false){
                return ["rpt"=>false,"msj"=>"Acceso no permitido."];
            }

            if ($objRolSucursal["cod_rol"] == 1){
                $sqlSucursales = " true ";
            } else {
                $sqlSucursales = " cod_sucursal = ".$objRolSucursal["cod_sucursal"];
            }

            $sql = "SELECT cod_sucursal, nombre FROM sucursal WHERE estado_mrcb AND cod_sucursal <> 0 AND ".$sqlSucursales." ORDER BY cod_sucursal";
            $sucursales = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>["sucursales"=>$sucursales]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function listar($fDesde, $fHasta, $todos, $codSucursal, $codEstado){
        try{
            $sqlWhere = "";
            $params =[];

            $sqlWhere = " estado = 1 ";

            if ($todos == "true"){
                $sqlWhere .= " AND true ";
            } else {
                $sqlWhere .= " AND (fecha_transaccion BETWEEN :0 AND :1)";
                array_push($params, $fDesde);
                array_push($params, $fHasta);
            }

            if ($codSucursal == "*"){
                $sqlWhere .= " AND true";
            } else {
                $j = count($params);
                $sqlWhere .= " AND suc.cod_sucursal = :$j";
                array_push($params, $codSucursal);
            }

            if ($codEstado == "T"){
                $sqlWhere .= " AND true";
            } else {
                $j = count($params);
                $sqlWhere .= " AND t.estado_sunat ".($codEstado == "A" ? "=" : "<>")." :$j";    
                array_push($params, "A");
            }

            $sql = "SELECT 
                    t.cod_transaccion as cod_transaccion,
                    t.cod_tipo_comprobante as tipo_comprobante,
                    COALESCE(CONCAT(tc.abrev,serie,'-',LPAD(correlativo,6,'0')),'NINGUNO') as comprobante,
                    UPPER(COALESCE(v.razon_social_nombre, CONCAT(c.nombres,' ',c.apellidos))) as cliente,
                    c.numero_documento,
                    DATE_FORMAT(fecha_transaccion,'%d-%m-%Y') as fecha_emision,
                    CAST((v.importe_total_venta) AS DECIMAL(10,2)) as total,
                    estado_generado,
                    estado_sunat,
                    c.correo,
                    (estado_generado = 1 AND estado_sunat = 'A') as estado_generado_aceptado,
                    cdr,
                    suc.nombre as sucursal,
                    DATE_FORMAT(fecha_envio_sunat,'%d-%m-%Y') as fecha_envio_sunat
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    LEFT JOIN sucursal suc ON suc.cod_sucursal = t.cod_sucursal
                    WHERE ".$sqlWhere." AND correlativo IS NOT NULL
                    ORDER BY t.fecha_transaccion DESC, cod_transaccion DESC";

            $registros = $this->consultarFilas($sql, $params);

           return array("rpt"=>true,"data"=>$registros);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


    public function generarEnviarSunat(){
        try{
            /*  
                1. Verifica si tiene comprobante
                 . Obtiene nombre comprobante.
                 . Actualizar Estado_suant = 'V'
                 . Verificar si hay una RPTA
                 . Elimina el xml si existe
                 . Consultar data comprobante
                 . Generar un XML           
                 . Actualizar Estado_generado = '1'
                 . Enviar a sunat
                 . ---Esperar---
                 . Obtener CDR, entrar archivo y consultar CDR
                 . Actualizar Registro Transac
                       CDR = OK => esado_sunat A
                       CDR = NOT OK =>estado_sunat R
                 . If  (error 0000)
                        CDR = No puedo conectarme a sunat
                        estado_suant = 'N'
                 . Eliminar el PDF
            
                 . Generar un PDF
            */

            /*VARS*/
            $RUC_EMISOR = F_RUC;
            $PASS_FIRMA = F_PASS_FIRMA;
            $CORRELATIVO_DIGITOS  = 6;
            $RUTA_SIS_FACT = "../sistema_facturacion";

            $RUTA_XML = $RUTA_SIS_FACT."/archivos_xml_sunat/cpe_xml/".(F_MODO_PROCESO == "3" ? "beta" : "produccion")."/".$RUC_EMISOR."/";
            $codTransaccion = $this->getCodTransaccion();

            $sql = "SELECT (COUNT(cod_transaccion) > 0) as c, serie, LPAD(correlativo,".$CORRELATIVO_DIGITOS.",'0') as correlativo,
                     cod_tipo_comprobante
                     FROM transaccion WHERE serie IS NOT NULL AND correlativo IS NOT NULL AND cod_transaccion = :0";
            $existeComprobante = $this->consultarFila($sql, [$codTransaccion]);


            if ($existeComprobante["c"] == 0){
                return ["rpt"=>false, "msj"=>"No existe comprobante."];
            }

            $TIPO_COMPROBANTE = $existeComprobante["cod_tipo_comprobante"];
            $SERIE = ($TIPO_COMPROBANTE == "01" ? "F" : "B").$existeComprobante["serie"];
            $CORRELATIVO = $existeComprobante["correlativo"];
            $NUMERO_COMPROBANTE = $SERIE.'-'.$CORRELATIVO;

            $this->beginTransaction();

            $campos_valores = ["estado_sunat"=>"V"];
            $campos_valores_where = ["cod_transaccion"=>$codTransaccion];

            $this->update("transaccion", $campos_valores, $campos_valores_where);

            //Verificar si hay una XML
            $nombre_xml = $RUC_EMISOR."-".$TIPO_COMPROBANTE."-".$NUMERO_COMPROBANTE.".XML";
            $archivo_xml = $RUTA_XML . $nombre_xml;
            $archivo_zip = $RUTA_XML . $RUC_EMISOR."-".$TIPO_COMPROBANTE."-".$NUMERO_COMPROBANTE.".ZIP";
            if (file_exists($archivo_xml)) {
                unlink($archivo_xml);
            }

            if (file_exists($archivo_zip)) {
                unlink($archivo_zip);
            }

            //Verificar si hay una RPTA
            $archivo_rpta = $RUTA_XML."R-".$RUC_EMISOR."-".$TIPO_COMPROBANTE."-".$NUMERO_COMPROBANTE.".XML";
            if (file_exists($archivo_rpta)) {
                unlink($archivo_rpta);
            }

            //Obtener data
            $sql = "SELECT 
                    t.cod_tipo_documento as cliente_tipodocumento, 
                    t.cod_tipo_comprobante as cod_tipo_documento, 
                    t.fecha_transaccion as fecha_comprobante,
                    t.fecha_transaccion as fecha_vto_comprobante,
                    total_gravadas,
                    '0.00' as total_exoneradas,
                    '0.00' as total_inafecta,
                    '0.00' as total_gratuitas,
                    '0.00' as total_exportacion,
                     total_valor_venta,
                     total_valor_venta_bruto,
                     subtotal,
                     total_gravadas,
                     porcentaje_igv,
                     sumatoria_igv as total_igv,
                     '0.00' as total_isc,
                     '0.00' as total_otro_imp,
                     importe_total_venta as total,
                     porcentaje_descuento_comprobante as porcentaje_descuento,
                     descuento_global_comprobante as descuento_global,
                     total_descuentos_comprobante as total_descuentos,
                     tm.abrev as cod_moneda,
                     cl.numero_documento as cliente_numerodocumento,
                     CONCAT(cl.nombres,' ',cl.apellidos) as cliente_nombre,
                     COALESCE(cl.direccion,'') as cliente_direccion,
                     correo,
                     'PE' as cliente_pais,
                     suc.nombre as cliente_ciudad
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    INNER JOIN cliente cl ON cl.cod_cliente = v.cod_cliente 
                    INNER JOIN tipo_moneda tm ON tm.cod_tipo_moneda = v.cod_tipo_moneda
                    INNER JOIN sucursal suc ON suc.cod_sucursal = t.cod_sucursal
                    LEFT JOIN descuento des ON des.cod_descuento = v.cod_descuento_global
                    WHERE t.cod_transaccion = :0 AND t.estado = 1";
            $cabeceraComprobante = $this->consultarFila($sql, $codTransaccion);

            $emisor = array(
                    "ruc"                       => F_RUC,
                    "tipo_doc"                  => "6",
                    "nom_comercial"             => F_NOMBRE_COMERCIAL,
                    "razon_social"              => F_RAZON_SOCIAL,
                    "codigo_ubigeo"             => F_CODIGO_UBIGEO,
                    "direccion"                 => F_DIRECCION,
                    "direccion_departamento"    => F_DIRECCION_DEPARTAMENTO,
                    "direccion_provincia"       => F_DIRECCION_PROVINCIA,
                    "direccion_distrito"        => F_DIRECCION_DISTRITO,
                    "direccion_codigopais"      => F_CODIGO_PAIS,
                    "usuariosol"                => F_USUARIO_SOL,
                    "clavesol"                  => F_CLAVE_SOL
             );

            $sql  = "SELECT 
                    vd.item as txtITEM,
                    um.codigo_ece as txtUNIDAD_MEDIDA_DET,
                    vd.cantidad_item as txtCANTIDAD_DET,
                    vd.precio_venta_unitario as txtPRECIO_DET,
                    vd.porcentaje_descuento_comprobante as txtPOR_DESCUENTO_DET,
                    vd.descuento_comprobante as txtDESCUENTO_DET,
                    v.tipo_operacion as txtPRECIO_TIPO_CODIGO,
                    vd.monto_igv as txtIGV,
                    '0' as txtISC,
                    vd.valor_venta_bruto as txtVALOR_VENTA_BRUTO,
                    vd.valor_venta as txtIMPORTE_DET,
                    vd.afectacion_igv as txtCOD_TIPO_OPERACION,
                    p.codigo as txtCODIGO_DET,
                    vd.descripcion_producto as txtDESCRIPCION_DET,
                    CAST(vd.valor_unitario as DECIMAL(10,2)) as txtPRECIO_SIN_IGV_DET
                    FROM venta_detalle vd
                    INNER JOIN venta v ON vd.cod_venta = v.cod_venta
                    INNER JOIN producto p ON vd.cod_producto = p.cod_producto
                    INNER JOIN unidad_medida um ON vd.cod_unidad_medida = um.cod_unidad_medida
                    LEFT JOIN descuento des ON des.cod_descuento = vd.cod_descuento
                    WHERE v.cod_transaccion = :0";

            $detalleComprobante = $this->consultarFilas($sql, $codTransaccion);

            $data = $cabeceraComprobante;

            $data["tipo_proceso"] = F_MODO_PROCESO;

            $data["nro_guia_remision"] = "";
            $data["cod_guia_remision"] = "";
            $data["nro_otr_comprobante"] = "";
            $data["serie_comprobante"] = $SERIE;
            $data["numero_comprobante"] = $CORRELATIVO;
            $data["total_letras"] = Funciones::numtoletras($cabeceraComprobante["total"]);

            $data["emisor"] = $emisor;
            $data["detalle"] = $detalleComprobante;

            require_once '../sistema_facturacion/inhouse_facturacion/boleta.factura.php';
            $rpta = generar_enviar($PASS_FIRMA, $data);
            $CORRECTO = false;

            if ($rpta["respuesta"] == "error"){
                $cdr  = $rpta["cod_sunat"].": ".$rpta["mensaje"];
                $estado_sunat = "R";
                $hash_cpe = NULL;
                $hash_cdr = NULL;
            } else {
                $cdr = $rpta["msj_sunat"];
                $hash_cpe = $rpta["hash_cpe"];
                $hash_cdr = $rpta["hash_cdr"];
                $estado_sunat = "A";
                $CORRECTO =  true;
            }

            if ($CORRECTO === false && strpos( $cdr, "El comprobante fue registrado previamente con otros datos") !== false){
                $campos_valores = ["estado_generado"=>"1", "estado_sunat"=>'A'];    
            }  else{
                $campos_valores = ["estado_generado"=>"1", "estado_sunat"=>$estado_sunat, "hash_cpe"=>$hash_cpe, "hash_cdr"=>$hash_cdr, "cdr"=>$cdr, "fecha_envio_sunat"=>date('Y-m-d')];    
            }
            
            $campos_valores_where = ["cod_transaccion"=>$codTransaccion];

            $this->update("transaccion", $campos_valores, $campos_valores_where);

        
            if ($CORRECTO){

                $rptaPDF = $this->generarPDF($RUTA_XML, $TIPO_COMPROBANTE, $NUMERO_COMPROBANTE, $hash_cpe);

                if ($rptaPDF["rpt"] == false){
                    return $rptaPDF;
                }

                if (F_ENVIAR_CORREOS == '1'){
                    $rptaCorre = $this->enviarCorreo($cabeceraComprobante["correo"], $TIPO_COMPROBANTE, $NUMERO_COMPROBANTE, $RUTA_XML, $hash_cpe);
                }
            }
            $this->commit();
            return $rpta;
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function generarPDF($RUTA_XML, $TIPO_COMPROBANTE, $NUMERO_COMPROBANTE, $hash_cpe){
        $archivo_pdf = $RUTA_XML.$TIPO_COMPROBANTE."-".$NUMERO_COMPROBANTE.".pdf";

        if (file_exists($archivo_pdf)) {
            unlink($archivo_pdf);
        }

        $dataComprobante = $this->obtenerComprobanteData();

        if ($dataComprobante["rpt"] == false){
            return ["rpt"=>false, "msj"=>$dataComprobante["msj"]];
        }

        $dataComprobante = $dataComprobante["data"];

        $objPrintpdf = new PrintPDF();

        $nombre_pdf = $TIPO_COMPROBANTE."-".$NUMERO_COMPROBANTE.".pdf";
        $archivo_pdf =  "../archivos_xml_sunat/cpe_xml/".(F_MODO_PROCESO == "3" ? "beta" : "produccion")."/".F_RUC."/" . $nombre_pdf;

        if ($hash_cpe == NULL){
            $hash_cpe = $dataComprobante["cabecera"]["hash_cpe"];
        }
        $objPrintpdf->getComprobantePDF($dataComprobante, $archivo_pdf, $hash_cpe);

        return ["rpt"=>true];
    }

    public function enviarCorreo($correoDestino, $TIPO_COMPROBANTE, $NUMERO_COMPROBANTE, $RUTA_XML, $hash_cpe){
        try{
            /*  
                1. Verifica si tiene comprobante
            */
            $from_name= utf8_decode(F_NOMBRE_COMERCIAL);
            $from_mail= F_CORREO;
            $pass_mail= F_PASS_MAIL;
            $mailto = $correoDestino;

            /*Aquí colocar la ruta del archivo en cuestión (pdf + xml)*/
            if ($RUTA_XML == NULL){
                $RUTA_XML = "../sistema_facturacion/archivos_xml_sunat/cpe_xml/".(F_MODO_PROCESO == "3" ? "beta" : "produccion")."/".F_RUC."/";
            } else {
                $RUTA_XML = "../".$RUTA_XML;
            }

            $comprobante = $TIPO_COMPROBANTE."-".$NUMERO_COMPROBANTE;
            $archivoZip = $RUTA_XML.$comprobante.".ZIP";
            $nombreArchivoXML = F_RUC.'-'.$comprobante.".XML";
            $archivoXML = $RUTA_XML.$nombreArchivoXML;
            $nombreArchivoPDF = $comprobante.".pdf";
            $archivoPDF = $RUTA_XML.$nombreArchivoPDF;

            //$file = C_SUNAT_REPO.$filename;
            if (!file_exists($archivoZip)){
                if (!file_exists($archivoXML)){
                    return ["rpt"=>false, "msj"=>"No existe comprobante XML generado."];
                }

                if (!file_exists($archivoPDF)){
                   $rpta =  $this->generarPDF($RUTA_XML, $TIPO_COMPROBANTE, $NUMERO_COMPROBANTE, $hash_cpe);
                   if ($rpta["rpt"] == false){
                    return $rpta;
                   }
                }

                 $zip = new ZipArchive();
                 
                 if ($zip->open($archivoZip, ZIPARCHIVE::CREATE) === true) {
                     $zip->addFile($archivoXML, $nombreArchivoXML); //ORIGEN, DESTINO
                     $zip->addFile($archivoPDF, $nombreArchivoPDF); //ORIGEN, DESTINO
                     $zip->close();
                 }

                /*
                Si no hay zip
                   Si no hay archivos xml, no se encontró xml RETURN
                   Si no hay pdf->generarlo
                   Crear zip
                Si hay Zip
                    Do nothing

                Adjuntar ZIP
                Enviar ZIP
                */
            }

            $subject  = utf8_decode("Envío de Comprobante Electrónico: ".$comprobante);

            $mail = new PHPMailer;

            //Enable SMTP debugging
                // 0 = off (for production use)
                // 1 = client messages
                // 2 = client and server messages
            $mail->SMTPDebug  = 0;
            $mail->isSMTP();                              // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';               // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                       // Enable SMTP authentication
            $mail->Username = $from_mail;                 // SMTP username
            $mail->Password = $pass_mail;                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            $mail->setFrom($from_mail, $from_name);
            //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
            $mail->addAddress($mailto);               // Name is optional
        //  $mail->addReplyTo('info@example.com', 'Information');
        //  $mail->addCC('cc@example.com');
        //  $mail->addBCC('bcc@example.com');

            $mail->addAttachment($archivoZip);         // Add attachments
            $mail->isHTML(true);
        //  $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        //  $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = $subject;
            $mail->Body    = 'Envío de comprobante electrónico.<br><br>¡Gracias por su preferencia!';
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            set_time_limit(0);
            
            if(!$mail->send()) {
                return array("rpt"=>true,"msj"=>"Problemas al enviar el correo. Error: ".$mail->ErrorInfo);       
            } else {
                return array("rpt"=>true,"msj"=>"Correo enviado.");
            }

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
}