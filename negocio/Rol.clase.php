<?php

require_once '../datos/Conexion.clase.php';

class Rol extends Conexion {
    private $cod_rol;
    private $descripcion;
    private $estado_mrcb;

    private $tbl = "rol";

    public function getCodRol()
    {
        return $this->cod_rol;
    }
    
    
    public function setCodRol($cod_rol)
    {
        $this->cod_rol = $cod_rol;
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
        $sql = "SELECT COUNT(descripcion) > 0 FROM rol WHERE upper(descripcion) = upper(:0) AND estado_mrcb";
        return $this->consultarValor($sql, [$this->getDescripcion()]);
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(descripcion) > 0 FROM rol WHERE upper(descripcion) = upper(:0) AND cod_rol <>:1 AND estado_mrcb";
        return $this->consultarValor($sql, [$this->getDescripcion(), $this->getCodRol()]);
    }


    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [
                "descripcion"=>$this->getDescripcion(),
                ];        
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_rol"=>$this->getCodRol()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];

        $this->lastCodigo = $this->getCodRol();
        $this->BITACORA_ON = true;
        return $campos;
    }

    public function agregar() {
        $this->beginTransaction();
        try {            
            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe este rol.");
            }

            $this->setCodCategoriaProducto($this->consultarValor("SELECT COALESCE(MAX(cod_rol)+1, 1) FROM rol"));

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
                return array("rpt"=>false,"msj"=>"Ya existe este rol.");
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

    public function listar(){
        try {
            $sql = "SELECT c.cod_rol, c.descripcion
                        FROM rol c
                        WHERE c.estado_mrcb
                        ORDER BY c.descripcion";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT cod_rol as codigo, descripcion FROM rol WHERE cod_rol = :0";
            $resultado = $this->consultarFila($sql, $this->getCodRol());
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerRoles(){
        try{

            $sql = "SELECT cod_rol as codigo, descripcion  FROM rol WHERE estado_mrcb ORDER BY descripcion";
            $r = $this->consultarFilas($sql);
            return array("rpt"=>true,"r"=>$r);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}