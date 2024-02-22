<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Impresión de Productos";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php  include '_css/main.css.php'; ?>
    </head>
    <body class="no-skin">
        <?php include './partials/_globals/navbar.php'; ?>

        <div class="main-container ace-save-state" id="main-container">
            <?php include './partials/_globals/menu.php'; ?>

            <div class="main-content">
              <div class="main-content-inner">

                <?php include './partials/_globals/breadcrumb.mantenimiento.php' ?>

                <div class="page-content">
               
                  <?php include './partials/_globals/ace.settings.php' ?>

                  <div class="page-header">
                    <h1>
                      <?php echo $TITULO_PAGINA; ?>
                    </h1>
                  </div><!-- /.page-header -->

                  <div class="row">
                    <div class="col-ms-12">
                      <div class="row">
                        <div class="col-xs-offset-6 col-xs-6 col-sm-offset-8 col-sm-4 text-right">
                          <a id="btn-agregar" type="button" class="btn btn-primary">
                          <i class="fa fa-plus bigger-120 white"></i> AGREGAR</a>
                          <a id="btn-imprimir" type="button" class="btn btn-secondary">
                          <i class="fa fa-print bigger-120 white"></i> IMPRIMIR</a>
                        </div>
                      </div><!-- /.row -->

                      <div class="space-6"></div>

                      <div class="row">
                        <div class="col-xs-12">
                          <div class="table-responsive">           
                              <small> 
                                <table id="tbl-listado" class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                    <thead>
                                      <tr>
                                        <th width="45px"></th>
                                        <th width="70px">Cantidad</th>
                                        <th width="90px">C.Barras</th>
                                        <th>Nombre Producto</th>
                                        <th width="150px">Categoría</th>
                                        <th width="150px">Marca</th>
                                        <th width="100px">P. Venta</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                          <script id="tpl8Listado" type="handlebars-x">
                                          {{#.}}
                                              <tr data-id="{{id}}">
                                                  <td>
                                                      <button class="btn btn-xs btn-danger btn-quitar">
                                                          <i class="fa fa-remove bigger-130"></i>
                                                      </button>
                                                  </td>
                                                  <td><input type="numeric" class="form-control text-right txt-cantidad" value="1"/></td>
                                                  <td class="text-center">{{codigo_generado}}</td>
                                                  <td>{{empresa_especial}} - {{producto}}</td>
                                                  <td>{{categoria}}</td>
                                                  <td>{{marca}}</td>
                                                  <td>S/ {{precio_unitario}}</td>
                                              </tr>
                                          {{/.}}
                                          </script>                                    
                                    </tbody>
                                </table>
                              </small>
                          </div>  <!-- table-responsive --> 
                        </div> 
                      </div>

                      <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->


            <?php include './partials/_globals/footer.php'; ?>
           
        </div><!-- /.main-container -->

      <div id="mdlBuscarProducto" class="modal fade" tabindex="-1" style="display: none;">
        <div class="modal-dialog modal-lg">
          <form id="frmgrabar">
            <div class="modal-content">
              <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 class="smaller lighter blue no-margin">Buscar Producto</h3>
              </div>

              <div class="modal-body">
                <div class="row">
                  <div class="col-xs-12">
                    <div class="form-group">
                    <label class="control-label">Busque por NOMBRE de producto. Use un CLICK/TAP al producto para agregarlo a la ronda de impresión.</label>
                    <span class="input-icon" style="width:100%">
                      <i class="ace-icon fa fa-search blue"></i>
                      <input id="txt-buscar" type="search"  class="form-control"placeholder="Buscar..."/>
                    </span>
                    </div>
                  </div>
                </div>
                <div class="space-6"></div>
                <h5>Seleccionados: <span id="lbl-seleccionados">0</span></h5>
                <div class="row">
                  <div class="col-xs-12" style="max-height: 280px;overflow-x: scroll;">
                    <table class="table tbl-detalle" id="tbldetalle">
                      <thead>
                        <tr>
                          <th>Producto</th>
                          <th style="width:160px">Categoría</th>
                          <th style="width:160px">Marca</th>
                          <th style="width:120px">Talla</th>
                          <th style="width:90px">Precio Unit.</th>
                        </tr>
                      </thead>
                      <tbody id="blk-listaproductos">
                        <script id="tpl8ListaProducto" type="handlebars-x">
                          {{#.}}
                          <tr class="pointer {{#if seleccionado}}seleccionado-tr{{/if}}" data-id="{{id}}">
                            <td>{{producto}}</td>	
                            <td style="width:160px">{{categoria}}</td>
                            <td style="width:160px">{{marca}}</td>
                            <td style="width:120px">{{talla}}</td>
                            <td style="width:90px">S/ {{precio_unitario}}</td>
                          </tr>
                          {{else}}
                          <tr class="tr-null">
                            <td colspan="100">
                              <div class="alert alert-info">
                                <strong>No hay PRODUCTOS para mostrar.</strong>
                              </div>
                            </td>
                          </tr>	
                          {{/.}}
                        </script>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                  <i class="ace-icon fa fa-times"></i>
                  CERRAR
                </button>
                <button type="button" class="btn btn-sm btn-primary pull-right" id="btn-confirmarproductos">
                  <i class="ace-icon fa fa-check"></i>
                  CONFIRMAR PRODUCTOS
                </button>
              </div>
            </div><!-- /.modal-content -->
          </form>
        </div>
      </div>

        <?php  include '_js/main.js.php';?>
        <script src="js/producto.impresion.vista.js"></script>
    </body>

</html>