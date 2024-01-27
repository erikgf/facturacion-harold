<?php

require_once '../datos/Conexion.clase.php';

class Proveedor extends Conexion {
    private $cod_proveedor;
    private $tipo_documento;
    private $numero_documento;
    private $razon_social;
    private $direccion;
    private $correo;
    private $celular_contacto;
    private $nombre_contacto;
    private $estado_mrcb;

    private $tbl = "proveedor";

    public function getCodProveedor()
    {
        return $this->cod_proveedor;
    }
    
    
    public function setCodProveedor($cod_proveedor)
    {
        $this->cod_proveedor = $cod_proveedor;
        return $this;
    }

    public function getTipoDocumento()
    {
        return $this->tipo_documento;
    }
    
    
    public function setTipoDocumento($tipo_documento)
    {
        $this->tipo_documento = $tipo_documento;
        return $this;
    }

    public function getNumeroDocumento()
    {
        return $this->numero_documento;
    }
    
    
    public function setNumeroDocumento($numero_documento)
    {
        $this->numero_documento = $numero_documento;
        return $this;
    }

    public function getRazonSocial()
    {
        return $this->razon_social;
    }
    
    
    public function setRazonSocial($razon_social)
    {
        $this->razon_social = $razon_social;
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

    public function getCorreo()
    {
        return $this->correo;
    }
    
    
    public function setCorreo($correo)
    {
        $this->correo = $correo;
        return $this;
    }

    public function getCelularContacto()
    {
        return $this->celular_contacto;
    }
    
    
    public function setCelularContacto($celular_contacto)
    {
        $this->celular_contacto = $celular_contacto;
        return $this;
    }

    public function getNombreContacto()
    {
        return $this->nombre_contacto;
    }
    
    
    public function setNombreContacto($nombre_contacto)
    {
        $this->nombre_contacto = $nombre_contacto;
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
        $sql = "SELECT COUNT(numero_documento) > 0 FROM ".$this->tbl." WHERE numero_documento = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getNumeroDocumento()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Número documento ya existente."];
        }

        $sql = "SELECT COUNT(correo) > 0 FROM ".$this->tbl." WHERE correo = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getCorreo()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Correo ya existente."];
        }

        $sql = "SELECT COUNT(razon_social) > 0 FROM ".$this->tbl." WHERE razon_social = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getRazonSocial()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Razón social de proveedor ya existente."];
        }

        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(numero_documento) > 0 FROM ".$this->tbl." WHERE numero_documento = :0  AND cod_proveedor <>:1 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getNumeroDocumento(),$this->getCodProveedor()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Número documento ya existente."];
        }

        $sql = "SELECT COUNT(correo) > 0 FROM ".$this->tbl." WHERE correo = :0  AND cod_proveedor <>:1 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getCorreo(),$this->getCodProveedor()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Correo ya existente."];
        }

        $sql = "SELECT COUNT(razon_social) > 0 FROM ".$this->tbl." WHERE razon_social = :0 AND cod_proveedor <>:1 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getRazonSocial(),$this->getCodProveedor()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Nombre de proveedor ya existente."];
        }

        return ["r"=>true, "msj"=>""];
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [
                "tipo_documento"=>$this->getTipoDocumento(),
                "numero_documento"=>$this->getNumeroDocumento(),
                "razon_social"=>strtoupper($this->getRazonSocial()),
                "correo"=>$this->getCorreo(),
                "direccion"=>strtoupper($this->getDireccion()),
                "celular_contacto"=>$this->getCelularContacto(),
                "nombre_contacto"=>strtoupper($this->getNombreContacto())
                ];        


            if ($tipoAccion == "+"){
                $campos_valores["cod_proveedor"] = $this->getCodProveedor();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_proveedor"=>$this->getCodProveedor()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $this->lastCodigo = $this->getCodProveedor();
        $this->BITACORA_ON = true;
        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];
        return $campos;
    }

    public function agregar() {
        $this->beginTransaction();
        try {         
            $objVerificar = $this->verificarRepetidoAgregar();
            if (!$objVerificar["r"]){
                return array("rpt"=>false,"msj"=>$objVerificar["msj"]);
            }
            $this->setCodProveedor($this->consultarValor("SELECT COALESCE(MAX(cod_proveedor)+1, 1) FROM proveedor"));
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
            $objVerificar = $this->verificarRepetidoEditar();
            if (!$objVerificar["r"]){
                return array("rpt"=>false,"msj"=>$objVerificar["msj"]);
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
            $sql = "SELECT p.cod_proveedor,p.razon_social, td.abrev as tipo_documento, numero_documento, direccion, correo,
                        celular_contacto, nombre_contacto
                        FROM proveedor p
                        INNER JOIN tipo_documento td ON td.cod_tipo_documento = p.tipo_documento
                        WHERE p.estado_mrcb
                        ORDER BY p.razon_social";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT  razon_social, tipo_documento, numero_documento, correo, 
                        direccion,
                        celular_contacto, nombre_contacto
                    FROM proveedor p
                    WHERE p.cod_proveedor = :0";
            $resultado = $this->consultarFila($sql, $this->getCodProveedor());
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

    public function obtenerProveedors(){
        try{

            $sql = "SELECT razon_social, celular_contacto  
                        FROM proveedor p
                        WHERE p.estado_mrcb
                        ORDER BY p.razon_social";
            $data = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}