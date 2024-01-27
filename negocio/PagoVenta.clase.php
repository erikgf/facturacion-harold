<?php

require_once '../datos/Conexion.clase.php';

class PagoVenta extends Conexion {

    private $cod_venta_pago;
    private $fecha_pago;
    private $pagado;
    private $observaciones;
    private $cod_venta;
    private $estado;

    public function getCodVenta()
    {
        return $this->cod_venta;
    }
    
    
    public function setCodVenta($cod_venta)
    {
        $this->cod_venta = $cod_venta;
        return $this;
    }

    public function getCodVentaPago()
    {
        return $this->cod_venta_pago;
    }
    
    
    public function setCodVentaPago($cod_venta_pago)
    {
        $this->cod_venta_pago = $cod_venta_pago;
        return $this;
    }

    public function getFechaPago()
    {
        return $this->fecha_pago;
    }
    
    
    public function setFechaPago($fecha_pago)
    {
        $this->fecha_pago = $fecha_pago;
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

    public function getPagado ()
    {
        return $this->pagado ;
    }
    
    
    public function setPagado ($pagado )
    {
        $this->pagado  = $pagado ;
        return $this;
    }

    public function getEstado()
    {
        return $this->estado;
    }
    
    
    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }

    public function obtenerData(){
        try{

            $USUARIO = $this->lastUsuario;
            /*obtener Sucursales: si rol == 1 => todas las sucursales, ordenadas por id, 
            sino, la sucursal segun su sucursal asignada.*/
            $sql = "SELECT cod_rol, cod_sucursal FROM personal WHERE cod_personal = :0 AND estado_mrcb";
            $objRolSucursal = $this->consultarFila($sql, [$USUARIO]);

            if ($objRolSucursal == false){
                return ["rpt"=>false,"msj"=>"Acceso no permitido."];
            }

            $sql = "SELECT 
                        vc.cod_venta,
                        COALESCE(CONCAT(tc.abrev,t.serie,'-',t.correlativo), CONCAT('Venta Cod:',vc.cod_venta)) as serie_comprobante,
                        SUM(vc.monto * vc.tipo_deuda) as deuda
                        FROM `venta_credito` vc
                        INNER JOIN venta v ON v.cod_venta =  vc.cod_venta
                        LEFT JOIN transaccion t ON t.cod_transaccion = v.cod_transaccion
                        LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                        WHERE vc.estado_mrcb
                        GROUP BY vc.cod_venta
                        HAVING deuda < 0";
            $comprobantes_deuda = $this->consultarFilas($sql);

            return ["data"=>["comprobantes_deuda"=>$comprobantes_deuda]];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerVentaPagar(){
        try{
            $sql = "SELECT 
                        v.cod_venta,
                        COALESCE(CONCAT(tc.abrev,t.serie,'-',t.correlativo), 'NINGUNO') as numero_comprobante,
                        t.fecha_transaccion as fecha_registro,
                        v.razon_social_nombre as cliente,
                        v.importe_total_venta,
                        (SELECT SUM(vc.monto * vc.tipo_deuda) FROM venta_credito vc WHERE vc.cod_venta = v.cod_venta GROUP BY vc.cod_venta) * -1 as deuda
                        FROM  venta v
                        LEFT JOIN transaccion t ON t.cod_transaccion = v.cod_transaccion                        
                        LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                        WHERE v.cod_venta = :0";
            $data = $this->consultarFila($sql, [$this->getCodVenta()]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function grabar($fechaDesde, $fechaHasta){
        try{

            $this->beginTransaction();
            $this->setCodVentaPago($this->consultarValor("SELECT COALESCE(MAX(cod_venta_credito)+1, 1) FROM venta_credito"));


            $sql = "SELECT SUM(vc.monto * vc.tipo_deuda)  * -1 as monto_deuda
                        FROM venta_credito vc 
                        WHERE vc.cod_venta = :0 AND vc.estado_mrcb
                        GROUP BY vc.cod_venta";

            $monto_deuda = $this->consultarFila($sql, [$this->getCodVenta()]);

            if ($monto_deuda == false){
                throw new Exception("La venta adeudada no existe.", 1);
            }

            $monto_deuda = $monto_deuda["monto_deuda"];    

            if ((float) $this->getPagado() > (float) $monto_deuda ){
                throw new Exception("El monto pagado ha pasado el monto de la deuda.");
            }

            $pendiente = $monto_deuda - $this->getPagado();

            $campos_valores  = [
                "cod_venta_credito"=>$this->getCodVentaPago(),
                "cod_venta"=>$this->getCodVenta(),
                "fecha_registro"=>$this->getFechaPago(),
                "monto"=>$this->getPagado(),
                "pendiente"=>$pendiente,
                "tipo_deuda"=>"1",
                "observaciones"=>$this->getObservaciones() === "" ? NULL : $this->getObservaciones()
            ];

            $o = $this->insert("venta_credito", $campos_valores);

            $this->commit();

            $lista = $this->obtenerListaVentas($fechaDesde, $fechaHasta)["data"];

            $sql = "SELECT 
                        vc.cod_venta,
                        COALESCE(CONCAT(tc.abrev,t.serie,'-',t.correlativo), CONCAT('Venta Cod:',vc.cod_venta)) as serie_comprobante,
                        SUM(vc.monto * vc.tipo_deuda) as deuda
                        FROM `venta_credito` vc
                        INNER JOIN venta v ON v.cod_venta =  vc.cod_venta
                        LEFT JOIN transaccion t ON t.cod_transaccion = v.cod_transaccion
                        LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                        WHERE vc.estado_mrcb
                        GROUP BY vc.cod_venta
                        HAVING deuda < 0";
            $comprobantes_deuda = $this->consultarFilas($sql);
            /*Retorno: 
                Lista ventas ,basado en las fechas de min - max
                venta => ID,
            */
            return array("rpt"=>true,"msj"=>"Pago Venta registrado correctamente.", 
                "data"=>["cod_venta"=>$this->getCodVenta(), "comprobantes_deuda"=>$comprobantes_deuda, "cod_pago_venta"=> $this->getCodVentaPago(),
                            "lista"=>$lista]);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerListaVentas($fechaDesde, $fechaHasta){
        try{

            $USUARIO = $this->lastUsuario;
            
            $sql = "SELECT 
                    vc.cod_venta,
                    cod_venta_credito as cod_venta_pago,
                    COALESCE(CONCAT(tc.abrev,t.serie,'-',t.correlativo), CONCAT('Venta Cod:',vc.cod_venta)) as comprobante,
                    suc.nombre as sucursal,
                    v.razon_social_nombre as cliente,
                    vc.fecha_registro as fecha_pago,
                    pendiente,
                    monto as pagado,
                    v.importe_total_venta as adeudado
                    FROM venta_credito vc
                    INNER JOIN venta v ON vc.cod_venta = v.cod_venta
                    INNER JOIN transaccion t ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    INNER JOIN sucursal suc ON suc.cod_sucursal = t.cod_sucursal
                    WHERE estado = 1  AND (vc.fecha_registro BETWEEN :0 AND :1) AND vc.estado_mrcb AND tipo_deuda = 1
                    ORDER BY vc.fecha_registro DESC";

            $params = [$fechaDesde, $fechaHasta];
            $ventas = $this->consultarFilas($sql, $params);

            return array("rpt"=>true,"data"=>$ventas);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerVenta(){
        try{

            $USUARIO = $this->lastUsuario;
            
            $sql = "SELECT 
                    LPAD(t.cod_transaccion,6,'0') as cod_transaccion,
                    t.cod_transaccion as x_cod_transaccion,
                    t.cod_tipo_comprobante as tipo_comprobante,
                    COALESCE(CONCAT(tc.abrev,serie,'-',LPAD(correlativo,6,'0')),'NINGUNO') as comprobante,
                    v.numero_voucher as voucher,
                    COALESCE(CONCAT(c.nombres,' ',c.apellidos),v.razon_social_nombre) as cliente,
                    COALESCE(v.numero_documento,'-') as numero_documento,
                    (CASE v.tipo_pago WHEN 'E' THEN 'EFECTIVO' ELSE 'TARJETA' END) as tipo_pago,
                    monto_efectivo, monto_tarjeta, monto_credito,
                    (CASE v.tipo_tarjeta WHEN 'C' THEN 'CREDITO' WHEN 'D' THEN 'DEBITO' ELSE NULL END) as tipo_tarjeta,
                    DATE_FORMAT(fecha_transaccion,'%d-%m-%Y') as fecha_venta,
                    CAST((ROUND(v.total_gravadas + v.sumatoria_igv - (total_descuentos - descuentos_globales),1)) AS DECIMAL(10,2))  as subtotal,
                    COALESCE(v.descuentos_globales, 0.00) as descuentos_globales,
                    d.codigo_generado as codigo_descuento,
                    v.importe_total_venta as importe_total_venta,
                    t.observaciones
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    LEFT JOIN descuento d ON v.cod_descuento_global = d.cod_descuento
                    WHERE estado = 1 AND t.cod_transaccion = :0";

            $cabecera = $this->consultarFila($sql, $this->getCodTransaccion());

            $sql = "SELECT 
                    item,
                    p.codigo as codigo_producto,
                    p.nombre as producto,
                    precio_venta_unitario as precio_unitario,
                    cantidad_item as cantidad,
                    vd.fecha_vencimiento,
                    vd.lote,
                    -- CAST(ROUND((monto_igv + valor_venta),1) AS DECIMAL(10,2)) as subtotal,
                    COALESCE(descuentos,0.00) as descuento,
                    d.codigo_generado,
                    CAST(ROUND((monto_igv + valor_venta - descuentos),1) AS DECIMAL(10,2)) as subtotal,
                    monto_igv,
                    valor_venta
                    FROM venta_detalle vd
                    LEFT JOIN producto p ON p.cod_producto = vd.cod_producto
                    LEFT JOIN venta v ON vd.cod_venta = v.cod_venta
                    LEFT JOIN descuento d ON d.cod_descuento = vd.cod_descuento
                    WHERE v.cod_transaccion = :0
                    ORDER BY vd.item";

            $detalle = $this->consultarFilas($sql, $this->getCodTransaccion());        

            return array("rpt"=>true,"data"=>["cabecera"=>$cabecera, "detalle"=>$detalle]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDatosComisionista(){
        try{

            $sql = "SELECT 
                    vd.cod_venta, vd.item,
                    vd.cod_producto,
                    p.nombre as producto, cantidad_item as cantidad, 
                    precio_venta_unitario as precio, 
                    COALESCE(vcp.tipo_comision, cp.tipo_comision) as tipo_comision, 
                    COALESCE(vcp.valor_comision, cp.valor_comision) as valor_comision, 
                    CAST((precio_venta_unitario * cantidad_item) AS DECIMAL(10,2)) as total_detalle,
                    IF (vcp.monto_comision IS NULL, false, true) as comisionar
                    FROM venta_detalle vd
                    INNER JOIN producto p ON p.cod_producto = vd.cod_producto
                    LEFT JOIN comisionista_producto cp ON cp.cod_producto = vd.cod_producto
                    LEFT JOIN venta_comision_producto vcp ON vcp.cod_venta = vd.cod_venta AND vd.item = vcp.item
                    WHERE vd.cod_venta = :0 AND cp.cod_comisionista = :1
                    ORDER BY vd.item";

            $detalle = $this->consultarFilas($sql, [$this->getCodVenta(), $this->getCodComisionista()]);


             $sql = "SELECT 
                    COALESCE(SUM(monto_comision),'0.00') 
                    FROM  venta_comision_producto vcp 
                    INNER JOIN venta v ON v.cod_venta = vcp.cod_venta
                    WHERE vcp.cod_venta = :0 AND v.cod_comisionista = :1
                    ORDER BY vcp.item";

            $total = $this->consultarValor($sql, [$this->getCodVenta(), $this->getCodComisionista()]);

            return array("rpt"=>true,"data"=>["detalle"=>$detalle, "total"=>$total]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function registrarComisionista($JSONProductos){
        try{
            $this->beginTransaction();
            $arProductos = json_decode($JSONProductos);

            /*actualizar venta*/
            $sql = "UPDATE venta SET cod_comisionista = ".$this->getCodComisionista(). " WHERE cod_venta =".$this->getCodVenta();
            $o = $this->consultaRaw($sql);

            $sql = "DELETE FROM venta_comision_producto WHERE cod_venta = ".$this->getCodVenta();
            $o = $this->consultaRaw($sql);

            foreach ($arProductos as $key => $value) {
                if ($value->tipo_comision == "P"){
                    $sql = "SELECT (precio_venta_unitario * cantidad_item) FROM venta_detalle WHERE cod_venta = :0 AND item = :1";
                    $precioBase = $this->consultarValor($sql, [$this->getCodVenta(), $value->item]);    
                    $montoComision = $precioBase * ($value->valor_comision / 100);
                } else {
                    $montoComision = $value->valor_comision;
                }

                $campos_valores = [
                    "cod_venta"=>$this->getCodVenta(),
                    "item"=>$value->item,
                    "cod_producto"=>$value->cod_producto,
                    "tipo_comision"=>$value->tipo_comision,
                    "valor_comision"=>$value->valor_comision,
                    "monto_comision"=>$montoComision
                ];

                $this->insert("venta_comision_producto", $campos_valores);
            }

            $this->commit();
            
            return array("rpt"=>true,"msj"=>"Comisiones registradas correctamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function eliminar($fechaDesde, $fechaHasta){
        try{
            $this->beginTransaction();

              /*Transaccion existe. */
            $sql = "SELECT vc.cod_venta_credito
                    FROM venta_credito vc
                    WHERE estado = 1 AND cod_venta_credito = :0";

            $objVenta = $this->consultarFila($sql, [$this->getCodVentaPago()]);

            if ($objVenta == false){
                return ["rpt"=>false, "msj"=>"Pago de Venta no existe."];
            }

            $this->update("venta_credito", ["estado_mrcb"=>0], ["cod_venta_credito"=>$this->getCodVentaPago()]);
            $data = $this->obtenerListaVentas($fechaDesde, $fechaHasta)["data"];
            $this->commit();

            return array("rpt"=>true,"data"=>$data,"msj"=>"Pago Venta eliminada correctamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
    /*A diferencia de eliminra la venta, aquí en destruir, se desaprece TODO rastro de la venta es decir no se da de baja o 
    se actualiza estados, se procede a deletear directamente.*/
    public function destruirVenta($MODO_EDITAR  = false){
        try{
            $this->beginTransaction();

              /*Transaccion existe. */
            $sql = "SELECT vc.cod_venta_credito
                    FROM venta_credito vc
                    WHERE estado = 1 AND cod_venta_credito = :0";

            $objVenta = $this->consultarFila($sql, [$this->getCodVentaPago()]);

            if ($objVenta == false){
                return ["rpt"=>false, "msj"=>"Pago de Venta no existe."];
            }

            //Eliminar detalle (despues de haber borrado lo necesario)
            $o = $this->delete("venta_credito", ["cod_venta_credito"=>$this->getCodVenta()]);
            
            $this->commit();    
            
            return array("rpt"=>true,"msj"=>"Pago Venta destruida correctamente.");
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
                    COALESCE(SUM(monto_credito),0.00) as monto_credito,
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
                    monto_credito,
                    DATE_FORMAT(fecha_transaccion,'%d-%m-%Y') as fecha_venta,
                    CAST((ROUND(v.total_gravadas + v.sumatoria_igv - (total_descuentos - descuentos_globales),1)) AS DECIMAL(10,2))  as subtotal,
                    v.total_descuentos,
                    CAST((ROUND(v.total_gravadas - (total_descuentos - descuentos_globales),2)) AS DECIMAL(10,2))  as total_gravadas,
                    v.sumatoria_igv,
                    CAST((v.importe_total_venta) AS DECIMAL(10,2)) as importe_total,
                    suc.nombre as sucursal,
                    COALESCE(co.nombres,'-') as comisionista,
                    COALESCE((SELECT SUM(monto_comision) FROM venta_comision_producto WHERE cod_venta = v.cod_venta), 0.00) as total_comisiones
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

    public function reporteGeneralComprobantes($fDesde, $fHasta, $todos, $codSucursal){
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

            $sql = "SELECT 
                    t.cod_transaccion,
                    LPAD(t.cod_transaccion,6,'0') as codigo,      
                    CONCAT(tc.abrev,serie) as serie,
                    LPAD(correlativo,6,'0') as correlativo,                                  
                    c.numero_documento,
                    COALESCE(v.razon_social_nombre, CONCAT(c.nombres,' ',c.apellidos)) as cliente,
                    DATE_FORMAT(fecha_transaccion,'%d-%m-%Y') as fecha_venta,
                    DATE_FORMAT(fecha_envio_sunat,'%d-%m-%Y') as fecha_envio,
                    CAST((ROUND(v.total_gravadas - (total_descuentos - descuentos_globales),2)) AS DECIMAL(10,2))  as total_gravadas,
                    v.sumatoria_igv,
                    CAST((v.importe_total_venta) AS DECIMAL(10,2)) as importe_total,
                    suc.nombre as sucursal,
                    t.cdr,
                    t.hash_cdr
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    LEFT JOIN sucursal suc ON suc.cod_sucursal = t.cod_sucursal
                    WHERE ".$sqlWhere." AND t.serie IS NOT NULL
                    ORDER BY t.fecha_transaccion DESC";

            $detalle = $this->consultarFilas($sql, $params);
           

            return array("rpt"=>true,"data"=>$detalle);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerVentaEditar(){
        try{

            $USUARIO = $this->lastUsuario;
            
            $sql = "SELECT 
                    t.cod_transaccion as cod_transaccion,
                    LPAD(t.cod_transaccion,6,'0') as x_cod_transaccion,
                    cod_tipo_comprobante,
                    serie, correlativo,
                    numero_voucher,
                    cod_cliente,
                    v.numero_voucher as voucher,
                    v.tipo_pago,
                    monto_efectivo, monto_tarjeta, monto_credito,
                    v.tipo_tarjeta,
                    fecha_transaccion,
                    v.cod_descuento_global,
                    d.codigo_generado as codigo_descuento_global,
                    IF(d.tipo_descuento = 'P', CONCAT(d.monto_descuento,'%'), CONCAT('S/ ',d.monto_descuento)) as rotulo_descuento,
                    d.tipo_descuento,
                    v.total_descuentos,
                    v.importe_total_venta,
                    t.observaciones
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN descuento d ON v.cod_descuento_global = d.cod_descuento
                    WHERE estado = 1 AND t.cod_transaccion = :0";

            $cabecera = $this->consultarFila($sql, $this->getCodTransaccion());

            $sql = "SELECT 
                    item,
                    vd.cod_producto,
                    p.nombre as nombre_producto,
                    (SELECT pi.img_url FROM producto_img pi WHERE pi.cod_producto = p.cod_producto AND pi.numero_imagen = p.numero_imagen_principal) as img_url,
                    vd.precio_venta_unitario as precio_unitario,
                    vd.cantidad_item as cantidad,
                    vd.cod_descuento,
                    COALESCE(d.codigo_generado,'') as codigo_descuento,
                    d.monto_descuento,
                    d.tipo_descuento,
                    IF(d.tipo_descuento = 'P', CONCAT(d.monto_descuento,'%'), CONCAT('S/ ',d.monto_descuento)) as rotulo_descuento,
                    CAST(ROUND((monto_igv + valor_venta - descuentos),1) AS DECIMAL(10,2)) as subtotal,
                    vd.fecha_vencimiento,
                    vd.lote,
                    m.nombre as marca
                    FROM venta_detalle vd
                    INNER JOIN producto p ON vd.cod_producto = p.cod_producto
                    LEFT JOIN marca m ON m.cod_marca = p.cod_marca
                    LEFT JOIN venta v ON vd.cod_venta = v.cod_venta
                    LEFT JOIN descuento d ON d.cod_descuento = vd.cod_descuento
                    WHERE v.cod_transaccion = :0
                    ORDER BY vd.item";

            $detalle = $this->consultarFilas($sql, $this->getCodTransaccion());

            $sql  = "SELECT d.cod_descuento, d.codigo_generado, d.tipo_descuento, d.monto_descuento,
                                    IF(d.tipo_descuento = 'P', CONCAT(d.monto_descuento,'%'), CONCAT('S/ ',d.monto_descuento)) as rotulo_descuento
                                    FROM venta v
                                    INNER JOIN venta_detalle vd ON vd.cod_venta = v.cod_venta
                                    INNER JOIN descuento d ON d.cod_descuento = vd.cod_descuento
                                    WHERE v.cod_transaccion = :0
                                    UNION
                                    SELECT d.cod_descuento, d.codigo_generado, d.tipo_descuento, d.monto_descuento,
                                    IF(d.tipo_descuento = 'P', CONCAT(d.monto_descuento,'%'), CONCAT('S/ ',d.monto_descuento)) as rotulo_descuento
                                    FROM venta v
                                    INNER JOIN descuento d ON d.cod_descuento = v.cod_descuento_global
                                    WHERE v.cod_transaccion = :0";

            $descuentos_usados = $this->consultarFilas($sql, [$this->getCodTransaccion()]);

            return array("rpt"=>true,"data"=>["cabecera"=>$cabecera, "detalle"=>$detalle, "descuentos_usados"=>$descuentos_usados]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerNumeroComprobante(){
        try{

            $USUARIO = $this->lastUsuario;
            
            $sql = "SELECT valor_variable  FROM variable_constante WHERE nombre_variable = 'correlativo_boletas'";
            $nuevo_correlativo = $this->consultarValor($sql);

            return array("rpt"=>true,"nuevo_correlativo"=>$nuevo_correlativo);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDataReporteMasVendido(){
        try{

            $sql = "SELECT cod_sucursal, nombre FROM sucursal WHERE estado_mrcb = 1 AND cod_sucursal <> 0";
            $sucursales = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>["sucursales"=>$sucursales]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function reporteMasVendido($fDesde, $fHasta, $todos, $codSucursal){
        try{
            $sqlWhere = "";
            $params =[];

            $sqlWhere = " t.estado = 1 ";

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

            $sql = "SELECT 
                    p.codigo as codigo_producto,
                    p.nombre as producto,
                    SUM(vd.precio_venta_unitario * vd.cantidad_item) as monto_vendido,
                    SUM(vd.cantidad_item) as unidades_vendidas,
                    SUM(vd.costo_producto) as monto_gastado,
                    SUM(vd.precio_venta_unitario * vd.cantidad_item) - SUM(vd.costo_producto) as utilidad,
                    GROUP_CONCAT(DISTINCT suc.nombre,',')  as sucursal
                    FROM venta_detalle vd 
                    INNER JOIN venta v ON vd.cod_venta = v.cod_venta
                    INNER JOIN transaccion t ON t.cod_transaccion = v.cod_transaccion
                    INNER JOIN producto p ON p.cod_producto = vd.cod_producto     
                    LEFT JOIN sucursal suc ON suc.cod_sucursal = t.cod_sucursal
                    WHERE ".$sqlWhere."
                    GROUP BY p.codigo
                    ORDER BY p.codigo DESC";      

            $detalle = $this->consultarFilas($sql, $params);      

            return array("rpt"=>true,"data"=>$detalle);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

   private function ___procesardetalle(){
        try{
          

            $sql = "SELECT 
                    vd.cod_venta,
                    vd.item,
                    vd.cod_producto,
                    vd.cadena_stock_producto
                    FROM venta_detalle vd 
                    INNER JOIN venta v ON vd.cod_venta = v.cod_venta
                    INNER JOIN transaccion t ON t.cod_transaccion = v.cod_transaccion
                    WHERE t.estado = 1
                    ORDER BY vd.cod_producto DESC";            

            $data = $this->consultarFilas($sql);

            $this->beginTransaction();
            foreach ($data as $key => $value) {
                $arreglo = json_decode($value["cadena_stock_producto"]);
                $monto  = 0;
                foreach ($arreglo as $_key => $_value) {
                    $monto += ($_value->cantidad  * $_value->precio_entrada);
                }

                $sql = "UPDATE venta_detalle SET costo_producto = ".$monto." WHERE cod_venta = ".$value["cod_venta"]." AND item = ".$value["item"]; 
                $this->consultaRaw($sql);
            }
            $this->commit();

            return array("rpt"=>true);
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

            $objDestruir = $this->destruirVenta($MODO_EDITAR);
            if ($objDestruir["rpt"] == false){
                return $objDestruir;
            }

            $objGrabar = $this->grabar($fechaDesde, $fechaHasta, $MODO_EDITAR);

            return $objGrabar;  
            /*
            return array("rpt"=>true,"msj"=>"Venta registrada correctamente.", 
                            "data"=>["cod_venta"=>$this->getCodVenta(), 
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