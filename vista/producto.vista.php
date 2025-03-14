<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestión de Productos";
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
                        <div class="col-xs-offset-6 col-xs-6 col-sm-offset-8 col-sm-4">
                          <a type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#mdlRegistro" onclick="app.agregar()">
                          <i class="fa fa-plus bigger-120 white"></i> NUEVO REGISTRO</a>
                        </div>
                      </div><!-- /.row -->

                      <div class="space-6"></div>

                      <div class="row">
                        <div class="col-xs-12">
                          <div  id="listado" class="table-responsive">           
                            <script id="tpl8Listado" type="handlebars-x">
                              <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th width="80px">Acción</th>
                                      <th width="120px">C.Barras</th>
                                      <th>Nombre</th>
                                      <th width="150px">Marca</th>
                                      <th width="120px">Talla</th>
                                      <th width="130px">Precio Venta</th>
                                      <th width="150px">Categoría</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      {{#this}}
                                        <tr>
                                            <td>
                                              {{id}}
                                                <button class="btn btn-xs btn-warning" onclick ="app.editar({{id}})" data-toggle="modal" data-target="#mdlRegistro">
                                                <i class="fa fa-edit bigger-130"></i>
                                                </button>
                                                <button class="btn btn-xs btn-danger" onclick ="app.eliminar({{id}})">
                                                <i class="fa fa-trash bigger-130"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">{{codigo_generado}}</td>
                                            <td>{{producto}}</td>
                                            <td>{{marca}}</td>
                                            <td>{{tallas}}</td>
                                            <td>{{precio_unitario}}</td>
                                            <td>{{categoria}}</td>
                                         </tr>
                                      {{/this}}
                                  </tbody>
                              </table>
                           </script>                                    
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


        <div id="mdlRegistro" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog modal-lg">
              <form id="frmgrabar">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Mantenimiento</h3>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                              <div class="col-xs-4 col-sm-2">
                                <div class="form-group">
                                    <label class="control-label">Empresa Esp.: (*) </label>
                                    <select name="cboempresaespecial" id="cboempresaespecial" class="form-control" required="">
                                      <option value="ABK" selected>ABK</option>
                                      <option value="MJS">MJS</option>
                                    </select>
                                </div>
                              </div>
                              <div class="col-xs-4 col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Código Único</label>
                                    <input name="txtcodigounico" maxlength="20" placeholder="Código Único" id="txtcodigounico" class="form-control"/>
                                </div>
                              </div>
                        </div>

                        <div class="row">
                              <div class="col-xs-6">
                                <div class="form-group">
                                    <label class="control-label">Nombre: (*)</label>
                                    <textarea name="txtnombre" id="txtnombre" placeholder="Nombre..." class="form-control" required=""></textarea>
                                </div>
                              </div>
                              <div class="col-xs-6">
                                <div class="form-group">
                                  <label class="control-label">Descripción: </label>
                                  <textarea name="txtdescripcion" id="txtdescripcion" class="form-control" placeholder="Descripción..."></textarea>
                                </div>
                              </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-6 col-sm-4">
                              <div class="form-group">
                                  <label class="control-label">Marca:</label>
                                  <select name="cbomarca" id="cbomarca" class="form-control" required="">
                                  </select>
                              </div>
                            </div>
                            <div class="col-xs-6 col-sm-4">
                              <div class="form-group">
                                  <label class="control-label">Tipo de Categoría: (*)</label>
                                  <select name="cbotipo" id="cbotipo" class="form-control" required="">
                                  </select>
                              </div>
                            </div>
                            <div class="col-xs-6 col-sm-4">
                              <div class="form-group">
                                <label class="control-label">Categoría Producto: (*)</label>
                                <select name="cbocategoria" id="cbocategoria" required class="form-control">
                                </select>
                              </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-xs-6 col-sm-4" style="display:none;">
                              <div class="form-group">
                                <label class="control-label">Presentación Producto: </label>
                                <select name="cbopresentacion" id="cbopresentacion" class="form-control">
                                </select>
                              </div>
                            </div>
                              <div class="col-xs-6 col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Precio Venta Unitario: (S/) (*) </label>
                                     <input type="number" step="0.001" name="txtpreciounitario" id="txtpreciounitario" class="form-control" placeholder="Precio venta..." required="">
                                </div>
                              </div>
                              <div class="col-xs-6 col-sm-4">
                                <div class="form-group">
                                  <label class="control-label">Unidad Medida:(*) </label>
                                  <select name="cbounidadmedida" id="cbounidadmedida" class="form-control" required="">
                                  </select>
                                </div>
                              </div>
                              <div class="col-xs-6 col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Tallas: </label>
                                    <input name="txttallas" id="txttallas"  type="text" placeholder="Tallas..." class="form-control"/>
                                </div>
                              </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-offset-9 col-xs-3">
                                <div class="form-group">
                                    <label class="control-label">N° Imagen Principal (*)</label>
                                     <select name="cboimagenprincipal" id="cboimagenprincipal"  class="form-control" required="">
                                      <?php $i = 1; ?>
                                      <?php while ($i <= 10) : ?>
                                        <option value="<?php echo $i ?>"><?php echo $i?></option>
                                      <?php $i++; ?>
                                      <?php endwhile; ?>
                                     </select>
                                </div>
                                <small>(*) Aparecerá como imagen principal en las miniaturas.</small>
                             </div>
                        </div>

                        <h4>Imágenes</h4>
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="tabbable">
                              <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="tabImgProductos">
                              </ul>
                              <div class="tab-content tab-content-imagenes">                       
                              </div>
                            </div>
                          </div>
                        </div>

                      <div class="modal-footer">
                        <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                          <i class="ace-icon fa fa-times"></i>
                          Cancelar
                        </button>
                        <button type="submit"  class="btn btn-sm btn-primary pull-right">
                          <i class="ace-icon fa fa-save"></i>
                          Guardar
                        </button>
                      </div>
                    </div><!-- /.modal-content -->
              </form>
            </div>

            <script id="tpl8TabPane" type="handlebars-x">
              {{#.}}              
                  <div data-id="{{id}}" id="img_{{id}}" class="tab-pane {{#is_active}}active{{/is_active}}">
                    <div class="row">
                      <div class="col-xs-6 form-group">
                        <input type="file" class="form-control on-cambiar-imagen"  accept="image/*">
                        <div class="text-center" style="padding:10px">
                          <a class="borrar-img-producto" style="display:none"><i class="fa fa-ban"></i></a>
                          <img style="width:256px" data-original="{{img_url}}" src="{{img_url}}">
                          <br><a href="javascript:;" class="on-imagen-defecto">Sin imagen</a>
                        </div>
                      </div>
                    </div>
                  </div>
              {{/.}}
            </script>

            <script id="tpl8Tab" type="handlebars-x">
              {{#.}}
                <li class="{{#is_active}}active{{/is_active}}">
                  <a data-toggle="tab" href="#img_{{id}}" aria-expanded="false">{{id}}</a>
                </li>
              {{/.}}
            </script>

            <script id="tpl8Combo" type="handlebars-x">
                <option value="">Seleccionar {{rotulo}}</option>
                {{#items}}
                  <option value='{{id}}'>{{nombre}}</option>
                {{/items}}
            </script>
        </div>

        <?php  include '_js/main.js.php';?>
        <script src="js/producto.vista.1.js"></script>
    </body>

</html>



