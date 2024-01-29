<div class="row">
	<div class="col-xs-6 col-sm-3">
		<div class="form-group">
			<label class="control-label">&nbsp;</label>
			<br> No repetir productos <input type="checkbox" checked id="chknorepetir" style="width: 18px;height: 18px;margin: 0px 10px;"/>
		</div>
	</div>
	<div class="col-xs-6 col-sm-3">
		<div class="form-group">
			<label class="control-label">Filtro por tipo</label>
			<select  class="form-control" id="cbotipostock">
			</select>
		</div>
	</div>
	<div class="col-xs-6 col-sm-3">
		<div class="form-group">
			<label class="control-label">Filtro por categoría</label>
			<select  class="form-control" id="cbocategoriastock">
			</select>
		</div>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-xs-12">
		<div  id="tblstock" class="table-responsive">    			
					<script type="handlebars-x" id="tpl8Stock">
					<table class="table tbl-detalle">
						<thead>
							<tr>
								<th style="width:75px">Item</th>
								<th class="text-left">Producto</th>
								<!-- <th class="text-left">F. Vencimiento</th> -->
								<th class="text-left" style="width:100px">Lote</th>
								<th class="text-center" style="width:175px">Precio Adquirido</th>
								<th class="text-center" style="width:150px">Stock</th>
							</tr>
						</thead>
						<tbody class="tr-middle-align">
						{{#.}}
							<tr >
								<td class="text-center">{{indexer @index}}</td>
								<td class="text-left">
									<span>{{producto.nombre}}</span>
								</td>
								<!--
								<td class="text-left">
									<span>{{fecha_vencimiento}}</span>
								</td>
								-->
								<td class="text-left">
									<span>{{lote}}</span>
								</td>
								<td class="text-center">{{#rotulo}} {{this}} {{else}} S/{{precio}} {{/rotulo}}</td>
								<td class="text-center">{{stock}}</td>
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
