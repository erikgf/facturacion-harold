<?php

require_once '../datos/Conexion.clase.php';

class AlmacenMovimiento extends Conexion {

    private $cod_historial;
    private $tipo_movimiento;
    private $cod_producto;
    private $precio;
    private $cod_transaccion;
    private $cantidad;
    private $cod_sucursal;
    private $fecha_movimiento;
    private $estado_mrcb;
    private $fecha_vencimiento;
    private $lote;

    public function getCodHistorial()
    {
        return $this->cod_historial;
    }
    
    
    public function setCodHistorial($cod_historial)
    {
        $this->cod_historial = $cod_historial;
        return $this;
    }

    public function getTipoMovimiento()
    {
        return $this->tipo_movimiento;
    }
    
    
    public function setTipoMovimiento($tipo_movimiento)
    {
        $this->tipo_movimiento = $tipo_movimiento;
        return $this;
    }

    public function getCodProducto()
    {
        return $this->cod_producto;
    }
    
    
    public function setCodProducto($cod_producto)
    {
        $this->cod_producto = $cod_producto;
        return $this;
    }

    public function getPrecio()
    {
        return $this->precio;
    }
    
    
    public function setPrecio($precio)
    {
        $this->precio = $precio;
        return $this;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }
    
    
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
        return $this;
    }

    public function getCodSucursal()
    {
        return $this->cod_sucursal;
    }
    
    
    public function setCodSucursal($cod_sucursal)
    {
        $this->cod_sucursal = $cod_sucursal;
        return $this;
    }

    public function getFechaMovimiento()
    {
        return $this->fecha_movimiento;
    }
    
    
    public function setFechaMovimiento($fecha_movimiento)
    {
        $this->fecha_movimiento = $fecha_movimiento;
        return $this;
    }

    public function getEstadoMrcb()
    {
        return $this->estado_mrcb;
    }
    
    
    public function setEstadoMrcb($estado_mrcb)
    {
        $this->estado_mrcb = $estado_mrcb;
        return $this;
    }

    public function getLote()
    {
        return $this->lote;
    }
    
    
    public function setLote($lote)
    {
        $this->lote = $lote;
        return $this;
    }

    public function getFechaVencimiento()
    {
        return $this->fecha_vencimiento;
    }
    
    
    public function setFechaVencimiento($fecha_vencimiento)
    {
        $this->fecha_vencimiento = $fecha_vencimiento;
        return $this;
    }

    public function guardarTransferencia($codOrigen, $codDestino, $JSONProductosCantidad){
        try{
            
            if ($codOrigen =="" || $codDestino == ""){
                return ["rpt"=>false, "msj"=>"Sucursal seleccionada inválida."];
            }   

            if ($codOrigen == $codDestino){
                return ["rpt"=>false, "msj"=>"No se puede realizar transferencia entre una MISMA sucursal."];
            }

            $productosCantidad = json_decode($JSONProductosCantidad);
            $this->beginTransaction();

            $codSucursalTransferencia = $this->consultarValor("SELECT COALESCE(MAX(cod_sucursal_transferencia)+1, 1) FROM sucursal_transferencia");
            $this->lastCodigo = $codSucursalTransferencia;
            $this->BITACORA_ON = true;

            $campos_valores = [
                    "cod_sucursal_transferencia"=>$codSucursalTransferencia,
                    "cod_sucursal_origen"=>$codOrigen,
                    "cod_sucursal_destino"=>$codDestino
                ];

            $this->insert("sucursal_transferencia", $campos_valores);

            //$this->setCodHistorial($this->consultarValor("SELECT COALESCE(MAX(cod_historial)+1, 1) FROM sucursal_producto_historial"));
            /*registrar transaccion*/
            $this->BITACORA_ON = false;

            $sqlObtenerSucursalProducto = "SELECT cod_sucursal, cod_producto, fecha_vencimiento, lote, precio_entrada, stock FROM sucursal_producto WHERE cod_sucursal_producto = :0";
            $this->setCodHistorial($this->consultarValor("SELECT COALESCE(MAX(cod_historial)+1, 1) FROM sucursal_producto_historial"));
            $fechaMovimiento =  date('Y-m-d');

            foreach ($productosCantidad as $key => $value) {
                $objSucursalProducto = $this->consultarFila($sqlObtenerSucursalProducto, [$value->cod_sucursal_producto]);
                /*
                $campos_valores = [
                    "cod_sucursal_transferencia"=>$codSucursalTransferencia,
                    "cod_sucursal_origen"=>$codOrigen,
                    "cod_sucursal_destino"=>$codDestino
                ];
                $this->insert("sucursal_transferencia", $campos_valores);
                */
                /*SALIDA */
                $campos_valores = [
                    "stock"=>($objSucursalProducto["stock"] - $value->cantidad_mover)
                ];

                $campos_valores_where = [
                    "cod_sucursal_producto"=>$value->cod_sucursal_producto
                ];

                $this->update("sucursal_producto", $campos_valores, $campos_valores_where);


                /*Registar el movimiento SALIDA*/
                $this->lastCodigo = $this->getCodHistorial();
                $this->BITACORA_ON = true;

                $campos_valores = [
                    "cod_historial"=> $this->getCodHistorial(),
                    "cod_sucursal"=>$objSucursalProducto["cod_sucursal"],
                    "cod_producto"=> $objSucursalProducto["cod_producto"],
                    "precio_salida"=> $objSucursalProducto["precio_entrada"],
                    "fecha_vencimiento"=>$objSucursalProducto["fecha_vencimiento"],
                    "lote"=>$objSucursalProducto["lote"],
                    "tipo_movimiento"=>"S",
                    "cantidad"=> $value->cantidad_mover,
                    "fecha_movimiento"=> $fechaMovimiento,
                    "cod_sucursal_transferencia"=>$codSucursalTransferencia
                ];

                $this->insert("sucursal_producto_historial", $campos_valores);
                $this->setCodHistorial($this->getCodHistorial()+1);
                $this->BITACORA_ON = false;

                /*ENTRADA*/
                /*vERIFICAR SI EXISTE EL PRODUCTOy el precio de entrada en la otra sucursal, si existe update, sino insert
                    luego registro historial
                */
                $existeRegistro = true;
                $sql = "SELECT cod_sucursal_producto, fecha_vencimiento, lote, stock FROM sucursal_producto WHERE cod_producto = :0 AND precio_entrada = :1 AND cod_sucursal = :2
                        AND fecha_vencimiento = :3 AND lote = :4";
                $objSucursalProductoDestino = $this->consultarFila($sql, [$objSucursalProducto["cod_producto"], $objSucursalProducto["precio_entrada"],$codDestino,
                             $objSucursalProducto["fecha_vencimiento"], $objSucursalProducto["lote"] ]);

                 if ($objSucursalProductoDestino == false){
                    $existeRegistro = false;
                    $nuevoStock = $value->cantidad_mover;
                } else {
                    $nuevoStock = $objSucursalProductoDestino["stock"] + $value->cantidad_mover;
                }

                if ($existeRegistro){
                    $campos_valores = [
                        "stock"=>$nuevoStock
                    ];

                    $campos_valores_where = [
                        "cod_sucursal_producto"=>$objSucursalProductoDestino["cod_sucursal_producto"]
                    ];

                    $this->update("sucursal_producto", $campos_valores, $campos_valores_where);
                } else {
                    $campos_valores = [
                        "cod_producto"=>$objSucursalProducto["cod_producto"],
                        "cod_sucursal"=>$codDestino,
                        "precio_entrada"=>$objSucursalProducto["precio_entrada"],
                        "fecha_vencimiento"=>$objSucursalProducto["fecha_vencimiento"],
                        "lote"=>$objSucursalProducto["lote"],
                        "stock"=>$nuevoStock
                    ];

                    $this->insert("sucursal_producto", $campos_valores);
                }

                /*Registar el movimiento ENTRADA*/
                $this->lastCodigo = $this->getCodHistorial();
                $this->BITACORA_ON = true;

                $campos_valores = [
                    "cod_historial"=> $this->getCodHistorial(),
                    "cod_sucursal"=>$codDestino,
                    "cod_producto"=> $objSucursalProducto["cod_producto"],
                    "precio_entrada"=> $objSucursalProducto["precio_entrada"],
                    "fecha_vencimiento"=>$objSucursalProducto["fecha_vencimiento"],
                    "lote"=>$objSucursalProducto["lote"],
                    "tipo_movimiento"=> "E",
                    "cantidad"=> $value->cantidad_mover,
                    "fecha_movimiento"=> $fechaMovimiento,
                    "cod_sucursal_transferencia"=>$codSucursalTransferencia
                ];

                $this->insert("sucursal_producto_historial", $campos_valores);   
                $this->setCodHistorial($this->getCodHistorial()+1);             

            }
            /*
                recorrer cada row cod_producto  cantidad_mover,
                obtener los datos del registro sucursal_producto

                registrar_movimiento S
                actualizar sucursal_producto S
                registrar_movimiento E
                actualizar sucursla_producto E
            */        
            $this->commit();

            return array("rpt"=>true,"msj"=>"Transferencia registrada con éxito.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function registrarMovimiento($fechaDesde, $fechaHasta) {
        $this->beginTransaction();
        try {     
            $esEntrada = $this->getTipoMovimiento() == "E";
            $existeRegistro = true;
            $sql = "SELECT cod_sucursal_producto, stock FROM sucursal_producto WHERE cod_producto = :0 AND precio_entrada = :1 AND cod_sucursal = :2 AND fecha_vencimiento = :3 AND lote = :4 ";
            $objSucursalProducto = $this->consultarFila($sql, [$this->getCodProducto(), $this->getPrecio(), $this->getCodSucursal(), $this->getFechaVencimiento(), $this->getLote()]);

            if ($objSucursalProducto == false){
                $existeRegistro = false;
                $nuevoStock = $this->getCantidad();
            } else {
                $nuevoStock = $objSucursalProducto["stock"] + ($this->getCantidad() * ($esEntrada ? 1 : -1));
            }

            if ($nuevoStock < 0){
                return ["rpt"=>false, "msj"=>"No hay suficiente stock para generar una SALIDA de producto."];
            }

            if ($existeRegistro){
                $campos_valores = [
                    "stock"=>$nuevoStock
                ];

                $campos_valores_where = [
                    "cod_sucursal_producto"=>$objSucursalProducto["cod_sucursal_producto"]
                ];

                $this->update("sucursal_producto", $campos_valores, $campos_valores_where);
            } else {

                $campos_valores = [
                    "cod_producto"=>$this->getCodProducto(),
                    "cod_sucursal"=>$this->getCodSucursal(),
                    "precio_entrada"=>$this->getPrecio(),
                    "fecha_vencimiento"=>$this->getFechaVencimiento(),
                    "lote"=>$this->getLote(),
                    "stock"=>$this->getCantidad()
                ];

                $this->insert("sucursal_producto", $campos_valores);
            }


            /*Registar el movimiento*/
            $this->setCodHistorial($this->consultarValor("SELECT COALESCE(MAX(cod_historial)+1, 1) FROM sucursal_producto_historial"));
            $this->lastCodigo = $this->getCodHistorial();
            $this->BITACORA_ON = true;

            $campos_valores = [
                "cod_historial"=> $this->getCodHistorial(),
                "cod_sucursal"=>$this->getCodSucursal(),
                "cod_producto"=> $this->getCodProducto(),
                "precio_".($esEntrada ? "entrada" : "salida")=> $this->getPrecio(),
                "fecha_vencimiento"=>$this->getFechaVencimiento(),
                "lote"=>$this->getLote(),
                "tipo_movimiento"=>$this->getTipoMovimiento(),
                "cantidad"=> $this->getCantidad(),
                "fecha_movimiento"=> date('Y-m-d')
            ];

            $this->insert("sucursal_producto_historial", $campos_valores);
            $this->commit();
            /*Consultar historial_productos*/
            include 'Almacen.clase.php';
            $objAlmacen = new Almacen();
            $objAlmacen->setCodSucursal($this->getCodSucursal());
            $objAlmacen->setFechaDesde($fechaDesde);
            $objAlmacen->setFechaHasta($fechaHasta);
            $historial_productos = $objAlmacen->getHistorialProductos()["data"];
            $obj_producto_sucursal = $objAlmacen->getProductoStockUno($this->getCodProducto(), $this->getPrecio(), $this->getFechaVencimiento(), $this->getLote())["data"];

            return array("rpt"=>true,"msj"=>"Movimiento registrado OK. Nuevo stock: ".$nuevoStock." del producto: ".$obj_producto_sucursal["producto"]. ", precio: ".$obj_producto_sucursal["precio"],
                                    "data"=>[   "obj_producto_sucursal"=>$obj_producto_sucursal,
                                                "historial_productos"=>$historial_productos]);
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function eliminarMovimiento($fechaDesde, $fechaHasta) {
        $this->beginTransaction();
        try {     

            /*SE RECIBE el cod_historial, desde este se obitne el producto, el precio y la sucursal */

            $sql = "SELECT cod_producto, cod_sucursal, IF(tipo_movimiento = 'E', precio_entrada, precio_salida) as precio,
                            cantidad, tipo_movimiento, fecha_vencimiento, lote
                    FROM sucursal_producto_historial 
                    WHERE cod_historial = :0";
            $existeRegistroHistorial = $this->consultarFila($sql, [$this->getCodHistorial()]);

            if ($existeRegistroHistorial == false){
                return ["rpt"=>false, "msj"=>"Movimiento inexistente."]; 
            }

            $this->setTipoMovimiento($existeRegistroHistorial["tipo_movimiento"]);
            $this->setCodProducto($existeRegistroHistorial["cod_producto"]);
            $this->setCodSucursal($existeRegistroHistorial["cod_sucursal"]);
            $this->setPrecio($existeRegistroHistorial["precio"]);
            $this->setCantidad($existeRegistroHistorial["cantidad"]);

            $this->setFechaVencimiento($existeRegistroHistorial["fecha_vencimiento"]);
            $this->setLote($existeRegistroHistorial["lote"]);

            $esEntrada = $this->getTipoMovimiento() == "E";

            $existeRegistro = true;
            $sql = "SELECT cod_sucursal_producto, stock, fecha_vencimiento, lote FROM sucursal_producto 
                        WHERE cod_producto = :0 AND precio_entrada = :1 AND cod_sucursal = :2 AND fecha_vencimiento = :3  AND lote = :4";
            $objSucursalProducto = $this->consultarFila($sql, [$this->getCodProducto(), $this->getPrecio(), $this->getCodSucursal(),
                                    $this->getFechaVencimiento(), $this->getLote()]);

            if ($objSucursalProducto == false){
                 return ["rpt"=>false, "msj"=>"Producto y precio inexistente en el almacén/sucursal."]; 
            } 

            if ($esEntrada && $objSucursalProducto["stock"] < $this->getCantidad()){
                /*Es Entrada: Signifca restaurará SALIDA*/
                return ["rpt"=>false, "msj"=>"No hay suficiente stock para realizar esta eliminación. Se desea evitar inconsistencias. Consulte al Administrador."];
            }

            $nuevoStock = $objSucursalProducto["stock"] + ($this->getCantidad() * ($esEntrada ? -1 : 1)); /*Si fue entrada, retirar. Si fue salida, aumentar*/

            $campos_valores = [
                "stock"=>$nuevoStock
            ];

            $campos_valores_where = [
                "cod_sucursal_producto"=>$objSucursalProducto["cod_sucursal_producto"]
            ];

            $this->update("sucursal_producto", $campos_valores, $campos_valores_where);

            /*Eliminar el movimiento*/
            $this->lastCodigo = $this->getCodHistorial();
            $this->BITACORA_ON = true;

            $campos_valores_where = [
                "cod_historial"=>$this->getCodHistorial()
            ];  

            $this->disable("sucursal_producto_historial", $campos_valores_where);
            $this->commit();
            /*Consultar historial_productos*/
            include 'Almacen.clase.php';
            $objAlmacen = new Almacen();
            $objAlmacen->setCodSucursal($this->getCodSucursal());
            $objAlmacen->setFechaDesde($fechaDesde);
            $objAlmacen->setFechaHasta($fechaHasta);
            $historial_productos = $objAlmacen->getHistorialProductos()["data"];
            $obj_producto_sucursal = $objAlmacen->getProductoStockUno($this->getCodProducto(), $this->getPrecio(), $this->getFechaVencimiento(), $this->getLote())["data"];

            return array("rpt"=>true,"msj"=>"Movimiento eliminado OK. Nuevo stock: ".$nuevoStock." del producto: ".$obj_producto_sucursal["producto"]. ", precio: ".$obj_producto_sucursal["precio"], 
                                    "data"=>[   "obj_producto_sucursal"=>$obj_producto_sucursal,
                                                "historial_productos"=>$historial_productos]);
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}