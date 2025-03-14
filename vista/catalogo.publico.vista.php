<?php
include '../datos/local_config_web.php';
$TITULO_PAGINA = "Catálogo";
$fechaHoy = date('Y-m-d');

$mostrarPrecios = true;// $objAcceso->getUsuario() != "";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <link rel="icon" type="image/jpeg" href="../imagenes/logo_peque.jpg" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php  include '_css/main.css.php'; ?>
    </head>
    <body class="no-skin">
    <?php include './partials/_globals/navbar.php'; ?>

        <div class="main-container ace-save-state" id="main-container">
            <script type="text/javascript">
                try{ace.settings.loadState('main-container')}catch(e){}
            </script>

            <div class="main-content">
              <div class="main-content-inner">

               <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                  <ul class="breadcrumb">
                    <li class="active"><i class="ace-icon fa fa-home home-icon"></i><?php  echo $TITULO_PAGINA; ?></li>
                  </ul><!-- /.breadcrumb -->
               </div>

                <div class="page-content">
               
                  <?php include './partials/_globals/ace.settings.php' ?>

                  <div class="page-header">
                    <h1>
                      <?php echo $TITULO_PAGINA; ?>
                      <!--
                      <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Gestión y mantenimiento
                      </small>s
                      -->
                    </h1>
                  </div><!-- /.page-header -->
          
                  <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-5">
                      <div class="control-group">
                        <label class="control-label">Búsqueda</label>
                        <span class="input-icon" style="width:100%">
                          <i class="ace-icon fa fa-search blue"></i>
                          <input id="txtbuscar" type="search"  class="form-control"placeholder="Buscar..."/>
                        </span>
                      </div>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-2">
                      <div class="control-group">
                        <label class="control-label">Filtrar por Marca</label>
                        <select id="cbofiltromarca" class="form-control">
                          <option value="">Todos</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-2">
                      <div class="control-group">
                        <label class="control-label">Filtrar por Tipo</label>
                        <select id="cbofiltrotipo" class="form-control">
                          <option value="">Todos</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-2">
                      <div class="control-group">
                        <label class="control-label">Filtrar por Categoría</label>
                        <select id="cbofiltrocategoria" class="form-control">
                          <option value="">Todos</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="space-10"></div>

                  <div class="row">
                    <div class="col-xs-12 col-md-4 text-left">
                      <h2 id="lbl-cargando" style="display: none;"><i class="fa fa-spin fa-spinner"></i>  Cargando...</h2>
                    </div>
                    <div class="col-xs-12 col-md-8 text-right">
                       <ul class="pagination"></ul>
                    </div>
                  </div>
                  
                  <div class="row" id="blklistaproductos">
                    <script id="tpl8ListaProducto" type="handlebars-x">
                      {{#.}}
                      <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="widget-box widget-color-blue">
                          <div class="widget-header text-center">
                            <h6 class="widget-title bigger lighter ">{{nombre}}</h6>
                          </div>
                          <div class="widget-body">
                            <div class="widget-main text-center">
                              <img src="{{img_url}}" class="img-catalogo-res">
                              <hr>
                              <?php if ($mostrarPrecios){
                                      echo '<div class="price precios-mostrar">S/ {{precio_unitario}}</div>';
                                    }
                              ?>
                            </div>
                            <div>
                              <a href="javascript:;" class="btn btn-block btn-primary" onclick="app.obtenerProducto({{id}})">
                                <i class="ace-icon fa fa-search bigger-110"></i>
                                <span>Ver Información</span>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                      {{else}}
                          <div class="alert alert-info">
                            <strong>No hay PRODUCTOS para mostrar.</strong>
                          </div>
                      {{/.}}
                    </script>
                  </div>


                  <div class="row">
                    <div class="col-xs-12 text-right">
                       <ul class="pagination">
                       </ul>
                    </div>
                  </div>

                <div id="mdlDetalle" class="modal fade" tabindex="-1" style="display: none;">
                    <div class="modal-dialog modal-lg">
                      <form id="frmgrabar">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h3 class="smaller lighter blue no-margin">Información del Producto</h3>
                              </div>

                              <div class="modal-body">
                                <div class="row">
                                      <div class="col-xs-6">
                                        <div class="control-group">
                                          <img style="width:100%" id="imgproductoprincipal" src="../imagenes/productos/default_producto.jpg">
                                        </div>
                                      </div>  
                                      <div class="col-xs-6">
                                        <div class="control-group" id="blkinformacionproducto">
                                          <script id="tpl8Info" type="handlebars-x">
                                            {{#.}}
                                              <h2 class="control-label">{{nombre}}</h2>
                                              <div class="row">
                                                <div class="col-sm-6">
                                                  <div style="font-size:1.25em"><strong>Categoría: </strong> <div >{{categoria}}</div></div>
                                                </div>
                                                <div class="col-sm-6">
                                                  <div style="font-size:1.25em"><strong>Marca: </strong> <div>{{marca}}</div></div>
                                                </div>
                                              </div>
                                              <p style="font-size:1.15em"><strong>Descripción: </strong> <div class="img-descripcion-catalogo">{{descripcion}}</div></p>
                                              <h3 class="precios-mostrar" <?php echo $mostrarPrecios ? 'style="display:block"' : 'style="display:none"'; ?>></strong>Precio Venta: S/ {{precio_unitario}}</strong></h3>
                                              <a class="btn btn-xs btn-info" <?php echo $mostrarPrecios ? 'style="display:none"' : 'style="display:block"'; ?> onclick="app.verPrecio(this)">VER PRECIO</a>
                                            {{/.}}
                                          </script>
                                        </div>
                                      </div>                            
                                </div>
                                <div class="space-6"></div>
                                <div class="row">
                                  <div class="col-xs-12">
                                     <div id="blkcarruselimg" class="carousel-items">
                                      <script id="tpl8Items" type="handlebars-x">
                                       {{#.}}
                                        <div class="item">
                                          <img class="img-thumbnail" src="{{img_url}}">
                                        </div>
                                       {{/.}}
                                      </script>
                                    </div>
                                  </div>  
                                </div>

                              </div>
                              <script id="tpl8Combo" type="handlebars-x">
                                  <option value="">Todos</option>
                                  {{#.}}
                                    <option value='{{id}}'>{{nombre}}</option>
                                  {{/.}}
                              </script>                                

                              <div class="modal-footer">
                                <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                  <i class="ace-icon fa fa-times"></i>
                                  Cerrar
                                </button>
                              </div>
                            </div><!-- /.modal-content -->
                      </form>
                    </div>
                </div>

                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->

           <?php include './partials/_globals/footer.php'; ?>

           <!--
           <script id="tpl8Paginacion" type="handlebars-x">
                <li class="pag-first disabled">
                  <a href="javascript:;" onclick="app.previousPagina()">
                    <i class="ace-icon fa fa-angle-double-left"></i>
                  </a>
                </li>
                <li class="pags active">
                  <a href="javacript:;" onclick="app.setPagina(1)">1</a>
                </li>
                {{#.}}
                  <li class="pags">
                    <a href="javacript:;" onclick="app.setPagina({{this}})">{{this}}</a>
                  </li>
                {{/.}}
                <li class="pag-last">
                  <a href="javascript:;" onclick="app.nextPagina()">
                    <i class="ace-icon fa fa-angle-double-right"></i>
                  </a>
                </li>
          </script>
                                  -->

          <script id="tpl8Paginacion" type="handlebars-x">
              {{#.}}
                <li class="pags {{#if active}}active{{/if}}">
                  <a href="javacript:;" data-url="{{url}}">{{{label}}}</a>
                </li>
              {{/.}}
          </script>

        </div><!-- /.main-container -->

        <?php  include '_js/main.js.php';?>
        <script type="text/javascript" src="js/catalogo.publico.vista.js<?php echo '?'.time();?>"></script>
        <script type="text/javascript">
          var MOSTRAR_PRECIOS = <?php echo $mostrarPrecios ? 'true' : 'false' ?>;
        </script>
    </body>

</html>



