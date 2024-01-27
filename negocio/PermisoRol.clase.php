<?php

require_once '../datos/Conexion.clase.php';

class PermisoRol extends Conexion {
    private $codRol;
    private $codPermiso;

    public function getCodRol()
    {
        return $this->cod_rol;
    }
    
    
    public function setCodRol($cod_rol)
    {
        $this->cod_rol = $cod_rol;
        return $this;
    }

    public function listarPermisoActivos(){
        try {
            $sql = "SELECT 
                        p.cod_permiso,
                        p.titulo_interfaz,
                        (SELECT titulo_interfaz FROM permiso WHERE cod_permiso = p.padre ) as superior,
                        p.orden
                    FROM 
                        permiso_rol pr INNER JOIN permiso p ON pr.cod_permiso = p.cod_permiso 
                    WHERE pr.cod_rol = :0 AND p.padre IS NOT NULL AND p.estado = 'A'
                    ORDER BY 3,4 ";
            $resultado = $this->consultarFilas($sql,[$this->getCodRol()]);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    public function listarPermisoInactivos(){
        try {
            $sql = "SELECT 
                        cod_permiso,
                        titulo_interfaz,
                        (SELECT titulo_interfaz FROM permiso WHERE cod_permiso = p.padre)  as superior  
                    FROM permiso p 
                    WHERE 
                        p.padre  IS NOT NULL AND 
                        p.cod_permiso NOT IN (SELECT cod_permiso FROM permiso_rol WHERE cod_rol = :0) AND p.estado = 'A'";
            $resultado = $this->consultarFilas($sql,[$this->getCodRol()]);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    public function agregar($p1,$p2) {
        $this->beginTransaction();
        try {            
            $sql = "SELECT COUNT(*) FROM permiso_rol
                    WHERE cod_permiso = 
                    (
                        SELECT padre FROM permiso WHERE titulo_interfaz = :0
                    ) AND cod_rol = :1";
            $cantidad = intval($this->consultarValor($sql,array($p1,$p2)));

            if ( $cantidad == 0 ) {
                $sql = "SELECT padre FROM permiso WHERE titulo_interfaz = :0";
                $padre  = $this->consultarValor($sql,array($p1));

                $campos_valores = 
                array(  "cod_permiso"=>$padre,
                        "cod_rol"=>$p2,
                        "estado"=>'A');
                $this->insert("permiso_rol", $campos_valores);
            }

            $sql = "SELECT cod_permiso FROM permiso WHERE titulo_interfaz = :0";
            $hijo  = $this->consultarValor($sql,array($p1));

            $campos_valores = 
            array(  "cod_permiso"=>$hijo,
                    "cod_rol"=>$p2,
                    "estado"=>'A');
            $this->insert("permiso_rol", $campos_valores);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha agregado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

    public function quitar($p1,$p2) {
        $this->beginTransaction();
        try {
            $sql = "SELECT COUNT(*) FROM permiso_rol
                    WHERE cod_permiso = 
                    (
                        SELECT padre FROM permiso WHERE titulo_interfaz = :0
                    ) AND cod_rol = :1";
            $cantidad = intval($this->consultarValor($sql,array($p1,$p2)));

            if ( $cantidad == 1 ) {
                $sql = "SELECT padre FROM permiso WHERE titulo_interfaz = :0";
                $padre  = $this->consultarValor($sql,array($p1));

                $campos_valores = 
                array(  "cod_permiso"=>$padre,
                        "cod_rol"=>$p2);

                $this->delete("permiso_rol", $campos_valores);
            }
            

            $sql = "SELECT cod_permiso FROM permiso WHERE titulo_interfaz = :0";
            $hijo  = $this->consultarValor($sql,array($p1));

            $campos_valores = 
            array(  "cod_permiso"=>$hijo,
                    "cod_rol"=>$p2);

            $this->delete("permiso_rol", $campos_valores);      

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha quitado exitosamente.");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc);
        }
    }

}
