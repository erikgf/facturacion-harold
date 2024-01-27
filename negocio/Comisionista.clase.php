<?php

require_once '../datos/Conexion.clase.php';

class Comisionista extends Conexion {
    private $cod_comisionista;
    private $numero_documento;
    private $nombres;
    private $correo;
    private $celular;
    private $estado_mrcb;

    private $productos_comisionista;

    private $tbl = "comisionista";

    public function getCodComisionista()
    {
        return $this->cod_comisionista;
    }
    
    
    public function setCodComisionista($cod_comisionista)
    {
        $this->cod_comisionista = $cod_comisionista;
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

    public function getNombres()
    {
        return $this->nombres;
    }
    
    
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;
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

    public function getCelular()
    {
        return $this->celular;
    }
    
    
    public function setCelular($celular)
    {
        $this->celular = $celular;
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

    public function getProductosComisionista()
    {
        return $this->productos_comisionista;
    }
    
    
    public function setProductosComisionista($productos_comisionista)
    {
        $this->productos_comisionista = $productos_comisionista;
        return $this;
    }

    private function verificarRepetidoAgregar(){
        $sql = "SELECT COUNT(numero_documento) > 0 FROM ".$this->tbl." WHERE numero_documento = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getNumeroDocumento()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"DNI ya existente."];
        }

        $sql = "SELECT COUNT(correo) > 0 FROM ".$this->tbl." WHERE correo = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getCorreo()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Correo ya existente."];
        }

        $sql = "SELECT COUNT(celular) > 0 FROM ".$this->tbl." WHERE celular = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getCelular()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Celular ya existente."];
        }

        $sql = "SELECT COUNT(nombres) > 0 FROM ".$this->tbl." WHERE nombres = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getNombres()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Nombre de comisionista ya existente."];
        }

        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(numero_documento) > 0 FROM ".$this->tbl." WHERE numero_documento = :0  AND cod_comisionista <>:1";
        $repetido = $this->consultarValor($sql, [$this->getNumeroDocumento(),$this->getCodComisionista()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Número documento ya existente."];
        }

        $sql = "SELECT COUNT(correo) > 0 FROM ".$this->tbl." WHERE correo = :0  AND cod_comisionista <>:1";
        $repetido = $this->consultarValor($sql, [$this->getCorreo(),$this->getCodComisionista()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Correo ya existente."];
        }

        $sql = "SELECT COUNT(celular) > 0 FROM ".$this->tbl." WHERE celular = :0  AND cod_comisionista <>:1";
        $repetido = $this->consultarValor($sql, [$this->getCelular(),$this->getCodComisionista()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Celular ya existente."];
        }

        $sql = "SELECT COUNT(nombres) > 0 FROM ".$this->tbl." WHERE nombres = :0 AND cod_comisionista <>:1";
        $repetido = $this->consultarValor($sql, [$this->getNombres(),$this->getCodComisionista()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Nombre de comisionista ya existente."];
        }

        return ["r"=>true, "msj"=>""];
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [
                "numero_documento"=>$this->getNumeroDocumento(),
                "nombres"=>$this->getNombres(),
                "correo"=>$this->getCorreo(),
                "celular"=>$this->getCelular()
                ];        

            if ($tipoAccion == "+"){
                $campos_valores["cod_comisionista"] = $this->getCodComisionista();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_comisionista"=>$this->getCodComisionista()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $this->lastCodigo = $this->getCodComisionista();
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
            $this->setCodComisionista($this->consultarValor("SELECT COALESCE(MAX(cod_comisionista)+1, 1) FROM comisionista"));
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
            $sql = "SELECT c.cod_comisionista,c.nombres, 
                        COALESCE(numero_documento,'-') as numero_documento, 
                        correo, celular 
                        FROM comisionista c
                        WHERE c.estado_mrcb
                        ORDER BY c.nombres";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT c.nombres, numero_documento, correo,celular
                    FROM comisionista c
                    WHERE c.cod_comisionista = :0";
            $resultado = $this->consultarFila($sql, $this->getCodComisionista());
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

    public function obtenerComisionistas(){
        try{
            $sql = "SELECT c.nombres, celular  
                        FROM comisionista c
                        WHERE c.estado_mrcb
                        ORDER BY c.nombres";
            $data = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerProductosComisionista(){
        try{
            $sql = "SELECT cod_producto, tipo_comision, valor_comision
                        FROM comisionista_producto WHERE cod_comisionista = :0
                        ORDER BY cod_producto";

            $data = $this->consultarFilas($sql, [$this->getCodComisionista()]);
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function grabarProductosComisionista(){
        $this->beginTransaction();
        try{
            $this->setProductosComisionista(json_decode($this->getProductosComisionista()));

            $sql= "DELETE FROM comisionista_producto WHERE cod_comisionista = ".$this->getCodComisionista();
            $this->consultaRaw($sql);
            
            foreach ($this->getProductosComisionista() as $key => $value) {
                $campos_valores = [
                    "cod_producto"=> $value->cod_producto,
                    "cod_comisionista"=> $this->getCodComisionista(),
                    "tipo_comision"=> $value->tipo_comision,
                    "valor_comision"=> $value->valor_comision
                ];

                $this->insert("comisionista_producto", $campos_valores);
            }

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se han guardado los registros.");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
    

}