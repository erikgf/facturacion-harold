<?php

require_once 'Transaccion.clase.php';

class Cotizacion extends Transaccion {
    private $cod_cotizacion;
    private $cod_cliente;
    private $numero_documento;
    private $razon_social_nombre;
    private $apellidos;
    private $direccion_cliente;
    private $celular_cliente;
    private $correo_envio;
    private $cod_tipo_moneda;
    private $dias_credito;
    private $dias_validez;
    private $dias_entrega;
    private $costo_delivery;
    private $fecha_cotizacion;
    private $importe_total;
    private $observaciones;

    private $detalle_cotizacion;

    public function getCelularCliente()
    {
        return $this->celular_cliente;
    }
    
    
    public function setCelularCliente($celular_cliente)
    {
        $this->celular_cliente = $celular_cliente;
        return $this;
    }

    public function getFechaCotizacion()
    {
        return $this->fecha_cotizacion;
    }
    
    
    public function setFechaCotizacion($fecha_cotizacion)
    {
        $this->fecha_cotizacion = $fecha_cotizacion;
        return $this;
    }

    public function getImporteTotal()
    {
        return $this->importe_total;
    }
    
    
    public function setImporteTotal($importe_total)
    {
        $this->importe_total = $importe_total;
        return $this;
    }

    public function getDiasCredito()
    {
        return $this->dias_credito;
    }
    
    
    public function setDiasCredito($dias_credito)
    {
        $this->dias_credito = $dias_credito;
        return $this;
    }

    public function getDiasValidez()
    {
        return $this->dias_validez;
    }
    
    
    public function setDiasValidez($dias_validez)
    {
        $this->dias_validez = $dias_validez;
        return $this;
    }

    public function getDiasEntrega()
    {
        return $this->dias_entrega;
    }
    
    
    public function setDiasEntrega($dias_entrega)
    {
        $this->dias_entrega = $dias_entrega;
        return $this;
    }

    public function getCostoDelivery()
    {
        return $this->costo_delivery;
    }
    
    
    public function setCostoDelivery($costo_delivery)
    {
        $this->costo_delivery = $costo_delivery;
        return $this;
    }

    public function getCodCotizacion()
    {
        return $this->cod_cotizacion;
    }
    
    
    public function setCodCotizacion($cod_cotizacion)
    {
        $this->cod_cotizacion = $cod_cotizacion;
        return $this;
    }

    public function getCodCliente()
    {
        return $this->cod_cliente;
    }
    
    
    public function setCodCliente($cod_cliente)
    {
        $this->cod_cliente = $cod_cliente;
        return $this;
    }

    public function getNumeroDocumento()
    {
        return $this->numero_documento;
    }
    
    
    public function setNumeroDocumento($numero_documento)
    {
        $this->numero_documento = $numero_documento;
        return $this;
    }

    public function getRazonSocialNombre()
    {
        return $this->razon_social_nombre;
    }
    
    
    public function setRazonSocialNombre($razon_social_nombre)
    {
        $this->razon_social_nombre = $razon_social_nombre;
        return $this;
    }

