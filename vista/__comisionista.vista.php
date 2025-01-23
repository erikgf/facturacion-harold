<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestión de Comisionistas";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <link rel="icon" type="image/jpeg" href="../imagenes/logo_peque.jpg" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php include '_css/main.css.php'; ?>
    </head>
    <body class="no-skin">
        <?php include 'navbar.php'; ?>

        <div class="main-container ace-save-state" id="main-container">
             <script type="text/javascript">
                try{ace.settings.loadState('main-container')}catch(e){}
             </script>

             <?php include 'menu.php'; ?>

            <div class="main-content">
              <div class="main-content-inner">

                <?php include 'breadcrumb.mantenimiento.php' ?>

                <div class="page-content">
               
                  <?php include 'ace.settings.php' ?>

                  <div class="page-header">
                    <h1>
                      <?php echo $TITULO_PAGINA; ?>
                      <!--
                      <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Gestión y mantenimiento
                      </small>
                      -->
                    </h1>
                  </div><!-- /.page-header -->

                  <div class="row">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-sm-offset-9 col-sm-3">
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
                                      <th width="100px">Acción</th>
                                      <th width="100px">DNI</th>
                                      <th>Nombres</th>
                                      <th width="120px">Celular</th>
                                      <th width="200px">Correo</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      {{#this}}
                                        <tr>
                                            <td>
                                                <button title="Asignar Comisión Producto" class="btn btn-xs btn-success" onclick ="app.gestionarProductos({{cod_comisionista}},'{{nombres}}')">
                                                <i class="fa fa-briefcase bigger-130"></i>
                                                </button>
                                                <button title="Editars" class="btn btn-xs btn-warning" onclick ="app.editar({{cod_comisionista}})" data-toggle="modal" data-target="#mdlRegistro">
                                                <i class="fa fa-edit bigger-130"></i>
                                                </button>
                                                <button title="Eliminar" class="btn btn-xs btn-danger" onclick ="app.eliminar({{cod_comisionista}})">
                                                <i class="fa fa-trash bigger-130"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">{{numero_documento}}</td>
                                            <td>{{nombres}}</td>
                                            <td>{{celular}}</td>
                                            <td>{{correo}}</td>
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

           <?php include 'footer.php'; ?>
           
        </div><!-- /.main-container -->


        <div id="mdlRegistro" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog">
              <form id="frmgrabar">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Mantenimiento</h3>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                              <div class="col-xs-4" id="blknumerodocumento">
                                <div class="control-group">
                                  <label class="control-label">Número DNI: </label>
                                  <input type="text" name="txtnumerodocumento" id="txtnumerodocumento" class="form-control" minlength="8" maxlength="11" placeholder="Número documento...">
                                </div>
                              </div>                            
                        </div>
                        <div class="row">
                              <div class="col-xs-12">
                                <div class="control-group">
                                  <label class="control-label">Nombres: </label>
                                  <input type="text" name="txtnombres" id="txtnombres" class="form-control" placeholder="Nombres..." required="">
                                </div>
                              </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4">
                                <div class="control-group">
                                  <label class="control-label">Celular: </label>
                                  <input type="text" name="txtcelular" minlength="9" maxlength="9" id="txtcelular" class="form-control" placeholder="Celular..." >
                                </div>
                            </div>
                            <div class="col-xs-offset-2 col-xs-6">
                                <div class="control-group">
                                  <label class="control-label">Correo: </label>
                                  <input type="email" name="txtcorreo" id="txtcorreo" class="form-control" placeholder="Correo..." >
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
        </div>

        <div id="mdlProductos" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog modal-lg">
              <form id="frmgrabarproductos">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Gestionar Comisiones de Productos</h3>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                              <div class="col-xs-8 col-sm-6">
                                <div class="control-group">
                                  <label class="control-label">Comisionista </label>
                                  <input type="text" readonly name="txtcomisionista" id="txtcomisionista" class="form-control">
                                </div>
                              </div>     
                              <div class="col-xs-4 col-sm-offset-2 col-sm-4">
                                  <div class="control-group">
                                    <br><button class="btn btn-primary btn-block btn-sm" id="btnagregarproducto">AGREGAR PRODUCTO</button>
                                  </div>
                              </div>                       
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                              <table class="table table-striped">
                                <thead>
                                    <tr>
                                      <th width="100px">Acción</th>
                                      <th>Producto</th>
                                      <th width="150px">Tipo Comisión</th>
                                      <th width="150px">Valor Comisión</th>
                                    </tr>
                                  </thead>
                                <tbody id="tblproductoscomision">
                                    <tr class="tr-null">
                                      <td class="text-center" colspan="4">Sin productos asignados</td>
                                    </tr>
                                </tbody>
                              </table>
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
        </div>

        <script id="tpl8ProductoComision" type="handlebars-x">
            {{#data}}
            <tr>
              <td>
                <button title="Eliminar" class="btn btn-xs btn-danger eliminar">
                <i class="fa fa-close bigger-130"></i>
                </button>
              </td>
              <td>
                <select class="form-control productos" data-cod="{{cod_producto}}">
                  <option value="">Seleccionar producto</option>
                  {{#../productos}}
                    <option value="{{cod_producto}}" {{#if_ ../cod_producto '==' this.cod_producto}}selected{{/if_}}>{{nombre}}</option>
                  {{/../productos}}
                </select>
              </td>
              <td>
              <select class="form-control">
                  <option value="P" {{#if_ tipo_comision '==' 'P'}}selected{{/if_}}>PORCENTAJE</option>
                  <option value="M" {{#if_ tipo_comision '==' 'M'}}selected{{/if_}}>MONTO FIJO</option>
                  </select>
              </td>
              <td><input type="number" class="form-control" value="{{valor_comision}}"></td>
            </tr
            {{else}}
              <tr class="tr-null">
                <td class="text-center" colspan="4">Sin productos asignados</td>
              </tr>
            {{/data}}
         </script>  

        <?php  include '_js/main.js.php'; ?>
        
        <script src="js/comisionista.vista.js" type="text/javascript"></script>
    </body>

</html>



