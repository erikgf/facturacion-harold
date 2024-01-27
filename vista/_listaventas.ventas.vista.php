<?php 
		$fechaHoy = date('Y-m-d');
 ?>
<div class="row">
	<div class="col-xs-6 col-sm-3">
		<div class="control-group">
			<label class="control-label">Sucursal</label>
			<select  class="form-control" id="cbosucursal">
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
								<th class="text-center" style="width:140px">F. Registro</th>
								<th class="text-center" style="width:140px">Efectivo</th>
								<th class="text-center" style="width:140px">Tarjeta</th>
								<th class="text-center" style="width:140px">YAPE</th>
								<th class="text-center" style="width:140px">PLIN</th>
								<th class="text-center" style="width:140px">Banco</th>
								<!--
								<th class="text-center" style="width:140px">Monto Crédito</th>
								<th class="text-center" style="width:130px">Comisión</th>
								<th class="text-center" style="width:120px">Subtotal</th>
								<th class="text-center" style="width:140px">Descuentos</th>
								-->
								<th class="text-center" style="width:140px">Total</th>
							</tr>
						</thead>
						<tbody class="tr-middle-align" id="tbllista">	
						<script type="handlebars-x" id="tpl8ListaVentas">
							{{#data}}
								<tr >
									<td  style="width:100px" class="text-center">
	                                   <button class="btn btn-xs btn-danger" title="Eliminar" onclick ="app.ListarVentas.anular({{id}})">
	                                   		<i class="fa fa-trash bigger-130"></i>
	                                   </button>
	                               		{{#../admin}}
		                               <button class="btn btn-xs btn-warning" title="Editar" onclick ="app.RegistrarVentas.editar({{id}})">
	                                   		<i class="fa fa-edit bigger-130"></i>
	                                   </button>
	                               		{{/../admin}}
		                               <button class="btn btn-xs" title="Ver Detalle {{id}}" onclick ="app.ListarVentas.verDetalle({{id}})">
	                                   		<i class="fa fa-eye bigger-130"></i>
	                                   </button>
									</td>
									<td class="text-center">{{serie}}-{{correlativo}}</td>
									<td class="text-left">{{cliente.nombres_apellidos}}</td>
									<td class="text-center">{{fecha_venta}}</td>
									<td class="text-right">S/{{monto_efectivo}}</td>
									<td class="text-right">S/{{monto_tarjeta}}</td>
									<td class="text-right">S/{{monto_yape}}</td>
									<td class="text-right">S/{{monto_plin}}</td>
									<td class="text-right">S/{{monto_transferencia}}</td>
									<!--
									<td class="text-center">S/ {{total_comisiones}} <br>{{comisionista}}</td>
									<td class="text-center">S/ {{subtotal}}</td>
									<td class="text-center">S/ {{total_descuentos}}</td> 
									-->
									<td class="text-right">S/{{monto_total_venta}}</td>
								</tr>
							{{else}}
								<tr class="tr-null ">
									<td class="text-center" colspan="10">Sin ventas para mostrar</td>
								</tr>
							{{/data}}
						</script>
						</tbody>
			</table>
		</div>
	</div>
</div>
