<form id="frmregistro">
	<h6 id="lblrotuloedicion" class="label label-danger label-lg"></h6>
	<div class="form-group" style="position:absolute;right:16px;top:12px;width:200px">
		<select  class="form-control" required id="cbosucursal">
		</select>
	</div>
	<h4>Información Proveedor</h4>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group">
				<label class="control-label">Buscar Proveedor</label>
				<select class="form-control" id="cboproveedorbuscar">
					<script type="handlebars-x" id="tpl8cboProveedoresBuscar">
						<option value="">Seleccionar proveedor</option>
						{{#.}}
							<option value="{{id}}">{{#numero_documento}}{{this}} -{{/numero_documento}} {{razon_social}}</option>
						{{/.}}
					</script>
				</select>			
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-offset-2 col-md-4">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="control-label">Comprobante</label>
						<select class="form-control" id="cbotipocomprobante" required>
							<option selected value="">Ninguno</option>
							<option value="03">BOLETA</option>
							<option value="01">FACTURA</option>
							<option value="00">VOUCHER/OTROS</option>
						</select>
					</div>
				</div>
				<div id="blkcomprobante" style="display:none;">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group">
							<label class="control-label">Número / Voucher</label>
							<input class="form-control text-center" id="txtnumerocomprobante" maxlength="18"/>
						</div>
					</div>
				</div>	
			</div>
		</div>
	</div>
	<div class="space-10"></div>
	<div class="row">
		<div class="col-sm-2 col-xs-6">
			<div class="form-group">
				<label class="control-label">Tipo Documento</label>
				<select class="form-control" required readonly id="cbotipodocumento">
					<option value="0">S/D</option>
					<option value="1">DNI</option>
					<option value="6">RUC</option>
				</select>			
			</div>
		</div>
		<div class="col-sm-3 col-xs-12" style="display:none;" id="blknumerodocumento">
			<div class="form-group" >
				<label class="control-label">Núm. Documento</label>
				<input class="form-control" readonly id="txtnumerodocumento" placeholder="Núm. Documento" maxlength="11"/>
			</div>
		</div>
		<div class="col-sm-7 col-xs-12">
			<div class="form-group">
				<label class="control-label">Nombre Contacto Proveedor</label>
				<input class="form-control" readonly id="txtproveedor" placeholder="Nombre contacto proveedor" maxlength="350"/>		
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label class="control-label">Dirección</label>
				<input class="form-control" readonly id="txtdireccion" placeholder="Dirección" maxlength="40"/>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label class="control-label">Celular Contacto</label>
				<input class="form-control" readonly id="txtcelular" placeholder="Celular" maxlength="10"/>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label class="control-label">Correo</label>
				<input class="form-control" readonly id="txtcorreo" placeholder="Correo" maxlength="40"/>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-2">
			<div class="form-group">
				<label class="control-label">Guía(s) de Remisión</label>
				<input class="form-control" id="txtguiasremision"/>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class="form-group">
				<label class="control-label">Observaciones</label>
				<textarea class="form-control" id="txtobservaciones"></textarea>
			</div>
		</div>
	</div>
	<hr>
	<h4>Detalle de Compra</h4>
	<div class="row">
		<div class="col-xs-12">
			<div class="table-responsive">
				<table class="table tbl-detalle" id="tbldetalle">
					<thead>
						<tr>
							<th style="width:50px">Item</th>
							<th>Producto</th>
							<th  style="width:145px">Marca</th>
							<!-- <th class="text-center" style="width:135px">F. Vencimiento</th> -->
							<th class="text-center" style="width:135px">Lote</th>
							<th class="text-center" style="width:125px">Precio Compra</th>
							<th class="text-center" style="width:85px">Cantidad</th>
							<th class="text-center" style="width:125px">Subtotal</th>
						</tr>
					</thead>
					<tbody id="tbldetallebody" class="tr-middle-align">		
						<script type="handlebars-x" id="tpl8tblDetalle">
							{{#.}}
									<tr >
										<td class="text-center"><button type="button" class="btn-danger btn-xs btn eliminar"><i class="fa fa-close"></i></button></td>
										<td class="text-left pointer" data-producto="{{id_producto}}">
											{{#id_producto}}
											<span>{{../nombre_producto}}</span>
											{{else}}
											Buscar producto...
											{{/id_producto}}
										</td>
										<td class="marca">{{marca}}</td>
										<!-- <td class="fecha_vencimiento"><input type="date" data-preval="{{fecha_vencimiento}}" class="form-control text-center" value="{{fecha_vencimiento}}"/></td> -->
										<td class="lote"><input type="text" data-preval="{{lote}}" class="form-control text-center" value="{{lote}}"/></td>
										<td class="precio"><input type="numeric" data-preval="{{precio_unitario}}" class="form-control text-right" value="{{precio_unitario}}"/></td>
										<td class="cantidad"><input type="numeric" data-preval="{{cantidad}}" class="form-control text-right" value="{{cantidad}}"/></td>
										<td class="text-right">{{subtotal}}</td>
									</tr>
									{{else}}
									<tr class="tr-null ">
										<td class="text-center" colspan="7">Sin registros agregados</td>
									</tr>
							{{/.}}
						</script>	
						<tr class="tr-null">
							<td class="text-center" colspan="7">Sin registros agregados</td>
						</tr>
					</tbody>
					<tfoot  style="font-size:1.85em">
						<tr>
							<td  colspan="2">
								<button  id="btnagregarproducto"  type="button"  class="btn btn-xs"><span class="fa fa-plus"></span> Agregar Producto</button>
								<span class="input-icon hide" >
									<input type="text"  value="" placeholder="Lectora de Barras" id="txtlectora" />
									<i class="ace-icon fa fa-barcode blue" style="line-height: 2.5"></i>
								</span>
							</td>
							<td class="text-right" colspan="4">TOTAL</td>
							<td class="text-right" id="lbltotal">0.00</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>

	
	<div class="space-6"></div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<h4>Método de Pago</h4>
			<div class="col-xs-6">
				<div class="form-group">
					<div class="radio">
						<label>
							<input name="radtipopago" value="E" checked type="radio" class="ace">
							<span class="lbl"> EFECTIVO</span>
						</label>
					</div>
					<div class="radio">
						<label>
							<input name="radtipopago" value="T" type="radio" class="ace">
							<span class="lbl"> TARJETA</span>
						</label>
					</div>
				</div>
			</div>
			<div class="col-xs-6" id="blktipotarjetas" style="display:none;">
				<div class="form-group">
					<div class="radio">
						<label>
							<input value="C" name="radtipotarjeta" type="radio" checked class="ace">
							<span class="lbl"> T. CRÉDITO</span>
						</label>
					</div>
					<div class="radio">
						<label>
							<input value="D" name="radtipotarjeta" type="radio" class="ace">
							<span class="lbl"> T. DÉBITO</span>
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6">
			<div class="col-sm-offset-4 col-sm-4 col-xs-12">
				<div class="form-group">
					<label class="control-label bolder">Fecha Compra</label>
					<input id="txtfechacompra" required type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>"/>
				</div>
			</div>
			<div class="col-sm-4 col-xs-12">
				<div class="form-group">
					<label class="control-label bolder">Hora Compra</label>
					<input id="txthoracompra" required type="time" class="form-control" value="<?php echo date('H:i'); ?>" />
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-offset-5 col-sm-3 col-xs-12">
			<button class="btn btn-xlg btn-danger btn-block" type="button" style="display:none" id="btncancelaredicion">CANCELAR EDICIÓN</button>
		</div>
		<div class="col-sm-4 col-xs-12">
			<button class="btn btn-xlg btn-primary btn-block" type="submit" id="btnguardar">GUARDAR</button>
		</div>
	</div>
</form>


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
						
					<h5>Seleccionados: <span id="lblSeleccionados">0</span></h5>
					<div class="row">
						<div class="col-xs-12" style="max-height: 400px;overflow-x: scroll;">
							<table class="table">
								<thead>
									<tr>
										<th>Producto</th>
										<th style="width:160px">Marca</th>
										<th style="width:90px">Precio Unit.</th>
										<th style="width:90px">STOCK</th>
									</tr>
								</thead>
								<tbody  id="blklistaproductos">
									<script id="tpl8ListaProducto" type="handlebars-x">
										{{#.}}
										<tr data-id="{{id}}" {{#if seleccionado}}class="seleccionado-tr"{{/if}}>
											<td>{{nombre_producto}}</td>	
											<td style="width:160px">{{marca}}</td>
											<td style="width:90px">S/ {{precio_unitario}}</td>
											<td style="width:90px"><b>{{stock}}</b></td>
										</tr>
										{{else}}
										<tr class="tr-null">
											<td colspan="4">
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
					<button type="button" class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
						<i class="ace-icon fa fa-times"></i>
						CERRAR
					</button>
					<button type="button" class="btn btn-sm btn-primary pull-right" id="btnagregarproductos">
						<i class="ace-icon fa fa-check"></i>
						AGREGAR PRODUCTOS
					</button>
				</div>
			</div><!-- /.modal-content -->
		</form>
	</div>
</div>

