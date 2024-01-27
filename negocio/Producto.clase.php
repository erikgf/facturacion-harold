<?php

require_once '../datos/Conexion.clase.php';

class Producto extends Conexion {
    private $cod_producto;
    private $codigo;
    private $nombre;
    private $descripcion;
    private $precio_unitario;
    private $cod_marca;
    private $cod_categoria_producto;
    private $cod_tipo_categoria;
    private $cod_unidad_medida;
    private $cod_presentacion;
    private $img_url;
    private $numero_imagen_principal;
    private $estado_mrcb;

    public $modoActualizarImg; /*0: DEFAULT, 1: No cambio, 2: Cambio*/

    private $tbl = "producto";

    public function getCodProducto()
    {
        return $this->cod_producto;
    }
    
    public function setCodProducto($cod_producto)
    {
        $this->cod_producto = $cod_producto;
        return $this;
    }


    public function getCodigo()
    {
        return $this->codigo;
    }
    
    
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
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

    public function getPrecioUnitario()
    {
        return $this->precio_unitario;
    }
    
    
    public function setPrecioUnitario($precio_unitario)
    {
        $this->precio_unitario = $precio_unitario;
        return $this;
    }

    public function getCodCategoriaProducto()
    {
        return $this->cod_categoria_producto;
    }
    
    public function setCodCategoriaProducto($cod_categoria_producto)
    {
        $this->cod_categoria_producto = $cod_categoria_producto;
        return $this;
    }

    public function getCodUnidadMedida()
    {
        return $this->cod_unidad_medida;
    }
    
    
    public function setCodUnidadMedida($cod_unidad_medida)
    {
        $this->cod_unidad_medida = $cod_unidad_medida;
        return $this;
    }

    public function getCodPresentacion()
    {
        return $this->cod_presentacion;
    }
    
    
    public function setCodPresentacion($cod_presentacion)
    {
        $this->cod_presentacion = $cod_presentacion;
        return $this;
    }

    public function getImgUrl()
    {
        return $this->img_url;
    }
    
    
    public function setImgUrl($img_url)
    {
        $this->img_url = $img_url;
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

    public function getNumeroImagenPrincipal()
    {
        return $this->numero_imagen_principal;
    }
    
    
    public function setNumeroImagenPrincipal($numero_imagen_principal)
    {
        $this->numero_imagen_principal = $numero_imagen_principal;
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

    public function getCodMarca()
    {
        return $this->cod_marca;
    }
    
    
    public function setCodMarca($cod_marca)
    {
        $this->cod_marca = $cod_marca;
        return $this;
    }

    private function verificarRepetidoAgregar(){
        $sql = "SELECT COUNT(nombre) > 0 FROM ".$this->tbl." WHERE upper(nombre) = upper(:0) AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getNombre()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Nombre ya existente."];
        }

        $sql = "SELECT COUNT(codigo) > 0 FROM ".$this->tbl." WHERE codigo = :0 AND estado_mrcb";
        $repetido = $this->consultarValor($sql, [$this->getCodigo()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"C�digo ya existente."];
        }
        return ["r"=>true, "msj"=>""];
    }

    private function verificarRepetidoEditar(){
        $sql = "SELECT COUNT(nombre) > 0 FROM ".$this->tbl." WHERE upper(nombre) = upper(:0) AND estado_mrcb  AND cod_producto <>:1 ";
        $repetido = $this->consultarValor($sql, [$this->getNombre(), $this->getCodProducto()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"Nombre ya existente."];
        }

        $sql = "SELECT COUNT(codigo) > 0 FROM ".$this->tbl." WHERE codigo = :0 AND estado_mrcb  AND cod_producto <>:1 ";
        $repetido = $this->consultarValor($sql, [$this->getCodigo(), $this->getCodProducto()]);

        if ($repetido){
            return ["r"=>false, "msj"=>"C�digo ya existente."];
        }
        return ["r"=>true, "msj"=>""];
    }

    private function seter($tipoAccion){
        //TipoAccion => + agregar, * editar, - eliminar
        $campos_valores = []; 
        $campos_valores_where = [];

        if ($tipoAccion != "-"){
            $campos_valores = [
                "nombre"=>strtoupper($this->getNombre()),
                "descripcion"=>strtoupper($this->getDescripcion()),
                "precio_unitario"=>$this->getPrecioUnitario(),
                "cod_marca"=>$this->getCodMarca(),
                "cod_unidad_medida"=>$this->getCodUnidadMedida(),
                "cod_presentacion"=>$this->getCodPresentacion(),
                "cod_categoria_producto"=>($this->getCodCategoriaProducto() == "" ? "0" : $this->getCodCategoriaProducto() ),
                "numero_imagen_principal"=>$this->getNumeroImagenPrincipal()
                ];

            if ($tipoAccion == "+"){
                $codigo = substr($this->getNombre(),0,3);
                $campos_valores["cod_producto"] = $this->getCodProducto();
                $campos_valores["codigo"] = $codigo;
            }
                
        }

        if ($tipoAccion != "+"){
            $campos_valores_where = ["cod_producto"=>$this->getCodProducto()];

            if ($tipoAccion == "-"){
                $campos_valores = ["estado_mrcb"=>"false"];
            }
        }

        $campos = ["valores"=>$campos_valores,"valores_where"=>$campos_valores_where];

        $this->lastCodigo = $this->getCodProducto();
        // $this->BITACORA_ON = false;
        return $campos;
    }

