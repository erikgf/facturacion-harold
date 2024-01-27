<?php

require_once '../datos/Conexion.clase.php';
//require_once 'util/Funciones.clase.php';

class Descuento extends Conexion {
    private $cod_descuento;
    private $codigo_generado;
    private $tipo_descuento;
    private $monto_descuento;
    private $estado_mrcb;

    private $tbl = "descuento";

    public function getCodDescuento()
    {
        return $this->cod_descuento;
    }
    
    public function setCodDescuento($cod_descuento)
    {
        $this->cod_descuento = $cod_descuento;
        return $this;
    }

    public function getCodigoGenerado()
    {
        return $this->codigo_generado;
    }
    
    
    public function setCodigoGenerado($codigo_generado)
    {
        $this->codigo_generado = $codigo_generado;
        return $this;
    }

    public function getTipoDescuento()
    {
        return $this->tipo_descuento;
    }
    
    
    public function setTipoDescuento($tipo_descuento)
    {
        $this->tipo_descuento = $tipo_descuento;
        return $this;
    }

    public function getMontoDescuento()
    {
        return $this->monto_descuento;
    }
    
    
    public function setMontoDescuento($monto_descuento)
    {
        $this->monto_descuento = $monto_descuento;
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

    private function verificarRepetido(){
        $sql = "SELECT COUNT(codigo_generado) > 0 FROM ".$this->tbl." WHERE codigo_generado = :0 AND estado_mrcb";
        return $this->consultarValor($sql, [$this->getCodigoGenerado()]);
    }
/*
    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(nombre) > 0 FROM ".$this->tbl." WHERE upper(nombre) = upper(:0) AND cod_descuento <>:1";
        return $this->consultarValor($sql, [$this->getNombre(), $this->getCodDescuento()]);
    }
    */

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){

            $campos_valores = [
                "tipo_descuento"=>$this->getTipoDescuento(),
                "monto_descuento"=>$this->getMontoDescuento(),
                ];  

            if ($tipoAccion == "+"){

                $codigoValido = false;
                while(!$codigoValido){
                   $this->setCodigoGenerado(Funciones::randomString(6));
                   $codigoValido = !$this->verificarRepetido();
                }

                $campos_valores["cod_descuento"] = $this->getCodDescuento();
                $campos_valores["codigo_generado"] = $this->getCodigoGenerado();

            }

        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_descuento"=>$this->getCodDescuento()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];

        $this->lastCodigo = $this->getCodDescuento();
        $this->BITACORA_ON = true;
        return $campos;
    }

    public function agregar() {
        $this->beginTransaction();
        try {    
            $this->setCodDescuento($this->consultarValor("SELECT COALESCE(MAX(cod_descuento)+1, 1) FROM descuento"));

            $campos = $this->seter("+");

            $this->insert($this->tbl, $campos["valores"]);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha agregado exitosamente. \nCódigo: ".$this->getCodigoGenerado());
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function editar() {
        $this->beginTransaction();
        try { 
            $campos = $this->seter("*");

            $this->update($this->tbl, $campos["valores"], $campos["valores_where"]);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha actualizado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function eliminar() {
        $this->beginTransaction();
        try { 

            $campos = $this->seter("-");
            $this->disable($this->tbl, $campos["valores_where"]);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha eliminado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function listar($codUsado){
        try {
            if ($codUsado == "*"){
                $sqlWhere = " true ";
            } else {
                $sqlWhere = " estado_uso = ".$codUsado;
            }

            $sql = "SELECT 
                        cod_descuento,
                        codigo_generado, 
                        IF (tipo_descuento = 'P', 'PORCENTAJE', 'MONTO FIJO') as tipo_descuento, 
                        monto_descuento, 
                        COALESCE( CONCAT(p.nombres,' ',p.apellidos),'-') as usuario_uso,
                        DATE_FORMAT(fecha_hora_uso,'%d/%m/%Y') as fecha,
                        DATE_FORMAT(fecha_hora_uso,'%h:%m:%s %r') as hora,
                        IF (estado_uso = '1', 'USADO', 'NO USADO') as estado,
                        IF (estado_uso = '1', 'danger', 'success') as color_estado
                        FROM descuento d
                        LEFT JOIN personal p ON d.usuario_uso = p.cod_personal
                        WHERE d.estado_mrcb AND ".$sqlWhere."
                        ORDER BY codigo_generado";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT tipo_descuento, monto_descuento
                    FROM descuento
                    WHERE cod_descuento = :0";
            $resultado = $this->consultarFila($sql, $this->getCodDescuento());
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

   
    public function obtenerDescuentoVenta(){
        try{
            $sql = "SELECT cod_descuento, tipo_descuento, monto_descuento,
                            IF(tipo_descuento = 'P', CONCAT(monto_descuento,'%'), CONCAT('S/ ',monto_descuento)) as rotulo_descuento
                        FROM descuento 
                        WHERE BINARY codigo_generado = :0 AND estado_uso = 0 AND estado_mrcb";
            $data = $this->consultarFila($sql, $this->getCodigoGenerado());
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}