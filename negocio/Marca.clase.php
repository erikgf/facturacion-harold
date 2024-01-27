<?php

require_once '../datos/Conexion.clase.php';

class Marca extends Conexion {
    private $cod_marca;
    private $nombre;
    private $estado_mrcb;

    private $tbl = "marca";

    public function getCodMarca()
    {
        return $this->cod_marca;
    }
    
    public function setCodMarca($cod_marca)
    {
        $this->cod_marca = $cod_marca;
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
        $sql = "SELECT COUNT(nombre) > 0 FROM ".$this->tbl." WHERE upper(nombre) = upper(:0) AND cod_marca <>:1 AND estado_mrcb";
        return $this->consultarValor($sql, [$this->getNombre(), $this->getCodMarca()]);
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [
                "nombre"=>$this->getNombre()
                ];       

            if ($tipoAccion == "+"){
                $campos_valores["cod_marca"] = $this->getCodMarca();
            } 
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_marca"=>$this->getCodMarca()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];

        $this->lastCodigo = $this->getCodMarca();
        $this->BITACORA_ON = true;
        return $campos;
    }

    public function agregar() {
        try {            
            $this->beginTransaction();
            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe esta marca.");
            }

            $this->setCodMarca($this->consultarValor("SELECT COALESCE(MAX(cod_marca)+1, 1) FROM marca"));

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
                return array("rpt"=>false,"msj"=>"Ya existe esta marca.");
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
            $sql = "SELECT cod_marca, tc.nombre
                        FROM marca tc
                        WHERE tc.estado_mrcb
                        ORDER BY tc.nombre";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT nombre
                    FROM marca
                    WHERE cod_marca = :0";
            $resultado = $this->consultarFila($sql, $this->getCodMarca());
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function eliminar() {
        
        try { 
            $this->beginTransaction();

            $campos = $this->seter("-");
            $this->disable($this->tbl, $campos["valores_where"]);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha eliminado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerMarcas(){
        try{

            $sql = "SELECT cod_marca as codigo, nombre
                        FROM marca 
                        WHERE estado_mrcb
                        ORDER BY  nombre";
            $data = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}