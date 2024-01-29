<?php

require_once 'Transaccion.clase.php';

class Venta extends Transaccion {
    private $cod_venta;
    private $cod_cliente;
    private $numero_documento;
    private $razon_social_nombre;
    private $apellidos;
    private $direccion_cliente;
    private $correo_envio;
    private $tipo_pago;
    private $monto_efectivo;
    private $monto_tarjeta;
    private $tipo_tarjeta;
    private $monto_credito;
    private $cod_descuento_global;
    private $descuentos_globales;
    private $total_descuentos;
    private $cod_tipo_moneda;
    private $importe_total_venta;
    private $total_gravadas;
    private $cod_comisionista;
    private $numero_voucher;
    private $observaciones;

    private $celular_cliente;

    private $detalle_venta;

    public function getCodVenta()
    {
        return $this->cod_venta;
    }
    
    public function setCodVenta($cod_venta)
    {
        $this->cod_venta = $cod_venta;
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

    public function getCodDescuentoGlobal()
    {
        return $this->cod_descuento_global;
    }
    
    
    public function setCodDescuentoGlobal($cod_descuento_global)
    {
        $this->cod_descuento_global = $cod_descuento_global;
        return $this;
    }
    public function getImporteTotalVenta()
    {
        return $this->importe_total_venta;
    }
    
    
    public function setImporteTotalVenta($importe_total_venta)
    {
        $this->importe_total_venta = $importe_total_venta;
        return $this;
    }


    public function getCodComisionista()
    {
        return $this->cod_comisionista;
    }
    
    
    public function setCodComisionista($cod_comisionista)
    {
        $this->cod_comisionista = $cod_comisionista;
        return $this;
    }

    public function getNumeroVoucher()
    {
        return $this->numero_voucher;
    }
    
    
    public function setNumeroVoucher($numero_voucher)
    {
        $this->numero_voucher = $numero_voucher;
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

    public function getDetalleVenta()
    {
        return $this->detalle_venta;
    }
    
    
    public function setDetalleVenta($detalle_venta)
    {
        $this->detalle_venta = $detalle_venta;
        return $this;
    }

    public function getCelularCliente()
    {
        return $this->celular_cliente;
    }
    
    
    public function setCelularCliente($celular_cliente)
    {
        $this->celular_cliente = $celular_cliente;
        return $this;
    }

    public function getMontoEfectivo()
    {
        return $this->monto_efectivo;
    }
    
    
    public function setMontoEfectivo($monto_efectivo)
    {
        $this->monto_efectivo = $monto_efectivo;
        return $this;
    }

    public function getMontoTarjeta()
    {
        return $this->monto_tarjeta;
    }
    
    public function setMontoTarjeta($monto_tarjeta)
    {
        $this->monto_tarjeta = $monto_tarjeta;
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

    public function getMontoCredito()
    {
        return $this->monto_credito;
    }
    
    
    public function setMontoCredito($monto_credito)
    {
        $this->monto_credito = $monto_credito;
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

            $sql = "SELECT c.cod_cliente, nombres, apellidos, td.cod_tipo_documento, 
                        td.abrev as tipo_documento, IF(c.numero_documento = '', NULL, c.numero_documento) as numero_documento,
                        c.direccion, correo, c.celular
                        FROM cliente c
                        INNER JOIN tipo_documento td ON td.cod_tipo_documento = c.tipo_documento
                        WHERE c.estado_mrcb";
            $clientes = $this->consultarFilas($sql);

            $sql = "SELECT cod_sucursal, nombre FROM sucursal WHERE estado_mrcb AND cod_sucursal <> 0 AND ".$sqlSucursales." ORDER BY cod_sucursal";
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

            $sql = "SELECT cod_comisionista as codigo, nombres as nombre
                    FROM comisionista
                    WHERE estado_mrcb";
            $comisionistas = $this->consultarFilas($sql);

            $correlativo_boletas = $this->consultarValor("SELECT valor_variable FROM variable_constante WHERE nombre_variable = 'correlativo_boletas'");
            $correlativo_facturas = $this->consultarValor("SELECT valor_variable FROM variable_constante WHERE nombre_variable = 'correlativo_facturas'");

            return array("rpt"=>true,"data"=>["clientes"=>$clientes,"productos"=>$productos, 
                                                "tipo_categorias"=>$tipo_categorias, "categoria_productos"=>$categoria_productos,
                                                "sucursales"=>$sucursales,
                                                "comisionistas"=>$comisionistas,
                                                    "correlativo_boletas_previo"=>$correlativo_boletas,
                                                    "correlativo_facturas_previo"=>$correlativo_facturas]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDataProductos(){
        try{

            $sql = "SELECT 
                        p.cod_producto,
                        SUM(sp.stock) as stock,
                        CAST(p.precio_unitario AS DECIMAL(10,2)) as precio_unitario,
                        p.nombre as nombre_producto,
                        cp.cod_tipo_categoria as cod_tipo,
                        p.cod_categoria_producto  as cod_categoria,
                        m.nombre as marca,
                        sp.fecha_vencimiento,
                        sp.lote,
                        CONCAT(p.cod_producto, fecha_vencimiento, lote) as codigo_unico_producto,
                        COALESCE( 
                            (SELECT pi.img_url FROM producto_img pi WHERE pi.cod_producto = p.cod_producto AND pi.numero_imagen = p.numero_imagen_principal),'default_producto.jpg') as img_url
                        FROM sucursal_producto sp
                        INNER JOIN producto p ON sp.cod_producto = p.cod_producto AND p.estado_mrcb
                        INNER JOIN categoria_producto cp ON cp.cod_categoria_producto = p.cod_categoria_producto AND cp.estado_mrcb
                        INNER JOIN marca m ON m.cod_marca = p.cod_marca
                        WHERE sp.cod_sucursal = :0
                        GROUP BY sp.cod_producto, sp.fecha_vencimiento, sp.lote";
                        //HAVING stock > 0";

            $productos = $this->consultarFilas($sql, $this->getCodSucursal());

            return array("rpt"=>true,"data"=>$productos);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function grabar($fechaDesde, $fechaHasta, $MODO_EDITAR = false){
        try{
            $idPersonal = $this->lastUsuario;

            $this->beginTransaction();
            if (!$MODO_EDITAR){
                $this->setCodTransaccion($this->consultarValor("SELECT COALESCE(MAX(cod_transaccion)+1, 1) FROM transaccion"));
            }

            $this->setDetalleVenta(json_decode($this->getDetalleVenta()));

            if($this->getCodTipoComprobante() == ""){
                $this->setSerie(NULL);
                $this->setCorrelativo(NULL);
                $nuevo_correlativo = NULL;
            } else {
                /*Verificar si EXISTE este correlativo.*/
                $this->setCorrelativo((int) $this->getCorrelativo());

                $sql  = "SELECT COUNT(cod_transaccion) > 0 FROM transaccion t
                            WHERE estado = 1 AND cod_transaccion = :0 AND serie = :1 AND correlativo = :2";
                $existeCorrelativo = $this->consultarValor($sql, [$this->getCodTransaccion(), $this->getSerie(), $this->getCorrelativo()]);

                if ($existeCorrelativo == "true"){
                    return ["rpt"=>false, "msj"=>"Ya existe una venta con este correlativo."];
                }

            }

            if($this->getCodTipoComprobante() == "6" && $this->getCodCliente() == ""){
                return ["rpt"=>false, "msj"=>"En caso de registrar factura, se debe registrar el cliente antes de hacer una venta."];
            }

            if ($this->getFechaTransaccion() == ""){
                return ["rpt"=>false, "msj"=>"Se debe ingresar una fecha de venta"];
            }

            $esRolAdmin = ($_SESSION["usuario"]["cod_rol"] == "1");

            if (!$esRolAdmin){
                if ($this->getFechaTransaccion() < date('Y-m-d')){
                    return ["rpt"=>false,"msj"=>"La fecha de la venta deber SER MAYOR O IGUAL que la fecha actual."];
                }
            }

            if ($this->getMontoEfectivo() + $this->getMontoTarjeta() + $this->getMontoCredito() <= 0){
                return ["rpt"=>false,"msj"=>"El monto de pagos no puede ser 0 o menor."];
            }

            if ($this->getMontoTarjeta() > 0){
                $this->setTipoPago("T");
            }

            if ($this->getMontoEfectivo() > 0){
                $this->setTipoTarjeta(null);
                $this->setTipoPago("E");
            }

            if ($this->getMontoCredito() > 0){
                $this->setTipoTarjeta(null);
                $this->setTipoPago("C");
            }      

            $this->setCodVenta($this->consultarValor("SELECT COALESCE(MAX(cod_venta)+1, 1) FROM venta"));

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

            /*Registrando cabecera TRANSA*/
            $campos_valores  = [
                "cod_transaccion"=>$this->getCodTransaccion(),
                "cod_tipo_documento"=>$this->getCodTipoDocumento(),
                "cod_tipo_comprobante"=>$this->getCodTipoComprobante(),
                "serie"=>$this->getSerie(),
                "correlativo"=>$this->getCorrelativo(),
                "cod_sucursal"=>$this->getCodSucursal(),
                "fecha_transaccion"=>$this->getFechaTransaccion(),
                "hora_transaccion"=>date("H:m"),
                "observaciones"=>$this->getObservaciones() === "" ? NULL : $this->getObservaciones(),
                "id_personal_registro"=>$idPersonal || ""
            ];

            $o = $this->insert("transaccion", $campos_valores);

            /*Repartiendo Detalle, verificando integridad, calcuclando sub total y descuentos*/
            $descuentosGlobales = 0;
            $totalDescuentos = 0;
            $totalGravadas = 0;
            $sumatoriaIGV= 0;

            $VARIABLES = $this->consultarFilas("SELECT valor_variable FROM variable_constante WHERE nombre_variable IN ('IGV','INCLUIR_IGV') ORDER BY nombre_variable");
            $IGV = $VARIABLES[0]["valor_variable"];
            $INCLUIR_IGV = $VARIABLES[1]["valor_variable"] == "1";
            $cadenaDescuentos = '';

            $this->setCodTipoMoneda(1);

            $codHistorial = $this->consultarValor("SELECT COALESCE(MAX(cod_historial)+1, 1) FROM sucursal_producto_historial");

            foreach ($this->getDetalleVenta() as $i => $fila) {
                /*validez STOCK*/  
                /*validez descuento*/
                $descuentoFila = 0;

                $sql = "SELECT nombre, precio_unitario as precio_venta_unitario, cod_unidad_medida 
                                FROM producto WHERE cod_producto = :0 AND estado_mrcb";
                $objProducto = $this->consultarFila($sql, [$fila->codProducto]);

                if ($objProducto == false){
                    return ["rpt"=>false, "msj"=>"Producto no existe en el sistema."];
                }

                $precioVentaUnitario = $objProducto["precio_venta_unitario"];

                $sql = "SELECT SUM(stock)   
                            FROM sucursal_producto 
                            WHERE cod_producto = :0 AND cod_sucursal = :1 
                                AND fecha_vencimiento = :2 AND lote = :3";
                $stockActual = $this->consultarValor($sql, [$fila->codProducto, $this->getCodSucursal(), $fila->fechaVencimiento, $fila->lote]);

                if ($fila->cantidad > $stockActual){
                    $this->rollBack();
                    return ["rpt"=>false, "msj"=>"Producto de fila ".($i+1). " no tiene stock suficiente."];
                }

                /*INSERTAMOS MOVIMEINTO */
                $campos_valores = [
                    "cod_historial"=>$codHistorial,
                    "cod_producto"=> $fila->codProducto,
                    "cod_sucursal"=> $this->getCodSucursal(),
                    "precio_salida"=> $precioVentaUnitario,
                    "fecha_vencimiento"=>$fila->fechaVencimiento,
                    "lote"=>$fila->lote,
                    "cod_transaccion"=> $this->getCodTransaccion(),
                    "cantidad"=> $fila->cantidad,
                    "fecha_movimiento"=>$this->getFechaTransaccion(),
                    "tipo_movimiento"=>"S"
                ];


                $o = $this->insert("sucursal_producto_historial", $campos_valores);
                $codHistorial++;

                /*ACTUALIZAMOS SUCURSAL*/
                $sql = "SELECT precio_entrada, stock 
                                FROM sucursal_producto 
                                WHERE cod_producto = :0 AND cod_sucursal = :1 AND fecha_vencimiento = :2 AND lote = :3
                                ORDER BY stock DESC";
                $arStocks = $this->consultarFilas($sql,[$fila->codProducto, $this->getCodSucursal(),$fila->fechaVencimiento, $fila->lote]);

                $restante = $fila->cantidad;
                $cadenaStock = "[";
                  //  var_dump("S",$arStocks);


                foreach ($arStocks as $key => $value) {
                    $stock = $value["stock"];
                    if ($restante > $stock){
                        $restante = $restante - $stock;
                        $consumido = $stock;
                    } else {
                        $consumido = $restante;
                        $restante = 0;
                    }

                    //var_dump("K",$value);

                    //var_dump("O",$this->consultarFila("SELECT cod_producto, precio_entrada, stock FROM sucursal_producto WHERE  cod_producto = ".$fila->codProducto." AND cod_sucursal = ".$this->getCodSucursal()." AND ".$value["precio_entrada"]." order by stock desc"));
                    $sql = "UPDATE sucursal_producto SET stock = stock - ".$consumido." 
                            WHERE cod_producto = ".$fila->codProducto." 
                            AND cod_sucursal = ".$this->getCodSucursal()." 
                            AND precio_entrada = ".$value["precio_entrada"]."
                            AND fecha_vencimiento = '".$fila->fechaVencimiento."'
                            AND lote = '".$fila->lote."'";
                    $this->consultaRaw($sql);

                    $cadenaStock .= '{"cantidad":"'.$consumido.'","precio_entrada":"'.$value["precio_entrada"].'"}';
                    if ($restante <= 0){
                        break;
                    }
                }
                $cadenaStock .= "]";

                /*precio de venta incluye IGV*/
                if ($INCLUIR_IGV){
                    $valorUnitario = round($precioVentaUnitario / (1 + $IGV),4);
                } else {
                    $valorUnitario = round($precioVentaUnitario, 4);
                    $precioVentaUnitario = $valorUnitario * (1 + $IGV);                    
                }

                $valorVenta = round($valorUnitario * $fila->cantidad,2);
                $montoIGV = round(($precioVentaUnitario - $valorUnitario) * $fila->cantidad, 2);

                if (strlen($fila->descuento) == 6){
                    $sql = "SELECT cod_descuento, tipo_descuento, monto_descuento FROM descuento WHERE codigo_generado = :0 AND estado_mrcb AND estado_uso = 0";
                    $descuentoValido = $this->consultarFila($sql, [$fila->descuento]);
                    if ($descuentoValido == false){
                        return ["rpt"=>false, "msj"=>"Producto de fila ".($i+1). " descuento no válido."];
                    }

                    $descuentoFila = $descuentoValido["tipo_descuento"] == "P" ? 
                                    (($valorVenta + $montoIGV) * ($descuentoValido["monto_descuento"]/100)) :
                                    $descuentoValido["monto_descuento"]; /*descuentos*/

                    $cadenaDescuentos .= ($descuentoValido["cod_descuento"].",");

                } else {
                    $fila->descuento = NULL;
                    $descuentoValido = ["cod_descuento"=>NULL,"tipo_descuento"=> NULL ,"monto_descuento"=>NULL];
                }

                $costo_producto  = 0;
                $arregloStock = json_decode($cadenaStock);
                foreach ($arregloStock as $__key => $__value) {
                    $costo_producto += ($__value->cantidad  * $__value->precio_entrada);
                }

                /*INSERTAMOS DETALLE*/            
                $campos_valores = [
                    "cod_venta"=>$this->getCodVenta(),
                    "item"=>($i + 1),
                    "cod_producto"=>$fila->codProducto,
                    "fecha_vencimiento"=>$fila->fechaVencimiento,
                    "lote"=>$fila->lote,
                    "valor_unitario"=>$valorUnitario,
                    "cantidad_item"=>$fila->cantidad,
                    "cod_descuento"=>$descuentoValido["cod_descuento"],
                    "tipo_descuento"=>$descuentoValido["tipo_descuento"],
                    "monto_descuento"=>$descuentoValido["monto_descuento"],
                    "descripcion_producto"=>$objProducto["nombre"],
                    "descuentos"=>$descuentoFila,
                    "monto_igv"=>$montoIGV,
                    "precio_venta_unitario"=>$precioVentaUnitario,
                    "valor_venta"=>$valorVenta,
                    "cadena_stock_producto"=> $cadenaStock,
                    "cod_unidad_medida"=>$objProducto["cod_unidad_medida"],
                    "costo_producto"=>$costo_producto
                ];

                $o = $this->insert("venta_detalle", $campos_valores);
                /*  generar una salida de producto 
                    actualiazr stock productos sucursal
                    guardar el detalle
                    calcular subTotal
                    calcuclar Descuntos (detalle)
                */

                $totalDescuentos += $descuentoFila;
                $sumatoriaIGV += $montoIGV;
                $totalGravadas += $valorVenta;/*Suma valorventa*/

            }

            /*Se procede a obtener el descuent global, si hay.*/
            /*
            if (strlen($cadenaDescuentos) > 0){
                $cadenaDescuentos = substr($cadenaDescuentos, 0, -1);  
            } 
            */

            $descuentoGlobal = 0;
            $lenDescuento = strlen($this->getCodDescuentoGlobal());

            $huboDescuentoGlobal = false;
            if ($lenDescuento > 0){
                $sql = "SELECT cod_descuento, tipo_descuento, monto_descuento FROM descuento WHERE codigo_generado = :0 AND estado_mrcb AND estado_uso = 0";
                $descuentoValido = $this->consultarFila($sql, [$this->getCodDescuentoGlobal()]);
                if ($descuentoValido == false){
                    return ["rpt"=>false, "msj"=>"Descuento global no válido."];
                } else {
                    $descuentoGlobal =  $descuentoValido["tipo_descuento"] == "P" ? 
                                    ($sumatoriaIGV + $totalGravadas - $totalDescuentos) * ($descuentoValido["monto_descuento"]/100) :
                                    $descuentoValido["monto_descuento"]; /*descuentos*/

                    $cadenaDescuentos .= ($descuentoValido["cod_descuento"]).",";  

                    $this->setCodDescuentoGlobal($descuentoValido["cod_descuento"]);

                    $totalDescuentos += $descuentoGlobal;
                    $huboDescuentoGlobal = true;
                }
            }

            if (strlen($cadenaDescuentos) > 0){
                $cadenaDescuentos = substr($cadenaDescuentos, 0, -1);  
                $sql = "UPDATE descuento SET estado_uso = 1, usuario_uso = ".$this->lastUsuario.", fecha_hora_uso = current_timestamp WHERE cod_descuento IN (".$cadenaDescuentos.")";
                $o = $this->consultaRaw($sql);
            }

            $importeTotalVenta = round((($sumatoriaIGV + $totalGravadas) - $totalDescuentos), 2);

            /*Registrando cabecera VENTA*/
            $campos_valores  = [
                "cod_venta"=>$this->getCodVenta(),
                "cod_transaccion"=>$this->getCodTransaccion(),
                "cod_cliente"=>$this->getCodCliente(),
                "numero_documento"=>$this->getNumeroDocumento() == "" ? NULL : $this->getNumeroDocumento(),
                "razon_social_nombre"=>($this->getCodCliente() == 0 ? NULL : $this->getRazonSocialNombre()." ".$this->getApellidos()),
                "direccion_cliente"=>$this->getDireccionCliente(),
                "tipo_pago"=>$this->getTipoPago(),
                "monto_efectivo"=>$this->getMontoEfectivo(),
                "monto_tarjeta"=>$this->getMontoTarjeta(),
                "tipo_pago"=>$this->getTipoPago(),
                "tipo_tarjeta"=>$this->getTipoTarjeta(),
                "monto_credito"=>$this->getMontoCredito(),
                "correo_envio"=>$this->getCorreoEnvio(),
                "cod_tipo_moneda"=>$this->getCodTipoMoneda(),
                "cod_descuento_global"=>$this->getCodDescuentoGlobal() == "" ? null : $this->getCodDescuentoGlobal(),
                "descuentos_globales"=>$descuentoGlobal,
                "total_descuentos"=>$totalDescuentos,
                "total_gravadas"=>$totalGravadas,
                "sumatoria_igv"=>$sumatoriaIGV,
                "importe_total_venta"=>$importeTotalVenta,
            ];

            $o = $this->insert("venta", $campos_valores);

            if ($this->getMontoCredito() > 0){
                $campos_valores = [
                    "cod_venta"=>$this->getCodVenta(),
                    "monto"=>$this->getMontoCredito(),
                    "tipo_deuda"=>"-1",
                    "fecha_registro"=>$this->getFechaTransaccion(),
                    "pendiente"=>$this->getMontoCredito()
                ];

                $o = $this->insert("venta_credito", $campos_valores);
            }


            if (!$MODO_EDITAR){
                /*Si se está grabando (no editar) autmentar el correaltivo.*/
                $nuevo_correlativo = $this->getCorrelativo() + 1;
                $sql = "UPDATE variable_constante SET valor_variable = ".$nuevo_correlativo." WHERE nombre_variable = 'correlativo_boletas'";
                $this->consultaRaw($sql);                 
            }

            $this->commit();

            $lista_ventas = $this->obtenerListaVentas($fechaDesde, $fechaHasta)["data"];

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
            return array("rpt"=>true,"msj"=>"Venta ".($MODO_EDITAR ? "editada" : "registrada")." correctamente.", 
                "data"=>["cod_venta"=>$this->getCodVenta(), "cod_transaccion"=> $this->getCodTransaccion(),
                            "lista_ventas"=>$lista_ventas, 
                            "cod_tipo_comprobante"=>$this->getCodTipoComprobante(),
                            "tipo_pago"=>$this->getTipoPago(),
                            "clientes"=>$clientes, 
                            "nuevo_correlativo"=>$nuevo_correlativo]);
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function registrarVoucher(){
        try{
            $this->beginTransaction();

            /*Check if no existe repetido*/
            $sql = "SELECT COUNT(numero_voucher) > 0 FROM venta v
                        INNER JOIN transaccion t ON v.cod_transaccion = t.cod_transaccion
                        WHERE t.estado = 1 AND numero_voucher = :0";

            $repetido = $this->consultarValor($sql, [$this->getNumeroVoucher()]);

            if ($repetido == true){
                return ["rpt"=>false, "msj"=>"Número de voucher repetido."];
            }
            $sql = "UPDATE venta SET numero_voucher = '".$this->getNumeroVoucher()."' WHERE cod_venta = ".$this->getCodVenta();
            $this->consultaRaw($sql);

            $this->commit();
            
            return array("rpt"=>true,"msj"=>"Datos registrados correctamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerListaVentas($fechaDesde, $fechaHasta){
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

                $sql = "SELECT cod_sucursal, nombre FROM sucursal WHERE estado_mrcb AND cod_sucursal <> 0 AND ".$sqlSucursales." ORDER BY cod_sucursal";
                $sucursales = $this->consultarFilas($sql);
                $this->setCodSucursal($sucursales[0]["cod_sucursal"]);
            }

            $sql = "SELECT 
                    LPAD(t.cod_transaccion,6,'0') as x_cod_transaccion,
                    t.cod_transaccion,
                    v.cod_venta,
                    COALESCE(CONCAT(tc.abrev,serie,'-',LPAD(correlativo,6,'0')),'NINGUNO') as comprobante,
                    v.numero_voucher as voucher,
                    c.numero_documento,
                    COALESCE(v.razon_social_nombre, CONCAT(c.nombres,' ',c.apellidos)) as cliente,
                    (CASE v.tipo_pago WHEN 'E' THEN 'EFECTIVO' ELSE 'TARJETA' END) as tipo_pago,
                    monto_efectivo, monto_tarjeta, monto_credito,
                    (CASE v.tipo_tarjeta WHEN 'C' THEN 'CREDITO' WHEN 'D' THEN 'DEBITO' ELSE NULL END) as tipo_tarjeta,
                    DATE_FORMAT(fecha_transaccion,'%d-%m-%Y') as fecha_venta,
                    CAST((ROUND(v.total_gravadas + v.sumatoria_igv,1)) AS DECIMAL(10,2))  as subtotal,
                    v.total_descuentos,
                    v.importe_total_venta as importe_total_venta,
                    COALESCE(co.nombres,'-') as comisionista,
                    COALESCE((SELECT SUM(monto_comision) FROM venta_comision_producto WHERE cod_venta = v.cod_venta), 0.00) as total_comisiones
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN comisionista co ON v.cod_comisionista = co.cod_comisionista
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    WHERE estado = 1 AND cod_sucursal = :0 AND
                    (fecha_transaccion BETWEEN :1 AND :2)
                    ORDER BY t.fecha_transaccion DESC";

            $params = [$this->getCodSucursal(), $fechaDesde, $fechaHasta];
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
    
    public function obtenerDatosParaComprobanteTicket(){
        try{

            $sql = "SELECT 
                    LPAD(t.cod_transaccion,6,'0') as cod_transaccion,
                    t.cod_transaccion as x_cod_transaccion,
                    t.cod_tipo_comprobante as tipo_comprobante,
                    CONCAT(tc.abrev,serie) as serie,
                    LPAD(correlativo,6,'0') as numero_correlativo,
                    -- COALESCE(CONCAT(tc.abrev,serie,'-',LPAD(correlativo,6,'0')),'NINGUNO') as comprobante,
                    -- v.numero_voucher as voucher,
                    t.cod_tipo_comprobante as idtipo_comprobante,
                    t.cod_tipo_documento as id_tipo_documento_cliente,
                    COALESCE(CONCAT(c.nombres,' ',c.apellidos),v.razon_social_nombre) as cliente,
                    COALESCE(v.numero_documento,'-') as numero_documento_cliente,
                    v.direccion_cliente,
                    (CASE v.tipo_pago WHEN 'E' THEN 'EFECTIVO' ELSE 'TARJETA' END) as tipo_pago,
                    monto_efectivo, monto_tarjeta, monto_credito,
                    (CASE v.tipo_tarjeta WHEN 'C' THEN 'CREDITO' WHEN 'D' THEN 'DEBITO' ELSE NULL END) as tipo_tarjeta,
                    DATE_FORMAT(fecha_transaccion,'%d-%m-%Y') as fecha_emision,
                    fecha_transaccion as fecha_emision_raw,
                    hora_transaccion as hora_emision,
                    v.sumatoria_igv as total_igv,
                    v.total_gravadas as total_gravadas,
                    CAST((ROUND(v.total_gravadas + v.sumatoria_igv - (total_descuentos - descuentos_globales),1)) AS DECIMAL(10,2))  as subtotal,
                    COALESCE(v.descuentos_globales, 0.00) as descuento_global,
                    d.codigo_generado as codigo_descuento,
                    v.importe_total_venta as importe_total,
                    t.observaciones,
                    COALESCE(t.hash_cpe,'') as valor_resumen,
                    COALESCE(t.hash_signature,'') as valor_firma,
                    COALESCE(t.mensaje_cdr, '') as mensaje_cdr
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    LEFT JOIN descuento d ON v.cod_descuento_global = d.cod_descuento
                    WHERE t.estado = 1 AND t.cod_transaccion = :0";

            $cabecera = $this->consultarFila($sql, $this->getCodTransaccion());

            $sql = "SELECT 
                    p.codigo as codigo_producto,
                    p.nombre as descripcion_item,
                    precio_venta_unitario,
                    cantidad_item,
                    vd.fecha_vencimiento,
                    vd.lote
                    -- CAST(ROUND((monto_igv + valor_venta),1) AS DECIMAL(10,2)) as subtotal,
                    -- COALESCE(descuentos,0.00) as descuento,
                    -- d.codigo_generado,
                    -- CAST(ROUND((monto_igv + valor_venta - descuentos),1) AS DECIMAL(10,2)) as subtotal,
                    -- monto_igv,
                    -- valor_venta
                    FROM venta_detalle vd
                    LEFT JOIN producto p ON p.cod_producto = vd.cod_producto
                    LEFT JOIN venta v ON vd.cod_venta = v.cod_venta
                    -- LEFT JOIN descuento d ON d.cod_descuento = vd.cod_descuento
                    WHERE v.cod_transaccion = :0
                    ORDER BY vd.item";

            $detalle = $this->consultarFilas($sql, $this->getCodTransaccion());
            
            $cabecera["detalle"] = $detalle;

            return $cabecera;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }
    
    
    public function obtenerDatosParaAtencionTicket(){
        try{

            $sql = "SELECT 
                    LPAD(t.cod_transaccion,6,'0') as numero_ticket,
                    t.cod_tipo_documento as id_tipo_documento_cliente,
                    COALESCE(CONCAT(c.nombres,' ',c.apellidos),v.razon_social_nombre) as nombres_completos,
                    COALESCE(v.numero_documento,'-') as numero_documento,
                    v.direccion_cliente,
                    monto_efectivo as total_efectivo, monto_tarjeta as total_tarjeta, monto_credito as total_credito,
                    DATE_FORMAT(fecha_transaccion,'%d-%m-%Y') as fecha_atencion,
                    fecha_transaccion as fecha_atencion_raw,
                    hora_transaccion as hora_atencion,
                    COALESCE(v.descuentos_globales, 0.00) as descuento_global,
                    v.importe_total_venta as importe_total,
                    t.observaciones
                    FROM transaccion t
                    INNER JOIN venta v ON t.cod_transaccion = v.cod_transaccion
                    LEFT JOIN tipo_comprobante tc ON tc.cod_tipo_comprobante = t.cod_tipo_comprobante
                    LEFT JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    WHERE t.estado = 1 AND t.cod_transaccion = :0";

            $cabecera = $this->consultarFila($sql, $this->getCodTransaccion());

            $sql = "SELECT 
                    p.codigo as codigo_producto,
                    p.nombre as nombre_servicio,
                    precio_venta_unitario as precio_unitario,
                    cantidad_item as cantidad
                    -- CAST(ROUND((monto_igv + valor_venta),1) AS DECIMAL(10,2)) as subtotal,
                    -- COALESCE(descuentos,0.00) as descuento,
                    -- d.codigo_generado,
                    -- CAST(ROUND((monto_igv + valor_venta - descuentos),1) AS DECIMAL(10,2)) as subtotal,
                    -- monto_igv,
                    -- valor_venta
                    FROM venta_detalle vd
                    LEFT JOIN producto p ON p.cod_producto = vd.cod_producto
                    LEFT JOIN venta v ON vd.cod_venta = v.cod_venta
                    -- LEFT JOIN descuento d ON d.cod_descuento = vd.cod_descuento
                    WHERE v.cod_transaccion = :0
                    ORDER BY vd.item";

            $detalle = $this->consultarFilas($sql, $this->getCodTransaccion());
            
            $cabecera["servicios"] = $detalle;

            return $cabecera;
        } catch (Exception $exc) {
            echo $exc->getMessage();
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

    public function eliminarVenta($fechaDesde, $fechaHasta){
        try{
            $this->beginTransaction();

              /*Transaccion existe. */
            $sql = "SELECT v.cod_venta, v.cod_comisionista , v.cod_descuento_global, t.cod_sucursal
                    FROM transaccion t 
                    INNER JOIN venta v ON v.cod_transaccion = t.cod_transaccion
                    WHERE t.cod_transaccion = :0 AND t.estado = 1";

            $objVenta = $this->consultarFila($sql, [$this->getCodTransaccion()]);

            if ($objVenta == false){
                return ["rpt"=>false, "msj"=>"Venta no existe."];
            }

            $this->setCodVenta($objVenta["cod_venta"]);  
            $this->setCodComisionista($objVenta["cod_comisionista"]);
            $this->setCodDescuentoGlobal($objVenta["cod_descuento_global"]);
            $this->setCodSucursal($objVenta["cod_sucursal"]);

            $this->update("transaccion", ["estado"=>0], ["cod_transaccion"=>$this->getCodTransaccion()]);

            $this->update("venta_credito", ["estado_mrcb"=>0], ["cod_venta"=>$this->getCodVenta()]);

            $sql = "SELECT cod_producto, cadena_stock_producto, cod_descuento, fecha_vencimiento, lote
                    FROM venta_detalle WHERE cod_venta = :0";

            $cadenaStockJSON = $this->consultarFilas($sql, [$this->getCodVenta()]);

            $sqlDetalleStock = "";
            //Cadena par arestaurar descuentos.
            $cadenaDescuentos = "";

            foreach ($cadenaStockJSON as $key => $detalle) {
                /*Regresar el stock.*/
                $arreglo = json_decode($detalle["cadena_stock_producto"]);
                foreach ($arreglo as $i => $subdetalle) {
                    # code...cnatidad | precio_entrada
                    $sqlDetalleStock .= " UPDATE sucursal_producto 
                                SET stock = stock + ".$subdetalle->cantidad." 
                                    WHERE cod_producto = ".$detalle["cod_producto"]." 
                                        AND precio_entrada = ".$subdetalle->precio_entrada."
                                        AND cod_sucursal = ".$this->getCodSucursal()."
                                        AND lote = '".$detalle["lote"]."'
                                        AND fecha_vencimiento = '".$detalle["fecha_vencimiento"]."';";
                }

                /*Restaurar posibles descuentos*/
                if ($detalle["cod_descuento"] != NULL){
                    $cadenaDescuentos .= $detalle["cod_descuento"].",";
                }
            }

            $o = $this->consultaRaw($sqlDetalleStock);
            //Restaurar descuentos, si es que hay algo en el
            /*1.- Verificar si hay desceunto global, se agrega al restro*/
            $cadenaDescuentosLength  = strlen($cadenaDescuentos);

            if ($this->getCodDescuentoGlobal() != NULL){
                $cadenaDescuentos .= $this->getCodDescuentoGlobal();
            } else {
                //Si no hay, preguntar si cadenaDescuentos length > 1, para eliminarle la coma
                if ($cadenaDescuentosLength > 1){
                    $cadenaDescuentos = substr($cadenaDescuentos, 0, -1);
                    $cadenaDescuentosLength--;
                }
            }

            // si hay algun descuento, restaurarlo
             if ($cadenaDescuentosLength > 1 ){
                $sqlRestaurarDescuentos = "UPDATE descuento SET fecha_hora_uso = NULL, estado_uso = 0 , usuario_uso = NULL WHERE cod_descuento IN (".$cadenaDescuentos.")";
                $this->consultaRaw($sqlRestaurarDescuentos);
            }


            $o = $this->update("sucursal_producto_historial",        
                            ["estado_mrcb"=>"0"],
                            ["cod_transaccion"=>$this->getCodTransaccion()]);


            $data = $this->obtenerListaVentas($fechaDesde, $fechaHasta)["data"];
            /*
                Estado transaccion = 0
                Ventas muertas / comisionista NULL
                Detalle muerto

                Recueperar el stock basado en los detalles
                Cancelar movimientos asociados a esta transaccion
                Eliminar comisiones asociadas a esta venta. (NO HAY NECESIDAD)
            */
            $this->commit();

            
            return array("rpt"=>true,"data"=>$data,"msj"=>"Venta eliminada correctamente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
    /*A diferencia de eliminra la venta, aquí en destruir, se desaprece TODO rastro de la venta es decir no se da de baja o 
    se actualiza estados, se procede a deletear directamente.*/
    public function destruirVenta($MODO_EDITAR  = false){
        try{
            $this->beginTransaction(); 
            if (!$MODO_EDITAR){
                   
            }

            /*Transaccion existe. */
            $sql = "SELECT v.cod_venta, v.cod_comisionista , v.cod_descuento_global, t.cod_sucursal
                    FROM transaccion t 
                    INNER JOIN venta v ON v.cod_transaccion = t.cod_transaccion
                    WHERE t.cod_transaccion = :0 AND t.estado = 1";

            $objVenta = $this->consultarFila($sql, [$this->getCodTransaccion()]);

            if ($objVenta == false){
                return ["rpt"=>false, "msj"=>"Venta no existe."];
            }

            $this->setCodVenta($objVenta["cod_venta"]);  
            $this->setCodComisionista($objVenta["cod_comisionista"]);
            $descuentoGlobal = $objVenta["cod_descuento_global"];
            $sucursal = $objVenta["cod_sucursal"];
            //Eliminar la venta
            $this->delete("transaccion",["cod_transaccion"=>$this->getCodTransaccion()]);

            $this->delete("venta_credito", ["cod_venta"=>$this->getCodVenta()]);

            $sql = "SELECT cod_producto, cadena_stock_producto, cod_descuento, fecha_vencimiento, lote
                    FROM venta_detalle WHERE cod_venta = :0";

            $cadenaStockJSON = $this->consultarFilas($sql, [$this->getCodVenta()]);

            $sqlDetalleStock = "";
            //Cadena par arestaurar descuentos.
            $cadenaDescuentos = "";

            foreach ($cadenaStockJSON as $key => $detalle) {
                /*Regresar el stock.*/
                $arreglo = json_decode($detalle["cadena_stock_producto"]);
                if ($arreglo != NULL){
                    foreach ($arreglo as $i => $subdetalle) {
                        # code...cnatidad | precio_entrada
                        $sqlDetalleStock .= " UPDATE sucursal_producto 
                                        SET stock = stock + ".$subdetalle->cantidad."
                                        WHERE cod_producto = ".$detalle["cod_producto"]."
                                            AND precio_entrada = ".$subdetalle->precio_entrada."
                                            AND cod_sucursal = ".$sucursal."
                                        AND lote = '".$detalle["lote"]."'
                                        AND fecha_vencimiento = '".$detalle["fecha_vencimiento"]."';";
                    }
                }
               
                /*Restaurar posibles descuentos*/
                if ($detalle["cod_descuento"] != NULL){
                    $cadenaDescuentos .= $detalle["cod_descuento"].",";
                }
            }

            if ($sqlDetalleStock != ""){
                $o = $this->consultaRaw($sqlDetalleStock);    
            }
            
            //Eliminar movimiento
            $o = $this->delete("sucursal_producto_historial", ["cod_transaccion"=>$this->getCodTransaccion()]);

            //Eliminar comisiones sobre esta venta
            $o = $this->delete("venta_comision_producto", ["cod_venta"=>$this->getCodVenta()]);

            //Restaurar descuentos, si es que hay algo en el
            /*1.- Verificar si hay desceunto global, se agrega al restro*/
            if ($descuentoGlobal != NULL){
                $cadenaDescuentos .= $descuentoGlobal;
            } else {
                //Si no hay, preguntar si cadenaDescuentos length > 1, para eliminarle la coma
                if (strlen($cadenaDescuentos) > 1){
                    $cadenaDescuentos = substr($cadenaDescuentos, 0, -1);
                }
            }

            // si hay algun descuento, restaurarlo
            if (strlen($cadenaDescuentos) > 1 ){
                $sqlRestaurarDescuentos = "UPDATE descuento SET fecha_hora_uso = NULL, estado_uso = 0, usuario_uso = NULL WHERE cod_descuento IN (".$cadenaDescuentos.")";
                $this->consultaRaw($sqlRestaurarDescuentos);
            }

            //Eliminar detalle (despues de haber borrado lo necesario)
            $o = $this->delete("venta_detalle", ["cod_venta"=>$this->getCodVenta()]);
            //Eliminar venta
            $this->delete("venta",["cod_venta"=>$this->getCodVenta()]);
            /*
                Estado transaccion = 0
                Recueperar el stock basado en los detalles
                Cancelar movimientos asociados a esta transaccion
                Eliminar comisiones asociadas a esta venta.

                eleiminar venta   y su detalle
                elliminar transaccon
            */

            if (!$MODO_EDITAR){
                $this->commit();    
            }
            
            return array("rpt"=>true,"msj"=>"Venta destruida correctamente.");
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