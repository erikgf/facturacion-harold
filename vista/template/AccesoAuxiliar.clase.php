<?php 

require_once '../datos/Conexion.clase.php';

Class AccesoAuxiliar extends Conexion{
	
	private $codRol;
	private $descripcionRol;
	private $codUsuario;
	private $usuario;

	private $nombres;

	private $URL = "";
	private $ID_URL_PADRE = "";

	public function getCodRol()
	{
	    return $this->codRol;
	}
	
	
	public function setCodRol($codRol)
	{
	    $this->codRol = $codRol;
	    return $this;
	}

	public function getCodUsuario()
	{
	    return $this->codUsuario;
	}
	
	
	public function setCodUsuario($codUsuario)
	{
	    $this->codUsuario = $codUsuario;
	    return $this;
	}

	public function getDescripcionRol()
	{
	    return $this->descripcionRol;
	}
	
	public function setDescripcionRol($descripcionRol)
	{
	    $this->descripcionRol = $descripcionRol;
	    return $this;
	}

	public function getUsuario()
	{
	    return $this->usuario;
	}
	
	
	public function setUsuario($usuario)
	{
	    $this->usuario = $usuario;
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

	public function getMenu()
	{
		return $this->phpMenu;
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

	public function getURL()
	{
	    return $this->URL;
	}
	
	
	public function setURL($URL)
	{
	    $this->URL = $URL;
	    return $this;
	}

	private function setDataUsuario($objUsuario){
		$this->codUsuario = $objUsuario["cod_usuario"];
		$this->usuario =  ucwords(strtolower($objUsuario["nombres_usuario"]));
		$this->descripcionRol = ucwords(strtolower($objUsuario["cargo"]));
		$this->codRol = $objUsuario["cod_rol"];

        $_SESSION["usuario"] =  array(
             "cod_usuario"=> $this->codUsuario ,
             "nombres_usuario"=> $this->usuario ,
             "cargo"=>$this->descripcionRol,
             "cod_rol"=>$this->codRol
             );

	}

	public function __construct()
	{	
		parent::__construct();
		$objUsuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : NULL;
		$ip = $_SERVER['REMOTE_ADDR'];

        $interfaz =  basename($_SERVER["SCRIPT_FILENAME"]); 
        $this->URL = $interfaz;
        if (!$objUsuario){
        	//Check from database.
				if (isset($_COOKIE["codusuario"]) && $_COOKIE["codusuario"] != null){
        			$codusuario = $_COOKIE["codusuario"];
			    } else {
			    	$codusuario = -1;
			    }
				$objUsuario = $this->consultarFila("SELECT cod_personal as cod_usuario, CONCAT(nombres,' ',apellidos) as nombres_usuario, 
													c.descripcion as cargo, cod_rol FROM personal u 
													INNER JOIN sesiones_cache sc ON sc.usuario_conexion = u.cod_personal 
													INNER JOIN cargo c ON u.cod_cargo = c.cod_cargo
													WHERE sc.ip_conexion = :0 AND sc.usuario_conexion = :1", [$ip, $codusuario]);

				if ($objUsuario != false){
					$this->setDataUsuario($objUsuario);
				} else{
					$objUsuario = NULL;
				}
        }

		if ($interfaz == "principal.vista.php"){
			if( !$objUsuario ){
		  		$this->usuario = "";
				$this->descripcionRol ="";
				$this->codRol = -1;
			} else {
				$this->setDataUsuario($objUsuario);
			}
		} else{
			if( !$objUsuario ){				
				header("location:index.php");
			} else {
				$this->setDataUsuario($objUsuario);
			}
		}

		if ($interfaz != "principal.vista.php"){
			 $this->ID_URL_PADRE = $this->consultarValor("SELECT padre FROM permiso WHERE url = :0", [$this->URL]); 
			//Funcion para verificar si es que mi acceso
			$permisoURL = $this->consultarValor("SELECT COUNT(pr.cod_permiso) > 0 FROM permiso p
									INNER JOIN permiso_rol pr ON pr.cod_permiso = p.cod_permiso
									WHERE url = :0 AND pr.cod_rol = :1 ORDER BY orden DESC", [$this->URL,$this->codRol]);

			if(!$permisoURL){
				header("location: error403.php");
				//header("location: ../index.php");
			}
	
		}
       
		//$this->phpMenu = $this->obtenerMenu();
	}

	public function __desctruct()
	{
		parent::__desctruct();	
		session_destroy(_SESION_);
	}

	public function dibujarMenu()
    {
        /*
        $arr = split("/",$_SERVER["PHP_SELF"]);
        $interfaz = $arr[count($arr) - 2];
        */
        $interfaz = basename($_SERVER["SCRIPT_FILENAME"]); 
        return $this->dibujarFila($this->crearMenu());
    }

    public function crearMenu($padre = NULL)
    {	
    	//?$this->idRol
        $sql = "SELECT pr.cod_permiso, p.es_menu_interfaz, p.titulo_interfaz, p.url, p.icono_interfaz, p.padre 
        		FROM permiso_rol pr 
        		INNER JOIN permiso p ON p.cod_permiso = pr.cod_permiso
        		WHERE pr.estado = 'A' AND pr.cod_rol = :0 AND p.padre";
        if ($padre == NULL){
            $sql .= " IS NULL ORDER BY 1";
            $hijos = $this->consultarFilas($sql,[$this->idRol]);
            $padre = array("cod_permiso"=>0);
        } else {
            $sql .= " = :1 ORDER BY 1";
            $hijos = $this->consultarFilas($sql, [$this->idRol,$padre["cod_permiso"]]);
        }

        if (count($hijos)){
            $padre["hijos"] = array();
            foreach ($hijos as $key => $value) {
                array_push($padre["hijos"], $this->crearMenu($value));
            }  
        }

        return $padre;
    }

    public function generarArregloMenu()
    {	
    	/*    	
    	$menu = [
            ["rotulo"=>"Mantenimientos", "icon"=>"fa fa-edit",
                "menu"=>[
                          ["rotulo"=>"Clientes","href"=>"cliente.vista.php","roles"=>[1,2]],
                          ["rotulo"=>"Proveedor","href"=>"proveedor.vista.php","roles"=>[1,2]],
                          ["rotulo"=>"Personal","href"=>"personal.vista.php","roles"=>[1]],
                          ["rotulo"=>"Sucursal","href"=>"sucursal.vista.php","roles"=>[1]],
                          ["rotulo"=>"Comisionistas","href"=>"comisionista.vista.php","roles"=>[1]],
                          ["rotulo"=>"Cargos","href"=>"cargo.vista.php","roles"=>[1]],
                          ["rotulo"=>"Productos","href"=>"producto.vista.php","roles"=>[1,2]],
                          ["rotulo"=>"Tipo de Productos","href"=>"tipo.producto.vista.php","roles"=>[1,2]],
                          ["rotulo"=>"Categoría de Productos","href"=>"categoria.producto.vista.php","roles"=>[1,2]]
                ]
            ],
            ["rotulo"=>"Transacciones", "icon"=>"fa fa-file-o", 
                "menu"=>[
                     ["rotulo"=>"Generar Descuentos","href"=>"descuento.vista.php", "roles"=>[1]],
                     ["rotulo"=>"Ventas","href"=>"ventas.vista.php","roles"=>[1,2]],
                     ["rotulo"=>"Compras","href"=>"compras.vista.php","roles"=>[1,2]],
                     ["rotulo"=>"Almacén","href"=>"almacen.vista.php","roles"=>[1,2]]
                ]
            ],
            ["rotulo"=>"Catálogo", "icon"=>"fa fa-file", "href"=>"principal.vista.php","roles"=>[1,2]]
          ];
          */


        $sql = "SELECT cod_permiso, titulo_interfaz as rotulo, icono_interfaz as icon, es_menu_interfaz, url as href
        		FROM permiso p WHERE p.estado = 'A' AND p.padre IS NULL";

        $menu = $this->consultarFilas($sql);

        $sql = "SELECT titulo_interfaz as rotulo, url as href FROM permiso p 
        		INNER JOIN permiso_rol pr ON pr.cod_permiso = p.cod_permiso
        		WHERE pr.estado = 'A' AND pr.cod_rol = :0 AND p.estado = 'A' AND p.es_menu_interfaz AND p.padre IS NOT NULL AND padre = :1
        		ORDER BY orden";

        $retornoMenu = [];
        foreach ($menu as $key => $menuPadre) {
        	if (!$menuPadre["es_menu_interfaz"]){
        		$menusHijo = $this->consultarFilas($sql, [$this->codRol, $menuPadre["cod_permiso"]]);
	        	if (count($menusHijo) > 0){
	        		$menuPadre["menu"] = $menusHijo;
	        		array_push($retornoMenu, $menuPadre);
	        	}
        	} else {
        		array_push($retornoMenu, $menuPadre);
        	}
        	
        }

        return $retornoMenu;
    }

    public function dibujarFila($item)
    {
        $html = "";        

        if ( !array_key_exists("hijos",$item) || count( $item["hijos"] ) <= 0){
            $active = ($this->URL == $item["url"])? 'class="active"' : '';
            $html.= '<li '.$active.'><a href="../'.$item["url"].'">'.$item["titulo_interfaz"].'</a></li>';
        } else {
            if ($item["cod_permiso"] == 0 ){
                foreach ($item["hijos"] as $key => $value) {
                    $html.= $this->dibujarFila($value);
                }
            } else {
                $html.= '<li>';
                $toggled = ($this->ID_URL_PADRE == $item["cod_permiso"]) ?  'toggled' : '';
                $html.= '<a href="javascript:void(0);" class="menu-toggle '.$toggled.'">';
                $html.= '<i class="material-icons">'.$item["icono_interfaz"].'</i>';
                $html.= '<span>'.$item["titulo_interfaz"].'</span>';
                $html.= '</a>';
                $html.= '<ul class="ml-menu">';
                foreach ($item["hijos"] as $key => $value) {
                    $html.= $this->dibujarFila($value);
                }
                $html.= '</ul>';
            $html.= '</li>';
            }
        }
        return $html;
    }


    public function obtenerMenu()
    {         
        return $this->dibujarFila( $this->crearMenu() );        
    }

};

?>