    private function obtenerNombreImg($nombreAntiguo, $i){
        $extension  = substr($nombreAntiguo, -3);
        $nombre_archivo_img = 'PRD_'.$this->getCodProducto().'_'.$i.'_'.time().'.'.$extension;
        return $nombre_archivo_img;
    }

    public function agregar() {
        $this->beginTransaction();
        try {            
            $objVerificar = $this->verificarRepetidoAgregar();
            if (!$objVerificar["r"]){
                return $objVerificar;
            }

            $this->setCodProducto($this->consultarValor("SELECT COALESCE(MAX(cod_producto)+1, 1) FROM producto"));

            $campos = $this->seter("+");
            $this->insert($this->tbl, $campos["valores"]);

            /*Tras la inserci��n del poducto, se procede a insertar lo siguiente (a la tabla producto_img)*/
            if ($this->getImgUrl() != null){
                foreach ($this->getImgUrl() as $key => $objImg) {
                    if ($objImg["check"] == 2){
                        $file = $objImg["file"];
                        $i = $objImg["i"];
                        $nuevoNombre = $this->obtenerNombreImg($file["name"], $i);
                        $campos_valores = ["numero_imagen"=>$i,
                                            "img_url"=>$nuevoNombre,
                                            "cod_producto"=>$this->getCodProducto()];
                        $this->insert("producto_img", $campos_valores);

                        if (!move_uploaded_file($file["tmp_name"], "../imagenes/productos/".$nuevoNombre)) {
                            $this->rollBack();
                            return array("rpt"=>false,"msj"=>"Error al subir la imagen.");
                        }
                    }
                }
 
            }
           
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
                return $objVerificar;
            }
            
            $campos = $this->seter("*");

            $this->update($this->tbl, $campos["valores"], $campos["valores_where"]);
            /*
            if ($this->modoActualizarImg == 2){
                if (!move_uploaded_file($this->getImgUrl()["tmp_name"], "../imagenes/productos/".$campos["valores"]["img_url"])) {
                    $this->rollBack();
                    return array("rpt"=>false,"msj"=>"Error al subir la imagen.");
                }
            }
            */
            /*Tras la inserci��n del poducto, se procede a insertar lo siguiente (a la tabla producto_img)*/
            if ($this->getImgUrl() != null){
                foreach ($this->getImgUrl() as $key => $objImg) {
                    if ($objImg["check"] == 2){
                        $file = $objImg["file"];
                        $i = $objImg["i"];
                        $nuevoNombre = $this->obtenerNombreImg($file["name"], $i);

                        $sql = "SELECT COUNT(img_url) > 0 FROM producto_img WHERE cod_producto = :0 AND numero_imagen = :1";
                        $existe = $this->consultarValor($sql, [$this->getCodProducto(), $i]);

                        if ($existe){
                            $campos_valores = ["img_url"=>$nuevoNombre];
                            $campos_valores_where = ["numero_imagen"=>$i, "cod_producto"=>$this->getCodProducto()];                                    
                            $this->update("producto_img", $campos_valores,$campos_valores_where);
                        } else {
                            $campos_valores = ["numero_imagen"=>$i,
                                            "img_url"=>$nuevoNombre,
                                            "cod_producto"=>$this->getCodProducto()];
                            $this->insert("producto_img", $campos_valores); 
                        }
                       
                        if (!move_uploaded_file($file["tmp_name"], "../imagenes/productos/".$nuevoNombre)) {
                            $this->rollBack();
                            return array("rpt"=>false,"msj"=>"Error al subir la imagen.");
                        }
                    }

                    if ($objImg["check"] == 0){
                        $campos_valores = ["img_url"=>"default_producto.jpg"];
                        $campos_valores_where = ["numero_imagen"=>$objImg["i"], "cod_producto"=>$this->getCodProducto()];                                    
                        $this->update("producto_img", $campos_valores,$campos_valores_where);
                    }
                }  
            }
            

            $this->commit();
            return array("rpt"=>true,"msj"=>"Se ha actualizado exitosamente");
        } catch (Exception $exc) {
            $this->rollBack();
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function listar(){
        try {
            $sql = "SELECT  p.cod_producto,
                        COALESCE( (SELECT pi.img_url FROM producto_img pi WHERE pi.cod_producto = p.cod_producto AND pi.numero_imagen = p.numero_imagen_principal),
                             'default_producto.jpg') as img_url,
                        CONCAT(p.codigo,' - ',p.nombre) as nombre, 
                        m.nombre as marca,
                        p.descripcion, precio_unitario, um.descripcion as unidad_medida, 
                        cp.nombre as categoria_producto,
                        pr.descripcion as presentacion
                        FROM producto p
                        INNER JOIN unidad_medida um ON p.cod_unidad_medida = um.cod_unidad_medida
                        LEFT JOIN categoria_producto cp ON p.cod_categoria_producto = cp.cod_categoria_producto
                        LEFT JOIN marca m ON m.cod_marca = p.cod_marca
                        LEFT JOIN presentacion pr ON pr.cod_presentacion = p.cod_presentacion
                        WHERE p.estado_mrcb
                        ORDER BY p.nombre";
            $resultado = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function leerDatos(){
        try {
            $sql = "SELECT p.codigo,p.nombre, descripcion, precio_unitario, cod_unidad_medida,  
                    cod_categoria_producto as cod_categoria_producto, 
                    cod_marca, cod_presentacion,
                    numero_imagen_principal
                    FROM producto p
                    WHERE cod_producto = :0";
            $resultado = $this->consultarFila($sql, $this->getCodProducto());

            $sql = "SELECT numero_imagen, img_url
                    FROM producto_img pi
                    WHERE pi.cod_producto = :0";
            $resultado["imagenes"] = $this->consultarFilas($sql, $this->getCodProducto());
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

    public function obtenerProductos(){
        try{


            $sql = "SELECT cod_producto, CONCAT(codigo,' - ',nombre) as nombre
                        FROM producto 
                        WHERE estado_mrcb
                        ORDER BY  nombre";
            $data = $this->consultarFilas($sql);
            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerDataMantenimiento(){
        try{

            $sql = "SELECT cod_unidad_medida as codigo, 
                        descripcion as nombre
                        FROM unidad_medida
                        ORDER BY  nombre";
            $unidad_medida = $this->consultarFilas($sql);

            $sql = "SELECT cod_tipo_categoria as codigo, nombre
                        FROM tipo_categoria
                        WHERE estado_mrcb
                        ORDER BY  nombre";
            $tipos = $this->consultarFilas($sql);

            $sql = "SELECT cod_categoria_producto as codigo, nombre, cod_tipo_categoria
                        FROM categoria_producto
                        WHERE estado_mrcb
                        ORDER BY  nombre";
            $categorias = $this->consultarFilas($sql);

            $sql = "SELECT cod_marca as codigo, nombre
                        FROM marca
                        WHERE estado_mrcb
                        ORDER BY nombre";
            $marcas = $this->consultarFilas($sql);

            $sql = "SELECT cod_presentacion as codigo, descripcion as nombre
                        FROM presentacion
                        WHERE estado_mrcb
                        ORDER BY descripcion";
            $presentaciones = $this->consultarFilas($sql);

            return array("rpt"=>true,"data"=>["tipos"=>$tipos,"categorias"=>$categorias,"unidad_medida"=>$unidad_medida, "marcas"=>$marcas, "presentaciones"=>$presentaciones]);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerProductosCatalogo($cadenaBusqueda){
        try{
            
            $sqlWhere = "";
            $iteradorParam = 0;
            $params = [];

            if ($this->getCodCategoriaProducto() != ""){
                $sqlWhere .= " AND p.cod_categoria_producto = :".$iteradorParam++." ";
                array_push($params, $this->getCodCategoriaProducto());
            }

            if ($this->getCodTipoCategoria() != ""){
                $sqlWhere .= " AND cp.cod_tipo_categoria = :".$iteradorParam++." ";
                array_push($params, $this->getCodTipoCategoria());
            }

            if (strlen($cadenaBusqueda) > 0){
                $sqlWhere .= " AND p.nombre LIKE CONCAT('%',:".$iteradorParam++.",'%')";
                array_push($params, $cadenaBusqueda);
            }

            $sql = "SELECT p.cod_producto, p.nombre, CAST(precio_unitario as DECIMAL(10,2)) as precio_unitario,
                        COALESCE( (SELECT pi.img_url FROM producto_img pi WHERE pi.cod_producto = p.cod_producto AND pi.numero_imagen = p.numero_imagen_principal),
                             'default_producto.jpg') as img_url
                        FROM producto p
                        INNER JOIN categoria_producto cp ON p.cod_categoria_producto = cp.cod_categoria_producto AND cp.estado_mrcb
                        WHERE p.estado_mrcb
                        ".$sqlWhere."
                        ORDER BY nombre";

            $data = $this->consultarFilas($sql, $params);

            return array("rpt"=>true,"data"=>$data);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function obtenerInformacion(){
        try {
            $sql = "SELECT cod_producto as id, CONCAT(p.codigo,' - ',p.nombre) as nombre,       
                        m.nombre as marca,
                        pr.descripcion as presentacion,
                        descripcion, CAST(precio_unitario AS DECIMAL(10,2)) as precio_unitario
                    FROM producto p
                    LEFT JOIN marca m ON m.cod_marca = p.cod_marca
                    LEFT JOIN presentacion pr ON pr.cod_presentacion = p.cod_presentacion
                    WHERE cod_producto = :0";
            $resultado = $this->consultarFila($sql, $this->getCodProducto());

            $sql = "SELECT numero_imagen, img_url
                    FROM producto_img pi
                    WHERE pi.cod_producto = :0";
            $resultado["imagenes"] = $this->consultarFilas($sql, $this->getCodProducto());
            return array("rpt"=>true,"data"=>$resultado);
        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}