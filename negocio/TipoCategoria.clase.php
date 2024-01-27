<?php

require_once '../datos/Conexion.clase.php';

class TipoCategoria extends Conexion {
    private $cod_tipo_categoria;
    private $nombre;
    private $descripcion;
    private $estado_mrcb;

    private $tbl = "tipo_categoria";

    public function getCodTipoCategoria()
    {
        return $this->cod_tipo_categoria;
    }
    
    public function setCodTipoCategoria($cod_tipo_categoria)
    {
        $this->cod_tipo_categoria = $cod_tipo_categoria;
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
        $sql = "SELECT COUNT(nombre) > 0 FROM ".$this->tbl." WHERE upper(nombre) = upper(:0) AND cod_tipo_categoria <>:1 AND estado_mrcb";
        return $this->consultarValor($sql, [$this->getNombre(), $this->getCodTipoCategoria()]);
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [
                "nombre"=>$this->getNombre(),
                "descripcion"=>$this->getDescripcion()
                ];       

            if ($tipoAccion == "+"){
                $campos_valores["cod_tipo_categoria"] = $this->getCodTipoCategoria();
            } 
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_tipo_categoria"=>$this->getCodTipoCategoria()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];

        $this->lastCodigo = $this->getCodTipoCategoria();
        $this->BITACORA_ON = true;
        return $campos;
    }

    public function agregar() {
        $this->beginTransaction();
        try {            
            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe esta tipo de producto.");
            }

            $this->setCodTipoCategoria($this->consultarValor("SELECT COALESCE(MAX(cod_tipo_categoria)+1, 1) FROM tipo_categoria"));

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
            $sql = "SELECT cod_tipo_categoria, tc.nombre, descripcion
                        FROM tipo_categoria tc
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
            $sql = "SELECT nombre, descripcion
                    FROM tipo_categoria
                    WHERE cod_tipo_categoria = :0";
            $resultado = $this->consultarFila($sql, $this->getCodTipoCategoria());
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

    public function obtenerTipoCategorias(){
        try{

            $sql = "SELECT cod_tipo_categoria as codigo, nombre
                        FROM tipo_categoria 
                        WHERE estado_mrcb
                        ORDER BY  nombre";
            $data = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}