<?php

require_once '../datos/Conexion.clase.php';

class SolicitudEvento extends Conexion {
    private $cod_solicitud;
    private $numero_evento;
    private $tipo_evento;
    private $cod_tipo_equipo;
    private $cod_tipo_problema;
    private $observacion;
    private $estado;

    public function getCodSolicitud()
    {
        return $this->cod_solicitud;
    }
    
    
    public function setCodSolicitud($cod_solicitud)
    {
        $this->cod_solicitud = $cod_solicitud;
        return $this;
    }

    public function getNumeroEvento()
    {
        return $this->numero_evento;
    }
    
    
    public function setNumeroEvento($numero_evento)
    {
        $this->numero_evento = $numero_evento;
        return $this;
    }

    public function getTipoEvento()
    {
        return $this->tipo_evento;
    }
    
    
    public function setTipoEvento($tipo_evento)
    {
        $this->tipo_evento = $tipo_evento;
        return $this;
    }

    public function getCodTipoEquipo()
    {
        return $this->cod_tipo_equipo;
    }
    
    
    public function setCodTipoEquipo($cod_tipo_equipo)
    {
        $this->cod_tipo_equipo = $cod_tipo_equipo;
        return $this;
    }

    public function getCodTipoProblema()
    {
        return $this->cod_tipo_problema;
    }
    
    
    public function setCodTipoProblema($cod_tipo_problema)
    {
        $this->cod_tipo_problema = $cod_tipo_problema;
        return $this;
    }

