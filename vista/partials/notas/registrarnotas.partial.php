<form id="frmregistro">
	<h4>Información Cliente</h4>
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
							<option selected value="07">N. Crédito</option>
							<option value="08">N. Débito</option>
						</select>
					</div>
				</div>
				<div id="blkcomprobante">
					<div class="col-xs-4">
						<div class="form-group">
							<label class="control-label">Serie</label>
							<input style="background: #ded2d2;color: darkred;" class="form-control text-center" id="txtserie" maxlength="4"/>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label">Correlativo</label>
							<input class="form-control text-center" id="txtcorrelativo" maxlength="6"/>
						</div>
					</div>
				</div>
			</div>		
		</div>
	</div>
	<div class="space-10"></div>
	<div class="row">
		<div class="col-sm-1 col-xs-6">
			<div class="form-group">
				<label class="control-label">T. Doc.</label>
				<select class="form-control" id="cbotipodocumento">
					<option value="0">S/D</option>
					<option value="1" selected>DNI</option>
					<option value="4">CARNET EXT.</option>
					<option value="6">RUC</option>
					<option value="7">PASAPORTE</option>
				</select>			
			</div>
		</div>
		<div class="col-sm-2 col-xs-6" id="blknumerodocumento">
			<div class="form-group" >
				<label class="control-label">Núm. Documento</label>
				<input class="form-control" id="txtnumerodocumento" readonly placeholder="Núm. Documento" maxlength="8"/>
			</div>
		</div>
		<div class="col-sm-5 col-xs-12">
			<div class="form-group">
				<label class="control-label">Descripción Cliente</label>
				<input class="form-control" id="txtclientedescripcion" readonly placeholder="Nombre cliente" maxlength="300"/>		
			</div>
		</div>
		<div class="col-sm-4 col-xs-12">
			<div class="form-group">
				<label class="control-label">Dirección Cliente</label>
				<textarea class="form-control"  id="txtclientedireccion" readonly placeholder="Dirección" maxlength="500"></textarea>
			</div>
		</div>
	</div>
	<hr>
	<div class="space-6"></div>
	<div class="row">
		<div class="col-sm-2 col-xs-6">
			<div class="form-group">
				<label class="control-label bolder">Fecha Emisión</label>
				<input id="txtfechaemision" required type="date" class="form-control" value="<?php echo $fechaHoy; ?>"/>
			</div>
		</div>
		<div class="col-sm-2 col-xs-6">
			<div class="form-group">
				<label class="control-label bolder">Hora Emisión</label>
				<input id="txthoraemision" required type="time" class="form-control" value="<?php echo date('H:i'); ?>" />
			</div>
		</div>
		<div class="col-sm-2 col-xs-6">
			<div class="form-group">
				<label class="control-label bolder">Fecha Vencimiento</label>
				<input id="txtfechavencimiento" required type="date" class="form-control" value="<?php echo $fechaHoy; ?>"/>
			</div>
		</div>
		<div class="col-sm-2 col-xs-6">
			<div class="form-group">
				<label class="control-label bolder">Moneda</label>
				<select id="txtmoneda" class="form-control"  name="txtmoneda">
					<option value="PEN" selected>Soles</option>
					<option value="USD">Dólares</option>
				</select>
			</div>
		</div>
	</div>
	<hr>	
	<h4>Detalle de Comprobante</h4>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
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
								<td class="text-left" data-codproducto="{{cod_producto}}" data-producto="{{cod_producto}}">
									{{#cod_producto}}
										<span>{{../producto}}</span>
									{{else}}
										Buscar producto...
									{{/cod_producto}}
								</td>
								<td class="text-center precio-unitario">
									<input data-preval="{{precio_unitario}}" type="number" step="0.001" class="form-control input-sm text-right" value="{{precio_unitario}}"/>
								</td>
								<td class="cantidad"><input type="number" data-preval="{{cantidad}}" class="form-control text-center" value="{{cantidad}}"/></td>
								<td class="text-center">{{subtotal}}</td>
							</tr>
						{{else}}
							<tr class="tr-null">
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
							<td class="text-right" colspan="2">SUBTOTAL</td>
							<td class="text-center" id="lblsubtotal">0.00</td>
						</tr>
						<tr>
							<td class="text-right" colspan="4">DESCUENTO GLOBAL</td>
							<td class="text-center">
								<input class="form-control text-center" type="number" step="0.001" id="txtdescuentoglobal" required value="0.00"/>
							</td>
						</tr>
						<tr>
							<td class="text-right" colspan="4">TOTAL</td>
							<td class="text-center" id="lbltotal">0.00</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-4">
			<div class="form-group">
				<label class="control-label">Observaciones</label>
				<textarea class="form-control" id="txtobservaciones"></textarea>
			</div>
		</div>
		<div class="col-xs-12 col-sm-2">
			<div class="form-group">
				<label class="control-label">Condición Pago</label>
				<select class="form-control" id="cbocondicionpago">
					<option value="1" selected>CONTADO</option>
					<!-- <option value="0">CRÉDITO</option> -->
				</select>
			</div>
		</div>
		<div class="col-xs-12 col-sm-2">
			<div class="form-group">
				<label class="control-label">Delivery?</label>
				<select class="form-control" id="txtdelivery">
					<option value="1">SÍ</option>
					<option value="0" selected>NO</option>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-3 col-sm-offset-9 col-xs-12">
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
							<table class="table tbl-detalle" id="tbldetalle">
								<thead>
									<tr>
										<th style="width:150px">Código</th>
										<th>Producto</th>
										<th style="width:90px">Precio Unit.</th>
									</tr>
								</thead>
								<tbody id="blklistaproductos">
									<script id="tpl8ListaProducto" type="handlebars-x">
										{{#.}}
										<tr class="pointer" data-id="{{id}}">
											<td style="width:150px">{{codigo_generado}}</td>
											<td>{{producto}}</td>	
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
					<button type="button" class="btn btn-sm btn-primary pull-right" id="btnagregarproductos">
						<i class="ace-icon fa fa-check"></i>
						AGREGAR PRODUCTOS
					</button>
				</div>
			</div><!-- /.modal-content -->
		</form>
	</div>
</div>

