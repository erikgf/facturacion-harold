<form id="frmregistro">
	<h4>Información Cliente</h4>
	<div class="form-group" style="position:absolute;right:16px;top:12px;width:200px">
		<select  class="form-control" required id="cbosucursal">
		</select>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group">
				<label class="control-label">Buscar Cliente</label>
				<select class="form-control" data-placeholder="Seleccionar opción" id="cboclientebuscar">
					<script type="handlebars-x" id="tpl8cboClientesBuscar">
						<option value="">Seleccionar cliente</option>
						{{#.}}
							<option value="{{id}}">{{#numero_documento}}{{this}} -{{/numero_documento}} {{nombres}} {{apellidos}}</option>
						{{/.}}
					</script>
				</select>			
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<label class="control-label">Comprobante</label>
						<select class="form-control" id="cbotipocomprobante" required>
							<option value="00">Ticket</option>
							<option selected value="03">Boleta</option>
							<option value="01">Factura</option>
						</select>
					</div>
				</div>
				<div id="blkcomprobante">
					<div class="col-xs-4">
						<div class="form-group">
							<label class="control-label">Serie</label>
							<select style="background: #ded2d2;color: darkred;" readonly class="form-control text-center" id="txtserie" required></select>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label">Correlativo</label>
							<input class="form-control text-center" readonly id="txtcorrelativo" maxlength="6"/>
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
		<div class="col-sm-4 col-xs-12">
			<div class="form-group">
				<label class="control-label">Nombre Cliente</label>
				<input class="form-control" id="txtcliente" placeholder="Nombre cliente" maxlength="300"/>		
			</div>
		</div>
		<div class="col-sm-4 col-xs-12">
			<div class="form-group">
				<label class="control-label">Apellidos Cliente</label>
				<input class="form-control" id="txtapellidos" placeholder="Apellidos cliente" maxlength="300"/>		
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label class="control-label">Dirección</label>
				<input class="form-control"  id="txtdireccion" placeholder="Dirección" maxlength="40"/>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label class="control-label">Celular</label>
				<input class="form-control"  id="txtcelular" placeholder="Celular" maxlength="10"/>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label class="control-label">Correo</label>
				<input class="form-control"  id="txtcorreo" placeholder="Correo" maxlength="40"/>
			</div>
		</div>
	</div>

	<hr>
	<div class="space-6"></div>

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class="form-group">
				<label class="control-label">Observaciones</label>
				<textarea class="form-control" id="txtobservaciones"></textarea>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="col-sm-offset-4 col-sm-4 col-xs-12">
				<div class="form-group">
					<label class="control-label bolder">Fecha Venta</label>
					<input id="txtfechaventa" required type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>"/>
				</div>
			</div>
			<div class="col-sm-4 col-xs-12">
				<div class="form-group">
					<label class="control-label bolder">Hora Venta</label>
					<input id="txthoraventa" required type="time" class="form-control" value="<?php echo date('H:i'); ?>" />
				</div>
			</div>
		</div>
	</div>
	<hr>	
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<h4>Detalle de Venta</h4>
		</div>
		<div class="col-xs-12 col-sm-6">
			<button class="btn btn-success btn-xs" style="margin-right: 10px;float: right;" type="button" id="btnactualizar">ACTUALIZAR STOCK</button>
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
							<th class="text-center" style="width:85px">P.U.</th>
							<th class="text-center" style="width:120px">Lote</th>
							<th class="text-center" style="width:85px">Cantidad</th>
							<th class="text-center" style="width:100px;display:none">Descuento</th>
							<th class="text-center" style="width:100px">Subtotal</th>
						</tr>
					</thead>
					<tbody id="tbldetallebody" class="tr-middle-align">
						<script type="handlebars-x" id="tpl8tblDetalle">
						{{#.}}
							<tr >
								<td class="text-center"><button class="btn-danger btn-xs btn eliminar"><i class="fa fa-close"></i></button></td>
								<td class="text-left" data-codproducto="{{cod_producto}}" data-producto="{{cod_producto}}{{fecha_vencimiento}}{{lote}}">
									{{#cod_producto}}
										<span>{{../nombre_producto}}</span>
									{{else}}
										Buscar producto...
									{{/cod_producto}}
								</td>
								<td class="text-left">{{marca}}</td>
								<td class="text-center precio-unitario">
									<input data-preval="{{precio_unitario}}" type="number" step="0.001" class="form-control input-sm text-right" value="{{precio_unitario}}"/>
								</td>
								<td class="text-center">{{lote}}</td>
								<td class="cantidad"><input type="number" data-preval="{{cantidad}}" class="form-control text-center" value="{{cantidad}}"/></td>

								{{#cod_descuento}}
									<td style="display:none" class="text-center descuento" data-id="{{../cod_descuento}}_{{../rotulo_descuento}}_{{../tipo_descuento}}_{{../monto_descuento}}">
										{{../rotulo_descuento}}
										<br>
										<a class="descuento-cancelar" href="javascript;" style="font-size: 14px;">Cancelar</a>
									</td>
								{{else}}
									<td style="display:none" class="text-center descuento" data-id="">
										<label><small>Código</small></label>
										<input class="form-control input-sm text-center" maxlength="6" value=""/>
									</td>
								{{/cod_descuento}}
								<td class="text-center">{{subtotal}}</td>
							</tr>
						{{else}}
							<tr class="tr-null ">
								<td class="text-center" colspan="100">Sin registros agregados</td>
							</tr>
						{{/.}}
						</script>
						<tr class="tr-null">
							<td class="text-center" colspan="100">Sin registros agregados</td>
						</tr>
					</tbody>
					<tfoot  style="font-size:1.25em">
						<tr>
							<td  colspan="2">
								<button  id="btnagregarproducto"  type="button"  class="btn btn-xs"><span class="fa fa-plus"></span> Agregar Producto</button>
								<span class="input-icon">
									<input type="text"  value="" placeholder="Lectora de Barras" id="txtlectora" />
									<i class="ace-icon fa fa-barcode blue" style="line-height: 2.5"></i>
								</span>
							</td>
							<td class="text-right" colspan="4">SUBTOTAL</td>
							<td class="text-center" id="lblsubtotal">0.00</td>
						</tr>
						<tr>
							<td class="text-right" colspan="6">DESCUENTO GLOBAL</td>
							<td class="text-center">
								<input class="form-control text-center" type="number" step="0.001" id="txtdescuentoglobal" required value="0.00"/>
							</td>
						</tr>
						<tr>
							<td class="text-right" colspan="6">TOTAL</td>
							<td class="text-center" id="lbltotal">0.00</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
	<h4>Método de Pago</h4>
	<div class="row">
		<div class="col-xs-6 col-sm-2">
			<div class="form-group">
				<label style="color:#3f3fac" class="control-label"><b>PAGO EFECTIVO</b></label>
				<input style="color:#3f3fac" class="form-control text-right" type="number" step="0.001" id="txtefectivo" value="0.00"/>
			</div>
		</div>
		<div class="col-xs-6 col-sm-2">
			<div class="form-group">
				<label class="control-label">PAGO TARJETA</label>
				<input class="form-control text-right" type="number" step="0.001" id="txttarjeta" value="0.00"/>
			</div>
		</div>
		<div class="col-xs-6 col-sm-2">
			<div class="form-group">
				<label class="control-label">PAGO CRÉDITO</label>
				<input class="form-control text-right" type="number" step="0.001" id="txtcredito" value="0.00"/>
			</div>
		</div>
		<div class="col-xs-6 col-sm-2">
			<div class="form-group">
				<label class="control-label">PAGO YAPE</label>
				<input class="form-control text-right" type="number" step="0.001" id="txtyape" value="0.00"/>
			</div>
		</div>
		<div class="col-xs-6 col-sm-2">
			<div class="form-group">
				<label class="control-label">PAGO PLIN</label>
				<input class="form-control text-right" type="number" step="0.001" id="txtplin" value="0.00"/>
			</div>
		</div>
		<div class="col-xs-6 col-sm-2">
			<div class="form-group">
				<label class="control-label">PAGO TRANSF. BANCO</label>
				<input class="form-control text-right" type="number" step="0.001" id="txtbanco" value="0.00"/>
			</div>
		</div>
		<!--
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
		-->
	</div>

	<div class="row">
		<div class="col-sm-offset-5 col-sm-3 col-xs-12">
			<button class="btn btn-xlg btn-danger btn-block" style="display:none" type="button" id="btncancelaredicion">CANCELAR EDICIÓN</button>
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
						<div class="col-xs-12" style="max-height: 280px;overflow-x: scroll;">
							<table class="table tbl-detalle" id="tbldetalle">
								<thead>
									<tr>
										<th>Producto</th>
										<th style="width:160px">Marca</th>
										<th style="width:120px">Lote</th>
										<th style="width:90px">Precio Unit.</th>
										<th style="width:90px">STOCK</th>
									</tr>
								</thead>
								<tbody id="blklistaproductos">
									<script id="tpl8ListaProducto" type="handlebars-x">
										{{#.}}
										<tr class="pointer" data-id="{{codigo_unico_producto}}" data-stock={{stock}} {{#if_ stock '==' '0'}}style="color:red;"{{/if_}}>
											<td>{{nombre_producto}}</td>	
											<td style="width:160px">{{marca}}</td>
											<td style="width:120px">{{lote}}</td>
											<td style="width:90px">S/ {{precio_unitario}}</td>
											<td style="width:90px"><b>{{stock}}</b></td>
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

