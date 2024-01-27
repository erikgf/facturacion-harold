<?php

require_once '../datos/Conexion.clase.php';

class Solicitud extends Conexion {
    private $cod_solicitud;
    private $cod_personal;
    private $eventos;

    private $observaciones_devolucion;

    private $fuente_origen;

    public function getCodSolicitud()
    {
        return $this->cod_solicitud;
    }
    
    
    public function setCodSolicitud($cod_solicitud)
    {
        $this->cod_solicitud = $cod_solicitud;
        return $this;
    }

    public function getCodPersonal()
    {
        return $this->cod_personal;
    }
    
    
    public function setCodPersonal($cod_personal)
    {
        $this->cod_personal = $cod_personal;
        return $this;
    }

    public function getEventos()
    {
        return $this->eventos;
    }
    
    
    public function setEventos($eventos)
    {
        $this->eventos = $eventos;
        return $this;
    }

    public function getFuenteOrigen()
    {
        return $this->fuente_origen;
    }
    
    
    public function setFuenteOrigen($fuente_origen)
    {
        $this->fuente_origen = $fuente_origen;
        return $this;
    }

    public function getObservacionesDevolucion()
    {
        return $this->observaciones_devolucion;
    }
    
    
    public function setObservacionesDevolucion($observaciones_devolucion)
    {
        $this->observaciones_devolucion = $observaciones_devolucion;
        return $this;
    }