    public function getEstado()
    {
        return $this->estado;
    }
    
    
    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }

    public function getObservacion()
    {
        return $this->observacion;
    }
    
    
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
        return $this;
    }

    public function cambiarEstado($codigo_numero) {
        $this->beginTransaction();
        try { 

            $arregloAuxiliar = explode("_", $codigo_numero);
            $this->setCodSolicitud($arregloAuxiliar[0]);
            $this->setNumeroEvento($arregloAuxiliar[1]);

            $currentTimestamp = date('Y-m-d G:i:s');

            $this->setObservacion(strtoupper($this->getObservacion()));

            switch($this->getEstado()){
                case "R":
                $campos_valores = ["fecha_hora_revisado"=>$currentTimestamp, 
                                    "observacion_revisado"=>$this->getObservacion(),
                                    "cod_personal_revisado"=>$_SESSION["usuario"]["cod_usuario"]];
                break;

                case "C":
                $campos_valores = ["fecha_hora_atendido_parcial"=>$currentTimestamp, 
                                    "observacion_atendido_parcial"=>$this->getObservacion()];
                break;

                case "E":
                $campos_valores = ["fecha_hora_espera"=>$currentTimestamp, 
                                    "observacion_espera"=>$this->getObservacion()];
                break;

                case "A":
                $campos_valores = ["fecha_hora_atendido"=>$currentTimestamp, 
                                    "observacion_atendido"=>$this->getObservacion()];
                break;

                case "X":
                $campos_valores = ["fecha_hora_cancelado"=>$currentTimestamp, 
                                    "observacion_cancelado"=>$this->getObservacion(),
                                    "cod_personal_cancelado"=>$_SESSION["usuario"]["cod_usuario"]];
                break;

            }

            $campos_valores["estado"] = $this->getEstado();

            $campos_valores_where = 
            array(  "cod_solicitud"=>$this->getCodSolicitud(), "numero_evento"=>$this->getNumeroEvento());

            $this->update("solicitud_evento", $campos_valores,$campos_valores_where);
    
            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha cambiado exitosamente el estado.");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    private function listar($tipoEvento, $estados){
        try{
            $sql = "SELECT 
                    ROW_NUMBER () OVER (ORDER BY fecha_hora_inicio) as numero,
                    CONCAT(se.cod_solicitud,'_',se.numero_evento) as codigo,
                    CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as personal,
                    se.descripcion,
                    c.descripcion as cargo,
                    cod_catalogo_servicio,
                    to_char(se.fecha_hora_inicio,'DD-MM-YYYY HH:mm:ss am') as fecha_hora_registro,
                    to_char(se.fecha_hora_revisado,'DD-MM-YYYY HH:mm:ss am') as fecha_hora_revisado,
                    to_char(se.fecha_hora_espera,'DD-MM-YYYY HH:mm:ss am') as fecha_hora_espera,
                    to_char(se.fecha_hora_atendido_parcial,'DD-MM-YYYY HH:mm:ss am') as fecha_hora_atendido_parcial,
                    to_char(se.fecha_hora_atendido,'DD-MM-YYYY HH:mm:ss am') as fecha_hora_atendido,
                    observacion_revisado,
                    observacion_atendido_parcial,
                    observacion_espera,
                    observacion_atendido,
                    te.descripcion as tipo_equipo,
                    tp.descripcion as tipo_problema,
                    se.estado,
                    fn_evento_obtener_estado_rotulo(se.estado ) as estado_rotulo,
                    fn_evento_obtener_estado_color(se.estado ) as estado_color
                    FROM solicitud_evento se
                    INNER JOIN solicitud s ON s.cod_solicitud = se.cod_solicitud
                    INNER JOIN personal p ON p.cod_personal = s.cod_personal
                    INNER JOIN tipo_equipo te ON te.cod_tipo_equipo = se.cod_tipo_equipo
                    INNER JOIN tipo_problema tp ON tp.cod_tipo_problema = se.cod_tipo_problema
                    INNER JOIN cargo c ON c.cod_cargo = p.cod_cargo
                    WHERE se.tipo_evento = :0 AND se.estado IN ('".$estados."')
                    ORDER BY fecha_hora_inicio::date DESC";

            $data = $this->consultarFilas($sql, $tipoEvento);

            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function listarPersonalEventosMovil(){
        try{
            $codPersonal = $_SESSION["usuario"]["cod_usuario"];
            $sqlWhereTEvento = " ";
            if ($this->getTipoEvento() != "0"){
                $sqlWhereTEvento = " AND se.tipo_evento IN ('".$this->getTipoEvento()."') ";
            }

            $sql = "SELECT 
                        ROW_NUMBER () OVER (ORDER BY se.fecha_hora_inicio) as numero,
                        te.descripcion as tipo_equipo,
                        tp.descripcion as tipo_problema,
                        (CASE se.tipo_evento
                            WHEN 'I' THEN 'INCIDENCIA'
                            WHEN 'P' THEN 'PROBLEMA'
                            WHEN 'G' THEN 'GESTION DE CAMBIO'
                         END) as tipo_evento,
                        se.descripcion,
                        fn_evento_obtener_estado_rotulo(se.estado) as estado_rotulo,
                        fn_evento_obtener_estado_color_movil(se.estado) as estado_color,
                        to_char(se.fecha_hora_inicio,'DD-MM-YYYY HH:MM:SS am') as fecha_hora_inicio,
                        to_char(COALESCE(se.fecha_hora_atendido, se.fecha_hora_atendido_parcial, se.fecha_hora_espera, se.fecha_hora_revisado, se.fecha_hora_inicio),'DD-MM-YYYY HH:MM:SS am') as ultima_revision,
                        COALESCE(se.observacion_atendido, se.observacion_atendido_parcial, se.observacion_espera,se.observacion_revisado, null) as ultima_observacion
                        FROM solicitud_evento se
                        INNER JOIN tipo_equipo te ON te.cod_tipo_equipo = se.cod_tipo_equipo
                        INNER JOIN tipo_problema tp ON tp.cod_tipo_problema = se.cod_tipo_problema
                        INNER JOIN solicitud s ON s.cod_solicitud = se.cod_solicitud
                        WHERE s.cod_personal = :0 AND se.estado NOT IN('X') $sqlWhereTEvento
                        ORDER BY  se.fecha_hora_inicio::date DESC";

            $data = $this->consultarFilas($sql, $codPersonal);

            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }
        
    public function listarIncidencias($estados){
        try{
            //return $this->listar("I", $estados);
            return $this->ejecutarAlgoritmo($estados);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function listarGestionCambios($estados){
        try{
            
            return $this->listar("G", $estados);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function listarProblemas($estados){
        try{
            
            return $this->listar("P", $estados);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function ejecutarAlgoritmo($estados) {
        try {
             $sqlListar = "SELECT 
                    -- ROW_NUMBER () OVER (ORDER BY fecha_hora_inicio) as numero,
                    CONCAT(se.cod_solicitud,'_',se.numero_evento) as codigo,
                    CONCAT(p.nombres,' ',p.apellido_paterno,' ',p.apellido_materno) as personal,
                    se.descripcion,
                    c.descripcion as cargo,
                    cod_catalogo_servicio,
                    to_char(se.fecha_hora_inicio,'DD-MM-YYYY HH:mm:ss am') as fecha_hora_registro,
                    to_char(se.fecha_hora_revisado,'DD-MM-YYYY HH:mm:ss am') as fecha_hora_revisado,
                    to_char(se.fecha_hora_espera,'DD-MM-YYYY HH:mm:ss am') as fecha_hora_espera,
                    to_char(se.fecha_hora_atendido_parcial,'DD-MM-YYYY HH:mm:ss am') as fecha_hora_atendido_parcial,
                    to_char(se.fecha_hora_atendido,'DD-MM-YYYY HH:mm:ss am') as fecha_hora_atendido,
                    observacion_revisado,
                    observacion_atendido_parcial,
                    observacion_espera,
                    observacion_atendido,
                    te.descripcion as tipo_equipo,
                    tp.descripcion as tipo_problema,
                    se.estado,
                    fn_evento_obtener_estado_rotulo(se.estado ) as estado_rotulo,
                    fn_evento_obtener_estado_color(se.estado ) as estado_color
                    FROM solicitud_evento se
                    INNER JOIN solicitud s ON s.cod_solicitud = se.cod_solicitud
                    INNER JOIN personal p ON p.cod_personal = s.cod_personal
                    INNER JOIN tipo_equipo te ON te.cod_tipo_equipo = se.cod_tipo_equipo
                    INNER JOIN tipo_problema tp ON tp.cod_tipo_problema = se.cod_tipo_problema
                    INNER JOIN cargo c ON c.cod_cargo = p.cod_cargo
                    WHERE se.tipo_evento = 'I' AND se.estado IN ('".$estados."')
                    ORDER BY se.cod_solicitud DESC, numero_evento DESC";

            //CRITERIOS CON LO QUE TRABAJAR.
            $sql = "SELECT * FROM ahp_criterio WHERE estado_activado = 'A' ORDER BY 1";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $arregloCrit = $sentencia->fetchAll(PDO::FETCH_NUM);
            // 1 - area
            // 2 - cargo
            // 3 - equipo
            // 4 - problema
            $SQL_TIP = "";
            $SQL_VS_CRIT = "";
            $SQL_WHERE_VS_CRIT = "";
            $cantidadCriterios = count($arregloCrit);

            if ($cantidadCriterios > 0) {
                for ($i = 0; $i < $cantidadCriterios; $i++) {
                    switch ($arregloCrit[$i][0]) {
                        case "1":
                            //  array_push($sql_tip, " tem.prioridad as tipo_empresa "); 
                            $SQL_TIP.= " a.peso as area ";
                            break;
                        case "2":
                            //  array_push($sql_tip, " (SELECT FORMAT(FLOOR(AVG(_ta.prioridad)),0) from incidencia_tipo_averia _ita INNER JOIN tipo_averia _ta ON _ta.codigo_tipo_averia = _ita.codigo_tipo_averia WHERE codigo_incidencia = i.codigo_incidencia) as tipo_averia ");
                            $SQL_TIP.= " c.peso as cargo ";
                            break;
                        case "3":
                            //array_push($sql_tip, " ag.codigo_distancia as distancia_agencia ");
                            $SQL_TIP.= " te.peso as tipo_equipo ";
                            break;
                        case "4":
                            $SQL_TIP.= " tp.peso as tipo_problema ";
                            //   array_push($sql_tip, " cat.prioridad as categoria_equipo ");
                            break;
                    }

                    $SQL_VS_CRIT.= " vs_criterio_" . $arregloCrit[$i][0] . " ";
                    $SQL_WHERE_VS_CRIT.= " cod_ahp_criterio = " . $arregloCrit[$i][0] . " ";

                    if ($cantidadCriterios - $i > 1) {
                        $SQL_TIP.=", ";
                        $SQL_VS_CRIT .= ", ";
                        $SQL_WHERE_VS_CRIT .= " OR ";
                    }
                    //          array_push($sql_vs_crit, " vs_criterio_".$arregloCrit[$i][0]." ");
                    //           array_push($sql_where_vs_crit, " codigo_criterio = ".$arregloCrit[$i][0]." ");
                }
            } else {
                $sentencia = $this->dblink->prepare($sqlListar);
                $sentencia->execute();
                $data = $sentencia->fetchAll(PDO::FETCH_ASSOC);
                return array("rpt"=>true,"data"=>$data);
            }

            $sql = "SELECT 
                CONCAT(se.cod_solicitud,'_',se.numero_evento) as codigo,"
                    . $SQL_TIP . "                  
                FROM solicitud_evento se
                INNER JOIN solicitud s ON se.cod_solicitud = s.cod_solicitud
                INNER JOIN personal p ON p.cod_personal = s.cod_personal
                INNER JOIN tipo_equipo te ON te.cod_tipo_equipo = se.cod_tipo_equipo
                INNER JOIN tipo_problema tp ON tp.cod_tipo_problema = se.cod_tipo_problema
                INNER JOIN cargo c ON c.cod_cargo = p.cod_cargo
                INNER JOIN area a ON a.cod_area = c.cod_area
                WHERE se.tipo_evento = 'I' AND se.estado IN ('".$estados."')
                ORDER BY 1";

            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $arregloIncidencias = $sentencia->fetchAll(PDO::FETCH_NUM);
            $arregloIndicenciasFormateado = $this->obtenerArregloIncidenciasFormateado($arregloIncidencias, $cantidadCriterios);

            $sql = "SELECT cod_ahp_criterio, "
                    . " $SQL_VS_CRIT "
                    . " FROM ahp_matriz_criterio "
                    . " WHERE $SQL_WHERE_VS_CRIT";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $arregloMatriz = $sentencia->fetchAll(PDO::FETCH_NUM);
            $arregloFinal = $this->obtenerArregloPesoCritero($arregloMatriz, $arregloIndicenciasFormateado, count($arregloIncidencias));
            $sentencia = $this->dblink->prepare($sqlListar);
            $sentencia->execute();

            if ($sentencia->rowCount()) {
                $dataArregloFinal = $sentencia->fetchAll(PDO::FETCH_ASSOC);
                return array("rpt"=>true,"data"=>$this->obtenerArregloJoin($arregloFinal, $dataArregloFinal));
            } else {
                return "ERROR";
            }


            //ejecutar criterio por criterio, si está activado claro.            
        } catch (Exception $exc) {
            throw $exc;
        }
    }

     private function obtenerArregloPesoCritero($arregloMatriz, $arregloIndicenciasFormateado, $numeroIncidencias) {
        $arregloFinal = array_pad(array(), $numeroIncidencias, 0); //este contiene N filas en base a N
        $arregloCriterio = array();
        $arregloValoresCriterio = array();
        $cantidadArregloMatriz = count($arregloMatriz);
        $total = 0;
        //obtener todos los valores de una fila y sumarlos.
        for ($i = 0; $i < $cantidadArregloMatriz; $i++) {
            $sum = 0;
            for ($j = 1; $j < count($arregloMatriz[$i]); $j++) {
                $sum += $arregloMatriz[$i][$j];
            }
            array_push($arregloCriterio, $sum);
            array_push($arregloValoresCriterio, $this->obtenerArregloCriterioEspecifico($arregloIndicenciasFormateado[$i]));
            $total += $sum;
        }

        for ($i = 0; $i < $cantidadArregloMatriz; $i++) {
            $arregloCriterio[$i] = round($arregloCriterio[$i] / $total, 4);
            for ($x = 0; $x < $numeroIncidencias; $x++) {//aqui hay 4 criterios.
                $arregloFinal[$x] += round(($arregloValoresCriterio[$i][$x] * $arregloCriterio[$i]), 4);
            }
        }
        return $arregloFinal;
    }

    private function obtenerArregloIncidenciasFormateado($arregloIncidencias, $cantidadCriterios) {
        $returnArray = array(array(), array(), array(), array());
        //$arreglo0 = array(); $arreglo1 = array(); $arreglo2 = array(); $arreglo3 = array();
        //obtener todos los valores de una fila y sumarlos.
        for ($i = 0; $i < count($arregloIncidencias); $i++) {
            for ($j = 1; $j <= $cantidadCriterios; $j++) {
                array_push($returnArray[$j - 1], $arregloIncidencias[$i][$j]);
            }
        }
        return $returnArray;
    }

    private function obtenerArregloCriterioEspecifico($arregloCriterioEspecifico) {
        $returnArray = array();
        $contador = count($arregloCriterioEspecifico);
        $total = 0;

        for ($i = 0; $i < $contador; $i++) {
            $actualVal = $arregloCriterioEspecifico[$i];
            //vs con todos los restantes.
            $sum = 0;
            for ($j = 0; $j < $contador; $j++) {
                if ($i != $j) {
                    $tmp = $this->obtenerValorMatrizCriterio($actualVal, $arregloCriterioEspecifico[$j]);
                    $sum += $tmp;
                }
            }
            array_push($returnArray, $sum);
            $total += $sum;
        }

        for ($i = 0; $i < $contador; $i++) {
            $returnArray[$i] = $total > 0 ? round($returnArray[$i] / $total, 4) : 0.0000;
        }
        return $returnArray;
    }

    private function obtenerArregloJoin($arregloFinal, $dataArregloFinal) {
        //var_dump($arregloFinal);
        //var_dump($dataArregloFinal);
        for ($i = 0; $i < count($arregloFinal); $i++) {
            $dataArregloFinal[$i]["numero"] = ($i+1);
            $dataArregloFinal[$i]["peso"] = $arregloFinal[$i];
        }
        return $dataArregloFinal;
    }

    private function obtenerValorMatrizCriterio($valor_izq, $valor_der) {
        $diferencia = $valor_izq - $valor_der;
        $esPositivo = $diferencia > 0;

        if ($diferencia <= -4){
            return 0.111;
        }

        if ($diferencia >= 4){
            return 9;
        }

        $absDiferencia = abs($diferencia);
        $auxCriterio = 2 * $absDiferencia + 1;
        return $esPositivo ? $auxCriterio  : round((1/$auxCriterio),3);
            /*
            switch($absDiferencia){
                case 1:
                    return $esPositivo ? 3 : round((1/3),3);
                case 2:
                    return $esPositivo ? 5 : round((1/5),3);
                case 3:
                    return $esPositivo ? 7 : round((1/7),3);
                default :
                    return -1; 
            }*/

       // return -1;
    }

}