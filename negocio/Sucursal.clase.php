<?php

require_once '../datos/Conexion.clase.php';

class Sucursal extends Conexion {
    private $cod_sucursal;
    private $nombre;
    private $direccion;
    private $telefono;
    private $estado_mrcb;

    private $tbl = "sucursal";

    public function getCodSucursal()
    {
        return $this->cod_sucursal;
    }
    
    public function setCodSucursal($cod_sucursal)
    {
        $this->cod_sucursal = $cod_sucursal;
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

    public function getDireccion()
    {
        return $this->direccion;
    }
    
    
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
        return $this;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }
    
    
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
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
        $sql = "SELECT COUNT(nombre) > 0 FROM sucursal WHERE upper(nombre) = upper(:0) AND estado_mrcb";
        return $this->consultarValor($sql, [$this->getNombre()]);
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(nombre) > 0 FROM sucursal WHERE upper(nombre) = upper(:0) AND cod_sucursal <>:1 AND estado_mrcb";
        return $this->consultarValor($sql, [$this->getNombre(), $this->getCodSucursal()]);
    }


    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [
                "nombre"=>$this->getNombre(),
                "direccion"=>$this->getDireccion(),
                "telefono"=>$this->getTelefono()
                ];        

            if ($tipoAccion == "+"){
                $campos_valores["cod_sucursal"] = $this->getCodSucursal();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_sucursal"=>$this->getCodSucursal()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];

        $this->lastCodigo = $this->getCodSucursal();
        $this->BITACORA_ON = true;
        return $campos;
    }

    public function agregar() {
        $this->beginTransaction();
        try {            
            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe este sucursal.");
            }

            $this->setCodSucursal($this->consultarValor("SELECT COALESCE(MAX(cod_sucursal)+1, 1) FROM sucursal"));

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
                return array("rpt"=>false,"msj"=>"Ya existe este sucursal.");
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
            $sql = "SELECT c.cod_sucursal,  nombre, direccion, telefono 
                        FROM sucursal c
                        WHERE c.estado_mrcb AND c.cod_sucursal <> 0 
                        ORDER BY c.nombre";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT cod_sucursal as codigo, nombre, direccion, telefono FROM sucursal WHERE cod_sucursal = :0";
            $resultado = $this->consultarFila($sql, $this->getCodSucursal());
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerSucursales(){
        try{

            $sql = "SELECT cod_sucursal as codigo, nombre  FROM sucursal WHERE estado_mrcb AND cod_sucursal <> 0 ORDER BY nombre";
            $r = $this->consultarFilas($sql);
            return array("rpt"=>true,"r"=>$r);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}