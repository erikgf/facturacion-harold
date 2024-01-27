<?php 
		$fechaHoy = date('Y-m-d');
 ?>
<div class="row">
	<div class="col-xs-6 col-sm-3">
		<div class="form-group">
			<label class="control-label">Filtro por movimiento</label>
			<select  class="form-control" id="cbofiltromovimiento">
				<option value="">Todos</option>
				<option value="E">ENTRADAS</option>
				<option value="S">SALIDAS</option>
			</select>
		</div>
	</div>
	<div class="col-xs-6 col-sm-3">
		<div class="form-group">
			<label class="control-label">Filtro por tipo</label>
			<select  class="form-control" id="cbotipohistorial">
			</select>
		</div>
	</div>
	<div class="col-xs-6 col-sm-3">
		<div class="form-group">
			<label class="control-label">Filtro por categoría</label>
			<select  class="form-control" id="cbocategoriahistorial">
			</select>
		</div>
	</div
>	<div class="col-xs-12 col-sm-3" style="display: none;">
		<div class="form-group">
			<br><button class="btn btn-danger btn-block" id="btntransferencia">TRANSFERENCIA</button>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-6 col-sm-3">
		<div class="form-group">
			<label class="control-label">Desde Fecha</label>
			<input type="date" value="<?php echo $fechaHoy; ?>" class="form-control" id="txtfechainicio">
		</div>
	</div>
	<div class="col-xs-6 col-sm-3">
		<div class="form-group">
			<label class="control-label">Hasta Fecha</label>
			<input type="date" value="<?php echo $fechaHoy; ?>" class="form-control" id="txtfechafin">
		</div>
	</div>
	<div class="col-xs-6 col-sm-3">
		<div class="form-group">
			<br><button class="btn btn-info btn-xs" id="btnbuscarfecha">BUSCAR POR FECHAS</button>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div id="blkalertmovimiento">
		</div>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-xs-12">
		<div  id="tblhistorial" class="table-responsive">    			
					<script type="handlebars-x" id="tpl8Historial">
					<table class="table tbl-detalle">
						<thead>
							<tr>
								<!-- <th style="width:7px">Item</th> -->
								<th class="text-left">Producto</th>
								<!-- <th class="text-left">F. Vencimiento</th> -->
								<th class="text-left" style="width:120px">Lote</th>
								<th class="text-center" style="width:140px">Precio Entrada</th>
								<th class="text-center" style="width:140px">Precio Salida</th>
								<th class="text-center" style="width:140px">Tipo Movimiento</th>
								<th class="text-center" style="width:90px">Cantidad</th>
								<th class="text-center" style="width:100px">Fecha Movimiento</th>
							</tr>
						</thead>
						<tbody class="tr-middle-align">
						{{#.}}
							<tr >
								<!-- 
								<td>
                                   <button class="btn btn-xs btn-danger" onclick ="app.HistorialMovimientos.eliminar({{id}})">
                                   	<i class="fa fa-trash bigger-130"></i>
                                   </button>
								</td>
								-->
								<td class="text-left"><span>{{producto.nombre}}</span></td>
								<td class="text-left">{{lote}}</td>
								<td class="text-center">{{precio_entrada}}</td>
								<td class="text-center">{{precio_salida}}</td>
								<td class="text-center">{{movimiento}} - {{nota}}</td>
								<td class="text-center">{{cantidad}}</td>
								<td class="text-center">{{fecha_movimiento}}</td>
							</tr>
						{{else}}
							<tr class="tr-null ">
								<td class="text-center" colspan="10">Sin productos para mostrar</td>
							</tr>
						{{/.}}
						</tbody>
					</table>
					</script>
		</div>
	</div>
</div>

<div id="mdlMovimiento" class="modal fade" tabindex="-1" style="display: none;">
	<div class="modal-dialog modal-lg">
		<form id="frmgrabar">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 class="smaller lighter blue no-margin">Nuevo Movimiento en Almacén</h3>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-6 col-sm-3">
							<div class="form-group">
								<label class="control-label">Tipo de Movimiento</label>
								<select id="cbotipomovimiento" class="form-control">
									<option value="E">ENTRADA</option>
									<option value="S">SALIDA</option>
								</select>
							</div>
						</div>
						<div class="col-xs-6 col-sm-offset-5 col-sm-4">
							<div class="form-group">
								<label class="control-label">Sucursal Actual</label>
								<select id="cbosucursalactual" class="form-control">
								</select>
								<!-- <input class="form-control" id="txtsucursalactual" type="text" value="PRINCIPAL" readonly /> -->
							</div>
						</div>
					</div>
					<div class="space-6"></div>
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label class="control-label">Productos</label>
								<select id="cboproductos" class="form-control" required >
									<option value="">Todos</option>
								</select>
							</div>
						</div>
						<div class="col-xs-6 col-sm-3">
							<div class="form-group">
								<label class="control-label">F. Venc.</label>
								<input class="form-control"  required id="txtfechavencimiento" type="date"/>
							</div>
						</div>
						<div class="col-xs-6 col-sm-3">
							<div class="form-group">
								<label class="control-label">Lote</label>
								<input class="form-control"  required id="txtlote" type="text"/>
							</div>
						</div>
						<div class="col-xs-6 col-sm-2">
							<div class="form-group">
								<label class="control-label">Precio (S/)</label>
								<input class="form-control"  required id="txtprecio" type="number" step="0.01"/>
							</div>
						</div>
					</div>
					<div class="space-6"></div>
					<div class="row">
						<div class="col-xs-offset-4  col-sm-offset-8 col-xs-6 col-sm-2">
							<div class="form-group">
								<label class="control-label">Stock actual</label>
								<input class="form-control" data-i=""  required  id="txtstockactual"readonly type="text"/>
							</div>
						</div>
						<div class="col-xs-6 col-sm-2">
							<div class="form-group">
								<label class="control-label">Cantidad</label>
								<input class="form-control"  required id="txtcantidad" type="number" value="1"/>
							</div>
						</div>
					</div>

					<div class="space-6"></div>
					<div class="row">
						<div class="col-xs-12">
							<div id="blkalert"></div>
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

<div id="mdlTransferencia" class="modal fade" tabindex="-1" style="display: none;">
	<div class="modal-dialog modal-lg">
		<form id="frmgrabartransferencia">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 class="smaller lighter blue no-margin">Nueva Transferencia en Almacén</h3>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-6 col-sm-3">
							<div class="form-group">
								<label class="control-label">Almacén de Origen</label>
								<select id="cboalmacenorigen" class="form-control">
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-8 col-xs-12">
							<div class="form-group">
								<label class="control-label">Producto</label>
								<select id="cboproductoorigen" class="form-control">
								</select>
							</div>
						</div>
						<div class="col-sm-2 col-xs-6">
							<div class="form-group">
								<label class="control-label">Stock Actual</label>
								<input id="txtstockactualtrans" data-maxstock="" readonly class="form-control"/>
							</div>
						</div>
						<div class="col-sm-2 col-xs-12">
							<button class="btn btn-primary btn-block" id="btnmover">
								MOVER
							</button>
						</div>
					</div>
					<div class="space-6"></div>
					<hr>
					<div class="row">
						<div class="col-xs-6 col-sm-3">
							<div class="form-group">
								<label class="control-label">Almacén de Destino</label>
								<select id="cboalmacendestino" class="form-control">
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<div  class="table-responsive">    			
									<table class="table tbl-detalle">
										<thead>
											<tr>
												<th style="width:80px">Acción</th>
												<th class="text-left">Producto</th>
												<th class="text-center" style="width:130px">Precio Entrada</th>
												<th class="text-center" style="width:130px">Stock Actual</th>
												<th class="text-center" style="width:130px">Cantidad Mover</th>
											</tr>
										</thead>
										<tbody class="tr-middle-align" id="tbltransferencia">
											<script type="handlebars-x" id="tpl8Transferencia">
											{{#.}}
												<tr data-id="{{cod_producto}}">
													<td>
					                                   <button class="btn btn-xs btn-danger" onclick ="app.HistorialMovimientos.quitarProductoTransferencia(this.parentElement.parentElement,)">
					                                   <i class="fa fa-trash bigger-130"></i>
					                                   </button>
													</td>
													<td class="text-left">{{nombre_producto}}</td>
													<td class="text-center">{{precio_entrada}}</td>
													<td class="text-center">{{stock}}</td>
													<td class="text-center"><input type="number" value="1" data-maxstock="{{stock}}" class="form-control text-center"/></td>
												</tr>
											{{/.}}
											</script>
										</tbody>
									</table>
								</div>
						</div>
					</div>
					<div class="space-6"></div>
					<div class="row">
						<div class="col-xs-12">
							<div id="blkalerttransferencia"></div>
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