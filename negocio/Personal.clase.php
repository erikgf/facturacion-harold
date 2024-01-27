<?php

require_once '../datos/Conexion.clase.php';

class Personal extends Conexion {
    private $cod_personal;
    private $dni;
    private $nombres;
    private $apellidos;
    private $celular;
    private $correo;
    private $fecha_nacimiento;
    private $fecha_ingreso;
    private $cod_cargo;
    private $cod_rol;
    private $sexo;
    private $acceso_sistema;
    private $estado_activo;
    private $clave;
    private $cod_sucursal;
    private $estado_mrcb;

    private $tbl = "personal";

    public function getCodPersonal()
    {
        return $this->cod_personal;
    }
    
    
    public function setCodPersonal($cod_personal)
    {
        $this->cod_personal = $cod_personal;
        return $this;
    }

    public function getDni()
    {
        return $this->dni;
    }
    
    
    public function setDni($dni)
    {
        $this->dni = $dni;
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

    public function getApellidos()
    {
        return $this->apellidos;
    }
    
    
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
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

    public function getCorreo()
    {
        return $this->correo;
    }
    
    
    public function setCorreo($correo)
    {
        $this->correo = $correo;
        return $this;
    }

    public function getCodCargo()
    {
        return $this->cod_cargo;
    }
    
    public function setCodCargo($cod_cargo)
    {
        $this->cod_cargo = $cod_cargo;
        return $this;
    }

    public function getCodRol()
    {
        return $this->cod_rol;
    }
    
    
    public function setCodRol($cod_rol)
    {
        $this->cod_rol = $cod_rol;
        return $this;
    }

    public function getEstadoActivo()
    {
        return $this->estado_activo;
    }
    
    
    public function setEstadoActivo($estado_activo)
    {
        $this->estado_activo = $estado_activo;
        return $this;
    }
    
    public function getClave()
    {
        return $this->clave;
    }
    
    
    public function setClave($clave)
    {
        $this->clave = $clave;
        return $this;
    }

    public function getFechaIngreso()
    {
        return $this->fecha_ingreso;
    }
    
    
    public function setFechaIngreso($fecha_ingreso)
    {
        $this->fecha_ingreso = $fecha_ingreso;
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

    public function getSexo()
    {
        return $this->sexo;
    }
    
    
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
        return $this;
    }

    public function getEstadoMrcb()
    {
        return $this->estado_mrcb;
    }

    public function getAccesoSistema()
    {
        return $this->acceso_sistema;
    }

    public function getCodSucursal()
    {
        return $this->cod_sucursal;
    }
    
    
    public function setCodSucursal($cod_sucursal)
    {
        $this->cod_sucursal = $cod_sucursal;
        return $this;
    }
    
    
    public function setAccesoSistema($acceso_sistema)
    {
        $this->acceso_sistema = $acceso_sistema;
        return $this;
    }
    
    
    public function setEstadoMrcb($estado_mrcb)
    {
        $this->estado_mrcb = $estado_mrcb;
        return $this;
    }

    private function verificarRepetidoAgregar(){
        $sql = "SELECT COUNT(dni) > 0 FROM personal WHERE dni = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, $this->getDni());

        if ($repetido){
            return array("rpt"=>false,"msj"=>"Ya existe este DNI.");
        }

        $sql = "SELECT COUNT(correo) > 0 FROM personal WHERE correo = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, $this->getCorreo());

        if ($repetido){
            return array("rpt"=>false,"msj"=>"Ya existe este CORREO.");
        }

        $sql = "SELECT COUNT(celular) > 0 FROM personal WHERE celular = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, $this->getCelular());

        if ($repetido){
            return array("rpt"=>false,"msj"=>"Ya existe este CELULAR.");
        }

        if (!$this->verificarFechaIngreso()){
            return array("rpt"=>false,"msj"=>"Fecha de ingreso no válida.");
        }

        if ($this->verificarFechaNacimiento() != "1"){
            return array("rpt"=>false,"msj"=>"Fecha de nacimiento no válida.");
        }

        return array("rpt"=>true);
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(dni) > 0 FROM personal WHERE dni = :0 AND cod_personal <>:1 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getDni(), $this->getCodPersonal()]);

        if ($repetido){
            return array("rpt"=>false,"msj"=>"Ya existe este DNI.");
        }

        $sql = "SELECT COUNT(correo) > 0 FROM personal WHERE correo = :0 AND cod_personal <>:1 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getCorreo(), $this->getCodPersonal()]);

        if ($repetido){
            return array("rpt"=>false,"msj"=>"Ya existe este EMAIL.");
        }

        $sql = "SELECT COUNT(celular) > 0 FROM personal WHERE celular = :0 AND cod_personal <>:1 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getCelular(), $this->getCodPersonal()]);

        if ($repetido){
            return array("rpt"=>false,"msj"=>"Ya existe este CELULAR.");
        }

        if (!$this->verificarFechaIngreso()){
            return array("rpt"=>false,"msj"=>"Fecha de ingreso no válida.");
        }

