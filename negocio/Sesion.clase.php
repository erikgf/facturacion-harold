<?php

require_once '../datos/Conexion.clase.php';

class Sesion extends Conexion {
    private $dni;
    private $clave;
    private $recordar;
    
    public function getRecordar() {
        return $this->recordar;
    }

    public function setRecordar($recordar) {
        $this->recordar = $recordar;
    }

    public function getClave() {
        return $this->clave;
    }

    public function setClave($clave) {
        $this->clave = $clave;
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

    public static function obtenerSesion(){
        return isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : null;
    }

    public function iniciarSesion()
    {
        try {            
            $sql = " SELECT p.cod_personal, p.dni, 
                        CONCAT(p.nombres) as nombres_usuario, 
                        c.descripcion as cargo, 
                        cod_rol, clave, estado_activo
                        FROM personal p 
                        INNER JOIN cargo c ON p.cod_cargo = c.cod_cargo
                        WHERE dni = :0 AND estado_activo = 'A' AND acceso_sistema";

            $res = $this->consultarFila($sql, $this->getDni());

            if ($res != false){
                if ($res["estado_activo"] == 'A'){
                    if ($res["clave"] == md5($this->getClave())){
                        $duracion = time() + (1000 * 3600 * 8);
                        if ($this->getRecordar() == "true"){
                            setcookie('dniusuario', $this->getDni(), $duracion, "/");
                        } else {
                            setcookie("dniusuario", "", $duracion,"/");
                        }
                        $codPersonal = $res["cod_personal"];
                        setcookie("codusuario",$codPersonal, $duracion,"/");
                        $sql = "DELETE FROM sesiones_cache WHERE usuario_conexion = ".$codPersonal."; ".
                               "INSERT INTO sesiones_cache(usuario_conexion, ip_conexion) VALUES (".$codPersonal.",'".$_SERVER['REMOTE_ADDR']."')";
                        $this->consultaRaw($sql);

                        $_SESSION["usuario"] =  array(
                                    "cod_usuario"=> $res["cod_personal"],
                                    "nombres_usuario"=> $res["nombres_usuario"],
                                    "cargo"=>$res["cargo"],
                                    "cod_rol"=>$res["cod_rol"]
                                    );

                        return array("rpt"=>true, "msj"=>"Acceso permitido.",
                                    "usuario" => $_SESSION["usuario"]);
                    }    
                    return array("rpt"=>false, "msj"=>"Clave incorrecta.");
                }
                return array("rpt"=>false, "msj"=>"Usuario inactivo.");                
            }
            
            return array("rpt"=>false, "msj"=>"Usuario inexistente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    /*
    public function iniciarSesionMovil()
    {
        try {            
            $sql = " SELECT p.cod_personal, p.dni, 
                        CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as nombres_usuario, 
                        c.descripcion as cargo, clave, estado
                        FROM personal p 
                        INNER JOIN cargo c ON p.cod_cargo = c.cod_cargo
                        WHERE dni = :0";

            $res = $this->consultarFila($sql, $this->getDni());


            if ($res != false){

                if ($res["estado"] == 'A'){
                    if ($res["clave"] == md5($this->getClave())){

                        if ($this->getRecordar()){
                            setcookie('dniusuario', $this->getDni());
                        }

                        $_SESSION["usuario"] =  array(
                                    "cod_usuario"=> $res["cod_personal"],
                                    "nombres_usuario"=> $res["nombres_usuario"],
                                    "cargo"=>$res["cargo"]
                                    );

                        return array("rpt"=>true, "msj"=>"Acceso permitido.",
                                    "usuario" => $_SESSION["usuario"]);
                    }    
                    return array("rpt"=>false, "msj"=>"Clave incorrecta.");
                }
                return array("rpt"=>false, "msj"=>"Usuario inactivo.");                
            }
            
            return array("rpt"=>false, "msj"=>"Usuario inexistente.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
    */

    public function cerrarSesion()
    {
        try {
            if (isset($_COOKIE["codusuario"]) && $_COOKIE["codusuario"] != null){
                $codPersonal = $_COOKIE["codusuario"];
                $sql = "DELETE FROM sesiones_cache WHERE usuario_conexion = ".$codPersonal;
                $this->consultaRaw($sql);
                setcookie("codusuario","",0,"/");
            }
            session_destroy();
            return array("rpt"=>true,"msj"=>"Sesión cerrada.");
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
        
}
