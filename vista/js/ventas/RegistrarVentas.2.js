const RegistrarVentas = function($contenedor, _tpl8){
  const CARACTERES_LECTORA = 16;
    var _Util = Util,
        _ArrayUtils = ArrayUtils,
        _INDEX = {
          "eliminar": 0,
          "producto": 1,
          "marca": 2,
          "precio_unitario": 3,
          "lote": 4,
          "cantidad": 5,
          "descuento": 6,
          "subtotal": 7
        },
        _VACIO = true,
        _TR_BUSCAR = null,
        _data = {
          series: [],
          productos: [],
          clientes : [],
          tipo_categorias : [],
          categoria_productos : []
        },
        MODO = "+",
        COD_VENTA_EDITAR = null;

    this.init = function(){
      this.setDOM();
      this.setEventos();
      this.obtenerData();
    };
  
    this.getData = function(){
      return _data;
    };
  
    this.setProductos = function(_productos){
      _data.productos = _productos;
    };
  
    this.setDataClientes = function(_clientes){
      _data.clientes = _clientes;
    };

    this.setDataSeries = (_series) => {
      _data.series = _series;
    };

    this.setDOM = function(){
        const DOM = _Util.preDOM2DOM($contenedor, [
                      {"frmRegistro": "#frmregistro"},
                      {"cboClienteBuscar": "#cboclientebuscar"},
                      {"cboTipoComprobante": "#cbotipocomprobante"},
                      {"blkComprobante": "#blkcomprobante"},
                      {"txtSerie": "#txtserie"},
                      {"txtCorrelativo": "#txtcorrelativo"},
                      {"cboTipoDocumento": "#cbotipodocumento"},
                      {"blkNumeroDocumento": "#blknumerodocumento"},
                      {"txtNumeroDocumento": "#txtnumerodocumento"},
                      {"txtCliente": "#txtcliente"},
                      {"txtApellidos": "#txtapellidos"},
                      {"txtDireccion": "#txtdireccion"},
                      {"txtCelular": "#txtcelular"},
                      {"txtCorreo": "#txtcorreo"},
                      {"cboSucursal": "#cbosucursal"},
                      {"txtObservaciones" : "#txtobservaciones"},
                      {"txtFechaVenta": "#txtfechaventa"},
                      {"txtHoraVenta": "#txthoraventa"},
                      {"btnActualizar": "#btnactualizar"},
                      {"tblDetalle": "#tbldetallebody"},
                      {"btnAgregarProducto": "#btnagregarproducto"},
                      {"txtLectora" : "#txtlectora"},
                      {"lblSubTotal": "#lblsubtotal"},
                      {"txtDescuentoGlobal": "#txtdescuentoglobal"},
                      //{"lblDescuento": "#lbldescuento"},
                      {"lblTotal": "#lbltotal"},
                      {"txtMontoEfectivo": "#txtefectivo"},
                      {"txtMontoTarjeta": "#txttarjeta"},
                      {"txtMontoCredito": "#txtcredito"},
                      {"txtMontoYape": "#txtyape"},
                      {"txtMontoPlin": "#txtplin"},
                      {"txtMontoBanco": "#txtbanco"},
                      //{"blkTipoTarjeta": "#blktipotarjetas"},
                      {"btnCancelarEdicion": "#btncancelaredicion"},
                      {"btnGuardar": "#btnguardar"},
                      {"mdlBuscarProducto": "#mdlBuscarProducto"},
                      {"txtBuscar":"#txtbuscar"},
                      {"cboTipo":"#cbofiltrotipo"},
                      {"cboCategoria":"#cbofiltrocategoria"},
                      {"lblSeleccionados":"#lblSeleccionados"},
                      {"blkListaProductos" : "#blklistaproductos"},
                      {"btnAgregarProductos": "#btnagregarproductos"}
                    ]);  

      // DOM.radTipoPago = $("input[name=radtipopago]");
      // DOM.radTipoTarjeta = $("input[name=radtipotarjeta]");
        this.DOM = DOM;
    };
  
    this.setEventos = function(){
      var self = this,
          DOM = self.DOM;
  
      DOM.cboTipoComprobante.on("change", (e) => {
        const idTipoComprobante = e.currentTarget.value;
        cargarSeriesPorTipoComprobante(idTipoComprobante);
      });
  
      DOM.txtCorrelativo.on("keypress", function(e){
        if (!_Util.soloNumeros(e)){
          e.preventDefault();
        }
        return;
      });
      
      DOM.txtCorrelativo.on("change", function(e){
          const correlativo = this.value;
          if (correlativo.length === 0){
            return;
          }

          if (correlativo.length < 6){
              this.value = _Util.completarNumero(correlativo, 6);
              return;
          }
      });
  
      DOM.cboClienteBuscar.on("change", function(){
        obtenerCliente(this.value, DOM);
      });
  
      DOM.cboTipoDocumento.on("change", function(){
        cambiarTipoDocumento(this.value, DOM.blkNumeroDocumento, DOM.txtNumeroDocumento);
      });
  
      const soloNumerosDecimales = function(e){
        if (!_Util.soloNumerosDecimales(e)){
          e.preventDefault(); return;
        }
        return;
      };
  
      DOM.txtMontoEfectivo.on("keypress", soloNumerosDecimales);
      DOM.txtMontoTarjeta.on("keypress", soloNumerosDecimales);
      DOM.txtMontoCredito.on("keypress", soloNumerosDecimales);
      DOM.txtMontoYape.on("keypress", soloNumerosDecimales);
      DOM.txtMontoPlin.on("keypress", soloNumerosDecimales);
      DOM.txtMontoBanco.on("keypress", soloNumerosDecimales);
      DOM.txtDescuentoGlobal.on("keypress", soloNumerosDecimales);
  
      DOM.txtMontoEfectivo.on("change", function(){
        if (this.value == ""){
          this.value = "0.00";
        }
        equilibrarMontoPago("E");
      });
  
      DOM.txtMontoTarjeta.on("change", function(){
        if (this.value == ""){
          this.value = "0.00";
        }
        equilibrarMontoPago("T");
      });
  
      DOM.txtMontoCredito.on("change", function(){
        if (this.value == ""){
          this.value = "0.00";
        }
        equilibrarMontoPago("C");
      });
  
      DOM.txtMontoYape.on("change", function(){
        if (this.value == ""){
          this.value = "0.00";
        }
        equilibrarMontoPago("Y");
      });
  
      DOM.txtMontoPlin.on("change", function(){
        if (this.value == ""){
          this.value = "0.00";
        }
        equilibrarMontoPago("P");
      });
  
      DOM.txtMontoBanco.on("change", function(){
        if (this.value == ""){
          this.value = "0.00";
        }
        equilibrarMontoPago("B");
      });
  
      DOM.btnAgregarProducto.on("click", () => {
        this.prepararAgregarProductos();
        //agregarFilaDetalle();
      });
  
      DOM.tblDetalle.on("click", "tr .pointer", function(e){
        //buscarProducto(this.parentElement);
      });
  
      DOM.txtBuscar.on("keyup", function(e){
        realizarBusquedaProducto(this.value);
      });
  
      DOM.cboTipo.on("change", function(e){
        realizarBusquedaProducto(DOM.txtBuscar.val());
        cargarCategorias(this.value);
      });
  
      DOM.cboCategoria.on("change", function(e){
        realizarBusquedaProducto(DOM.txtBuscar.val());
      });
  
      DOM.tblDetalle.on("click", "tr button.eliminar", function(e){
        eliminarFilaDetalle(this.parentElement.parentElement);
      });
  
      DOM.frmRegistro.on("submit", function(e){
        e.preventDefault();
        grabarVenta();  
      });
  
      DOM.tblDetalle.on("change", "tr .cantidad input", function(){
        const valor = this.value,
            $tr = this.parentElement.parentElement;
  
        if ($tr.children[_INDEX.producto].dataset.producto == ""){
          this.value = 1;
          return;
        }

        if (valor == "" || valor.length <= 0){
          this.value = this.dataset.preval;
          return;
        }
  
        const maxstock = this.dataset.maxstock ? this.dataset.maxstock : "";
  
          if (maxstock != "" && parseInt(valor) > maxstock){
            this.value = this.dataset.preval;
            return;
          }
  
          if (parseInt(valor) <= 0){
            this.value = 1;
          }
  
          modificarCantidadDetalle($tr, this);
      });

      DOM.tblDetalle.on("focusout", "tr .cantidad input", () => {
        //const $txtLectora = this.DOM.txtLectora;
        //$txtLectora.focus();
        //$txtLectora.val("");
      });
  
      DOM.tblDetalle.on("change", "tr .precio-unitario input", function(){
        var valor = this.value,
            $tr = this.parentElement.parentElement;
  
            if ($tr.children[_INDEX.producto].dataset.producto == ""){
            this.value ="0.00";
            return;
          }
  
          if (valor == "" || valor.length <= 0){
            this.value = this.dataset.preval;
            return;
          }
  
          modificarPUDetalle($tr, this);
      });
  
      DOM.tblDetalle.on("keypress", "tr .cantidad input", function(e){
        if (!_Util.soloNumeros(e)){
          e.preventDefault(); return;
        }
        return;
      });
  
      DOM.tblDetalle.on("keyup", "tr .descuento input", function(e){
        var valor = this.value,
            $tr = [].slice.call(this.parentElement.parentElement.children);
  
        if ($tr[_INDEX.producto].dataset.producto == ""){
          this.value = "";
          return;
        }
  
        if (valor.length >= 6){
          buscarDescuento(valor, "detalle", $tr);
        }
      });
  
      DOM.mdlBuscarProducto.on("shown.bs.modal", function(e){
        /*
        var txtBuscar = DOM.txtBuscar,
            txtBuscarVal = txtBuscar.val();
  
        txtBuscar.focus();
        txtBuscar[0].setSelectionRange(0, txtBuscarVal.length);
        realizarBusquedaProducto(txtBuscarVal);
        */
      });
  
      DOM.blkListaProductos.on("click", "tr:not(.tr-null)", (e) => {
        /*
        const itemProducto = self.getProducto(this.dataset.id);
        if (itemProducto.i != -1 && _TR_BUSCAR != null){
          seleccionarProductoBuscar(itemProducto);
        }
        */
        this._seleccionarProductoBuscar($(e.currentTarget));
      });

      DOM.txtDescuentoGlobal.on("focusin", function(e){
        this.select();
      });
  
      DOM.txtDescuentoGlobal.on("change", function(e){
        console.log(this.value)
        if (this.value == ""){
          this.value = "0.00";
        }
        const subTotal = parseFloat(DOM.lblSubTotal.html());
        let descuento = parseFloat(this.value);

        if (descuento < 0){
          this.value = "0.00";
          DOM.lblTotal.html(parseFloat(subTotal).toFixed(2));
          return;
        }

        if (subTotal <= descuento){
          this.value = parseFloat(subTotal).toFixed(2);
          descuento = subTotal;
        }

        DOM.lblTotal.html(parseFloat(subTotal - descuento).toFixed(2));
        equilibrarMontoPago();
      });
  
      DOM.cboSucursal.on("change", function(){
        self.obtenerDataProductos();
      });
  
      DOM.btnActualizar.on("click", function(e){
        e.preventDefault();
        self.obtenerDataProductos(false, reafirmarStock);
      });
  
      DOM.btnCancelarEdicion.on("click", function(){
        self.cancelarEdicion();
      });

      DOM.txtLectora.on("change", (e)=>{
        const codigoBarra = e.currentTarget.value.trim();
        if (codigoBarra.length >= CARACTERES_LECTORA){
          agregarProductoUsandoLectora(codigoBarra);
        }
      });

      DOM.txtLectora.on("keypress", (e)=>{
        if (e.charCode === 13){
          e.preventDefault();
          const codigoBarra = e.currentTarget.value.trim();
          agregarProductoUsandoLectora(codigoBarra);
        }
      });

      DOM.btnAgregarProductos.on("click", (e)=> {
        e.preventDefault();
        this._agregarProductosAlDetalle();
      });

      /*
      const observer = new MutationObserver(functiona(mutationsList, observer) {
        console.log(mutationsList);
      });
      observer.observe(document.getElementById("lbltotal"), {characterData: false, childList: true, attributes: false});
      */
      //cargarCorrelativo(DOM.cboTipoComprobante.val());
    };
  
    const cargarCategorias = function(codTipo){
      var DOM = self.DOM;
      if (codTipo == ""){
        DOM.cboCategoria.html(_tpl8.Combo([]));
        return;
      }
      DOM.cboCategoria.html(_tpl8.Combo(ArrayUtils.conseguirTodos(_data.categoria_productos,"cod_tipo_categoria", codTipo)));  
    };

    const agregarProductoUsandoLectora = (codigoBarra) => {
      let indexItemProducto = 0;
      const itemProducto = _data.productos.find((item, i) => {
        indexItemProducto = i;
        return item.codigo_generado === codigoBarra;
      });

      if (Boolean(itemProducto)){
        if (itemProducto.stock <= 0){
          swal("Error", `El producto ${itemProducto.nombre_producto} no tiene STOCK disponible.`);
          this.DOM.txtLectora.val("");
          this.DOM.txtLectora.focus();
          return;
        }

        const dataFila = {
          cod_producto: itemProducto.id,
          nombre_producto: itemProducto.nombre_producto,
          precio_unitario: itemProducto.precio_unitario,
          fecha_vencimiento: itemProducto.fecha_vencimiento,
          lote: itemProducto.lote,
          marca : itemProducto.marca,
          cantidad: 1,
          monto_descuento: null,
          tipo_descuento: null,
          cod_descuento: null,
          subtotal: itemProducto.precio_unitario,
          maxstock : itemProducto.stock
        };
  
        const $nuevoDetalle = $(_tpl8.tblDetalle(dataFila));
        this.DOM.tblDetalle[!_VACIO ? "append" : "html"]($nuevoDetalle);
        const $precio = $nuevoDetalle.find(".precio-unitario input");
        this.DOM.txtLectora.val("");
        //$precio.focus();
        //$precio.select();
        this.DOM.txtLectora.focus();

        modificarStockProducto({o: itemProducto, i: indexItemProducto}, 1, "-");
        modificarTotalGeneral();

        _VACIO = false;
        return;
      }

      swal("Error", `Producto no encontrado.`);
      this.DOM.txtLectora.val("");
      this.DOM.txtLectora.focus();
    };
  
    const reafirmarStock = () => {
      const DOM = this.DOM,
          //arrDataProductos = _data.productos,
          arregloTR = DOM.tblDetalle.find("tr:not(.tr-null)").toArray();
  
        swal("Éxito", "Stock actualizado...", "success");
        for (let i = 0; i < arregloTR.length; i++) {
          const $tds = [].slice.call(arregloTR[i].children),
              id_producto = $tds[_INDEX.producto].dataset.producto,
              cantidad = $tds[_INDEX.cantidad].children[0].value;
  
            if (id_producto != undefined && id_producto != "0000-00-00"){
              const item = self.getProducto(id_producto);
              _data.productos[item.i].stock = item.o.stock - cantidad;
            }
        };
    };    
  
    const equilibrarMontoPago = (txtAccion = "E", montoTotal) => {
      /*Obtener monto 1 y monto 2,*/
      const DOM = this.DOM;
      const $efectivo = DOM.txtMontoEfectivo,
            $tarjeta = DOM.txtMontoTarjeta,
            $credito = DOM.txtMontoCredito,
            $yape = DOM.txtMontoYape,
            $plin = DOM.txtMontoPlin,
            $banco = DOM.txtMontoBanco;
  
      let   efectivo = $efectivo.val(),
            tarjeta = $tarjeta.val(),
            credito = $credito.val(),
            yape = $yape.val(),
            plin = $plin.val(),
            banco = $banco.val(),
            total = (montoTotal == undefined ? parseFloat(DOM.lblTotal.html())  : montoTotal);
  
          switch(txtAccion){
            case "E":
              efectivo = total;            
              tarjeta = "0.00";
              credito = "0.00";
              yape = "0.00";
              plin = "0.00";
              banco = "0.00";
            break;
            case "T":
              efectivo = total - tarjeta - credito - yape - plin - banco;
              if (efectivo < 0.00){
                efectivo = "0.00";
                tarjeta = total;
                credito = "0.00";
                yape = "0.00";
                plin = "0.00";
                banco = "0.00";
              }
            break;
            case "C":
              efectivo = total - tarjeta - credito - yape - plin - banco;
              if (efectivo < 0.00){
                efectivo = "0.00";
                tarjeta = "0.00";
                credito = total;
                yape = "0.00";
                plin = "0.00";
                banco = "0.00";
              }
            break;
            case "Y":
              efectivo = total - tarjeta - credito - yape - plin - banco;
              if (efectivo < 0.00){
                efectivo = "0.00";
                tarjeta = "0.00";
                credito = "0.00";
                yape = total;
                plin = "0.00";
                banco = "0.00";
              }
            break;
            case "P":
              efectivo = total - tarjeta - credito - yape - plin - banco;
              if (efectivo < 0.00){
                efectivo = "0.00";
                tarjeta = "0.00";
                credito = "0.00";
                yape = "0.00";
                plin = total;
                banco = "0.00";
              }
            break;
            case "B":
              efectivo = total - tarjeta - credito - yape - plin - banco;
              if (efectivo < 0.00){
                efectivo = "0.00";
                tarjeta = "0.00";
                credito = "0.00";
                yape = "0.00";
                plin = "0.00";
                banco = total;
              }
            break;
          }
         
          //cambiarTarjeta( (tarjeta > 0 ? "T" : "E"), DOM.blkTipoTarjeta);
  
          $tarjeta.val(parseFloat(tarjeta).toFixed(2));
          $efectivo.val(parseFloat(efectivo).toFixed(2));
          $credito.val(parseFloat(credito).toFixed(2));
          $yape.val(parseFloat(yape).toFixed(2));
          $plin.val(parseFloat(plin).toFixed(2));
          $banco.val(parseFloat(banco).toFixed(2));
    };
  
    this.getProducto = function(codigo_unico_producto){
      return _ArrayUtils.conseguirPID(_data.productos, "codigo_unico_producto", codigo_unico_producto);
    };
  
    this.setDataProductos = function(_dataProductos){
      _data.productos = _dataProductos.map(p => {
        return {
          ...p, seleccionado : false
        };
      });
    }

    /*
    this.setDataProductos = function(_dataProductos){
      _data.productos = _dataProductos;
    }
    */
  
    this.obtenerDataProductos = async function(deboEliminarCarrito = true, fnPostStocked = undefined ){
        const idSucursal = this.DOM.cboSucursal.val();
        try {
          const { data } = await apiAxios.get(`ventas-productos/${idSucursal}`);
          this.setDataProductos(data);
  
        } catch (error) {
          swal("Error", "Error al obtener los productos para Venta.", "error");
          console.error(error);
        }
  
        if (deboEliminarCarrito){
          eliminarTodoCarrito();
        }
        
        if (Boolean(fnPostStocked)){
          fnPostStocked();
        }
    };
  
    this.obtenerData = function(){
      obtenerClientes();
      obtenerSucursales();
      obtenerTipoCategorias();
      obtenerSeries();
    };
  
    const obtenerClientes = async () => {
      try {
          const { data } = await apiAxios.get('clientes');
          this.DOM.cboClienteBuscar.html(_tpl8.cboClientesBuscar(data)).chosen();
          this.setDataClientes(data);
  
      } catch (error) {
          swal("Error", "Error al obtener los clientes.", "error");
          console.error(error);
      }
    };
  
    const obtenerSucursales = async () => {
      try {
          const { data } = await apiAxios.get('sucursales');
          const sucursalHTML = _tpl8.Sucursal(data);
  
          app.ListarVentas.DOM.cboSucursal.html(sucursalHTML);
          this.DOM.cboSucursal.html(sucursalHTML);
  
          this.obtenerDataProductos();
  
      } catch (error) {
          swal("Error",  "Error al obtener las sucursales.", "error");
          console.error(error);
      }
    };
  
    const obtenerTipoCategorias = async () => {
      try {
          const { data } = await apiAxios.get('tipo-categorias');
          this.DOM.cboTipo.html(_tpl8.Combo(data));
  
      } catch (error) {
          swal("Error",  "Error al obtener los tipo de categorías.", "error");
          console.error(error);
      }
    };

    const obtenerSeries = async () => {
      try {
          const { data } = await apiAxios.get(`serie-documentos`);
          this.setDataSeries(data);

          cargarSeriesPorTipoComprobante(this.DOM.cboTipoComprobante.val());
      } catch (error) {
          swal("Error", "Error al obtener los series.", "error");
          console.error(error);
      }
    };

    const cargarSeriesPorTipoComprobante = (idTipoComprobante) => {
      const seriesPorTC = _data.series.filter(s => s.id_tipo_comprobante == idTipoComprobante);
      this.DOM.txtSerie.html(_tpl8.Series(seriesPorTC.map(({serie, correlativo})=> {
        return {serie, correlativo};
      })));
    };

    const eliminarTodoCarrito = (resetearStock) => {
      /*Clean up, sub total 0, descuento vacío, total 0, descuentos 0, eliminar dscuent globaal*/
      /*Reseetar stock si y solo si se eliminó todo el carrito sin haber regitrado nada */
      const DOM = this.DOM, index = _INDEX;
      let arregloTR;
  
      _VACIO = true;
      DOM.lblSubTotal.html("0.00");
      DOM.lblTotal.html("0.00"); 
      DOM.txtDescuentoGlobal.val("0.00");
  
      if (resetearStock == true){   
        arregloTR = DOM.tblDetalle.find("tr:not(.tr-null)").toArray();
        $.each(arregloTR, function(i,o){
          var arregloTD = [].slice.call(o.children),
            $producto = arregloTD[index.producto].dataset.producto,
            $cantidad = arregloTD[index.cantidad].children[0];
            if ($producto != "0000-00-00"){
              modificarStockProducto(self.getProducto($producto), $cantidad.value, "+");
            }
  
        });
      } 
  
      //cancelarDescuento("global");
      DOM.tblDetalle.html(_tpl8.tblDetalle([]));
    };
  
    const obtenerCliente = (idCliente, DOM) => {
      const objCliente = _ArrayUtils.conseguir(_data.clientes, "id", idCliente);
      
      if (objCliente === -1){
        DOM.cboTipoDocumento.val("0");
        DOM.txtNumeroDocumento.val(null);
        DOM.blkNumeroDocumento.hide();
        DOM.txtCliente.val(null);
        DOM.txtApellidos.val(null);
        DOM.txtDireccion.val(null);
        DOM.txtCorreo.val(null);
        DOM.txtCelular.val(null);
        cambiarTipoDocumento(DOM.cboTipoDocumento.val(), DOM.blkNumeroDocumento, DOM.txtNumeroDocumento);
        return;
      }

      DOM.cboTipoDocumento.val(objCliente.id_tipo_documento);
  
      if (objCliente.id_tipo_documento != "0"){
        DOM.txtNumeroDocumento.val(objCliente.numero_documento);
        DOM.blkNumeroDocumento.show();
      } else {
        DOM.txtNumeroDocumento.val(null);
        DOM.blkNumeroDocumento.hide();
      }

      DOM.txtCliente.val(objCliente.nombres);
      DOM.txtApellidos.val(objCliente.apellidos);
      DOM.txtDireccion.val(objCliente.direccion);
      DOM.txtCelular.val(objCliente.celular);
      DOM.txtCorreo.val(objCliente.correo);

      cambiarTipoDocumento(DOM.cboTipoDocumento.val(), DOM.blkNumeroDocumento, DOM.txtNumeroDocumento);
    };
  
    const cambiarTipoDocumento = (tipoDocumento, bloqueNumeroDocumento, txtNumeroDocumento) => {
      let maxLength;
      switch(tipoDocumento){
        case "0":
          bloqueNumeroDocumento.hide();
          maxLength = 0;
        break;
        case "1":
          bloqueNumeroDocumento.show();
          maxLength = 8;
        break;
        case "4":
        case "7":
          bloqueNumeroDocumento.show();
          maxLength = 12;
        break;
        case "6":
          bloqueNumeroDocumento.show();
          maxLength = 11;
        break;
      }

      const DOM = this.DOM;
      const blkCliente = DOM.txtCliente.parents(".col-xs-12");
      const blkApellidos = DOM.txtApellidos.parents(".col-xs-12");
      if (tipoDocumento == "6"){
        blkApellidos.hide();
        blkCliente.removeClass("col-sm-4").addClass("col-sm-8");
        blkCliente.find(".control-label").html("Razón Social");
      } else {
        blkApellidos.show();
        blkCliente.addClass("col-sm-4").removeClass("col-sm-8");
        blkCliente.find(".control-label").html("Nombre cliente");
      }

      txtNumeroDocumento[0].maxLength = maxLength;
      txtNumeroDocumento[0].value = txtNumeroDocumento[0].value.substr(0, maxLength);
    };

    const buscarProducto  = ($tr) => {
      _TR_BUSCAR = $tr;
      this.DOM.mdlBuscarProducto.modal("show");
    };
  
    const seleccionarProductoBuscar = (itemProducto) => {
      const objProducto = itemProducto.o,
            stock = objProducto.stock;
  
      if (stock <= 0){
        swal("Error", `El producto ${objProducto.nombre_producto} no tiene STOCK disponible.`);
        return;
      }
  
      const index = _INDEX,
          arregloTD = [].slice.call(_TR_BUSCAR.children),
          $producto = arregloTD[index.producto],
          $precio = arregloTD[index.precio_unitario].children[0],
          $marca = arregloTD[index.marca],
          //$fecha_vencimiento = arregloTD[index.fecha_vencimiento],
          $lote = arregloTD[index.lote],
          $cantidad = arregloTD[index.cantidad].children[0], /*cantidad*/
          $subtotal = arregloTD[index.subtotal],
          valorPrecio = parseFloat(objProducto.precio_unitario).toFixed(2);
      let cantidadDefault, subtotal;
  
      this.DOM.mdlBuscarProducto.modal("hide");
  
      /*Si hubo un producto seleccionad con anteiorirdad regresar sus datos a como estaban antes de cagarla.*/
      if ($producto.dataset.producto != "0000-00-00"){
        modificarStockProducto(this.getProducto($producto.dataset.producto), $cantidad.value, "+");  
      }
  
      $producto.dataset.codproducto = objProducto.id;
      $producto.dataset.producto = objProducto.codigo_unico_producto;
  
      $producto.innerHTML = '<span>'+objProducto.nombre_producto+'</span>';
      $precio.value = valorPrecio;
  
      $marca.innerHTML = objProducto.marca;
      //$fecha_vencimiento.innerHTML = objProducto.fecha_vencimiento;
      $lote.innerHTML = objProducto.lote;
  
      cantidadDefault = 1;
      $cantidad.value = cantidadDefault;
  
      $cantidad.dataset.maxstock = objProducto.stock;
  
      subtotal = valorPrecio * cantidadDefault;
  
      modificarStockProducto(itemProducto, cantidadDefault, "-");
      modificarSubTotalDetalle( subtotal, $subtotal);
  
      $precio.focus();
      $precio.select();
  
      _TR_BUSCAR = null;
    };
  
    const agregarFilaDetalle = (dataFila) => {
      const DOM = this.DOM;
  
      if (!dataFila){
        dataFila = {
          cod_producto: null,
          precio_unitario: "0.00",
          fecha_vencimiento: "0000-00-00",
          lote: "",
          marca : "",
          cantidad: 1,
          monto_descuento: null,
          tipo_descuento: null,
          cod_descuento: null,
          subtotal: "0.00",
          maxstock: 0
        };
      }
      
      const $nuevoDetalle = $(_tpl8.tblDetalle(dataFila));
      DOM.tblDetalle[!_VACIO ? "append" : "html"]($nuevoDetalle);
      $nuevoDetalle.find(".pointer").click();
      
      _VACIO = false;
      return dataFila;
    };
  
    const realizarBusquedaProducto = (cadena) => {
      const DOM = this.DOM;
      /*
      if (cadena == ""){
        //DOM.blkListaProductos.html('<div class="alert alert-info"><strong>Realice la búsqueda del producto a vender.</strong></div>');
        return;
      }
      */
      if (cadena == "" || cadena.length >= 3){
        const parametrosBusqueda = [{
            propiedad: "nombre_producto",
            valor: cadena,
            mayusculas : true,
            exactitud : false
          },
          { propiedad : "id_tipo_categoria",
            valor : DOM.cboTipo.val(),
            mayusculas: false,
            exactitud : true
          },
          { propiedad : "id_categoria",
            valor : DOM.cboCategoria.val(),
            mayusculas: false,
            exactitud : true
          }
        ];

        const productos = _ArrayUtils.buscarTodos(_data.productos, parametrosBusqueda);
        DOM.blkListaProductos.html(_tpl8.ListaProducto(productos));
       //DOM.blkListaProductos.html(_tpl8.ListaProducto(_ArrayUtils.buscarTodos(_data.productos, parametrosBusqueda)));
      }
    };
  
    const eliminarFilaDetalle = ($tr) => {
      var tblDetalle = this.DOM.tblDetalle,
          index = _INDEX,
          arregloTD = [].slice.call($tr.children),
          cantidad = arregloTD[index.cantidad].children[0].value,
          itemProducto = this.getProducto(arregloTD[index.producto].dataset.producto),
          arregloTR;
  
      $tr.remove();
  
      arregloTR= tblDetalle.find("tr:not(.tr-null)").toArray();
  
      if (itemProducto.i >= 0){
        modificarStockProducto(itemProducto,cantidad, "+");  
      }
  
      var numFila = arregloTR.length;
      if (numFila <= 0){
        _VACIO = true;
        tblDetalle.html(_tpl8.tblDetalle());
      }
  
      modificarTotalGeneral(arregloTR);
    };
  
    const modificarStockProducto = (itemProducto, cantidad, tipo) => {
      /*tipo == + / -*/
        const objProducto = itemProducto.o,
            stock = objProducto.stock,
            nuevoStock  = parseInt(stock) + parseInt(cantidad  * (tipo == "+" ? 1 : -1)),
            exceso = 0;
  
        if (nuevoStock < 0){
          exceso = nuevoStock * -1;
          nuevoStock = 0;  
        }
  
        _data.productos[itemProducto.i].stock = nuevoStock;
        return {viejo: stock, exceso: exceso, nuevo: nuevoStock};
    };
  
    const modificarCantidadDetalle = ($tr, $cantidad_) => {
      /*obtener producto, actualizarse la cantidad, mdoifcar subtotal, modificar gran sub total*/
      const index = _INDEX,
          $cantidad = ($cantidad_ == null ? arregloTD[index.cantidad].children[0] : $cantidad_),
          cantidadAnterior = $cantidad.dataset.preval, 
          cantidadNueva = $cantidad.value,
          arregloTD = [].slice.call($tr.children);
  
      const itemProducto = this.getProducto(arregloTD[index.producto].dataset.producto),
          $subtotal = arregloTD[index.subtotal],
          $precio_unitario = arregloTD[index.precio_unitario].children[0],
          valorPrecio = parseFloat($precio_unitario.value).toFixed(2),
          cantidadVender = cantidadNueva - cantidadAnterior,
          stockModificado = modificarStockProducto(itemProducto, cantidadVender, "-");
  
      if (stockModificado.nuevo < 0){
        cantidadNueva = cantidadNueva - stockModificado.exceso;
        $cantidad.value = cantidadNueva;
      }
  
      const subtotal = valorPrecio * cantidadNueva;
      $cantidad.dataset.preval = cantidadNueva;
      modificarSubTotalDetalle( subtotal, $subtotal);
    };
  
    const modificarPUDetalle = function($tr, $precio){
      /*obtener producto, actualizarse la cantidad, mdoifcar subtotal, modificar gran sub total*/
      const index = _INDEX,
            precioNuevo = $precio.value,
            arregloTD = [].slice.call($tr.children);
  
      const $subtotal = arregloTD[index.subtotal],
            $cantidad = arregloTD[index.cantidad].children[0],
            valorCantidad = parseFloat($cantidad.value).toFixed(2);
          
      const subtotal = precioNuevo * valorCantidad;
      $precio.dataset.preval = precioNuevo;
      $precio.value = parseFloat($precio.value).toFixed(2);
      modificarSubTotalDetalle( subtotal, $subtotal);
    };
  
    const modificarSubTotalDetalle = (subtotal, $subtotal) => {
        $subtotal.innerHTML =  parseFloat(subtotal).toFixed(2);
        modificarTotalGeneral();
    };  
  
    const modificarTotalGeneral = (arregloTR) => {
      /*recorret todos los TR, obtener ultimos valores (subtotal), sumarlos
          obtener DescuentoTotal
          obtener Total
          */
        const DOM = this.DOM;
        let subtotal = 0.00, total = 0.00;
  
        if (!arregloTR){
            arregloTR = DOM.tblDetalle.find("tr:not(.tr-null)").toArray();
        }
  
        for (let i = arregloTR.length - 1; i >= 0; i--) {
          subtotal += parseFloat(arregloTR[i].children[_INDEX.subtotal].innerHTML);
        };

        total = parseFloat(subtotal).toFixed(2);
  
        DOM.lblSubTotal.html(parseFloat(subtotal).toFixed(2));
        DOM.lblTotal.html(total);
        DOM.txtDescuentoGlobal.val("0.00");

        equilibrarMontoPago("E", total);

    };
  
    const grabarVenta = () => {
      const objButtonLoading = new ButtonLoading({$: this.DOM.btnGuardar[0]});
      const objVenta = verificarVenta();
      const fnConfirm = async (isConfirm) => {
            if(!isConfirm){
              return;
            }

            objButtonLoading.start();

            try {
              const cabecera = objVenta.datos.cabecera,
                    detalle  = objVenta.datos.detalle;

              const sentData = {
                id_tipo_comprobante : cabecera.id_tipo_comprobante,
                serie: cabecera.serie,
                correlativo: cabecera.correlativo,
                id_cliente: Boolean(cabecera.id_cliente) ? cabecera.id_cliente : null,
                cliente_id_tipo_documento : cabecera.id_tipo_documento_cliente,
                cliente_numero_documento : cabecera.numero_documento_cliente,
                cliente_nombres : cabecera.nombre_cliente,
                cliente_apellidos : cabecera.apellidos_cliente,
                cliente_direccion : cabecera.direccion_cliente,
                cliente_celular : cabecera.celular_cliente,
                cliente_correo : cabecera.correo_cliente,
                monto_efectivo: cabecera.monto_efectivo,
                monto_tarjeta: cabecera.monto_tarjeta,
                monto_credito : cabecera.monto_credito,
                monto_yape: cabecera.monto_yape,
                monto_plin : cabecera.monto_plin,
                monto_transferencia : cabecera.monto_transferencia,
                descuento_global : cabecera.descuento_global,
                importe_total : cabecera.importe_total,
                fecha_venta : cabecera.fecha_venta,
                hora_venta: cabecera.hora_venta,
                id_sucursal : cabecera.id_sucursal,
                observaciones: cabecera.observaciones,
                productos: detalle
              };
          
              const {data} = MODO === '+' 
                              ? await apiAxios.post('ventas', sentData)
                              : await apiAxios.put(`ventas/${COD_VENTA_EDITAR}`, sentData);

              //swal("Éxito", "Registrado con éxito.", "success");
              //app.ListarVentas.verDetalle(data.id);
  
              COD_VENTA_EDITAR = null;
              this.DOM.btnCancelarEdicion.hide();
              this.DOM.cboSucursal.attr("disabled",false);
              this.DOM.btnActualizar.show();
              $("#lblrotuloedicion").empty();
              limpiarVenta();
              //$('.nav-tabs a[href="#tabListadoVentas"]').tab('show');
              //ListarVentas.listarVentas(data.lista_ventas);
              MODO = "+";
              
              const idTipoComprobante =  this.DOM.cboTipoComprobante.val();
              if (idTipoComprobante == "00"){
                  app.ListarVentas.verAtencion(data.id);
              } else {
                  app.ListarVentas.verComprobante(data.id_documento_electronico);
              }
    
            } catch (error) {
              const { response } = error;
              if (Boolean(response?.data?.message)){
                swal("Error", response.data.message, "error");
                return;
              }

              swal("Error", "Problema con el registro de la venta.", "error");
              console.error(error);
            } finally {
              objButtonLoading.finish();
            }
        };
  
      if (!objVenta.rpt){
        swal("Error", objVenta.msj, "error");
        return;
      }
          
      if (Boolean(objButtonLoading.isLoading)){
        return;
      }

      fnConfirm(true);
      /*
      swal({
            title: "Confirme",
            text: "¿Esta seguro de grabar los datos ingresados?",
            showCancelButton: true,
            confirmButtonColor: '#3d9205',
            confirmButtonText: 'Si',
            cancelButtonText: "No",
            closeOnConfirm: false,
            closeOnCancel: true,
            imageUrl: "../images/pregunta.png"
          }, fnConfirm);
      */
    };
  
    const verificarVenta = () => {
      const objVerificarCabecera = verificarCabecera();
  
      if (!objVerificarCabecera.rpt){
        return objVerificarCabecera;
      }
  
      const objVerificarDetalle = verificarDetalle();
      if (!objVerificarDetalle.rpt){
        return objVerificarDetalle;
      }
  
      return {rpt: true, datos: {cabecera: objVerificarCabecera.datos, detalle: objVerificarDetalle.datos}};
    };
   
    const verificarCabecera = () => {
      const DOM = this.DOM,
          id_tipo_comprobante = DOM.cboTipoComprobante.val(),
          serie = DOM.txtSerie.val(),
          correlativo = parseInt(DOM.txtCorrelativo.val()),
          id_cliente = DOM.cboClienteBuscar.val(),
          id_tipo_documento_cliente = DOM.cboTipoDocumento.val(),
          numero_documento_cliente = DOM.txtNumeroDocumento.val(),
          nombre_cliente = DOM.txtCliente.val(),
          apellidos_cliente = DOM.txtApellidos.val(),
          direccion_cliente = DOM.txtDireccion.val(),
          correo_cliente = DOM.txtCorreo.val(),
          celular_cliente = DOM.txtCelular.val(),
          fecha_venta = DOM.txtFechaVenta.val(),
          hora_venta = DOM.txtHoraVenta.val(),
          //tipoPago = DOM.radTipoPago[0].checked ? 'E' : 'T', /*0: EFECTIVO, 1: TARJETA*/     
          monto_efectivo = DOM.txtMontoEfectivo.val(),
          monto_tarjeta = DOM.txtMontoTarjeta.val(), 
          //tipoTarjeta = DOM.radTipoTarjeta[0].checked ? 'C' : 'D',
          monto_credito = DOM.txtMontoCredito.val(), 
          monto_yape = DOM.txtMontoYape.val(), 
          monto_plin = DOM.txtMontoPlin.val(), 
          monto_transferencia = DOM.txtMontoBanco.val(), 
          id_sucursal = DOM.cboSucursal.val(),
          descuento_global = DOM.txtDescuentoGlobal.val(),
          importe_total = DOM.lblTotal.html(),
          observaciones = DOM.txtObservaciones.val(),
          numeroDocumentoLength = numero_documento_cliente.length;
  
      if (nombre_cliente.length < 0){
        return {rpt: false, msj: "Ingrese nombre de cliente."};
      }
  
      if (id_tipo_documento_cliente == '1' && (numeroDocumentoLength != 8 && numeroDocumentoLength != 0)) {
        return {rpt: false, msj: "Ingrese un número de DNI válido."};
      }
  
      if (id_tipo_documento_cliente == '6' && (numeroDocumentoLength != 11 && numeroDocumentoLength != 0)) {
        return {rpt: false, msj: "Ingrese un número de RUC válido."};
      }
  
      if (fecha_venta == ""){
        return {rpt: false, msj: "Ingrese fecha de venta."};
      }

      if (hora_venta == ""){
        return {rpt: false, msj: "Ingrese hora de venta."};
      }
  
      if (id_tipo_comprobante.length > 0){
        if (serie.length != 4){
          return {rpt: false,msj: "Número de serie no válido. Debe tener 4 dígitos"};
        }
  
        if (correlativo < 0){
          return {rpt: false, msj: "Número correlativo no válido"};
        }
      }
  
      return {
        rpt: true,
        datos: {
          id_tipo_comprobante,
          serie,
          correlativo,
          id_cliente,
          id_tipo_documento_cliente,
          numero_documento_cliente,
          nombre_cliente,
          apellidos_cliente,
          direccion_cliente,
          correo_cliente,
          celular_cliente,
          fecha_venta,
          hora_venta,
          monto_efectivo,
          monto_tarjeta,
          monto_credito,
          monto_yape,
          monto_plin,
          monto_transferencia,
          id_sucursal,
          descuento_global,
          importe_total,
          observaciones
        }
      };
    };
  
    const verificarDetalle = () => {
      /*Verificar cada detalle
          producto (cod_producto)
          cantidad > 0,
          subtotal
          precio ( > 0)
        Detalles < 0 => Error
      */
      const DOM = this.DOM,
          index = _INDEX,
          arregloTR = DOM.tblDetalle.find("tr:not(.tr-null)").toArray(), 
          objDetalles = [],
          fnVerificarFila = function($tr, n){
            var arregloTD = [].slice.call($tr.children),
                producto = arregloTD[index.producto].dataset.producto,
                id_producto = arregloTD[index.producto].dataset.codproducto,
                precio_unitario = arregloTD[index.precio_unitario].children[0].value,
                cantidad = arregloTD[index.cantidad].children[0].value,
                fecha_vencimiento = '0000-00-00',
                lote = arregloTD[index.lote].innerHTML;
  
              if (producto == ""){
                return {rpt: false, msj: "Fila "+n+" sin producto válido."};
              }
  
              if (cantidad <= 0){
                return {rpt: false, msj: "Fila "+n+" sin cantidad válida."};
              }
  
              if (precio_unitario <= 0){
                return {rpt: false, msj: "Fila "+n+" sin precio unitario válida."};
              }
  
              return {rpt: true, 
                datos: {
                   producto,
                   id_producto,
                   fecha_vencimiento,
                   lote,
                   precio_unitario,
                   cantidad
                  }
                };
          };
  
        if (!arregloTR.length){
          return {rpt: false,msj:"No hay productos agregados a la venta"};
        }
  
        for (let i = 0, len = arregloTR.length; i < len; i++) {
          const res = fnVerificarFila(arregloTR[i], (i+1));
          if (!res.rpt){
            return res;
          }
          objDetalles.push(res.datos);
        };
  
        return {rpt: true, datos: objDetalles};
    };  
  
    const limpiarVenta = () => {
      /*formulario, descuentos, detalle*/ 
      const DOM = this.DOM;
  
      DOM.cboClienteBuscar.val("").trigger("chosen:updated");
      DOM.cboTipoDocumento.val("0");
      DOM.blkNumeroDocumento.hide();
      DOM.txtCliente.val(null);
      DOM.txtApellidos.val(null);
      DOM.txtDireccion.val(null);
      DOM.txtCelular.val(null);
      DOM.txtCorreo.val(null);
      DOM.txtObservaciones.val(null);
  
      DOM.txtMontoTarjeta.val("0.00");
      DOM.txtMontoEfectivo.val("0.00");
      DOM.txtMontoCredito.val("0.00");
      DOM.txtMontoYape.val("0.00");
      DOM.txtMontoPlin.val("0.00");
      DOM.txtMontoBanco.val("0.00");
      DOM.txtDescuentoGlobal.val("0.00");
  
      eliminarTodoCarrito(MODO == "*");
    };
  
    this.editar = function(cod_transaccion){
      /*1.- Obtener los datosa asociados a lad venta
      cabecera
      detalle
      imprimir cabecera
      imprimir detalle
        set teb stock
      */
       var self = this, 
           DOM  = self.DOM,
           sucursalAnterior = DOM.cboSucursal.val(),
            fn = function (xhr){
                var datos = xhr.datos,
                    cabecera,
                    detalle;
  
                  if (datos.rpt) {  
                    MODO = "*";
  
                    cabecera = datos.data.cabecera;
                    detalle = datos.data.detalle;
                    $("#lblrotuloedicion").html("EDITANDO VENTA: "+cabecera.x_cod_transaccion);
  
  
                    COD_VENTA_EDITAR = cabecera.cod_transaccion;
  
                    DOM.cboClienteBuscar.val(cabecera.cod_cliente).change().trigger("chosen:updated");
  
                    DOM.cboTipoComprobante.val(cabecera.cod_tipo_comprobante);//.change();
  
                    if (cabecera.cod_tipo_comprobante != ""){
                      DOM.txtSerie.val(cabecera.serie);
                      DOM.txtCorrelativo.val(cabecera.correlativo).change();
                      DOM.blkComprobante.show();
                    } else {
                      DOM.blkComprobante.hide();
                    }
                    
                    DOM.txtFechaVenta.val(cabecera.fecha_transaccion);
  
                    if (cabecera.tipo_tarjeta !=  null){
                      DOM.radTipoTarjeta[0].checked = cabecera.tipo_tarjeta == "C";
                      DOM.blkTipoTarjeta.show();  
                    } else {
                      DOM.blkTipoTarjeta.hide();  
                    }
                    
                    DOM.cboSucursal.attr("disabled", true);
                    DOM.btnActualizar.hide();
  
                    eliminarTodoCarrito(MODO == "+");
  
                    for (var i = 0, len = detalle.length; i < len ;i++) {
                      var objDetalle = detalle[i];
                       agregarFilaDetalle({
                          cod_producto: objDetalle.cod_producto,
                          nombre_producto: objDetalle.nombre_producto,
                          img_url: objDetalle.img_url,
                          precio_unitario: objDetalle.precio_unitario,
                          cantidad: objDetalle.cantidad,
                          codigo_descuento : objDetalle.codigo_descuento,
                          cod_descuento : objDetalle.cod_descuento,
                          monto_descuento: objDetalle.monto_descuento,
                          tipo_descuento: objDetalle.tipo_descuento,
                          rotulo_descuento: objDetalle.rotulo_descuento,
                          subtotal: objDetalle.subtotal,
                          fecha_vencimiento: objDetalle.fecha_vencimiento,
                          lote : objDetalle.lote,
                          marca : objDetalle.marca
                        });
  
                    };
  
                    DOM.lblSubTotal.html(cabecera.importe_total_venta);
  
                    if (cabecera.cod_descuento_global != null){
                        DOM.txtDescuentoGlobal[0].dataset.id =
                                cabecera.cod_descuento_global+"_"+cabecera.monto_descuento+"_"+cabecera.tipo_descuento+"_"+cabecera.rotulo_descuento+"_"+cabecera.codigo_descuento_global;
  
                        DOM.txtDescuentoGlobal.html(cabecera.rotulo_descuento+'<br><small>'+cabecera.codigo_descuento_global+'</small><br><a class="descuento-cancelar" href="javascript:;" style="font-size: 14px;">Cancelar</a>');         
                        DOM.lblDescuento.html(cabecera.total_descuentos);
  
                    } else {
                       DOM.txtDescuentoGlobal[0].dataset.id ="";
  
                       DOM.txtDescuentoGlobal.html('<label><small>Código</small></label><input style="width:85px;text-align: center;" class=""><br>');         
                       DOM.lblDescuento.html("0.00");
                    }
  
                    ARREGLO_DESCUENTOS_TEMPORAL_EDITANDO = datos.data.descuentos_usados;
                    
                    DOM.lblTotal.html(cabecera.importe_total_venta);
                    DOM.txtMontoEfectivo.val(cabecera.monto_efectivo);
                    DOM.txtMontoTarjeta.val(cabecera.monto_tarjeta);
                    DOM.txtMontoCredito.val(cabecera.monto_credito);
  
                   $('.nav-tabs a[href="#tabRegistrarVentas"]').tab('show');
  
                   DOM.btnCancelarEdicion.show();
               
                  }else{
                    console.error(datos.msj);
                  }
            };
  
        new _Ajxur.Api({
          modelo: "Venta",
          metodo: "leerVentaEditar",
          data_in : {
            p_codTransaccion : cod_transaccion
          }
        },fn);
    };
  
    this.cancelarEdicion = function(){
      COD_VENTA_EDITAR = null;
      ARREGLO_DESCUENTOS_TEMPORAL_EDITANDO = [];
      MODO = "+";
      $("#lblrotuloedicion").empty();
      self.DOM.cboSucursal.attr("disabled",false);       
      self.DOM.btnActualizar.show();
      self.DOM.btnCancelarEdicion.hide();
  
      limpiarVenta();
    };

    this.prepararAgregarProductos = () => {
      _data.productos = _data.productos.map( p => {
        return {
          ...p, seleccionado: false
        }
      })

      this.DOM.mdlBuscarProducto.modal("show");
      this.DOM.txtBuscar.val("");
      this.DOM.txtBuscar.focus();
      this.DOM.txtBuscar.select();

      realizarBusquedaProducto("");
    };

    this._seleccionarProductoBuscar = ($tr) =>{
      const classNameSeleccionado = "seleccionado-tr";
      const idSeleccionado = $tr.data("id");
      const stock = $tr.data("stock");
      const estaSeleccionado = $tr.hasClass(classNameSeleccionado);

      if (stock <= 0){
        const nombreProducto = $tr.children()[0].innerText;
        swal("Error", `El producto ${nombreProducto} no tiene STOCK disponible.`);
        return;
      }

      if (estaSeleccionado){
        $tr.removeClass(classNameSeleccionado);
      } else {
        $tr.addClass(classNameSeleccionado);
      }

      _data.productos = _data.productos.map( p => {
        if (p.codigo_unico_producto == idSeleccionado){
          return {
            ...p, seleccionado: !estaSeleccionado
          }
        }
        return p;
      });

      this.DOM.lblSeleccionados.html(_data.productos.filter(p=>p.seleccionado).length);
    };

    this._agregarProductosAlDetalle = () => {
      const productosSeleccionados = _data.productos
                                        .filter(p => p.seleccionado)
                                        .map( itemProducto => {
                                          return {
                                            cod_producto: itemProducto.id,
                                            nombre_producto: itemProducto.nombre_producto,
                                            precio_unitario: itemProducto.precio_unitario,
                                            fecha_vencimiento: itemProducto.fecha_vencimiento,
                                            lote: itemProducto.lote,
                                            marca : itemProducto.marca,
                                            cantidad: 1,
                                            monto_descuento: null,
                                            tipo_descuento: null,
                                            cod_descuento: null,
                                            subtotal: itemProducto.precio_unitario,
                                            maxstock : itemProducto.stock
                                          }
                                        });

      this.DOM.tblDetalle[!_VACIO ? "append" : "html"](_tpl8.tblDetalle(productosSeleccionados));
      _VACIO = false;
      modificarTotalGeneral();

      this.DOM.mdlBuscarProducto.modal("hide");
    };
  
    return this.init();
};