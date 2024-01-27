<?php 
		$fechaHoy = date('Y-m-d');
 ?>
<div class="row">
	<div class="col-xs-6 col-sm-3">
		<div class="control-group">
			<label class="control-label">Desde Fecha</label>
			<input type="date" value="<?php echo $fechaHoy; ?>" class="form-control" id="txtfechainicio">
		</div>
	</div>
	<div class="col-xs-6 col-sm-3">
		<div class="control-group">
			<label class="control-label">Hasta Fecha</label>
			<input type="date" value="<?php echo $fechaHoy; ?>" class="form-control" id="txtfechafin">
		</div>
	</div>
	<div class="col-xs-6 col-sm-3">
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
			<table class="table tbl-detalle tablalista display nowrap" style="width:100%">
						<thead>
							<tr>
								<th style="width:70px">Acción</th>
								<th class="text-center" style="width:160px">Comprobante</th>
								<th class="text-left">Cliente</th>
								<th class="text-center" style="width:150px">Fecha Cotización</th>
								<th class="text-center" style="width:150px">Fecha Vencimiento</th>
								<th class="text-center" style="width:140px">Subtotal</th>
								<th class="text-center" style="width:140px">IGV</th>
								<th class="text-center" style="width:140px">Importe Total</th>
							</tr>
						</thead>
						<tbody class="tr-middle-align" id="tbllista">	
						<script type="handlebars-x" id="tpl8ListaCotizaciones">
							{{#data}}
								<tr >
									<td class="text-center">
									   <b>{{x_cod_transaccion}}</b>
	                                   <button class="btn btn-xs btn-danger" title="Eliminar" onclick ="app.ListarVentas.anular({{cod_transaccion}})">
	                                   		<i class="fa fa-trash bigger-130"></i>
	                                   </button>
	                                   {{#../admin}}
		                               <button class="btn btn-xs btn-warning" title="Editar" onclick ="app.RegistrarCotizaciones.editar({{../cod_transaccion}})">
	                                   		<i class="fa fa-edit bigger-130"></i>
	                                   </button>
	                               		{{/../admin}}
		                               <button class="btn btn-xs" title="Ver Detalle" onclick ="app.ListarCotizaciones.verDetalle({{cod_transaccion}})">
	                                   		<i class="fa fa-eye bigger-130"></i>
	                                   </button>
									</td>
									<td class="text-center">{{comprobante}} {{#voucher}}<br>VOUCHER: {{this}} {{/voucher}}</td>
									<td class="text-left">{{numero_documento}} - {{cliente}}</td>
									<td class="text-center">{{fecha_cotizacion}}</td>
									<td class="text-center">{{fecha_vencimiento}}</td>
									<td class="text-center">S/ {{subtotal}}</td>
									<td class="text-center">S/ {{monto_igv}}</td>
									<td class="text-center">S/ {{importe_total}}</td>
								</tr>
							{{else}}
								<tr class="tr-null ">
									<td class="text-center" colspan="10">Sin cotizaciones para mostrar</td>
								</tr>
							{{/data}}
						</script>
						</tbody>
			</table>
		</div>
	</div>
</div>
