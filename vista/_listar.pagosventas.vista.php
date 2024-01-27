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
								<th style="width:100px">Acción</th>
								<th class="text-center" style="width:160px">Sucursal</th>
								<th class="text-center" style="width:160px">Comprobante</th>
								<th class="text-left">Cliente</th>
								<th class="text-center" style="width:140px">Fecha Pago</th>
								<th class="text-center" style="width:140px">Adeudado</th>
								<th class="text-center" style="width:140px">Pagado</th>
								<th class="text-center" style="width:140px">Pendiente</th>

							</tr>
						</thead>
						<tbody class="tr-middle-align" id="tbllista">	
						<script type="handlebars-x" id="tpl8ListaVentas">
							{{#data}}
								<tr >
									<td  style="width:100px" class="text-center">
									   <b>{{x_cod}}</b>
	                                   <button class="btn btn-xs btn-danger" title="Eliminar" onclick ="app.ListarVentas.anular({{cod_venta_pago}})">
	                                   		<i class="fa fa-trash bigger-130"></i>
	                                   </button>
									</td>
									<td class="text-left">{{sucursal}}</td>
									<td class="text-left">{{comprobante}}</td>
									<td class="text-left">{{cliente}}</td>
									<td class="text-center">{{fecha_pago}}</td>
									<td class="text-center">S/ {{adeudado}}</td>
									<td class="text-center">S/ {{pagado}}</td>
									<td class="text-center">S/ {{pendiente}}</td>
								</tr>
							{{else}}
								<tr class="tr-null ">
									<td class="text-center" colspan="15">Sin ventas para mostrar</td>
								</tr>
							{{/data}}
						</script>
						</tbody>
			</table>
		</div>
	</div>
</div>
