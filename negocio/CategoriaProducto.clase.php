<?php

require_once '../datos/Conexion.clase.php';

class CategoriaProducto extends Conexion {
    private $cod_categoria_producto;
    private $nombre;
    private $descripcion;
    private $cod_tipo_categoria;
    private $estado_mrcb;

    private $tbl = "categoria_producto";


    public function getCodCategoriaProducto()
    {
        return $this->cod_categoria_producto;
    }
    
    
    public function setCodCategoriaProducto($cod_categoria_producto)
    {
        $this->cod_categoria_producto = $cod_categoria_producto;
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

    public function getCodTipoCategoria()
    {
        return $this->cod_tipo_categoria;
    }
    
    
    public function setCodTipoCategoria($cod_tipo_categoria)
    {
        $this->cod_tipo_categoria = $cod_tipo_categoria;
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
        $sql = "SELECT COUNT(nombre) > 0 FROM ".$this->tbl." WHERE upper(nombre) = upper(:0) AND cod_categoria_producto <>:1 AND estado_mrcb";
        return $this->consultarValor($sql, [$this->getNombre(), $this->getCodCategoriaProducto()]);
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [
                "nombre"=>$this->getNombre(),
                "descripcion"=>$this->getDescripcion(),
                "cod_tipo_categoria"=>$this->getCodTipoCategoria()
                ];        

            if ($tipoAccion == "+"){
                $campos_valores["cod_categoria_producto"] = $this->getCodCategoriaProducto();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_categoria_producto"=>$this->getCodCategoriaProducto()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];

        $this->lastCodigo = $this->getCodCategoriaProducto();
        $this->BITACORA_ON = true;
        return $campos;
    }

    public function agregar() {
        $this->beginTransaction();
        try {            
            if ($this->verificarRepetidoAgregar()){
                return array("rpt"=>false,"msj"=>"Ya existe esta categoría de producto.");
            }

            $this->setCodCategoriaProducto($this->consultarValor("SELECT COALESCE(MAX(cod_categoria_producto)+1, 1) FROM categoria_producto"));

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
                return array("rpt"=>false,"msj"=>"Ya existe esta categoría de producto.");
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
            $sql = "SELECT cod_categoria_producto, cp.nombre, cp.descripcion, tc.nombre as tipo_categoria
                        FROM categoria_producto cp
                        INNER JOIN tipo_categoria tc ON tc.cod_tipo_categoria = cp.cod_tipo_categoria
                        WHERE cp.estado_mrcb
                        ORDER BY cp.nombre";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT nombre, descripcion, cod_tipo_categoria
                    FROM categoria_producto
                    WHERE cod_categoria_producto = :0";
            $resultado = $this->consultarFila($sql, $this->getCodCategoriaProducto());
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerCategoriaProductos(){
        try{

            $sql = "SELECT nombre
                        FROM categoria_producto 
                        WHERE estado_mrcb AND cod_tipo_categoria = :0
                        ORDER BY  nombre";
            $data = $this->consultarFilas($sql, $this->getCodTipoCategoria());
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}