        if ($this->verificarFechaNacimiento() != "1"){
            return array("rpt"=>false,"msj"=>"Fecha de nacimiento no válida.");
        }
        return array("rpt"=>true);
    }

    private function verificarFechaIngreso(){
        if ($this->getFechaIngreso() != ""){
           $sql = "SELECT :0 <= current_date";
           return $this->consultarValor($sql, $this->getFechaIngreso());  
        }
        return true;
    }

    private function verificarFechaNacimiento(){
        if ($this->getFechaNacimiento() != ""){
            $sql = "SELECT (YEAR(current_date) - YEAR(:0)) >= 16 ";
            return $this->consultarValor($sql, $this->getFechaNacimiento());
        }
        return "1";
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [
                "dni"=>$this->getDni(),
                "nombres"=>$this->getNombres(),
                "apellidos"=>$this->getApellidos(),
                "correo"=>$this->getCorreo(),
                "celular"=>$this->getCelular(),
                "sexo"=>$this->getSexo(),
                "fecha_ingreso"=>$this->getFechaIngreso(),
                "fecha_nacimiento"=>$this->getFechaNacimiento(),
                "cod_cargo"=>$this->getCodCargo(),
                "cod_rol"=>$this->getCodRol(),
                "cod_sucursal"=>$this->getCodSucursal(),
                "estado_activo"=>$this->getEstadoActivo(),
                "acceso_sistema"=>($this->getAccesoSistema() == "true" ? 1 : 0)
                ];  

            if ($tipoAccion == "+"){
                $campos_valores["cod_personal"] = $this->getCodPersonal();
                if ($this->getClave() != null){
                    $campos_valores["clave"] = $this->getClave();
                }
            }
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_personal"=>$this->getCodPersonal()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $this->lastCodigo = $this->getCodPersonal();
        $this->BITACORA_ON = true;
        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];
        return $campos;
    }

    public function agregar() {
        $this->beginTransaction();
        try {         
            $objVerificar = $this->verificarRepetidoAgregar();
            if (!$objVerificar["rpt"]){
                return array("rpt"=>false,"msj"=>$objVerificar["msj"]);
            }
            $this->setCodPersonal($this->consultarValor("SELECT COALESCE(MAX(cod_personal)+1, 1) FROM personal"));
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
            if (!$objVerificar["rpt"]){
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
            $sql = "SELECT 
                p.cod_personal, p.dni, 
                nombres,
                apellidos,
                celular, correo, 
                c.descripcion as cargo, 
                r.descripcion as rol,
                (IF(sexo = 'F', 'FEMENINO', 'MASCULINO')) as sexo,
                (CASE p.estado_activo WHEN 'A' THEN 'success' ELSE 'danger' END) as color_estado,
                (CASE p.estado_activo WHEN 'A' THEN 'ACTIVO' ELSE 'INACTIVO' END) as estado,
                acceso_sistema,
                DATE_FORMAT(fecha_nacimiento,'%d/%m/%Y') as fecha_nacimiento,
                (YEAR(CURDATE()) - YEAR(fecha_nacimiento)) as edad
                FROM personal p 
                INNER JOIN cargo c ON c.cod_cargo = p.cod_cargo
                INNER JOIN rol r ON p.cod_rol = r.cod_rol
                WHERE p.estado_mrcb
                ORDER BY apellidos, nombres";

            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT dni, nombres, correo, apellidos, 
                    celular, correo, sexo, cod_rol, cod_cargo, estado_activo as estado, 
                    fecha_ingreso, fecha_nacimiento, acceso_sistema, cod_sucursal
                FROM personal WHERE cod_personal = :0";
            $resultado = $this->consultarFila($sql, $this->getCodPersonal());
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

            $campos_valores = ["estado_activo"];
            $campos_valores_where = ["I"];
            $this->update($this->tbl,$campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha eliminado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerPersonal(){
        try{
            $sql = "SELECT cod_personal as codigo,  CONCAT(nombres,' ',apellidos) as nombres, dni 
                FROM personal 
                WHERE estado_mrcb ORDER BY apellidos, nombres";
            $r = $this->consultarFilas($sql);
            return array("rpt"=>true,"r"=>$r);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function cambiarClave() {
        $this->beginTransaction();
        try { 

            $campos_valores = 
            array(  "clave"=> md5($this->getClave()));

            $campos_valores_where = 
            array(  "cod_personal"=>$this->getCodPersonal());

            $this->update("personal", $campos_valores,$campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha cambiado de clave exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


     public function obtenerCargosRoles(){
        try{

            $sql = "SELECT cod_cargo as codigo, descripcion FROM cargo WHERE estado_mrcb ORDER BY descripcion DESC";
            $cargos = $this->consultarFilas($sql);

            $sql = "SELECT cod_rol as codigo, descripcion FROM rol WHERE estado_mrcb ORDER BY descripcion DESC";
            $roles = $this->consultarFilas($sql);

            $sql = "SELECT cod_sucursal as codigo, nombre as descripcion FROM sucursal WHERE estado_mrcb AND cod_sucursal <> 0 ORDER BY nombre DESC";
            $sucursales = $this->consultarFilas($sql);

            return array("rpt"=>true,"r"=>["cargos"=>$cargos,"roles"=>$roles,"sucursales"=>$sucursales]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


}