<?php

require_once 'Transaccion.clase.php';

class Compra extends Transaccion {
    private $cod_compra;
    private $cod_proveedor;
    private $tipo_pago;
    private $tipo_tarjeta;
    private $numero_comprobante;
    private $importe_total_compra;
    private $total_gravadas;
    private $guias_remision;
    private $observaciones;

    private $detalle_compra;

    public function getCodCompra()
    {
        return $this->cod_compra;
    }
    
    
    public function setCodCompra($cod_compra)
    {
        $this->cod_compra = $cod_compra;
        return $this;
    }

    public function getCodProveedor()
    {
        return $this->cod_proveedor;
    }
    
    
    public function setCodProveedor($cod_proveedor)
    {
        $this->cod_proveedor = $cod_proveedor;
        return $this;
    }

    public function getTipoPago()
    {
        return $this->tipo_pago;
    }
    
    
    public function setTipoPago($tipo_pago)
    {
        $this->tipo_pago = $tipo_pago;
        return $this;
    }

    public function getTipoTarjeta()
    {
        return $this->tipo_tarjeta;
    }
    
    
    public function setTipoTarjeta($tipo_tarjeta)
    {
        $this->tipo_tarjeta = $tipo_tarjeta;
        return $this;
    }

    public function getNumeroComprobante()
    {
        return $this->numero_comprobante;
    }
    
    
    public function setNumeroComprobante($numero_comprobante)
    {
        $this->numero_comprobante = $numero_comprobante;
        return $this;
    }

    public function getImporteTotalCompra()
    {
        return $this->importe_total_compra;
    }
    
    
    public function setImporteTotalCompra($importe_total_compra)
    {
        $this->importe_total_compra = $importe_total_compra;
        return $this;
    }