    public function getApellidos()
    {
        return $this->apellidos;
    }
    
    
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
        return $this;
    }

    public function getDireccionCliente()
    {
        return $this->direccion_cliente;
    }
    
    
    public function setDireccionCliente($direccion_cliente)
    {
        $this->direccion_cliente = $direccion_cliente;
        return $this;
    }

    public function getCorreoEnvio()
    {
        return $this->correo_envio;
    }
    
    
    public function setCorreoEnvio($correo_envio)
    {
        $this->correo_envio = $correo_envio;
        return $this;
    }

    public function getDetalleCotizacion()
    {
        return $this->detalle_cotizacion;
    }
    
    
    public function setDetalleCotizacion($detalle_cotizacion)
    {
        $this->detalle_cotizacion = $detalle_cotizacion;
        return $this;
    }

    public function getObservaciones()
    {
        return $this->observaciones;
    }
    
    
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
        return $this;
    }

    public function getCodTipoMoneda()
    {
        return $this->cod_tipo_moneda;
    }
    
    
    public function setCodTipoMoneda($cod_tipo_moneda)
    {
        $this->cod_tipo_moneda = $cod_tipo_moneda;
        return $this;
    }

    public function obtenerData(){
        try{

            $USUARIO = $this->lastUsuario;

            $sql = "SELECT c.cod_cliente, nombres, apellidos, td.cod_tipo_documento, 
                        td.abrev as tipo_documento, 
                        IF(c.numero_documento = '', NULL, c.numero_documento) as numero_documento,
                        c.direccion, correo, COALESCE(numero_contacto, c.celular) as celular
                        FROM cliente c
                        INNER JOIN tipo_documento td ON td.cod_tipo_documento = c.tipo_documento
                        WHERE c.estado_mrcb";
            $clientes = $this->consultarFilas($sql);

            $productos = $this->obtenerDataProductos();
            if ($productos["rpt"]){
                $productos = $productos["data"];
            }

            $sql = "SELECT cod_tipo_categoria as codigo, nombre
                    FROM tipo_categoria
                    WHERE estado_mrcb";
            $tipo_categorias = $this->consultarFilas($sql);

            $sql = "SELECT cod_categoria_producto as codigo, nombre, cod_tipo_categoria
                    FROM categoria_producto
                    WHERE estado_mrcb";
            $categoria_productos = $this->consultarFilas($sql);


            $correlativo = $this->consultarValor("SELECT valor_variable FROM variable_constante WHERE nombre_variable = 'correlativo_cotizacion'");

            return array("rpt"=>true,"data"=>["clientes"=>$clientes,"productos"=>$productos, 
                                                "tipo_categorias"=>$tipo_categorias, "categoria_productos"=>$categoria_productos,
                                                    "correlativo_previo"=>$correlativo]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDataProductos(){
        try{
            //No importa STOCK
            $sql = "SELECT 
                        p.cod_producto,
                        CAST(p.precio_unitario AS DECIMAL(10,2)) as precio_unitario,
                        p.nombre as nombre_producto,
                        m.nombre as marca,
                        cp.cod_tipo_categoria as cod_tipo,
                        p.cod_categoria_producto  as cod_categoria,
                        sp.fecha_vencimiento,
                        sp.lote
                        FROM producto p
                        INNER JOIN sucursal_producto sp ON p.cod_producto = sp.cod_producto AND sp.stock > 0
                        INNER JOIN categoria_producto cp ON cp.cod_categoria_producto = p.cod_categoria_producto AND cp.estado_mrcb
                        INNER JOIN marca m ON m.cod_marca = p.cod_marca
                        WHERE p.estado_mrcb
                        GROUP BY p.nombre";

            $productos = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>$productos);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function grabar($fechaDesde, $fechaHasta, $MODO_EDITAR = false){
        try{
            
            $this->beginTransaction();
                
            if (!$MODO_EDITAR){
                $this->setCodTransaccion($this->consultarValor("SELECT COALESCE(MAX(cod_transaccion)+1, 1) FROM transaccion"));
            }

            $this->setCodTipoComprobante("CO");

            $this->setDetalleCotizacion(json_decode($this->getDetalleCotizacion()));

            $this->setCorrelativo((int) $this->getCorrelativo());

            $sql  = "SELECT COUNT(cod_transaccion) > 0 FROM transaccion t
                        WHERE estado = 1 AND cod_transaccion = :0 AND serie = :1 AND correlativo = :2";
            $existeCorrelativo = $this->consultarValor($sql, [$this->getCodTransaccion(), $this->getSerie(), $this->getCorrelativo()]);

            if ($existeCorrelativo == "true"){
                return ["rpt"=>false, "msj"=>"Ya existe una cotización con este correlativo."];
            }

            if($this->getCodTipoComprobante() == "6" && $this->getCodCliente() == ""){
                return ["rpt"=>false, "msj"=>"En caso de registrar factura, se debe registrar el cliente antes de hacer una cotización."];
            }

            if ($this->getFechaCotizacion() == ""){
                return ["rpt"=>false, "msj"=>"Se debe ingresar una fecha de cotización"];
            }

            $esRolAdmin = ($_SESSION["usuario"]["cod_rol"] == "1");

            $this->setCodCotizacion($this->consultarValor("SELECT COALESCE(MAX(cod_cotizacion)+1, 1) FROM cotizacion"));

            $clienteRegistrado = false;

            /*REGISTRAR CLIENTE SI ES QUE SE NECESITA*/
            if (strlen($this->getRazonSocialNombre()) > 0 && ($this->getCodCliente() == 0) ){
                $clienteRegistrado = true;  
                $this->setCodCliente($this->consultarValor("SELECT COALESCE(MAX(cod_cliente)+1, 1) FROM cliente"));
                $objVerificar = $this->verificarRepetidoCliente();

                if ($objVerificar["rpt"] == false){
                    return $objVerificar;
                }

                $campos_valores = [
                    "cod_cliente"=>$this->getCodCliente(),
                    "tipo_documento"=>$this->getCodTipoDocumento(),
                    "numero_documento"=>($this->getNumeroDocumento() == "" || $this->getCodTipoDocumento() == "0" )? NULL : $this->getNumeroDocumento(),
                    "nombres"=>$this->getRazonSocialNombre(),
                    "apellidos"=>$this->getApellidos(),
                    "direccion"=>$this->getDireccionCliente(),
                    "celular"=>$this->getCelularCliente() == "" ? NULL : $this->getCelularCliente(),
                    "correo"=>$this->getCorreoEnvio()
                ];

                $this->insert("cliente", $campos_valores);
            }

/*
            $sql = "SELECT valor_variable FROM variable_constante WHERE nombre_variable = 'correlativo_cotizacion'";
            $correlativo = $this->consultarValor($sql);
            */
            /*Registrando cabecera TRANSA*/
            $campos_valores  = [
                "cod_transaccion"=>$this->getCodTransaccion(),
                "cod_tipo_documento"=>$this->getCodTipoDocumento(),
                "cod_tipo_comprobante"=>$this->getCodTipoComprobante(),
                "serie"=>$this->getSerie(),
                "correlativo"=>$this->getCorrelativo(),
                "cod_sucursal"=>NULL,
                "fecha_transaccion"=>$this->getFechaCotizacion(),
                "observaciones"=>$this->getObservaciones() === "" ? NULL : $this->getObservaciones()
            ];

            $o = $this->insert("transaccion", $campos_valores);

            /*Repartiendo Detalle, verificando integridad, calcuclando sub total y descuentos*/
            $subTotal = 0;
            $sumatoriaIGV= 0;

            $VARIABLES = $this->consultarFilas("SELECT valor_variable FROM variable_constante WHERE nombre_variable IN ('IGV','INCLUIR_IGV') ORDER BY nombre_variable");
            $IGV = $VARIABLES[0]["valor_variable"];
            $INCLUIR_IGV = $VARIABLES[1]["valor_variable"] == "1";

            $this->setCodTipoMoneda(1);
            $importeTotal = 0;

            foreach ($this->getDetalleCotizacion() as $i => $fila) {
                $sql = "SELECT nombre, precio_unitario as precio_venta_unitario, cod_unidad_medida, cod_marca FROM producto WHERE cod_producto = :0 AND estado_mrcb";
                $objProducto = $this->consultarFila($sql, [$fila->codProducto]);

                if ($objProducto == false){
                    return ["rpt"=>false, "msj"=>"Producto no existe en el sistema."];
                }

                $precioCotizacionUnitario = $objProducto["precio_venta_unitario"];

                if ($INCLUIR_IGV){
                    $valorUnitario = round($precioCotizacionUnitario / (1 + $IGV), 4);
                } else {
                    $valorUnitario = round($precioCotizacionUnitario,4);
                    $precioCotizacionUnitario = $valorUnitario * (1 + $IGV);                    
                }

                $importeSubTotal = $precioCotizacionUnitario * $fila->cantidad;
                $valorCotizacion = round($valorUnitario * $fila->cantidad,2);
                $montoIGV = ($precioCotizacionUnitario - $valorUnitario);
                //$subTotalFila = $fila->cantidad * $objProducto["precio_venta_unitario"];
                $costo_producto  = 0;

                /*INSERTAMOS DETALLE*/            
                $campos_valores = [
                    "cod_cotizacion"=>$this->getCodCotizacion(),
                    "item"=>($i + 1),
                    "cod_producto"=>$fila->codProducto,
                    "valor_unitario"=>$valorUnitario,
                    "cantidad_item"=>$fila->cantidad,
                    "cod_marca"=>$objProducto["cod_marca"],
                    "descripcion_producto"=>$objProducto["nombre"],
                    "monto_igv"=>$montoIGV,
                    "precio_unitario"=>$precioCotizacionUnitario,
                    "cod_unidad_medida"=>$objProducto["cod_unidad_medida"],
                    "fecha_vencimiento"=>$fila->fechaVencimiento,
                    "lote"=>$fila->lote
                ];

                $o = $this->insert("cotizacion_detalle", $campos_valores);             
                $subTotal += $valorCotizacion;/*Suma valorventa*/
                $importeTotal += $importeSubTotal;
            }

            $importeTotal = number_format($importeTotal);
            $sumatoriaIGV = $importeTotal - $subTotal;

            $cta_bcp = "305-8971 166-0-44";
            $cta_bcp_cci = "002-30500897116604412";
            /*Registrando cabecera COTIZACION*/
            $campos_valores  = [
                "cod_cotizacion"=>$this->getCodCotizacion(),
                "cod_transaccion"=>$this->getCodTransaccion(),
                "cod_cliente"=>$this->getCodCliente(),
                "numero_documento"=>$this->getNumeroDocumento() == "" ? NULL : $this->getNumeroDocumento(),
                "razon_social_nombre"=>($this->getCodCliente() == 0 ? NULL : $this->getRazonSocialNombre()." ".$this->getApellidos()),
                "direccion_cliente"=>$this->getDireccionCliente(),
                "correo_envio"=>$this->getCorreoEnvio(),
                "fecha_cotizacion"=>$this->getFechaCotizacion(),
                "cod_tipo_moneda"=>$this->getCodTipoMoneda(),
                "subtotal"=>$subTotal,
                "igv"=>$sumatoriaIGV,
                "total"=>$importeTotal,
                "condicion_dias_credito"=>$this->getDiasCredito(),
                "condicion_dias_validez"=>$this->getDiasValidez(),
                "condicion_dias_entrega"=>$this->getDiasEntrega(),
                "condicion_delivery"=>$this->getCostoDelivery(),
                "cta_bcp"=>$cta_bcp,
                "cta_bcp_cci"=>$cta_bcp_cci,
                "correlativo_cotizacion"=>$this->getCorrelativo()
            ];

            $o = $this->insert("cotizacion", $campos_valores);

            if (!$MODO_EDITAR){
                /*Si se está grabando (no editar) autmentar el correaltivo.*/
                $nuevo_correlativo = $this->getCorrelativo() + 1;
                $sql = "UPDATE variable_constante SET valor_variable = ".$nuevo_correlativo." WHERE nombre_variable = 'correlativo_cotizacion'";
                $this->consultaRaw($sql);                 
            }

            $this->commit();

            $lista_cotizaciones = $this->obtenerListaCotizaciones($fechaDesde, $fechaHasta)["data"];

            $clientes = []; 
            if ($clienteRegistrado == true){
                $sql = "SELECT c.cod_cliente, nombres, apellidos, td.cod_tipo_documento, 
                        td.abrev as tipo_documento, IF(c.numero_documento = '', NULL, c.numero_documento) as numero_documento,
                        c.direccion, correo, c.celular
                        FROM cliente c
                        INNER JOIN tipo_documento td ON td.cod_tipo_documento = c.tipo_documento
                        WHERE c.estado_mrcb";
                $clientes = $this->consultarFilas($sql);
            }
             
            /*Retorno: 
                Lista ventas ,basado en las fechas de min - max
                venta => ID,
            */
            return array("rpt"=>true,"msj"=>"Cotización ".($MODO_EDITAR ? "editada" : "registrada")." correctamente.", 
                "data"=>["cod_cotizacion"=>$this->getCodCotizacion(), 
                            "cod_transaccion"=> $this->getCodTransaccion(),
                            "lista_cotizaciones"=>$lista_cotizaciones, 
                            "clientes"=>$clientes, 
                            "nuevo_correlativo"=>$nuevo_correlativo]);
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


    public function obtenerListaCotizaciones($fechaDesde, $fechaHasta){
        try{

            $USUARIO = $this->lastUsuario;
            
            $sql = "SELECT 
                    LPAD(t.cod_transaccion,6,'0') as x_cod_transaccion,
                    t.cod_transaccion,
                    v.cod_cotizacion,
                    COALESCE(CONCAT(tc.abrev,serie,'-',LPAD(correlativo,6,'0')),'NINGUNO') as comprobante,
                    c.numero_documento,
                    COALESCE(v.razon_social_nombre, CONCAT(c.nombres,' ',c.apellidos)) as cliente,
                    DATE_FORMAT(fecha_transaccion,'%d-%m-%Y') as fecha_cotizacion,
                    DATE_FORMAT(DATE_ADD(fecha_transaccion, INTERVAL condicion_dias_validez DAY),'%d-%m-%Y') as fecha_vencimiento,
                    subtotal,
                    igv as monto_igv,
                    v.total as importe_total
                    FROM transaccion t
                    INNER JOIN cotizacion v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    WHERE estado = 1 AND (fecha_transaccion BETWEEN :0 AND :1)
                    ORDER BY t.fecha_transaccion DESC";

            $params = [$fechaDesde, $fechaHasta];
            $ventas = $this->consultarFilas($sql, $params);

            return array("rpt"=>true,"data"=>$ventas);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerCotizacion(){
        try{

            $USUARIO = $this->lastUsuario;
            
            $sql = "SELECT 
                    LPAD(t.cod_transaccion,6,'0') as cod_transaccion,
                    t.cod_transaccion as x_cod_transaccion,
                    t.cod_tipo_comprobante as tipo_comprobante,
                    COALESCE(CONCAT(tc.abrev,serie,'-',LPAD(correlativo,6,'0')),'NINGUNO') as comprobante,
                    COALESCE(CONCAT(c.nombres,' ',c.apellidos),v.razon_social_nombre) as cliente,
                    COALESCE(v.numero_documento,'Ninguno') as numero_documento,
                    DATE_FORMAT(fecha_transaccion,'%d-%m-%Y') as fecha_cotizacion,
                    IF(v.condicion_delivery = 0,'Gratis',v.condicion_delivery) as condicion_delivery,
                    COALESCE(correo_envio,'-') as correo_envio,
                    v.condicion_dias_validez,
                    v.condicion_dias_credito,
                    v.condicion_dias_entrega,
                    v.igv as monto_igv,
                    subtotal,
                    v.total as importe_total,
                    t.observaciones
                    FROM transaccion t
                    INNER JOIN cotizacion v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    WHERE estado = 1 AND t.cod_transaccion = :0";

            $cabecera = $this->consultarFila($sql, $this->getCodTransaccion());

            $sql = "SELECT 
                    item,
                    p.codigo as codigo_producto,
                    p.nombre as producto,
                    m.nombre as marca,
                    vd.precio_unitario as precio_unitario,
                    cantidad_item as cantidad,
                    -- CAST(ROUND((monto_igv + valor_venta),1) AS DECIMAL(10,2)) as subtotal,
                    CAST(ROUND(((vd.precio_unitario * cantidad_item)),1) AS DECIMAL(10,2)) as subtotal,
                    monto_igv,
                    (vd.precio_unitario * cantidad_item) as valor_venta
                    FROM cotizacion_detalle vd
                    LEFT JOIN producto p ON p.cod_producto = vd.cod_producto
                    LEFT JOIN cotizacion v ON vd.cod_cotizacion = v.cod_cotizacion
                    LEFT JOIN marca m ON m.cod_marca = p.cod_marca
                    WHERE v.cod_transaccion = :0
                    ORDER BY vd.item";

            $detalle = $this->consultarFilas($sql, $this->getCodTransaccion());        

            return array("rpt"=>true,"data"=>["cabecera"=>$cabecera, "detalle"=>$detalle]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function eliminarCotizacion($fechaDesde, $fechaHasta){
        try{
            $this->beginTransaction();

              /*Transaccion existe. */
            $sql = "SELECT v.cod_cotizacion
                    FROM transaccion t 
                    INNER JOIN cotizacion v ON v.cod_transaccion = t.cod_transaccion
                    WHERE t.cod_transaccion = :0 AND t.estado = 1";

            $objCotizacion = $this->consultarFila($sql, [$this->getCodTransaccion()]);

            if ($objCotizacion == false){
                return ["rpt"=>false, "msj"=>"Cotizacion no existe."];
            }

            $this->setCodCotizacion($objCotizacion["cod_cotizacion"]);  

            $this->update("transaccion", ["estado"=>0], ["cod_transaccion"=>$this->getCodTransaccion()]);

            $data = $this->obtenerListaCotizaciones($fechaDesde, $fechaHasta)["data"];
            /*
                Estado transaccion = 0
                Cotizacions muertas / comisionista NULL
                Detalle muerto

                Recueperar el stock basado en los detalles
                Cancelar movimientos asociados a esta transaccion
                Eliminar comisiones asociadas a esta venta. (NO HAY NECESIDAD)
            */
            $this->commit();

            
            return array("rpt"=>true,"data"=>$data,"msj"=>"Cotizacion eliminada correctamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
    /*A diferencia de eliminra la venta, aquí en destruir, se desaprece TODO rastro de la venta es decir no se da de baja o 
    se actualiza estados, se procede a deletear directamente.*/
    public function destruirCotizacion($MODO_EDITAR  = false){
        try{
            if (!$MODO_EDITAR){
                $this->beginTransaction();    
            }

            /*Transaccion existe. */
            $sql = "SELECT v.cod_cotizacion
                    FROM transaccion t 
                    INNER JOIN cotizacion v ON v.cod_transaccion = t.cod_transaccion
                    WHERE t.cod_transaccion = :0 AND t.estado = 1";

            $objCotizacion = $this->consultarFila($sql, [$this->getCodTransaccion()]);

            if ($objCotizacion == false){
                return ["rpt"=>false, "msj"=>"Cotizacion no existe."];
            }

            $this->setCodCotizacion($objCotizacion["cod_cotizacion"]);  
            //Eliminar la venta
            $this->delete("transaccion",["cod_transaccion"=>$this->getCodTransaccion()]);
            //Eliminar detalle (despues de haber borrado lo necesario)
            $o = $this->delete("cotizacion_detalle", ["cod_cotizacion"=>$this->getCodCotizacion()]);
            //Eliminar venta
            $this->delete("cotizacion",["cod_cotizacion"=>$this->getCodCotizacion()]);
            
            if (!$MODO_EDITAR){
                $this->commit();    
            }
            
            return array("rpt"=>true,"msj"=>"Cotizacion destruida correctamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    private function verificarRepetidoCliente(){
        $tbl = "cliente";
        if ($this->getNumeroDocumento() != NULL){
            $sql = "SELECT COUNT(numero_documento) > 0 FROM ".$tbl." WHERE numero_documento = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getNumeroDocumento()]);

            if ($repetido){
                return ["rpt"=>false, "msj"=>"Número documento de CLIENTE ya existente."];
            }
        }

        if ($this->getCorreoEnvio() != NULL && $this->getCorreoEnvio() != ""){
            $sql = "SELECT COUNT(correo) > 0 FROM ".$tbl." WHERE correo = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getCorreoEnvio()]);

            if ($repetido){
                return ["rpt"=>false, "msj"=>"Correo de CLIENTE ya existente."];
            }
        }

        if ($this->getCelularCliente() != NULL && $this->getCelularCliente() !=""){
            $sql = "SELECT COUNT(celular) > 0 FROM ".$tbl." WHERE celular = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getCelularCliente()]);

            if ($repetido){
                return ["rpt"=>false, "msj"=>"Celular de CLIENTE ya existente."];
            }
        }
        return ["rpt"=>true, "msj"=>""];
    }
    public function obtenerDataReporte(){
        try{

            $sql = "SELECT cod_sucursal, nombre FROM sucursal WHERE estado_mrcb = 1 AND cod_sucursal <> 0";
            $sucursales = $this->consultarFilas($sql);

            $sql = "SELECT cod_cliente, CONCAT(nombres,' ',apellidos) as nombres FROM cliente 
                    WHERE estado_mrcb = 1 AND cod_cliente <> 0";
            $clientes = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>["sucursales"=>$sucursales,"clientes"=>$clientes]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function reporteGeneral($fDesde, $fHasta, $todos, $codSucursal, $codCliente){
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

            if ($codSucursal == ""){
                $sqlWhere .= " AND true";
            } else {
                $j = count($params);
                $sqlWhere .= " AND suc.cod_sucursal = :$j";
                array_push($params, $codSucursal);
            }

            if ($codCliente == ""){
                $sqlWhere .= " AND true";
            } else {
                $j = count($params);
                $sqlWhere .= " AND v.cod_cliente = :$j";
                array_push($params, $codCliente);
            }

           $sql = "SELECT 
                    COALESCE(SUM(monto_efectivo),0.00) as monto_efectivo,
                    COALESCE(SUM(monto_tarjeta),0.00) as monto_tarjeta,
                    COALESCE(SUM(v.importe_total_venta + v.total_descuentos),0.00) as subtotal,
                    COALESCE(SUM(v.total_descuentos),0.00) as total_descuentos,
                    COALESCE(SUM(CAST((v.importe_total_venta) AS DECIMAL(10,2))), 0.00) as total
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    LEFT JOIN sucursal suc ON suc.cod_sucursal = t.cod_sucursal
                    WHERE ".$sqlWhere;

            $cabecera = $this->consultarFila($sql, $params);

            $sql = "SELECT 
                    t.cod_transaccion,
                    LPAD(t.cod_transaccion,6,'0') as codigo,                                        
                    COALESCE(CONCAT(tc.abrev,serie,'-',LPAD(correlativo,6,'0')),'NINGUNO') as comprobante,
                    v.numero_voucher as voucher,
                    c.numero_documento,
                    COALESCE(v.razon_social_nombre, CONCAT(c.nombres,' ',c.apellidos)) as cliente,
                    monto_efectivo,
                    monto_tarjeta,
                    DATE_FORMAT(fecha_transaccion,'%d-%m-%Y') as fecha_venta,
                    CAST((ROUND(v.total_gravadas + v.sumatoria_igv - (total_descuentos - descuentos_globales),1)) AS DECIMAL(10,2))  as subtotal,
                    v.total_descuentos,
                    CAST((ROUND(v.total_gravadas - (total_descuentos - descuentos_globales),2)) AS DECIMAL(10,2))  as total_gravadas,
                    v.sumatoria_igv,
                    CAST((v.importe_total_venta) AS DECIMAL(10,2)) as importe_total,
                    suc.nombre as sucursal,
                    COALESCE(co.nombres,'-') as comisionista,
                    COALESCE((SELECT SUM(monto_comision) FROM venta_comision_producto WHERE cod_cotizacion = v.cod_cotizacion), 0.00) as total_comisiones
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    LEFT JOIN sucursal suc ON suc.cod_sucursal = t.cod_sucursal
                    LEFT JOIN comisionista co ON co.cod_comisionista = v.cod_comisionista
                    WHERE ".$sqlWhere."
                    ORDER BY t.fecha_transaccion DESC";

            $detalle = $this->consultarFilas($sql, $params);

           

            return array("rpt"=>true,"data"=>$detalle, "cabecera"=>$cabecera);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerCotizacionEditar(){
        try{

            $USUARIO = $this->lastUsuario;
            
            $sql = "SELECT 
                    t.cod_transaccion as cod_transaccion,
                    LPAD(t.cod_transaccion,6,'0') as x_cod_transaccion,
                    cod_tipo_comprobante,
                    serie, correlativo,
                    cod_cliente,
                    fecha_transaccion,
                    v.total as importe_total,
                    t.observaciones,
                    condicion_delivery,
                    condicion_dias_entrega,
                    condicion_dias_validez,
                    condicion_dias_credito
                    FROM transaccion t
                    INNER JOIN cotizacion v ON t.cod_transaccion = v.cod_transaccion
                    WHERE estado = 1 AND t.cod_transaccion = :0";

            $cabecera = $this->consultarFila($sql, $this->getCodTransaccion());

            $sql = "SELECT 
                    item,
                    vd.cod_producto,
                    p.nombre as nombre_producto,
                    m.nombre as marca,
                    vd.precio_unitario,
                    vd.cantidad_item as cantidad,
                    subtotal, igv as monto_igv,
                    fecha_vencimiento,
                    lote
                    FROM cotizacion_detalle vd
                    INNER JOIN producto p ON vd.cod_producto = p.cod_producto
                    LEFT JOIN cotizacion v ON vd.cod_cotizacion = v.cod_cotizacion
                    LEFT JOIN marca m ON m.cod_marca = p.cod_marca
                    WHERE v.cod_transaccion = :0
                    ORDER BY vd.item";

            $detalle = $this->consultarFilas($sql, $this->getCodTransaccion());

            return array("rpt"=>true,"data"=>["cabecera"=>$cabecera, "detalle"=>$detalle]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerNumeroComprobante(){
        try{

            $USUARIO = $this->lastUsuario;
            
            $sql = "SELECT valor_variable  FROM variable_constante WHERE nombre_variable = 'correlativo_cotizacion'";
            $nuevo_correlativo = $this->consultarValor($sql);

            return array("rpt"=>true,"nuevo_correlativo"=>$nuevo_correlativo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function editar($fechaDesde, $fechaHasta){
        try{
            /*  
                1.Salvar el código de la transacción
                2. Destruir venta (FULL DESTRUCCION EN BASE AL COD_TRANSACCION)
                3. grabar venta con modo EDITAR
                4.- se obtiene el restultado de grabar
            */
            $this->beginTransaction();

            $esRolAdmin = ($_SESSION["usuario"]["cod_rol"] == "1");

            if (!$esRolAdmin){
                return ["rpt"=>false, "msj"=>"Las ediciones solo se pueden realizar por los usuarios Administrador."];
            }

            $MODO_EDITAR = true;

            $objDestruir = $this->destruirCotizacion($MODO_EDITAR);
            if ($objDestruir["rpt"] == false){
                return $objDestruir;
            }

            $objGrabar = $this->grabar($fechaDesde, $fechaHasta, $MODO_EDITAR);

            return $objGrabar;  
            /*
            return array("rpt"=>true,"msj"=>"Cotizacion registrada correctamente.", 
                            "data"=>["cod_cotizacion"=>$this->getCodCotizacion(), 
                            "lista_ventas"=>$lista_ventas, 
                            "tipo_pago"=>$this->getTipoPago(),
                            "clientes"=>$clientes, 
                            "nuevo_correlativo"=>$nuevo_correlativo]);
            */
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


}