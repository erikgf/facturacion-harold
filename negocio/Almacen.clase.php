<?php

require_once '../datos/Conexion.clase.php';

class Almacen extends Conexion {

    private $cod_sucursal;

    private $fecha_desde;
    private $fecha_hasta;

    public function getFechaDesde()
    {
        return $this->fecha_desde;
    }
    
    
    public function setFechaDesde($fecha_desde)
    {
        $this->fecha_desde = $fecha_desde;
        return $this;
    }

    public function getFechaHasta()
    {
        return $this->fecha_hasta;
    }
    
    
    public function setFechaHasta($fecha_hasta)
    {
        $this->fecha_hasta = $fecha_hasta;
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

    public function getHistorialProductos(){
         try {

            if ($this->getFechaDesde() == null){
                $fechaHoy = date('Y-m-d');
                $this->setFechaHasta($fechaHoy);
                $this->setFechaDesde($fechaHoy);
            }

            $sql = "SELECT h.cod_historial, h.cod_producto, 
                        COALESCE(CAST(h.precio_entrada as DECIMAL(10,2)),'-') as precio_entrada,
                        COALESCE(CAST(h.precio_salida as DECIMAL(10,2)),'-') as precio_salida, 
                        h.cantidad, 
                        cp.cod_tipo_categoria as cod_tipo,
                        p.cod_categoria_producto  as cod_categoria,
                        p.nombre as producto,
                        COALESCE( (SELECT pi.img_url FROM producto_img pi WHERE pi.cod_producto = p.cod_producto AND pi.numero_imagen = p.numero_imagen_principal),
                             'default_producto.jpg') as img_url,
                        DATE_FORMAT(h.fecha_movimiento,'%d/%m/%Y') as fecha_movimiento,
                        h.tipo_movimiento,
                        h.fecha_vencimiento,
                        h.lote,
                        IF(h.tipo_movimiento = 'E', 'ENTRADA', 'SALIDA') as movimiento,
                        IF(h.cod_transaccion IS NULL,'', IF(h.tipo_movimiento = 'E','COMPRA','VENTA')) as nota
                        FROM sucursal_producto_historial h
                        INNER JOIN producto p ON p.cod_producto = h.cod_producto AND p.estado_mrcb
                        INNER JOIN categoria_producto cp ON cp.cod_categoria_producto = p.cod_categoria_producto AND cp.estado_mrcb
                        WHERE 
                            h.estado_mrcb AND h.cod_sucursal = :0 AND
                            (fecha_movimiento BETWEEN :1 AND :2)
                        ORDER BY p.nombre";

            $historial_productos = $this->consultarFilas($sql, [$this->getCodSucursal(), 
                                                                $this->getFechaDesde(), $this->getFechaHasta()]);
            return array("rpt"=>true,"data"=>$historial_productos);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    private function getSQLProductoStock(){
        return   "SELECT cod_sucursal, sp.cod_producto, 
                        CAST(sp.precio_entrada as DECIMAL(10,2)) as precio, 
                        sp.stock,
                        cp.cod_tipo_categoria as cod_tipo,
                        p.cod_categoria_producto as cod_categoria,
                        p.nombre as producto,
                        sp.fecha_vencimiento, 
                        sp.lote
                        FROM sucursal_producto sp
                        INNER JOIN producto p ON p.cod_producto = sp.cod_producto AND p.estado_mrcb
                        INNER JOIN categoria_producto cp ON cp.cod_categoria_producto = p.cod_categoria_producto AND cp.estado_mrcb
                        WHERE sp.cod_sucursal = :0 ";
    }

    public function getProductoStockUno($cod_producto, $precio_entrada, $fecha_vencimiento, $lote){
        try {
            $sql = $this->getSQLProductoStock()." AND sp.cod_producto = :1 AND sp.precio_entrada = CAST(:2 AS DECIMAL) AND sp.fecha_vencimiento = :3 AND sp.lote = :4";
            return array("rpt"=>true,"data"=>$this->consultarFila($sql, [$this->getCodSucursal(), $cod_producto, $precio_entrada, $fecha_vencimiento, $lote]));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function getProductoStock(){
        try {

            $sql = $this->getSQLProductoStock()." ORDER BY p.nombre";
            return array("rpt"=>true,"data"=>$this->consultarFilas($sql, [$this->getCodSucursal()]));
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function getProductos(){
        try {

            $sql = "SELECT 
                        p.cod_producto, p.nombre as nombre_producto
                        FROM producto p
                        WHERE 
                            p.estado_mrcb
                        ORDER BY p.nombre";
            $lista_productos = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>$lista_productos);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


    public function obtenerDataInterfaz(){
        try {

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

            $sql  ="SELECT cod_sucursal, nombre FROM sucursal WHERE estado_mrcb AND ".$sqlSucursales." ORDER BY cod_sucursal";
            $sucursales = $this->consultarFilas($sql);

            $this->setCodSucursal($sucursales[0]["cod_sucursal"]);

            $sql = "SELECT tp.cod_tipo_categoria, tp.nombre
                        FROM tipo_categoria tp
                        WHERE tp.estado_mrcb
                        ORDER BY tp.nombre";
            $tipo = $this->consultarFilas($sql);

            $sql = "SELECT cp.cod_categoria_producto, cp.nombre, cp.cod_tipo_categoria
                        FROM categoria_producto cp
                        WHERE cp.estado_mrcb
                        ORDER BY cp.nombre";
            $categoria = $this->consultarFilas($sql);

            $producto_stock = $this->getProductoStock()["data"];

            $historial_productos = $this->getHistorialProductos()["data"];

            $lista_productos = $this->getProductos()["data"];

            return array("rpt"=>true,"data"=>["sucursales"=>$sucursales,"tipo"=>$tipo, "categoria"=>$categoria, 
                                                "producto_stock"=>$producto_stock, "historial_productos"=>$historial_productos,
                                                "lista_productos"=>$lista_productos]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDataProductosAlmacen(){
        try {

            $producto_stock = $this->getProductoStock()["data"];
            $historial_productos = $this->getHistorialProductos()["data"];
     
            return array("rpt"=>true,"data"=>["producto_stock"=>$producto_stock, 
                                                "historial_productos"=>$historial_productos]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDataReporteStock(){
        try{

            $sql = "SELECT cod_sucursal, nombre FROM sucursal WHERE estado_mrcb = 1";
            $sucursales = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>["sucursales"=>$sucursales]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function reporteAlmacenStock($codSucursal){
        try{
            $sqlWhere = "";
            $params =[];

            if ($codSucursal == ""){
                $sqlWhere .= " true";
            } else {
                $j = count($params);
                $sqlWhere .= " sp.cod_sucursal = :$j";
                array_push($params, $codSucursal);
            }

            $sql = "SELECT
                        p.codigo as codigo_producto,
                        p.nombre as producto,
                        sp.fecha_vencimiento,
                        sp.lote,
                        cp.nombre as categoria,
                        tc.nombre as tipo,
                        SUM(sp.stock) as stock,
                        s.nombre as sucursal
                        FROM sucursal_producto sp
                        INNER JOIN producto p ON p.cod_producto = sp.cod_producto AND p.estado_mrcb
                        INNER JOIN categoria_producto cp ON cp.cod_categoria_producto = p.cod_categoria_producto
                        INNER JOIN tipo_categoria tc ON tc.cod_tipo_categoria = cp.cod_tipo_categoria
                        INNER JOIN sucursal s ON s.cod_sucursal = sp.cod_sucursal
                        WHERE ".$sqlWhere."
                    GROUP BY sp.cod_producto, sp.fecha_vencimiento, sp.lote
                    ORDER BY p.codigo";

            $data = $this->consultarFilas($sql, $params);

            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerProductosTransferencia(){
        try{
            $params =[$this->getCodSucursal()];

            $sql = "SELECT
                        sp.cod_sucursal_producto as cod_producto,
                        CONCAT(p.codigo,' - ',p.nombre,' | LT:',IF(sp.lote ='','S/L', sp.lote),' - FV:',sp.fecha_vencimiento,' | S/',CAST(sp.precio_entrada AS DECIMAL(10,2))) as nombre_producto,
                        CAST(sp.precio_entrada AS DECIMAL(10,2)) as precio_entrada,
                        sp.stock
                        FROM sucursal_producto sp
                        LEFT JOIN producto p ON p.cod_producto = sp.cod_producto AND p.estado_mrcb
                        WHERE sp.cod_sucursal =  :0 AND sp.stock > 0
                    ORDER BY p.codigo";
            $data = $this->consultarFilas($sql, $params);

            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


    public function reporteKardex($codSucursal, $codProducto = NULL){
        try{
            $sqlWhere = "";
            $params =[];

            if ($codSucursal == ""){
                $sqlWhere .= " true";
            } else {
                $j = count($params);
                $sqlWhere .= " sp.cod_sucursal = :$j ";
                array_push($params, $codSucursal);
            }


            if (!($codProducto  == NULL || $codProducto == "")){
                $sqlWhere .= " AND sp.cod_producto = ".$codProducto." ";
            }

            $sql = "SELECT
                        sp.fecha_movimiento,
                        s.nombre as sucursal,
                        p.codigo as codigo_producto,
                        p.nombre as producto,
                        sp.fecha_vencimiento,
                        sp.lote,
                        COALESCE(sp.precio_entrada, 0.00) as precio_entrada,
                        COALESCE(sp.precio_salida, 0.00) as precio_salida,
                        IF(tipo_movimiento = 'E', cantidad, cantidad * -1) as cantidad,
                        (CASE tipo_movimiento WHEN 'E' THEN 'INGRESO' ELSE 'SALIDA' END) as movimiento,
                        (COALESCE(sp.precio_entrada, sp.precio_salida) * IF(tipo_movimiento = 'E', cantidad, cantidad * -1)) as totalizado,
                        cp.nombre as categoria,
                        tc.nombre as tipo
                        FROM sucursal_producto_historial sp
                        INNER JOIN producto p ON p.cod_producto = sp.cod_producto AND p.estado_mrcb
                        INNER JOIN categoria_producto cp ON cp.cod_categoria_producto = p.cod_categoria_producto
                        INNER JOIN tipo_categoria tc ON tc.cod_tipo_categoria = cp.cod_tipo_categoria
                        INNER JOIN sucursal s ON s.cod_sucursal = sp.cod_sucursal
                        WHERE ".$sqlWhere." AND sp.estado_mrcb                    
                    ORDER BY sp.fecha_movimiento , cod_historial ";

            $data = $this->consultarFilas($sql, $params);

            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
        

    public function obtenerDataReporteKardex(){
        try {

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

            $sql  ="SELECT cod_sucursal, nombre FROM sucursal WHERE estado_mrcb AND ".$sqlSucursales." ORDER BY cod_sucursal";
            $sucursales = $this->consultarFilas($sql);

            $lista_productos = $this->getProductos()["data"];

            return array("rpt"=>true,"data"=>["sucursales"=>$sucursales,"lista_productos"=>$lista_productos]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
    
}