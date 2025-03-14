<?php 
		$fechaHoy = date('Y-m-d');
 ?>
<div class="row">
	<div class="col-xs-6 col-sm-3">
		<div class="form-group">
			<label class="control-label">Sucursal</label>
			<select  class="form-control" id="cbosucursal">
			</select>
		</div>
	</div>
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
		<div id="blkalert">
		</div>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-xs-12">
		<div  id="tbllista" class="table-responsive">    			
			<script type="handlebars-x" id="tpl8ListaCompras">
				<table class="table tbl-detalle tablalista display nowrap" style="width:100%">
					<thead>
						<tr>
							<th style="width:150px">Acción</th>
							<th style="width:75px">ID</th>
							<th class="text-center" style="width:120px">Comprobante</th>
							<th class="text-left">Proveedor</th>
							<th class="text-center" style="width:140px">Fecha Registro</th>
							<th class="text-center" style="width:120px">Tipo Pago</th>
							<th class="text-center" style="width:120px">Importe Total</th>
						</tr>
					</thead>
					<tbody class="tr-middle-align">
					{{#data}}
						<tr >
							<td class="text-center">
                               <button class="btn btn-xs btn-danger" onclick ="app.ListarCompras.anular({{id}}, $(this))">
                               		<i class="fa fa-trash bigger-130"></i>
                               </button>
                               {{#../admin}}
                               <button class="btn btn-xs btn-warning" title="Editar" onclick ="app.RegistrarCompras.editar({{../id}})">
                               		<i class="fa fa-edit bigger-130"></i>
                               </button>
                               {{/../admin}}
                               <button class="btn btn-xs" title="Ver Detalle" onclick ="app.ListarCompras.verDetalle({{id}})">
									<i class="fa fa-eye bigger-130"></i>
                               </button>
							</td>
							<td><b>{{id}}</b></td>
							<td class="text-center">{{numero_comprobante}}</td>
							<td class="text-left">{{proveedor.razon_social}}</td>
							<td class="text-center">{{fecha_compra}}</td>
							<td class="text-center">{{#if_ tipo_pago '==' 'E'}}EFECTIVO{{else}}'TARJETA'{{/if_}}</td>
							<td class="text-center">S/ {{importe_total}}</td>
						</tr>
					{{else}}
						<tr class="tr-null ">
							<td class="text-center" colspan="8">Sin compras para mostrar</td>
						</tr>
					{{/data}}
					</tbody>
				</table>
			</script>
		</div>
	</div>
</div>

<!--
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
								<input class="form-control" id="txtsucursalactual" type="text" value="PRINCIPAL" readonly />
							</div>
						</div>
					</div>
					<div class="space-6"></div>
					<div class="row">
						<div class="col-xs-12 col-sm-8">
							<div class="form-group">
								<label class="control-label">Productos</label>
								<select id="cboproductos" class="form-control" required >
									<option value="">Todos</option>
								</select>
							</div>
						</div>
						<div class="col-xs-6 col-sm-2">
							<div class="form-group">
								<label class="control-label">Precio (S/)</label>
								<input class="form-control"  required id="txtprecio" type="number"/>
							</div>
						</div>
						<div class="col-xs-6 col-sm-2">
							<div class="form-group">
								<label class="control-label">Stock actual</label>
								<input class="form-control" data-i=""  required  id="txtstockactual"readonly type="text"/>
							</div>
						</div>
					</div>
					<div class="space-6"></div>
					<div class="row">
						<div class="col-xs-offset-6 col-xs-6 col-sm-offset-10 col-sm-2">
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
			</div>
		</form>
	</div>
</div>
-->