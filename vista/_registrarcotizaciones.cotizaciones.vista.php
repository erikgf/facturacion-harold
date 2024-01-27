	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-5">
			<h4 style="color: #438eb9;font-weight: bold;" id="lblrotuloedicion"></h4>
		</div>
	</div>

	<div class="row">
		<div class="col-md-offset-8 col-xs-offset-3 col-md-2 col-xs-4">
			<div class="form-group">
				<label class="control-label">Serie</label>
				<input class="form-control text-center" required id="txtserie" value="001" maxlength="3"/>
			</div>
		</div>
		<div class="col-md-2 col-xs-5">
			<div class="form-group">
				<label class="control-label">Correlativo</label>
				<input class="form-control text-center" required id="txtcorrelativo" value="0000001" maxlength="6"/>
			</div>
		</div>
	</div>	

	<h4>Información Cliente</h4>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group">
				<label class="control-label">Buscar Cliente</label>
				<select class="form-control" data-placeholder="Seleccionar opción" id="cboclientebuscar">
					<script type="handlebars-x" id="tpl8cboClientesBuscar">
						{{#.}}
							<option value="{{cod_cliente}}">{{#numero_documento}}{{this}} -{{/numero_documento}} {{nombres}} {{apellidos}}</option>
						{{/.}}
					</script>
				</select>			
			</div>
		</div>
	</div>
	<div class="space-10"></div>
	<div class="row">
		<div class="col-sm-2 col-xs-6">
			<div class="form-group">
				<label class="control-label">Tipo Documento</label>
				<select class="form-control" id="cbotipodocumento">
					 <option value="0">S/D</option>
					 <option value="1">DNI</option>
                     <option value="4">CARNET EXT.</option>
                     <option value="6">RUC</option>
                     <option value="7">PASAPORTE</option>
				</select>			
			</div>
		</div>
		<div class="col-sm-2 col-xs-12" style="display:none;" id="blknumerodocumento">
			<div class="form-group" >
				<label class="control-label">Núm. Documento</label>
				<input class="form-control" id="txtnumerodocumento" placeholder="Núm. Documento" maxlength="12"/>
			</div>
		</div>
		<div class="col-sm-8 col-xs-12" >
			<div class="form-group" style="display:none">
				<label class="control-label">Razón Social</label>
				<input class="form-control" id="txtrazonsocial" placeholder="Razón Social..." maxlength="300"/>		
			</div>
		</div>
		<div class="col-sm-4 col-xs-12">
			<div class="form-group">
				<label class="control-label">Nombre Cliente</label>
				<input class="form-control" id="txtcliente" placeholder="Nombre cliente..." maxlength="300"/>		
			</div>
		</div>
		<div class="col-sm-4 col-xs-12">
			<div class="form-group">
				<label class="control-label">Apellidos Cliente</label>
				<input class="form-control" id="txtapellidos" placeholder="Apellidos cliente..." maxlength="200"/>		
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label class="control-label">Dirección</label>
				<input class="form-control" id="txtdireccion" placeholder="Dirección" maxlength="40"/>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="form-group">
				<label class="control-label">Celular</label>
				<input class="form-control" id="txtcelular" placeholder="Celular" maxlength="10"/>
			</div>
		</div>

		<div class="col-sm-3" style="display:none;">
			<div class="form-group">
				<label class="control-label">Núm. Contacto</label>
				<input class="form-control" id="txtnumerocontacto" placeholder="Número contacto" maxlength="10"/>
			</div>
		</div>

		<div class="col-sm-3">
			<div class="form-group">
				<label class="control-label">Correo</label>
				<input class="form-control" id="txtcorreo" placeholder="Correo" maxlength="40"/>
			</div>
		</div>
	</div>

	<div class="space-6"></div>

	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<label class="control-label ">Días Crédito</label>
				<input type="number" class="form-control" value="" name="txtcondiciondiascredito" id="txtcondiciondiascredito" required placeholder="Días Crédito...">
			</div>	
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<label class="control-label ">Días Validez</label>
				<input type="number" class="form-control" value="" name="txtcondiciondiasvalidez" id="txtcondiciondiasvalidez" required placeholder="Días Validez...">
			</div>	
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<label class="control-label ">Días Entrega</label>
				<input type="number" class="form-control" value="" name="txtcondiciondiasentrega" id="txtcondiciondiasentrega" required placeholder="Días Entrega...">
			</div>	
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<label class="control-label ">Costo Delivery</label>
				<input type="number" class="form-control" value="" name="txtcondiciondelivery" id="txtcondiciondelivery" value="0.00" required placeholder="Costo Delivery...">
			</div>	
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class="form-group">
				<label class="control-label ">Observaciones</label>
				<textarea class="form-control" id="txtobservaciones"></textarea>
			</div>
		</div>
		<div class="col-sm-3 col-xs-6">
			<div class="form-group">
				<label class="control-label ">Fecha Cotización</label>
				<input name="txtfechacotizacion" id="txtfechacotizacion" type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" <?php echo $objAcceso->getCodRol() == '1' ? '' : 'readonly'?>/>
			</div>
		</div>
	</div>
	<hr>	
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<h4>Detalle de Cotización</h4>
		</div>
		<div class="col-xs-12 col-sm-6">
			<button  id="btnagregarproducto" class="btn btn-xs" style="float: right;"><span class="fa fa-plus"></span> Agregar Producto</button>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="table-responsive">
				<table class="table tbl-detalle" id="tbldetalle">
					<thead>
						<tr>
							<th style="width:50px">Item</th>
							<th>Producto</th>
							<th>Marca</th>
							<th class="text-center" style="width:100px">F.Venc.</th>
							<th class="text-center" style="width:100px">Lote</th>
							<th class="text-center" style="width:85px">P.U.</th>
							<th class="text-center" style="width:85px">Cantidad</th>
							<th class="text-center" style="width:100px">Subtotal</th>
						</tr>
					</thead>
					<tbody id="tbldetallebody" class="tr-middle-align">
						<script type="handlebars-x" id="tpl8tblDetalle">
						{{#.}}
							<tr >
								<td class="text-center">
									<button class="btn-danger btn-xs btn eliminar"><i class="fa fa-close"></i></button>
								</td>
								<td class="text-left pointer" data-producto="{{cod_producto}}" data-fechavencimiento="{{fecha_vencimiento}}" data-lote="{{lote}}">
									{{#cod_producto}}
										<span>{{../nombre_producto}}</span>
									{{else}}
										Buscar producto...
									{{/cod_producto}}
								</td>
								<td>{{marca}}</td>
								<td class="text-center">{{fecha_vencimiento}}</td>
								<td class="text-center">{{lote}}</td>
								<td class="text-center">{{precio_unitario}}</td>
								<td class="cantidad"><input type="numeric" data-preval="{{cantidad}}" class="form-control text-center" value="{{cantidad}}"/></td>
								<td class="text-center">{{subtotal}}</td>
							</tr>
						{{else}}
							<tr class="tr-null ">
								<td class="text-center" colspan="8">Sin registros agregados</td>
							</tr>
						{{/.}}
						</script>
						<tr class="tr-null">
							<td class="text-center" colspan="8">Sin registros agregados</td>
						</tr>
					</tbody>
					<tfoot  style="font-size:1.25em">
						<tr>
							<td class="text-right" colspan="7">TOTAL</td>
							<td class="text-center" id="lbltotal">0.00</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-offset-5 col-sm-3 col-xs-12">
			<button class="btn btn-xlg btn-danger btn-block" style="display:none" id="btncancelaredicion">CANCELAR EDICIÓN</button>
		</div>
		<div class="col-sm-4 col-xs-12">
			<button class="btn btn-xlg btn-primary btn-block" id="btnguardar">GUARDAR</button>
		</div>
	</div>


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
	                        <label class="control-label">Busque por NOMBRE de producto. Use un CLICK/TAP al producto para agregarlo a la venta.</label>
	                        <span class="input-icon" style="width:100%">
	                          <i class="ace-icon fa fa-search blue"></i>
	                          <input id="txtbuscar" type="search"  class="form-control"placeholder="Buscar..."/>
	                        </span>
	                      </div>
	                    </div>
	                    <div class="col-xs-12 col-sm-6 col-md-3">
	                      <div class="form-group">
	                        <label class="control-label">Filtrar por Tipo</label>
	                        <select id="cbofiltrotipo" class="form-control">
	                          <option value="">Todos</option>
	                        </select>
	                      </div>
	                    </div>
	                    <div class="col-xs-12 col-sm-6 col-md-3">
	                      <div class="form-group">
	                        <label class="control-label">Filtrar por Categoría</label>
	                        <select id="cbofiltrocategoria" class="form-control">
	                          <option value="">Todos</option>
	                        </select>
	                      </div>
	                    </div>
	                </div>
	              <div class="space-6"></div>
	                  
	              <div class="row">
	              	<div class="col-xs-12">
	              		<table class="table">
	              			<thead>
	              				<tr>
	              					<th>Producto</th>
	              					<th style="width:160px">Marca</th>
	              					<th style="width:120px">F. Venc.</th>
	              					<th style="width:120px">Lote</th>
	              					<th style="width:90px">Precio Unit.</th>
	              				</tr>
	              			</thead>
	              			<tbody  id="blklistaproductos">
	              				<script id="tpl8ListaProducto" type="handlebars-x">
			                      {{#.}}
			                      	<tr data-id="{{cod_producto}}">
			                      		<td>{{nombre_producto}}</td>	
			                      		<td style="width:160px">{{marca}}</td>
			                      		<td style="width:120px">{{fecha_vencimiento}}</td>
			                      		<td style="width:120px">{{lote}}</td>
			                      		<td style="width:90px">S/ {{precio_unitario}}</td>
			                      	</tr>
			                      {{else}}
			                      	<tr class="tr-null">
			                      		<td>
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
	              Cerrar
	            </button>
	          </div>
	        </div><!-- /.modal-content -->
	  </form>
	</div>
</div>

