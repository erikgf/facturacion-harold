<div class="row">
	<div class="col-xs-6 col-sm-2">
		<div class="control-group">
			<label class="control-label">Tipo Comprobante</label>
			<select  class="form-control" id="cbotipocomprobante-listar">
				<option value="01">FACTURAS</option>
				<option value="03" selected>BOLETAS</option>
			</select>
		</div>
	</div>
	<div class="col-xs-6 col-sm-2">
		<div class="control-group">
			<label class="control-label">Desde Fecha</label>
			<input type="date" value="<?php echo $fechaHoy; ?>" class="form-control" id="txtfechainicio">
		</div>
	</div>
	<div class="col-xs-6 col-sm-2">
		<div class="control-group">
			<label class="control-label">Hasta Fecha</label>
			<input type="date" value="<?php echo $fechaHoy; ?>" class="form-control" id="txtfechafin">
		</div>
	</div>
	<div class="col-xs-6 col-sm-2">
		<div class="control-group">
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
		<div class="table-responsive">    		
			<table class="table tbl-detalle tablalista display text-nowrap" style="width:100%">
				<thead>
					<tr>
						<th style="width:100px">Acción</th>
						<th class="text-center" style="width:130px">Comprobante</th>
						<th class="text-left">Cliente</th>
						<th class="text-center" style="width:140px">F. Emisión</th>
						<th class="text-center" style="width:140px">Gravadas</th>
						<th class="text-center" style="width:140px">Descuentos</th>
						<th class="text-center" style="width:140px">IGV</th>
						<th class="text-center" style="width:140px">Importe Total</th>
					</tr>
				</thead>
				<tbody class="tr-middle-align" id="tbllista">	
				<script type="handlebars-x" id="tpl8ListaFacturas">
					{{#data}}
						<tr >
							<td  style="width:100px" class="text-center">
								<button class="btn btn-xs btn-danger" title="Eliminar" onclick ="app.ListarFacturas.anular({{id}})">
									<i class="fa fa-trash bigger-130"></i>
								</button>
								{{#../admin}}
								<!-- 
								<button class="btn btn-xs btn-warning" title="Editar" onclick ="app.RegistrarFacturas.editar({{id}})">
									<i class="fa fa-edit bigger-130"></i>
								</button>
								-->
								{{/../admin}}
								<button class="btn btn-xs" title="Ver Detalle {{id}}" onclick ="app.ListarFacturas.verDetalle({{id}})">
									<i class="fa fa-eye bigger-130"></i>
								</button>
							</td>
							<td class="text-center">{{serie}}-{{correlativo}}</td>
							<td class="text-left">{{descripcion_cliente}}</td>
							<td class="text-center">{{fecha_emision}}</td>
							<td class="text-right">{{id_tipo_moneda}} {{total_gravadas}}</td>
							<td class="text-right">{{id_tipo_moneda}} {{descuento_global}}</td>
							<td class="text-right">{{id_tipo_moneda}} {{total_igv}}</td>
							<td class="text-right">{{id_tipo_moneda}} {{importe_total}}</td>
						</tr>
					{{else}}
						<tr class="tr-null ">
							<td class="text-center" colspan="10">Sin Facturas/Boletas para mostrar</td>
						</tr>
					{{/data}}
				</script>
				</tbody>
			</table>
		</div>
	</div>
</div>