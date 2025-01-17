const RegistrarNotas = function($contenedor, _tpl8){
  const CARACTERES_LECTORA = 16;
    var _Util = Util,
        _ArrayUtils = ArrayUtils,
        _INDEX = {
          "eliminar": 0,
          "producto": 1,
          "precio_unitario": 2,
          "cantidad": 3,
          "subtotal": 4
        },
        _VACIO = true,
        _TR_BUSCAR = null,
        _data = {
          productos: [],
          clientes : [],
          tipo_categorias : [],
          categoria_productos : [],
          series : []
        },
        NOMBRE_LOCALSTORAGE = "___jp",
        MODO = "+",
        COD_EDITAR = null;
  
    this.init = function(){
      this.setDOM();
      this.setEventos();
      this.obtenerData();
      this.obtenerSeries();
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
  
    this.setDOM = function(){
      var DOM = _Util.preDOM2DOM($contenedor, [
                      {"frmRegistro": "#frmregistro"},
                      {"cboTipoComprobanteMod" : "#cbotipocomprobantemodificar"},
                      {"blkComprobanteMod": "#blkcomprobantemod"},
                      {"txtSerieMod" : "#txtseriemodificar"},
                      {"txtCorrelativoMod" : "#txtcorrelativomodificar"},
                      {"cboClienteBuscar": "#cboclientebuscar"},
                      {"cboTipoComprobante": "#cbotipocomprobante"},
                      {"blkComprobante": "#blkcomprobante"},
                      {"txtSerie": "#txtserie"},
                      {"txtCorrelativo": "#txtcorrelativo"},
                      {"cboTipoDocumento": "#cbotipodocumento"},
                      {"blkNumeroDocumento": "#blknumerodocumento"},
                      {"txtNumeroDocumento": "#txtnumerodocumento"},
                      {"txtCliente": "#txtclientedescripcion"},
                      {"txtDireccion": "#txtclientedireccion"},
                      {"txtFechaEmision": "#txtfechaemision"},
                      {"txtHoraEmision": "#txthoraemision"},
                      {"txtFechaVencimiento": "#txtfechavencimiento"},
                      {"txtMoneda": "#txtmoneda"},
                      {"tblDetalle": "#tbldetallebody"},
                      {"btnAgregarProducto": "#btnagregarproducto"},
                      {"txtLectora" : "#txtlectora"},
                      {"lblSubTotal": "#lblsubtotal"},
                      {"txtDescuentoGlobal": "#txtdescuentoglobal"},
                      {"lblTotal": "#lbltotal"},
                      {"txtObservaciones": "#txtobservaciones"},
                      {"cboCondicionPago": "#cbocondicionpago"},
                      {"txtDelivery": "#txtdelivery"},
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

      DOM.txtSerieMod.on("change", (e) => {
        const value = e.currentTarget.value;
        const primeraLetraSerie = value.charAt(0);
        if ( this.DOM.cboTipoComprobanteMod.val() === "03" && primeraLetraSerie != "B"){
          alert("La serie ingresada no es válida para una BOLETA.")
          this.DOM.txtSerieMod.val("");
          this.DOM.txtCorrelativoMod.val("");
          return;
        }

        if ( this.DOM.cboTipoComprobanteMod.val() === "01" && primeraLetraSerie != "F"){
          alert("La serie ingresada no es válida para una FACTURA.")
          this.DOM.txtSerieMod.val("");
          this.DOM.txtCorrelativoMod.val("");
          return;
        }

        e.currentTarget.value = value.substr(0, 4);
      });

      DOM.txtCorrelativoMod.on("change", (e) => {
        this.buscarComprobanteModificar({
          serie: this.txtSerieMod.val(),
          correlativo: this.txtCorrelativoMod.val()
        });
      }); 
  
      DOM.cboTipoComprobante.on("change", function(){
        const keyStorageTipoComprobante = NOMBRE_LOCALSTORAGE+"tipocomprobante";
        localStorage.setItem(keyStorageTipoComprobante, this.value);
        //cargarCorrelativo(this.value);
      });
  
      DOM.txtCorrelativo.on("keypress", function(e){
        if (!_Util.soloNumeros(e)){
          e.preventDefault(); return;
        }
        return;
      });
      
      DOM.txtSerie.on("change", function(e){
          const tipoComprobante = DOM.cboTipoComprobante.val();



          const keyStorageSerie = NOMBRE_LOCALSTORAGE+"serie"+tipoComprobante;
          let serie = this.value;    
  
          if (serie.length > 0 && serie.length < 4){
            serie = `${primeraLetraRecomendada}${serie.padStart(3, '0')}`;
            this.value = serie;
          }
          
          if (serie === "" || serie === null || serie === undefined){
              serie = `${primeraLetraRecomendada}001`;
              this.value = serie;
          }
          
          localStorage.setItem(keyStorageSerie, serie);
          
          const keyStorageComprobante = NOMBRE_LOCALSTORAGE+"correlativo"+tipoComprobante+serie;

          let correlativo = localStorage.getItem(keyStorageComprobante);    
          
          if (correlativo === null || correlativo === undefined || correlativo == ''){
              correlativo = '1';
              DOM.txtCorrelativo.val(_Util.completarNumero(correlativo, 6));
              localStorage.setItem(keyStorageComprobante, correlativo);
          }
      });
  
      DOM.txtCorrelativo.on("change", function(e){

        return;

          const correlativo = this.value;
          if (correlativo.length < 6){
              this.value = _Util.completarNumero(correlativo, 6);
          }
      
          const tipoComprobante = DOM.cboTipoComprobante.val();
          const primeraLetraRecomendada = MAP_COMPROBANTE_VENTA[tipoComprobante];
          let serie = DOM.txtSerie.val();
          const keyStorageSerie = NOMBRE_LOCALSTORAGE+"serie"+tipoComprobante;
          
          if (serie === null || serie === undefined || serie == ''){
              serie = `${primeraLetraRecomendada}001`;
              DOM.txtSerie.val(serie);
              localStorage.setItem(keyStorageSerie, serie);
          }
          
          const keyStorageComprobante = NOMBRE_LOCALSTORAGE+"correlativo"+tipoComprobante+serie;
          localStorage.setItem(keyStorageComprobante, correlativo);
      });
  
      DOM.cboClienteBuscar.on("change", function(){
        obtenerCliente(this.value, DOM);
      });
  
      DOM.cboTipoDocumento.on("change", function(){
        cambiarTipoDocumento(this.value, DOM.blkNumeroDocumento, DOM.txtNumeroDocumento);
      });
  
      var soloNumerosDecimales = function(e){
        if (!_Util.soloNumerosDecimales(e)){
          e.preventDefault(); return;
        }
        return;
      };
  
      /*
      DOM.txtMontoEfectivo.on("keypress", soloNumerosDecimales);
      DOM.txtMontoTarjeta.on("keypress", soloNumerosDecimales);
      DOM.txtMontoCredito.on("keypress", soloNumerosDecimales);
      */
      DOM.txtDescuentoGlobal.on("keypress", soloNumerosDecimales);
      /*
      DOM.txtMontoEfectivo.on("change", function(){
        if (this.value == ""){
          this.value = "0.00";
        }
        equilibrarMontoPago("E");
      });
      */
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
        grabar();  
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
  
      DOM.blkListaProductos.on("click", "tr:not(.tr-null)", (e) => {
        this._seleccionarProductoBuscar($(e.currentTarget));
      });

      DOM.txtDescuentoGlobal.on("focusin", function(e){
        this.select();
      });
  
      DOM.txtDescuentoGlobal.on("change", function(e){
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

        this.value = descuento.toFixed(2);
        DOM.lblTotal.html(parseFloat(subTotal - descuento).toFixed(2));
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
          if (codigoBarra.length >= CARACTERES_LECTORA){
            agregarProductoUsandoLectora(codigoBarra);
          }
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
      cargarCorrelativo(DOM.cboTipoComprobante.val());
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
        $precio.focus();
        $precio.select();

        modificarTotalGeneral();

        _VACIO = false;
        return;
      }

      swal("Error", `Producto no encontrado.`);
      this.DOM.txtLectora.val("");
      this.DOM.txtLectora.focus();
    };
  
    this.getProducto = function(id){
      return _ArrayUtils.conseguirPID(_data.productos, "id", id);
    };
  
    this.setDataProductos = function(_dataProductos){
      _data.productos = _dataProductos.map(p => {
        return {
          ...p, seleccionado : false
        };
      });
    }

    const obtenerDataProductos = async (deboEliminarCarrito = true, fnPostStocked = undefined ) => {
        try {
          const { data } = await apiAxios.get(`productos`);
          this.setDataProductos(data);
        } catch (error) {
          swal("Error", "Error al obtener los productos.", "error");
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
      obtenerTipoCategorias();
      obtenerDataProductos();
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
  
    const obtenerTipoCategorias = async () => {
      try {
          const { data } = await apiAxios.get('tipo-categorias');
          this.DOM.cboTipo.html(_tpl8.Combo(data));
  
      } catch (error) {
          swal("Error",  "Error al obtener los tipo de categorías.", "error");
          console.error(error);
      }
    }
  
    const eliminarTodoCarrito = () => {
      /*Clean up, sub total 0, descuento vacío, total 0, descuentos 0, eliminar dscuent globaal*/
      /*Reseetar stock si y solo si se eliminó todo el carrito sin haber regitrado nada */
      const DOM = this.DOM;
  
      _VACIO = true;
      DOM.lblSubTotal.html("0.00");
      DOM.lblTotal.html("0.00"); 
      DOM.txtDescuentoGlobal.val("0.00");
  
      //cancelarDescuento("global");
      DOM.tblDetalle.html(_tpl8.tblDetalle([]));
    };
  
    const cargarCorrelativo =  (tipoComprobante) => {
      return ;
      const primeraLetraRecomendada = MAP_COMPROBANTE_VENTA[tipoComprobante];
      const keyStorageSerie = NOMBRE_LOCALSTORAGE+"serie"+tipoComprobante;
      let serie = localStorage.getItem(keyStorageSerie);

                
      if (serie === null || serie === undefined){
        serie = `${primeraLetraRecomendada}001`;
        localStorage.setItem(keyStorageSerie, serie);
    }

      if (serie.length > 0 && serie.length < 4){
        serie = `${primeraLetraRecomendada}${serie.padStart(3, '0')}`;
        localStorage.setItem(keyStorageSerie, serie);
      }
      
      const keyStorageComprobante = NOMBRE_LOCALSTORAGE+"correlativo"+tipoComprobante+serie;
      
      let correlativo = localStorage.getItem(keyStorageComprobante);    
                          
      if (correlativo === null || correlativo === undefined){
          correlativo = '1';
          localStorage.setItem(keyStorageComprobante, correlativo);
      }
      
      this.DOM.txtSerie.val(serie);
      this.DOM.txtCorrelativo.val(_Util.completarNumero(correlativo,6));
    };
  
    const obtenerCliente = (idCliente, DOM) => {
      const objCliente = _ArrayUtils.conseguir(_data.clientes, "id", idCliente);
      
      if (objCliente === -1){
        DOM.cboTipoDocumento.val("0");
        DOM.txtNumeroDocumento.val(null);
        DOM.blkNumeroDocumento.hide();
        DOM.txtCliente.val(null);
        DOM.txtDireccion.val(null);
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

      DOM.txtCliente.val(`${objCliente.nombres} ${objCliente.apellidos}`.trim());
      DOM.txtDireccion.val(objCliente.direccion);

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

      txtNumeroDocumento[0].maxLength = maxLength;
      txtNumeroDocumento[0].value = txtNumeroDocumento[0].value.substr(0, maxLength);
    };

    const buscarProducto  = ($tr) => {
      _TR_BUSCAR = $tr;
      this.DOM.mdlBuscarProducto.modal("show");
    };
  
    const seleccionarProductoBuscar = (itemProducto) => {
      const objProducto = itemProducto.o;
  
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
            propiedad: "producto",
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
  
      const numFila = arregloTR.length;
      if (numFila <= 0){
        _VACIO = true;
        tblDetalle.html(_tpl8.tblDetalle());
      }
  
      modificarTotalGeneral(arregloTR);
    };

    const modificarCantidadDetalle = ($tr, $cantidad_) => {
      /*obtener producto, actualizarse la cantidad, mdoifcar subtotal, modificar gran sub total*/
      const index = _INDEX,
          $cantidad = ($cantidad_ == null ? arregloTD[index.cantidad].children[0] : $cantidad_),
          cantidadNueva = $cantidad.value,
          arregloTD = [].slice.call($tr.children);
  
      const $subtotal = arregloTD[index.subtotal],
            $precio_unitario = arregloTD[index.precio_unitario].children[0],
            valorPrecio = parseFloat($precio_unitario.value).toFixed(2);
  
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

        //equilibrarMontoPago("E", total);

    };
  
    const grabar = () => {
      const objButtonLoading = new ButtonLoading({$: this.DOM.btnGuardar[0]});
      const objComprobante = verificar();
      const fnConfirm = async (isConfirm) => {
            if(!isConfirm){
              return;
            }

            objButtonLoading.start();

            try {
              const cabecera = objComprobante.datos.cabecera,
                    detalle  = objComprobante.datos.detalle;

              const sentData = {
                id_tipo_comprobante : cabecera.id_tipo_comprobante,
                serie: cabecera.serie,
                correlativo: cabecera.correlativo,
                id_cliente: Boolean(cabecera.id_cliente) ? cabecera.id_cliente : null,
                cliente_id_tipo_documento : cabecera.id_tipo_documento_cliente,
                cliente_numero_documento : cabecera.numero_documento_cliente,
                cliente_descripcion : cabecera.descripcion_cliente,
                cliente_direccion : cabecera.direccion_cliente,
                descuento_global : cabecera.descuento_global,
                importe_total : cabecera.importe_total,
                fecha_emision : cabecera.fecha_emision,
                hora_emision: cabecera.hora_emision,
                fecha_vencimiento : cabecera.fecha_vencimiento,
                id_tipo_moneda : cabecera.id_tipo_moneda,
                observaciones: cabecera.observaciones,
                condicion_pago: cabecera.condicion_pago,
                es_delivery: cabecera.es_delivery,
                productos: detalle
              };
              
              const {data} = MODO === '+' 
                              ? await apiAxios.post('comprobantes/registrar-factura', sentData)
                              : await apiAxios.put(`comprobantes/registrar-factura/${COD_EDITAR}`, sentData);

              //swal("Éxito", "Registrado con éxito.", "success");

              if (cabecera.id_tipo_comprobante != ""){
                const serie = this.DOM.txtSerie.val(),
                      tipoComprobante = this.DOM.cboTipoComprobante.val();
                      
                const keyStorageComprobante = NOMBRE_LOCALSTORAGE+"correlativo"+tipoComprobante+serie;
                const correlativoNuevo = parseInt(localStorage.getItem(keyStorageComprobante)) + 1;
                localStorage.setItem(keyStorageComprobante, correlativoNuevo );
                this.DOM.txtCorrelativo.val(_Util.completarNumero(correlativoNuevo, 6));
              }
              //app.ListarVentas.verDetalle(data.id);
              COD_EDITAR = null;
              //this.DOM.btnCancelarEdicion.hide();
              $("#lblrotuloedicion").empty();
              limpiarComprobante();
              //$('.nav-tabs a[href="#tabListadoVentas"]').tab('show');
              //ListarVentas.listarVentas(data.lista_ventas);
              MODO = "+";
              console.log({data});
              //app.ListarNotas.verComprobante(data.id_documento_electronico);
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
  
      if (!objComprobante.rpt){
        swal("Error", objComprobante.msj, "error");
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
  
    const verificar = () => {
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
          descripcion_cliente = DOM.txtCliente.val(),
          direccion_cliente = DOM.txtDireccion.val(),
          fecha_emision = DOM.txtFechaEmision.val(),
          hora_emision = DOM.txtHoraEmision.val(),
          fecha_vencimiento = DOM.txtFechaVencimiento.val(),
          id_tipo_moneda = DOM.txtMoneda.val(),
          //tipoPago = DOM.radTipoPago[0].checked ? 'E' : 'T', /*0: EFECTIVO, 1: TARJETA*/     
          descuento_global = DOM.txtDescuentoGlobal.val(),
          importe_total = DOM.lblTotal.html(),
          observaciones = DOM.txtObservaciones.val(),
          condicion_pago = DOM.cboCondicionPago.val(),
          es_delivery = DOM.txtDelivery.val(),
          numeroDocumentoLength = numero_documento_cliente.length;
  
      if (descripcion_cliente.length < 0){
        return {rpt: false, msj: "Ingrese nombre de cliente."};
      }
  
      if (id_tipo_documento_cliente == '1' && (numeroDocumentoLength != 8 && numeroDocumentoLength != 0)) {
        return {rpt: false, msj: "Ingrese un número de DNI válido."};
      }
  
      if (id_tipo_documento_cliente == '6' && (numeroDocumentoLength != 11 && numeroDocumentoLength != 0)) {
        return {rpt: false, msj: "Ingrese un número de RUC válido."};
      }
  
      if (fecha_emision == ""){
        return {rpt: false, msj: "Ingrese fecha de emisión."};
      }

      if (hora_emision == ""){
        return {rpt: false, msj: "Ingrese hora de emisión."};
      }

      if (fecha_vencimiento == ""){
        return {rpt: false, msj: "Ingrese fecha de vencimiento."};
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
          descripcion_cliente,
          direccion_cliente,
          fecha_emision,
          hora_emision,
          fecha_vencimiento,
          id_tipo_moneda,
          descuento_global,
          importe_total,
          observaciones,
          condicion_pago,
          es_delivery
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
                cantidad = arregloTD[index.cantidad].children[0].value;
  
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
  
    const limpiarComprobante = () => {
      /*formulario, descuentos, detalle*/ 
      const DOM = this.DOM;
  
      DOM.cboClienteBuscar.val("").trigger("chosen:updated");
      DOM.cboTipoDocumento.val("0");
      DOM.blkNumeroDocumento.hide();
      DOM.txtCliente.val(null);
      DOM.txtDireccion.val(null);
      DOM.txtObservaciones.val(null);
  
      DOM.txtDescuentoGlobal.val("0.00");
  
      eliminarTodoCarrito(MODO == "*");
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
      const estaSeleccionado = $tr.hasClass(classNameSeleccionado);

      if (estaSeleccionado){
        $tr.removeClass(classNameSeleccionado);
      } else {
        $tr.addClass(classNameSeleccionado);
      }

      _data.productos = _data.productos.map( p => {
        if (p.id == idSeleccionado){
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
                                            producto: itemProducto.producto,
                                            precio_unitario: itemProducto.precio_unitario,
                                            marca : itemProducto.marca,
                                            cantidad: 1,
                                            monto_descuento: null,
                                            tipo_descuento: null,
                                            cod_descuento: null,
                                            subtotal: itemProducto.precio_unitario
                                          }
                                        });

      this.DOM.tblDetalle[!_VACIO ? "append" : "html"](_tpl8.tblDetalle(productosSeleccionados));
      _VACIO = false;
      modificarTotalGeneral();

      this.DOM.mdlBuscarProducto.modal("hide");
    };

    this.obtenerSeries = async () => {
      try {
          const { data } = await apiAxios.get(`serie-documentos`);
          _data.series = data;

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

    this.buscarComprobanteModificar = async ({ serie, correlativo}) => {
      try{

        const {data} = await apiAxios.get(`comprobantes-sc/${serie}-${correlativo}`);
        if (Boolean(data)){

          return;
        }

        

      } catch (error){
        swal("Error", "Error al obtener comprobante a modificar.", "error");
        console.error(error);
      } finally {

      }
    }
  
    return this.init();
};