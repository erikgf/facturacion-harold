<?php

require_once '../datos/Conexion.clase.php';

class Transaccion extends Conexion {
    private $cod_transaccion;
    private $cod_tipo_documento;
    private $cod_tipo_comprobante;
    private $serie;
    private $correlativo;
    private $cod_sucursal;

    private $fecha_transaccion;
    private $estado;

    protected $tbl = "transaccion";

    public function getCodTransaccion()
    {
        return $this->cod_transaccion;
    }
    
    
    public function setCodTransaccion($cod_transaccion)
    {
        $this->cod_transaccion = $cod_transaccion;
        return $this;
    }

    public function getCodTipoComprobante()
    {
        return $this->cod_tipo_comprobante;
    }
    
    
    public function setCodTipoComprobante($cod_tipo_comprobante)
    {
        $this->cod_tipo_comprobante = $cod_tipo_comprobante;
        return $this;
    }

    public function getSerie()
    {
        return $this->serie;
    }
    
    
    public function setSerie($serie)
    {
        $this->serie = $serie;
        return $this;
    }

    public function getCorrelativo()
    {
        return $this->correlativo;
    }
    
    
    public function setCorrelativo($correlativo)
    {
        $this->correlativo = $correlativo;
        return $this;
    }

    public function getCodTipoDocumento()
    {
        return $this->cod_tipo_documento;
    }
    
    
    public function setCodTipoDocumento($cod_tipo_documento)
    {
        $this->cod_tipo_documento = $cod_tipo_documento;
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

    public function getFechaTransaccion()
    {
        return $this->fecha_transaccion;
    }
    
    
    public function setFechaTransaccion($fecha_transaccion)
    {
        $this->fecha_transaccion = $fecha_transaccion;
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

    public function obtenerCotizacionDataPDF()
    {
        try{
            $sql = "SELECT  
                    t.cod_tipo_comprobante as tipo_comprobante,
                    CONCAT(tcomp.abrev,t.serie) as serie,
                    LPAD(t.correlativo,6,'0') as correlativo,
                    CONCAT(c.nombres,' ',COALESCE(c.apellidos,'')) as nombre_cliente,
                    c.direccion as direccion_cliente,
                    COALESCE(numero_contacto, celular) as numero_contacto,
                    DATE_FORMAT(t.fecha_transaccion,'%d-%m-%Y') as fecha_emision,
                    tdoc.abrev as tipo_documento,
                    tdoc.cod_tipo_documento as cod_tipo_documento,
                    c.numero_documento as numero_documento,
                    tm.abrev as moneda,
                    '18.00' AS porcentaje_igv,
                    v.subtotal,
                    v.igv,
                    v.total,
                    condicion_delivery,
                    condicion_dias_entrega,
                    condicion_dias_validez,
                    condicion_dias_credito,
                    cta_bcp,
                    cta_bcp_cci,
                    COALESCE(correo,'-') as correo
                    FROM transaccion t
                    INNER JOIN cotizacion v ON t.cod_transaccion = v.cod_transaccion
                    INNER JOIN tipo_comprobante tcomp ON tcomp.cod_tipo_comprobante = t.cod_tipo_comprobante
                    INNER JOIN tipo_documento tdoc ON t.cod_tipo_documento = tdoc.cod_tipo_documento
                    INNER JOIN tipo_moneda tm ON tm.cod_tipo_moneda = v.cod_tipo_moneda
                    INNER JOIN cliente c ON c.cod_cliente = v.cod_cliente
                    WHERE t.estado = 1 AND t.cod_transaccion = :0";
            $cabecera = $this->consultarFila($sql, [$this->getCodTransaccion()]);

            $sql = "SELECT 
                    item,
                    p.nombre as nombre_producto,
                    m.nombre as marca,
                    vd.precio_unitario, 
                    cantidad_item,
                    (cantidad_item * vd.precio_unitario) as subtotal,
                    igv as monto_igv,
                    um.codigo_ece as unidad_medida,
                    COALESCE(fecha_vencimiento,'') as fecha_vencimiento,
                    COALESCE(lote,'') as lote
                    FROM cotizacion_detalle vd
                    INNER JOIN cotizacion v ON v.cod_cotizacion = vd.cod_cotizacion
                    INNER JOIN unidad_medida um ON um.cod_unidad_medida = vd.cod_unidad_medida
                    INNER JOIN producto p ON p.cod_producto = vd.cod_producto
                    INNER JOIN marca m ON m.cod_marca = p.cod_marca
                    WHERE v.cod_transaccion = :0
                    ORDER BY item";

            $detalle = $this->consultarFilas($sql, [$this->getCodTransaccion()]);

            return ["rpt"=>true, "data"=>["cabecera"=>$cabecera, "detalle"=>$detalle]];
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
    
    

}