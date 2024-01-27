<?php

require_once '../datos/Conexion.clase.php';

class TipoProducto extends Conexion {
    private $cod_tipo_producto;
    private $nombre;
    private $descripcion;
    private $estado_mrcb;

    private $tbl = "tipo_producto";

    public function getCodTipoProducto()
    {
        return $this->cod_tipo_producto;
    }
    
    public function setCodTipoProducto($cod_tipo_producto)
    {
        $this->cod_tipo_producto = $cod_tipo_producto;
        return $this;
    }

    public function getNombre()
    {
        return $this->nombre;
    }
    
    
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }
    
    
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
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

    private function verificarRepetidoAgregar(){
        $sql = "SELECT COUNT(nombre) > 0 FROM ".$this->tbl." WHERE upper(nombre) = upper(:0) AND estado_mrcb";
        return $this->consultarValor($sql, [$this->getNombre()]);
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(nombre) > 0 FROM ".$this->tbl." WHERE upper(nombre) = upper(:0) AND cod_tipo_producto <>:1 AND estado_mrcb";
        return $this->consultarValor($sql, [$this->getNombre(), $this->getCodTipoProducto()]);
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [
                "nombre"=>$this->getNombre(),
                "descripcion"=>$this->getDescripcion(),
                ];       

            if ($tipoAccion == "+"){
                $campos_valores["cod_tipo_producto"] = $this->getCodTipoProducto();
            } 
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_tipo_producto"=>$this->getCodTipoProducto()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];

        $this->lastCodigo = $this->getCodTipoProducto();
        $this->BITACORA_ON = true;
        return $campos;
    }

    public function agregar() {
        $this->beginTransaction();
        try {            
            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe esta tipo de producto.");
            }

            $this->setCodTipoProducto($this->consultarValor("SELECT COALESCE(MAX(cod_tipo_producto)+1, 1) FROM tipo_producto"));

            $campos = $this->seter("+");

            $this->insert($this->tbl, $campos["valores"]);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha agregado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function editar() {
        $this->beginTransaction();
        try { 
            if ($this->verificarRepetidoEditar()){
                return array("rpt"=>false,"msj"=>"Ya existe esta tipo de producto.");
            }
            $campos = $this->seter("*");

            $this->update($this->tbl, $campos["valores"], $campos["valores_where"]);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha actualizado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function listar(){
        try {
            $sql = "SELECT cod_tipo_producto, nombre, descripcion
                        FROM tipo_producto
                        WHERE estado_mrcb
                        ORDER BY nombre";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT nombre, descripcion 
                    FROM tipo_producto
                    WHERE cod_tipo_producto = :0";
            $resultado = $this->consultarFila($sql, $this->getCodTipoProducto());
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
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

    public function obtenerTipoProductos(){
        try{

            $sql = "SELECT nombre
                        FROM tipo_producto 
                        WHERE estado_mrcb
                        ORDER BY  nombre";
            $data = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}