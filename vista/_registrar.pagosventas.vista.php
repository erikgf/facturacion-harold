	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-5">
			<h4 style="color: #438eb9;font-weight: bold;" id="lblrotuloedicion"></h4>
		</div>
	</div>

	<h4>Información Comprobante</h4>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group">
				<label class="control-label">Buscar Comprobante</label>
				<select class="form-control" id="cbocomprobantebuscar">
					<script type="handlebars-x" id="tpl8CboComprobantesBuscar">
						<option value="">Seleccionar Comprobante</option>
						{{#.}}
							<option value="{{cod_venta}}">
								{{serie_comprobante}}
							</option>
						{{/.}}
					</script>
				</select>			
			</div>
		</div>
	</div>
	<div class="space-10"></div>
	<div class="row">
		<div class="col-sm-2 col-xs-12">
			<div class="form-group" >
				<label class="control-label">Núm. Comprobante</label>
				<input class="form-control" id="txtnumerocomprobante" placeholder="Núm. Comprobante"  readonly/>
			</div>
		</div>
		<div class="col-sm-6 col-xs-12">
			<div class="form-group">
				<label class="control-label">Nombre Cliente</label>
				<input class="form-control" id="txtnombrecliente" placeholder="Nombre cliente" readonly maxlength="300"/>		
			</div>
		</div>
		<div class="col-sm-2 col-xs-12">
			<div class="form-group">
				<label class="control-label">Fecha Registro</label>
				<input class="form-control" id="txtfecharegistro" placeholder="Apellidos cliente"  readonly type="date"/>		
			</div>
		</div>
		<div class="col-sm-2 col-xs-12">
			<div class="form-group">
				<label class="control-label">Total Venta</label>
				<input class="form-control" id="txttotalventa" placeholder="Total Venta" readonly type="number"/>
			</div>
		</div>
	</div>

	<div class="space-6"></div>
	<h4>Información Pago</h4>
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-2">
			<div class="form-group">
				<label class="control-label">Pendiente </label>
				<input class="form-control" id="txtpendiente" readonly placeholder="Pendiente" type="number"/>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4 col-md-2">
			<div class="form-group">
				<label class="control-label"><b>Fecha Pago</b> </label>
				<input class="form-control" id="txtfechapago" required type="date"/>
			</div>
		</div>

		<div class="col-xs-12 col-sm-4 col-md-2">
			<div class="form-group">
				<label class="control-label"><b>Pagado</b> </label>
				<input class="form-control" id="txtpagado" required placeholder="Pagado" step="0.001" value="0.00"  type="number"/>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4 col-md-6">
			<div class="form-group">
				<label class="control-label">Observaciones</label>
				<textarea class="form-control" id="txtobservaciones"></textarea>
			</div>
		</div>
	</div>
	<hr>	

	<div class="row">
		<div class="col-sm-4 col-xs-12">
			<button class="btn btn-xlg btn-primary btn-block" id="btnguardar">GUARDAR</button>
		</div>
	</div>