    private function guardarSolicitud() {
        $this->beginTransaction();
        try {            

            if ($this->getCodSolicitud() == null || $this->getCodSolicitud() == -1){
                /*Agregando*/
                $sql = "SELECT COALESCE(MAX(cod_solicitud) + 1, 1) FROM solicitud";
                $this->setCodSolicitud($this->consultarValor($sql));

                $campos_valores = 
                    array(  "cod_personal"=>$this->getCodPersonal(),
                            "cod_solicitud"=>$this->getCodSolicitud(),
                            "fuente_origen"=>$this->getFuenteOrigen());

                $this->insert("solicitud", $campos_valores);
            } else {

                $campos_valores = ["cod_personal"=>$this->getCodPersonal(), "estado_devuelto"=>"false"];
                $campos_valores_where = ["cod_solicitud"=>$this->getCodSolicitud()];

                $this->update("solicitud", $campos_valores, $campos_valores_where);

                $this->delete("solicitud_evento", $campos_valores_where);
                $this->delete("evento_detalle_opcion", $campos_valores_where);
                /* update solicitudes */
                /* delete detalle + opciones*/
            }

            $arrEventos = json_decode($this->getEventos());

            $numeroEvento = 0;
            foreach ($arrEventos as $key => $value) {
                $campos_valores = 
                    array(  "cod_solicitud"=>$this->getCodSolicitud(),
                            "numero_evento"=>++$numeroEvento,
                            "descripcion"=>$value->descripcion,
                            "cod_tipo_equipo"=>$value->cod_tipo_equipo,
                            "cod_tipo_problema"=>$value->cod_tipo_problema,
                            "tipo_evento"=>$value->tipo_evento);

                $this->insert("solicitud_evento", $campos_valores);
            }

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha agregado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function guardarSolicitudWeb() {
        $this->setFuenteOrigen(1);
        return $this->guardarSolicitud();
    }

    public function guardarSolicitudMovil() {
        $this->setFuenteOrigen(0);
        $this->setCodPersonal($_SESSION["usuario"]["cod_usuario"]);
        return $this->guardarSolicitud();
    }

    public function listarPersonalSolicitudes(){
        try {
            $this->setCodPersonal($_SESSION["usuario"]["cod_usuario"]);

            $sql = "SELECT 
                        cod_solicitud as codigo,
                        LPAD(cod_solicitud::text, 6 , '0')as cod_solicitud, 
                        to_char(fecha_hora_registro,'DD-MM-YYYY') as fecha, 
                        to_char(fecha_hora_registro,'HH:MM:SS am') as hora, 
                        fn_solicitud_obtener_estado_color_movil(estado) as estado_color,
                        fn_solicitud_obtener_estado_rotulo(estado) as estado_rotulo,
                        estado_devuelto,
                        veces_devuelto,
                        revisado_personal_cliente
                        FROM solicitud 
                        WHERE cod_personal = :0
                        ORDER BY fecha_hora_registro DESC";
                        
            $resultado = $this->consultarFilas($sql, $this->getCodPersonal());
            return array("rpt"=>true,"datos"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function eliminar() {
        $this->beginTransaction();
        try { 

            /*
            $campos_valores = 
            array(  "estado_mrcb" => "false");

            $campos_valores_where = 
            array(  "cod_solicitud"=>$this->getCodSolicitud());

            $this->update("solicitud", $campos_valores,$campos_valores_where);
    
            */
            $campos_valores_where = 
            array(  "cod_solicitud"=>$this->getCodSolicitud());

            $this->delete("evento_detalle_opcion", $campos_valores_where);
            $this->delete("solicitud_evento", $campos_valores_where);
            $this->delete("solicitud", $campos_valores_where);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha eliminado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

     public function revisarSolicitudWeb() {
        $this->beginTransaction();
        try { 

            /*La solicitud debería esta en estado P*/

            $campos_valores = 
            array(  "estado" => "R",
                    "estado_devuelto" => "false",
                    "cod_personal_revision" => $_SESSION["usuario"]["cod_usuario"]);

            $campos_valores_where = 
            array(  "cod_solicitud"=>$this->getCodSolicitud());

            $this->update("solicitud", $campos_valores, $campos_valores_where);

            $sql = "UPDATE solicitud_evento SET 
                        fecha_hora_revisado = current_timestamp, 
                        cod_personal_revisado = ".$_SESSION["usuario"]["cod_usuario"].",
                        estado  = 'R' 
                        WHERE cod_solicitud = ".$this->getCodSolicitud();
            $this->consultaRaw($sql);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha revisado la solicitud exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

     public function devolverSolicitudWeb() {
        $this->beginTransaction();
        try { 
            $sql = "UPDATE solicitud SET 
                        veces_devuelto = veces_devuelto + 1,
                        observaciones_devolucion = ".($this->getObservacionesDevolucion() == null ? "null" : "'".$this->getObservacionesDevolucion()."'").",
                        estado_devuelto = true                        
                        WHERE cod_solicitud = ".$this->getCodSolicitud();

            $this->consultaRaw($sql);

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha devuelto la solicitud exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }


    public function listar(){
        try{

            $sql = "SELECT 
                    ROW_NUMBER () OVER (ORDER BY fecha_hora_registro) as numero_orden,
                    s.cod_solicitud, 
                    CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as personal,                    
                    veces_devuelto,
                    (SELECT COUNT(se.numero_evento) FROM solicitud_evento se WHERE se.cod_solicitud = s.cod_solicitud ) as numero_eventos,
                    fn_solicitud_obtener_estado_rotulo(s.estado) as estado,
                    fn_solicitud_obtener_estado_color(s.estado) as estado_color,
                    (CASE fuente_origen WHEN 0 THEN 'MÓVIL' ELSE 'WEB' END) as origen,
                    to_char(fecha_hora_registro,'DD-MM-YYYY') as fecha,
                    to_char(fecha_hora_registro,'HH:MM:SS am') as hora
                    FROM solicitud s
                    INNER JOIN personal p ON p.cod_personal = s.cod_personal
                    ORDER BY fecha_hora_registro::date";

            $data = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerSolicitud(){
          try{
              $mensaje = ""; $rpt = true; $solicitud = null;

              if ($this->getCodSolicitud() != null && $this->getCodSolicitud() > -1){
                $sql = "SELECT s.cod_solicitud, s.cod_personal, s.estado, 
                        fn_solicitud_obtener_estado_rotulo(s.estado) as estado_rotulo,
                        fn_solicitud_obtener_estado_color(s.estado) as estado_color,
                        fn_solicitud_obtener_estado_color_movil(s.estado) as estado_color_movil,
                        estado_devuelto
                        FROM solicitud s WHERE cod_solicitud = :0";

                $cabecera = $this->consultarFila($sql, $this->getCodSolicitud());

                if ($cabecera == false){
                    $solicitud = null;
                    $rpt = false;
                    $mensaje = "Solicitud no encontrada.";
                } else {
                    $sql = "SELECT 
                            numero_evento, 
                            se.cod_tipo_equipo, 
                            te.descripcion as tipo_equipo,
                            se.cod_tipo_problema, 
                            tp.descripcion as tipo_problema,
                            se.descripcion,
                            (CASE se.tipo_evento WHEN 'I' THEN 'INCIDENCIA' WHEN 'P' THEN 'PROBLEMA' ELSE 'GESTION CAMBIO' END) as tipo_evento
                            FROM solicitud_evento se
                            INNER JOIN tipo_equipo te ON te.cod_tipo_equipo = se.cod_tipo_equipo
                            INNER JOIN tipo_problema tp ON tp.cod_tipo_problema = se.cod_tipo_problema
                            WHERE se.cod_solicitud = :0 ORDER BY numero_evento";

                    $eventos = $this->consultarFilas($sql, $this->getCodSolicitud());

                    $solicitud = ["cabecera"=>$cabecera, "eventos"=>$eventos];  
                }
            } else {
                $mensaje = "No se ha ingresado código solicitud.";
                $rpt = false;
            }

            return ["rpt"=>$rpt, "solicitud"=>$solicitud, "msj"=>$mensaje];

            } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
            }
    }


    public function obtenerDatosFormulario(){
        try{

            $sql = "SELECT cod_personal, dni, CONCAT(nombres,' ',apellido_paterno,' ',apellido_materno) as nombres_apellidos
                    FROM personal WHERE estado_mrcb ORDER BY apellido_paterno, apellido_materno, nombres";
            $personal = $this->consultarFilas($sql);

            $sql = "SELECT cod_tipo_equipo, descripcion FROM tipo_equipo WHERE estado_mrcb ORDER BY  descripcion";
            $tipo_equipos = $this->consultarFilas($sql);

            $sql = "SELECT sea.cod_tipo_problema, tp.descripcion, (CASE sea.tipo_evento WHEN 'I' THEN 'INCIDENCIA' WHEN 'P' THEN 'PROBLEMA' ELSE 'GESTION CAMBIO' END) as tipo_evento,
                    sea.cod_tipo_equipo
                    FROM servicio_equipo_problema sea 
                    INNER JOIN tipo_problema tp ON tp.cod_tipo_problema = sea.cod_tipo_problema AND tp.estado_mrcb 
                    ORDER BY tp.descripcion DESC";
            $tipo_problemas = $this->consultarFilas($sql);

            $data = ["tipo_equipos"=>$tipo_equipos, "tipo_problemas"=>$tipo_problemas, "personal"=>$personal];

            $objSolicitud = $this->obtenerSolicitud();
            $solicitud = $objSolicitud["solicitud"];

            return array("rpt"=>true,"data"=>["data_base"=>$data,"solicitud"=>$solicitud]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDatosFormularioMovil(){
        try{

            $sql = "SELECT cod_tipo_equipo as codigo, descripcion FROM tipo_equipo WHERE estado_mrcb ORDER BY  descripcion";
            $tipo_equipos = $this->consultarFilas($sql);
            /*
            $sql = "SELECT cod_tipo_problema as codigo, descripcion, cod_tipo_equipo FROM tipo_problema WHERE estado_mrcb ORDER BY  descripcion";
            $tipo_problemas = $this->consultarFilas($sql);
            */

            $sql = "SELECT sea.cod_tipo_problema as codigo, tp.descripcion, (CASE sea.tipo_evento WHEN 'I' THEN 'INCIDENCIA' WHEN 'P' THEN 'PROBLEMA' ELSE 'GESTION CAMBIO' END) as tipo_evento,
                    sea.cod_tipo_equipo
                    FROM servicio_equipo_problema sea 
                    INNER JOIN tipo_problema tp ON tp.cod_tipo_problema = sea.cod_tipo_problema AND tp.estado_mrcb 
                    ORDER BY tp.descripcion DESC";
            $tipo_problemas = $this->consultarFilas($sql);

            $data = ["tipo_equipos"=>$tipo_equipos, "tipo_problemas"=>$tipo_problemas];

            $objSolicitud = $this->obtenerSolicitud();
            $solicitud = $objSolicitud["solicitud"];

            return array("rpt"=>true,"data"=>["data_base"=>$data, "solicitud"=>$solicitud]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerSolicitudMovil(){
        try{
            /*Verificar si es dueño de la de solicutud*/
            $sql = "UPDATE solicitud SET revisado_personal_cliente = true WHERE revisado_personal_cliente = false AND cod_solicitud = ".$this->getCodSolicitud();
            $this->consultaRaw($sql);

            $objSolicitud = $this->obtenerSolicitud();
            $solicitud = $objSolicitud["solicitud"];

            return array("rpt"=>true,"data"=>$solicitud);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
}