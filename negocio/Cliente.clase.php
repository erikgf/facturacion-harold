<?php

require_once '../datos/Conexion.clase.php';

class Cliente extends Conexion {
    private $cod_cliente;
    private $tipo_documento;
    private $numero_documento;
    private $nombres;
    private $apellidos;
    private $correo;
    private $sexo;
    private $celular;
    private $fecha_nacimiento;
    private $direccion;
    private $numero_contacto;
    private $razon_social;
    private $estado_mrcb;

    private $tbl = "cliente";

    public function getDireccion()
    {
        return $this->direccion;
    }
    
    
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
        return $this;
    }

    public function getNumeroContacto()
    {
        return $this->numero_contacto;
    }
    
    
    public function setNumeroContacto($numero_contacto)
    {
        $this->numero_contacto = $numero_contacto;
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


    public function getCodCliente()
    {
        return $this->cod_cliente;
    }
    
    
    public function setCodCliente($cod_cliente)
    {
        $this->cod_cliente = $cod_cliente;
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

    public function getSexo()
    {
        return $this->sexo;
    }
    
    
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
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

    public function getFechaNacimiento()
    {
        return $this->fecha_nacimiento;
    }
    
    
    public function setFechaNacimiento($fecha_nacimiento)
    {
        $this->fecha_nacimiento = $fecha_nacimiento;
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

    public function getApellidos()
    {
        return $this->apellidos;
    }
    
    
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
        return $this;
    }

    private function verificarRepetidoAgregar(){
        if ($this->getNumeroDocumento() != NULL && $this->getNumeroDocumento() != ""){
            $sql = "SELECT COUNT(numero_documento) > 0 FROM ".$this->tbl." WHERE numero_documento = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getNumeroDocumento()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Número documento ya existente."];
            }
        }
        
        if ($this->getCorreo() != NULL && $this->getCorreo() != ""){
            $sql = "SELECT COUNT(correo) > 0 FROM ".$this->tbl." WHERE correo = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getCorreo()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Correo ya existente."];
            }
        }

        if ($this->getCelular() != NULL && $this->getCelular() != ""){
            $sql = "SELECT COUNT(celular) > 0 FROM ".$this->tbl." WHERE celular = :0 AND estado_mrcb";
            $repetido = $this->consultarValor($sql, [$this->getCelular()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Celular ya existente."];
            }
        }
        
         if ($this->verificarFechaNacimiento() != "1"){
            return array("rpt"=>false,"msj"=>"Fecha de nacimiento no válida (menor de edad).");
        }

        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){
        if ($this->getNumeroDocumento() != NULL && $this->getNumeroDocumento() != ""){
            $sql = "SELECT COUNT(numero_documento) > 0 FROM ".$this->tbl." WHERE numero_documento = :0 AND estado_mrcb  AND cod_cliente <>:1";
            $repetido = $this->consultarValor($sql, [$this->getNumeroDocumento(),$this->getCodCliente()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Número documento ya existente."];
            }
        }

        if ($this->getCorreo() != NULL && $this->getCorreo() != ""){
            $sql = "SELECT COUNT(correo) > 0 FROM ".$this->tbl." WHERE correo = :0  AND estado_mrcb  AND cod_cliente <>:1";
            $repetido = $this->consultarValor($sql, [$this->getCorreo(),$this->getCodCliente()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Correo ya existente."];
            }
        }

        if ($this->getCelular() != NULL && $this->getCelular() != ""){
            $sql = "SELECT COUNT(celular) > 0 FROM ".$this->tbl." WHERE celular = :0  AND estado_mrcb AND  cod_cliente <>:1";
            $repetido = $this->consultarValor($sql, [$this->getCelular(),$this->getCodCliente()]);

            if ($repetido){
                return ["r"=>false, "msj"=>"Celular ya existente."];
            }
        }


        if ($this->verificarFechaNacimiento() != "1"){
            return array("rpt"=>false,"msj"=>"Fecha de nacimiento no válida (menor de edad).");
        }

        return ["r"=>true, "msj"=>""];
    }

    private function verificarFechaNacimiento(){
        if ($this->getFechaNacimiento() != ""){
            $sql = "SELECT (YEAR(current_date) - YEAR(:0)) >= 18 AND  (YEAR(current_date) - YEAR(:0)) <= 100  ";
            return $this->consultarValor($sql, $this->getFechaNacimiento());
        }
        return "1";
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){

            if ($this->getTipoDocumento() == "6"){
                $campos_valores = [
                    "tipo_documento"=>$this->getTipoDocumento(),
                    "numero_documento"=>$this->getNumeroDocumento(),
                    "nombres"=>$this->getRazonSocial(),
                    "apellidos"=>NULL,
                    "correo"=>$this->getCorreo(),
                    "celular"=>NULL,
                    "sexo"=>NULL,
                    "fecha_nacimiento"=>NULL,
                    "direccion"=>$this->getDireccion(),
                    "numero_contacto"=>$this->getNumeroContacto()
                    ];

            } else {
                $campos_valores = [
                    "tipo_documento"=>$this->getTipoDocumento(),
                    "numero_documento"=>$this->getNumeroDocumento(),
                    "nombres"=>$this->getNombres(),
                    "apellidos"=>$this->getApellidos(),
                    "correo"=>$this->getCorreo(),
                    "celular"=>$this->getCelular(),
                    "sexo"=>$this->getSexo(),
                    "fecha_nacimiento"=>$this->getFechaNacimiento() == "" ? NULL : $this->getFechaNacimiento(),
                    "direccion"=>$this->getDireccion(),
                    "numero_contacto"=>""
                    ];

            }
            
            if ($tipoAccion == "+"){
                $campos_valores["cod_cliente"] = $this->getCodCliente();
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_cliente"=>$this->getCodCliente()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $this->lastCodigo = $this->getCodCliente();
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
            $this->setCodCliente($this->consultarValor("SELECT COALESCE(MAX(cod_cliente)+1, 1) FROM cliente"));
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
            $sql = "SELECT c.cod_cliente,
                        CONCAT(c.nombres,' ',COALESCE(c.apellidos,'')) as razon_social, 
                        td.abrev as tipo_documento, 
                        COALESCE(numero_documento,'-') as numero_documento, 
                        correo, 
                        (IF (sexo = 'F', 'FEMENINO','MASCULINO')) as sexo, 
                        COALESCE(celular, numero_contacto) as celular, 
                        DATE_FORMAT(fecha_nacimiento,'%d/%m/%Y') as fecha_nacimiento,
                        direccion,
                        TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) as edad
                        FROM cliente c
                        INNER JOIN tipo_documento td ON c.tipo_documento = td.cod_tipo_documento
                        WHERE c.estado_mrcb AND c.cod_cliente <> 0
                        ORDER BY c.nombres";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT c.nombres, c.apellidos, tipo_documento, numero_documento, correo, sexo, celular, fecha_nacimiento,
                    direccion, numero_contacto
                    FROM cliente c
                    WHERE c.cod_cliente = :0";
            $resultado = $this->consultarFila($sql, $this->getCodCliente());
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

    public function obtenerClientes(){
        try{
            $sql = "SELECT c.nombres, celular  
                        FROM cliente c
                        WHERE c.estado_mrcb
                        ORDER BY c.nombres";
            $data = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}