    public function getDetalleCompra()
    {
        return $this->detalle_compra;
    }
    
    
    public function setDetalleCompra($detalle_compra)
    {
        $this->detalle_compra = $detalle_compra;
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

    public function getGuiasRemision()
    {
        return $this->guias_remision;
    }
    
    
    public function setGuiasRemision($guias_remision)
    {
        $this->guias_remision = $guias_remision;
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

            if ($objRolSucursal["cod_rol"] == 1){
                $sqlSucursales = " true ";
            } else {
                $sqlSucursales = " cod_sucursal = ".$objRolSucursal["cod_sucursal"];
            }

            $sql = "SELECT p.cod_proveedor, p.razon_social, nombre_contacto, td.cod_tipo_documento, 
                        td.abrev as tipo_documento, IF(p.numero_documento = '', NULL, p.numero_documento) as numero_documento,
                        p.direccion, correo, p.celular_contacto
                        FROM proveedor p
                        INNER JOIN tipo_documento td ON td.cod_tipo_documento = p.tipo_documento
                        WHERE p.estado_mrcb";
            $proveedores = $this->consultarFilas($sql);

            $sql = "SELECT cod_sucursal, nombre FROM sucursal WHERE estado_mrcb AND ".$sqlSucursales." ORDER BY cod_sucursal";
            $sucursales = $this->consultarFilas($sql);

            $this->setCodSucursal($sucursales[0]["cod_sucursal"]);

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

            return array("rpt"=>true,"data"=>[  "proveedores"=>$proveedores,"productos"=>$productos, 
                                                "tipo_categorias"=>$tipo_categorias, "categoria_productos"=>$categoria_productos,
                                                "sucursales"=>$sucursales]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDataProductos(){
        try{

            $sql = "SELECT 
                        p.cod_producto,
                        COALESCE(SUM(sp.stock),0) as stock,
                        CAST(p.precio_unitario AS DECIMAL(10,2)) as precio_unitario,
                        p.nombre as nombre_producto,
                        m.nombre as marca,
                        cp.cod_tipo_categoria as cod_tipo,
                        p.cod_categoria_producto  as cod_categoria,
                        COALESCE( 
                            (SELECT pi.img_url FROM producto_img pi WHERE pi.cod_producto = p.cod_producto AND pi.numero_imagen = p.numero_imagen_principal),'default_producto.jpg') as img_url
                        FROM producto p
                        LEFT JOIN sucursal_producto sp ON sp.cod_producto = p.cod_producto AND p.estado_mrcb
                        INNER JOIN categoria_producto cp ON cp.cod_categoria_producto = p.cod_categoria_producto AND cp.estado_mrcb
                        INNER JOIN marca m ON m.cod_marca = p.cod_marca
                        GROUP BY p.cod_producto";
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
            //checka
            $this->setDetalleCompra(json_decode($this->getDetalleCompra()));

            if($this->getCodTipoComprobante() == ""){
                $this->setNumeroComprobante(null);
                $this->setCodTipoComprobante(null);
            }

            if($this->getCodTipoDocumento() == "6" && $this->getCodProveedor() == ""){
                return ["rpt"=>false, "msj"=>"En caso de registrar factura, se debe registrar el proveedor antes de hacer una compra."];
            }
    
    
    /*
            if ($this->getFechaTransaccion() == "" || $this->getFechaTransaccion() < date('Y-m-d')){
                return ["rpt"=>false,"msj"=>"La fecha de la compra deber SER MAYOR O IGUAL que la fecha actual."];
            }
            */

            if ($this->getTipoPago() == "E"){
                $this->setTipoTarjeta(null);
            }

            $this->setCodCompra($this->consultarValor("SELECT COALESCE(MAX(cod_compra)+1, 1) FROM compra"));

            /*Registrando cabecera TRANSA*/
            $campos_valores  = [
                "cod_transaccion"=>$this->getCodTransaccion(),
                "cod_tipo_documento"=>$this->getCodTipoDocumento(),
                "cod_tipo_comprobante"=>$this->getCodTipoComprobante(),
                "serie"=>NULL,
                "correlativo"=>NULL,
                "cod_sucursal"=>$this->getCodSucursal(),
                "fecha_transaccion"=>$this->getFechaTransaccion(),
                "guias_remision"=>$this->getGuiasRemision() === "" ? NULL : $this->getGuiasRemision(),
                "observaciones"=>$this->getObservaciones() === "" ? NULL : $this->getObservaciones()
            ];

            $o = $this->insert("transaccion", $campos_valores);

            /*Repartiendo Detalle, verificando integridad, calcuclando sub total y descuentos*/
            $importeTotalCompra = 0;

            $codHistorial = $this->consultarValor("SELECT COALESCE(MAX(cod_historial)+1, 1) FROM sucursal_producto_historial");

            foreach ($this->getDetalleCompra() as $i => $fila) {
                /*verificar si existe sucursal producto, sino crearlo.*/  
                $sql = "SELECT nombre FROM producto WHERE cod_producto = :0 AND estado_mrcb";
                $objProducto = $this->consultarFila($sql, [$fila->codProducto]);

                if ($fila->cantidad <= 0){
                    $this->rollBack();
                    return ["rpt"=>false, "msj"=>"Producto de fila ".($i+1). " no tiene stock válido (0)."];
                }

                if ($objProducto == false){
                    return ["rpt"=>false, "msj"=>"Producto de la fila ".($i+1)." no existe en el sistema."];
                }

                $sql = "SELECT SUM(stock) FROM sucursal_producto 
                            WHERE cod_producto = :0 AND cod_sucursal = :1 AND precio_entrada = :2
                                AND fecha_vencimiento = :3 AND lote = :4";
                $stockActual = $this->consultarValor($sql, [$fila->codProducto, $this->getCodSucursal(), $fila->precioCompra, $fila->fechaVencimiento, $fila->lote]);
                
                $fila->fechaVencimiento = $fila->fechaVencimiento == "" ? "0000-00-00" : $fila->fechaVencimiento;

                /*ACTUALIZAMOS SUCURSAL*/
                if ($stockActual == null){
                    /*No existe sucursal_producto, deberíamos ingresar*/
                    $campos_valores = [
                        "cod_sucursal"=>$this->getCodSucursal(),
                        "cod_producto"=>$fila->codProducto,
                        "precio_entrada"=>$fila->precioCompra,
                        "stock"=>$fila->cantidad,
                        "fecha_vencimiento"=>$fila->fechaVencimiento,
                        "lote"=>$fila->lote
                    ];
                    $this->insert("sucursal_producto", $campos_valores);
                } else {
                    /*Existe, actualicemos stock*/
                    $sql = "UPDATE sucursal_producto 
                                SET stock = stock + ".$fila->cantidad." 
                                WHERE cod_producto = ".$fila->codProducto." 
                                    AND cod_sucursal = ".$this->getCodSucursal()." 
                                    AND precio_entrada = ".$fila->precioCompra." 
                                    AND fecha_vencimiento = '".$fila->fechaVencimiento."' 
                                    AND lote = '".$fila->lote."'";
                    $this->consultaRaw($sql);
                }

                /*Generar el movimiento de ENTRADA*/
             
                /*INSERTAMOS MOVIMEINTO */
                $campos_valores = [
                    "cod_historial"=>$codHistorial,
                    "cod_producto"=> $fila->codProducto,
                    "cod_sucursal"=> $this->getCodSucursal(),
                    "precio_salida"=> $fila->precioCompra,
                    "cod_transaccion"=> $this->getCodTransaccion(),
                    "cantidad"=> $fila->cantidad,
                    "fecha_movimiento"=>$this->getFechaTransaccion(),
                    "tipo_movimiento"=>"E",
                    "fecha_vencimiento"=>$fila->fechaVencimiento,
                    "lote"=>$fila->lote
                ];

                $o = $this->insert("sucursal_producto_historial", $campos_valores);
                $codHistorial++;
                //$subTotalFila = $fila->cantidad * $objProducto["precio_compra_unitario"];
                /*INSERTAMOS DETALLE*/
                $campos_valores = [
                    "cod_compra"=>$this->getCodCompra(),
                    "item"=>($i + 1),
                    "cod_producto"=>$fila->codProducto,
                    "cantidad"=>$fila->cantidad,
                    "precio_unitario"=>$fila->precioCompra,
                    "fecha_vencimiento"=>$fila->fechaVencimiento,
                    "lote"=>$fila->lote
                ];

                $o = $this->insert("compra_detalle", $campos_valores);
                $importeTotalCompra += ($fila->cantidad * $fila->precioCompra);/*Suma valorcompra*/
            }

            /*Registrando cabecera COMPRA*/
            $campos_valores  = [
                "cod_compra"=>$this->getCodCompra(),
                "cod_transaccion"=>$this->getCodTransaccion(),
                "numero_comprobante"=>$this->getNumeroComprobante(),
                "cod_proveedor"=>$this->getCodProveedor(),
                "tipo_pago"=>$this->getTipoPago(),
                "tipo_tarjeta"=>$this->getTipoTarjeta(),
                "importe_total_compra"=>$importeTotalCompra
            ];

            $o = $this->insert("compra", $campos_valores);
            $this->commit();
            $lista_compras = $this->obtenerListaCompras($fechaDesde, $fechaHasta)["data"];
            /*Retorno: 
                Lista compras ,basado en las fechas de min - max
                compra => ID,
            */
            return array("rpt"=>true,"msj"=>"Compra registrada correctamente.", "data"=>["cod_compra"=>$this->getCodCompra(), "lista_compras"=>$lista_compras]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerListaCompras($fechaDesde, $fechaHasta){
        try{

            $USUARIO = $this->lastUsuario;
            
            if ($this->getCodSucursal() == null){
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

                $sql = "SELECT cod_sucursal, nombre FROM sucursal WHERE estado_mrcb AND ".$sqlSucursales." ORDER BY cod_sucursal";
                $sucursales = $this->consultarFilas($sql);
                $this->setCodSucursal($sucursales[0]["cod_sucursal"]);
            }

            $sql = "SELECT 
                    LPAD(t.cod_transaccion,6,'0') as x_cod_transaccion,
                    t.cod_transaccion,
                    c.numero_comprobante,
                    p.razon_social as proveedor,
                    (CASE c.tipo_pago WHEN 'E' THEN 'EFECTIVO' ELSE 'TARJETA' END) as tipo_pago,
                    (CASE c.tipo_tarjeta WHEN 'C' THEN 'CREDITO' WHEN 'D' THEN 'DEBITO' ELSE NULL END) as tipo_tarjeta,
                    DATE_FORMAT(t.fecha_transaccion,'%d-%m-%Y') as fecha_compra,
                    c.importe_total_compra
                    FROM transaccion t
                    INNER JOIN compra c ON t.cod_transaccion = c.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN proveedor p ON p.cod_proveedor = c.cod_proveedor
                    WHERE t.estado = 1 AND t.cod_sucursal = :0 AND
                    (t.fecha_transaccion BETWEEN :1 AND :2)
                    ORDER BY t.cod_transaccion DESC";

            $params = [$this->getCodSucursal(), $fechaDesde, $fechaHasta];
            $compras = $this->consultarFilas($sql, $params);

            return array("rpt"=>true,"data"=>$compras);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


    public function eliminarCompra($fechaDesde, $fechaHasta){
        try{
            $this->beginTransaction();

            /*Transaccion existe. */
            $sql = "SELECT cod_compra, cod_sucursal FROM transaccion t 
                    INNER JOIN compra c ON c.cod_transaccion = t.cod_transaccion
                    WHERE t.cod_transaccion = :0";

            $objCompra = $this->consultarFila($sql, [$this->getCodTransaccion()]);

            if ($objCompra == false){
                return ["rpt"=>false, "msj"=>"Compra no existe."];
            }

            $this->setCodCompra($objCompra["cod_compra"]);

            $this->update("transaccion", ["estado"=>0], ["cod_transaccion"=>$this->getCodTransaccion()]);

            $sql = "SELECT cod_producto, precio_unitario, cantidad, fecha_vencimiento, lote
                    FROM compra_detalle WHERE cod_compra = :0";

            $detalleCompra = $this->consultarFilas($sql, [$this->getCodCompra()]);

             $sql = "";
            foreach ($detalleCompra as $key => $detalle) {
                /*Regresar el stock.*/
                $sqlStock = "SELECT stock FROM sucursal_producto 
                            WHERE cod_producto = ".$detalle["cod_producto"]." AND precio_entrada = ".$detalle["precio_unitario"]." 
                                    AND cod_sucursal = ".$objCompra["cod_sucursal"]. " 
                                    AND fecha_vencimiento = '".$detalle["fecha_vencimiento"]."' AND lote = '".$detalle["lote"]."'";

                $stock = $this->consultarValor($sqlStock);
                $nuevoStock = $stock - $detalle["cantidad"];

                if ($nuevoStock < 0){
                    $this->rollBack();
                    return ["rpt"=>false, "msj"=>"No se puede analizar esta compra, no hay stock disponible para anulación de salida de producto."];
                }

                $sql .= " UPDATE sucursal_producto SET stock = stock - ".$detalle["cantidad"]." 
                                WHERE cod_producto = ".$detalle["cod_producto"]." AND precio_entrada = ".$detalle["precio_unitario"]." AND cod_sucursal = ".$objCompra["cod_sucursal"]."
                                       AND fecha_vencimiento = '".$detalle["fecha_vencimiento"]."' AND lote = '".$detalle["lote"]."'; ";
            }

            $o = $this->consultaRaw($sql);

            $o = $this->update("sucursal_producto_historial",        
                            ["estado_mrcb"=>"0"],
                            ["cod_transaccion"=>$this->getCodTransaccion()]);


            $data = $this->obtenerListaCompras($fechaDesde, $fechaHasta)["data"];
            /*
                Estado transaccion = 0
                Compras muertas / comisionista NULL
                Detalle muerto

                Recueperar el stock basado en los detalles
                Cancelar movimientos asociados a esta transaccion
                Eliminar comisiones asociadas a esta compra. (NO HAY NECESIDAD)
            */
            $this->commit();
            
            return array("rpt"=>true,"data"=>$data,"msj"=>"Compra eliminada correctamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function destruirCompra($MODO_EDITAR  = false){
        try{
            $this->beginTransaction();    
            
            if (!$MODO_EDITAR){
                
            }

            /*Transaccion existe. */
            $sql = "SELECT cod_compra, cod_sucursal 
                    FROM transaccion t 
                    INNER JOIN compra c ON c.cod_transaccion = t.cod_transaccion
                    WHERE t.cod_transaccion = :0";

            $objCompra = $this->consultarFila($sql, [$this->getCodTransaccion()]);

            if ($objCompra == false){
                return ["rpt"=>false, "msj"=>"Compra no existe."];
            }

            $this->setCodCompra($objCompra["cod_compra"]);

            $this->delete("transaccion",["cod_transaccion"=>$this->getCodTransaccion()]);

            $sql = "SELECT cod_producto, precio_unitario, cantidad, fecha_vencimiento, lote
                    FROM compra_detalle WHERE cod_compra = :0";

            $detalleCompra = $this->consultarFilas($sql, [$this->getCodCompra()]);

            $sql = "";
            foreach ($detalleCompra as $key => $detalle) {
                /*Regresar el stock.*/
                $sqlStock = "SELECT stock FROM sucursal_producto 
                            WHERE cod_producto = ".$detalle["cod_producto"]." AND precio_entrada = ".$detalle["precio_unitario"]."
                                    AND cod_sucursal = ".$objCompra["cod_sucursal"]. " 
                                    AND fecha_vencimiento = '".$detalle["fecha_vencimiento"]."' AND lote = '".$detalle["lote"]."'";;

                $stock = $this->consultarValor($sqlStock);
                $nuevoStock = $stock - $detalle["cantidad"];

                if ($nuevoStock < 0){
                    $this->rollBack();
                    return ["rpt"=>false, "msj"=>"No se puede analizar esta compra, no hay stock disponible para anulación de salida de producto."];
                }

                $sql .= " UPDATE sucursal_producto SET stock = stock - ".$detalle["cantidad"]." WHERE cod_producto = ".$detalle["cod_producto"]." AND precio_entrada = ".$detalle["precio_unitario"]." 
                                        AND cod_sucursal = ".$objCompra["cod_sucursal"]."
                                        AND fecha_vencimiento = '".$detalle["fecha_vencimiento"]."' AND lote = '".$detalle["lote"]."'; ";
            }

            $o = $this->consultaRaw($sql);

            $this->delete("sucursal_producto_historial",["cod_transaccion"=>$this->getCodTransaccion()]);

            
            $this->delete("compra_detalle",["cod_compra"=>$this->getCodCompra()]);
            $this->delete("compra",["cod_compra"=>$this->getCodCompra()]);
            /*
                Estado transaccion = 0
                Compras muertas / comisionista NULL
                Detalle muerto

                Recueperar el stock basado en los detalles
                Cancelar movimientos asociados a esta transaccion
                Eliminar comisiones asociadas a esta compra. (NO HAY NECESIDAD)
            */
            if (!$MODO_EDITAR){
                $this->commit();    
            }
            
            return array("rpt"=>true,"msj"=>"Compra destruida correctamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerCompra(){
        try{

            $USUARIO = $this->lastUsuario;
            
           

            $sql = "SELECT 
                    LPAD(t.cod_transaccion,6,'0') as cod_transaccion,
                    c.numero_comprobante as comprobante,
                    p.razon_social as proveedor,
                    (CASE c.tipo_pago WHEN 'E' THEN 'EFECTIVO' ELSE 'TARJETA' END) as tipo_pago,
                    (CASE c.tipo_tarjeta WHEN 'C' THEN 'CREDITO' WHEN 'D' THEN 'DEBITO' ELSE NULL END) as tipo_tarjeta,
                    DATE_FORMAT(t.fecha_transaccion,'%d-%m-%Y') as fecha_compra,
                    c.importe_total_compra
                    FROM transaccion t
                    INNER JOIN compra c ON t.cod_transaccion = c.cod_transaccion
                    LEFT JOIN proveedor p ON p.cod_proveedor = c.cod_proveedor
                    WHERE estado = 1 AND t.cod_transaccion = :0";

            $cabecera = $this->consultarFila($sql, $this->getCodTransaccion());

            $sql = "SELECT 
                    item,
                    p.nombre as producto,
                    cd.precio_unitario,
                    cd.cantidad,
                    CAST( (cantidad * cd.precio_unitario) AS DECIMAL(10,2)) as subtotal,
                    cd.fecha_vencimiento,
                    cd.lote
                    FROM compra_detalle cd
                    LEFT JOIN producto p ON p.cod_producto = cd.cod_producto
                    LEFT JOIN compra c ON cd.cod_compra = c.cod_compra
                    WHERE c.cod_transaccion = :0
                    ORDER BY cd.item";

            $detalle = $this->consultarFilas($sql, $this->getCodTransaccion());        

            return array("rpt"=>true,"data"=>["cabecera"=>$cabecera, "detalle"=>$detalle]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


    public function leerCompraEditar(){
        try{

            $USUARIO = $this->lastUsuario;

            $sql = "SELECT  
                    t.cod_tipo_comprobante,
                    t.cod_transaccion,
                    LPAD(t.cod_transaccion,6,'0') as x_cod_transaccion,
                    c.numero_comprobante as comprobante,
                    cod_proveedor,
                    c.tipo_pago,
                    c.tipo_tarjeta,
                    t.fecha_transaccion,
                    c.importe_total_compra,
                    t.guias_remision,
                    t.observaciones
                    FROM transaccion t
                    INNER JOIN compra c ON t.cod_transaccion = c.cod_transaccion
                    WHERE estado = 1 AND t.cod_transaccion = :0";

            $cabecera = $this->consultarFila($sql, $this->getCodTransaccion());

            $sql = "SELECT 
                    cd.cod_producto,
                    p.nombre as nombre_producto,
                    (SELECT pi.img_url FROM producto_img pi WHERE pi.cod_producto = p.cod_producto AND pi.numero_imagen = p.numero_imagen_principal) as img_url,
                    cd.precio_unitario as precio_unitario,
                    cd.cantidad as cantidad,
                    cd.precio_unitario * cd.cantidad as subtotal,
                    cd.fecha_vencimiento,
                    cd.lote
                    FROM compra_detalle cd
                    INNER JOIN producto p ON cd.cod_producto = p.cod_producto
                    LEFT JOIN compra c ON cd.cod_compra = c.cod_compra
                    WHERE c.cod_transaccion = :0
                    ORDER BY cd.item";

            $detalle = $this->consultarFilas($sql, $this->getCodTransaccion());

            return array("rpt"=>true,"data"=>["cabecera"=>$cabecera, "detalle"=>$detalle]);
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
            $MODO_EDITAR = true;

            $objDestruir = $this->destruirCompra($MODO_EDITAR);
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