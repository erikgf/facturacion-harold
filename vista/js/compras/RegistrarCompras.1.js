const RegistrarCompras = function($contenedor, _tpl8){
    const CARACTERES_LECTORA = 16;
    var _Util = Util,
        _ArrayUtils = ArrayUtils,
        _INDEX = {
          "eliminar": 0,
          "producto": 1,
          "marca" : 2,
          "lote": 3,
          "precio_unitario": 4,
          "cantidad": 5,
          "subtotal": 6
        },
        _VACIO = true,
        _TR_BUSCAR = null,
        _Ajxur = Ajxur,
        MODO = "+",
        _data = {
          productos: [],
          proveedores : [],
        },
        COD_COMPRA_EDITAR,
        self = this;
  
    this.init = function(){
      this.setDOM();
      this.setEventos();
      this.obtenerData();
    };
  
    this.getData = function(){
      return _data;
    };
  
    this.setDOM = function(){
      var DOM = _Util.preDOM2DOM($contenedor, 
                      [{"frmRegistro": "#frmregistro"},
                        {"cboProveedorBuscar": "#cboproveedorbuscar"},
                        {"cboTipoComprobante": "#cbotipocomprobante"},
                        {"blkComprobante": "#blkcomprobante"},
                        {"txtNumeroComprobante": "#txtnumerocomprobante"},
                        {"cboTipoDocumento": "#cbotipodocumento"},
                        {"blkNumeroDocumento": "#blknumerodocumento"},
                        {"txtNumeroDocumento": "#txtnumerodocumento"},
                        {"txtProveedor": "#txtproveedor"},
                        {"txtDireccion": "#txtdireccion"},
                        {"txtCelular": "#txtcelular"},
                        {"txtCorreo": "#txtcorreo"},
                        {"cboSucursal": "#cbosucursal"},
                        {"txtGuiasRemision" : "#txtguiasremision"},
                        {"txtObservaciones" : "#txtobservaciones"},
                        {"tblDetalle": "#tbldetallebody"},
                        {"btnAgregarProducto": "#btnagregarproducto"},
                        {"txtLectora" : "#txtlectora"},
                        {"lblTotal": "#lbltotal"},
                        {"blkTipoTarjeta": "#blktipotarjetas"},
                        {"txtFechaCompra": "#txtfechacompra"},
                        {"txtHoraCompra": "#txthoracompra"},
                        {"btnCancelarEdicion": "#btncancelaredicion"},
                        {"btnGuardar": "#btnguardar"},
                        {"mdlBuscarProducto": "#mdlBuscarProducto"},
                        {"txtBuscar":"#txtbuscar"},
                        {"cboTipo":"#cbofiltrotipo"},
                        {"cboCategoria":"#cbofiltrocategoria"},
                        {"blkListaProductos" : "#blklistaproductos"},
                        {"btnAgregarProductos": "#btnagregarproductos"}
                    ]);  
    
        DOM.radTipoPago = $("input[name=radtipopago]");
        DOM.radTipoTarjeta = $("input[name=radtipotarjeta]");
  
        this.DOM = DOM;
    };
  
    this.setEventos = function(){
      var self = this,
          DOM = self.DOM;
  
      DOM.cboTipoComprobante.on("change", function(){
        cambiarComprobante(this.value, DOM.blkComprobante);
      });
  
      DOM.cboProveedorBuscar.on("change", function(){
        if (this.value == ""){
          limpiarProveedor(DOM);
          return;
        }
        obtenerProveedor(this.value, DOM);
      });
  
      DOM.cboTipoDocumento.on("change", function(){
        cambiarTipoDocumento(this.value, DOM.blkNumeroDocumento, DOM.txtNumeroDocumento);
      });
  
      DOM.radTipoPago.on("change", function(){
        cambiarTarjeta(this.value, DOM.blkTipoTarjeta);
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
        obtenerCategorias(this.value);
        //cargarCategorias(this.value);
      });
  
      DOM.cboCategoria.on("change", function(e){
        realizarBusquedaProducto(DOM.txtBuscar.val());
      });
  
      DOM.tblDetalle.on("click", "tr button.eliminar", function(e){
        eliminarFilaDetalle(this.parentElement.parentElement);
      });
  
      DOM.frmRegistro.on("submit", function(e){
        e.preventDefault();
        grabarCompra();
      });
  
      DOM.tblDetalle.on("change", "tr .cantidad input", function(){
        var valor = this.value,
            $tr = this.parentElement.parentElement;
  
          if ($tr.children[_INDEX.producto].dataset.producto == ""){
            this.value = 1;
            return;
          }
  
          if (valor == "" || valor.length <= 0){
            this.value = this.dataset.preval;
            return;
          }

          modificarCantidadDetalle($tr, this, null);
      });
  
      DOM.tblDetalle.on("change", "tr .precio input", function(){
        var valor = this.value,
            $tr = this.parentElement.parentElement;
  
          if ($tr.children[_INDEX.producto].dataset.producto == ""){
            this.value = "0.00";
            return;
          }
  
          if (valor == "" || valor.length <= 0){
            this.value = "0.00";
            return;
          }

          this.value = parseFloat(this.value).toFixed(2);
  
          modificarCantidadDetalle($tr, null, this);
      });
  
      DOM.tblDetalle.on("keypress", "tr .cantidad input", function(e){
        if (!_Util.soloNumeros(e)){
          e.preventDefault(); return;
        }
        return;
      });
  
      DOM.tblDetalle.on("keypress", "tr .precio input", function(e){
        if (!_Util.soloNumerosDecimales(e)){
          e.preventDefault(); return;
        }
        return;
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
        e.preventDefault();
        /*
        var itemProducto = self.getProducto(this.dataset.id);
  
        if (itemProducto.i != -1 && _TR_BUSCAR != null){
          seleccionarProductoBuscar(itemProducto);
        }
        */
        this._seleccionarProductoBuscar($(e.currentTarget));
      });
  
      DOM.cboSucursal.on("change", function(){
        self.obtenerDataProductos();
      });
  
      DOM.btnCancelarEdicion.on("click", function(){
        self.cancelarEdicion();
      });
      
      DOM.txtLectora.on("change", (e)=>{
        const codigoBarra = e.currentTarget.value;
        if (codigoBarra.length >= CARACTERES_LECTORA){
          agregarProductoUsandoLectora(codigoBarra);
        }
      });

      DOM.btnAgregarProductos.on("click", (e)=> {
        e.preventDefault();
        this._agregarProductosAlDetalle();
      });
    };
  
    /*
    const cargarCategorias = function(codTipo){
      var DOM = self.DOM;
      if (codTipo == ""){
        DOM.cboCategoria.html(_tpl8.Combo([]));
        return;
      }
      DOM.cboCategoria.html(_tpl8.Combo(ArrayUtils.conseguirTodos(_data.categoria_productos,"id_tipo_categoria", codTipo)));  
    };
    */
  
    this.getProducto = function(id_producto){
      return _ArrayUtils.conseguirPID(_data.productos, "id", id_producto);
    };
  
    this.setDataProductos = function(_dataProductos){
      _data.productos = _dataProductos.map(p => {
        return {
          ...p, seleccionado : false
        };
      });
    }
  
    this.getProveedor = function(id_proveedor){
      return _ArrayUtils.conseguirPID(_data.proveedores, "id", id_proveedor);
    };
  
    this.setDataProveedores = function(_proveedores){
      _data.proveedores = _proveedores;
    }
  
    this.obtenerDataProductos = async function(){
      try {
        const idSucursal = this.DOM.cboSucursal.val();
        const { data } = await apiAxios.get(`compras-productos/${idSucursal}`);
        this.setDataProductos(data);
        eliminarTodoCarrito();
  
      } catch (error) {
        swal("Error", "Error al obtener los productos para Compra.", "error");
        console.error(error);
      }
    };
  
    this.obtenerData = function(){
      obtenerProveedores();
      obtenerSucursales();
      obtenerTipoCategorias();
      //obtenerCategorias();
    };
  
    const obtenerProveedores = async () => {
      try {
          const { data } = await apiAxios.get('proveedores');
          this.DOM.cboProveedorBuscar.html(_tpl8.cboProveedoresBuscar(data)).chosen();
          this.setDataProveedores(data);
  
      } catch (error) {
          swal("Error", "Error al obtener los proveedores.", "error");
          console.error(error);
      }
    };
  
    const obtenerSucursales = async () => {
      try {
          const { data } = await apiAxios.get('sucursales');
          const sucursalHTML = _tpl8.Sucursal(data);
  
          app.ListarCompras.DOM.cboSucursal.html(sucursalHTML);
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

    const obtenerCategorias = async ( idTipo ) => {
      const DOM = this.DOM;
      try {
        
        if (idTipo == ""){
          DOM.cboCategoria.html(_tpl8.Combo([]));
          return;
        }
        
        const { data } = await apiAxios.get(`categorias/tipo/${idTipo}`);
        DOM.cboCategoria.html(_tpl8.Combo(data));  
  
      } catch (error) {
          swal("Error",  "Error al obtener las categorías de productos.", "error");
          console.error(error);
      }
    };
  
    const eliminarTodoCarrito = function(resetearStock){
      /*Clean up, sub total 0, descuento vacío, total 0, descuentos 0, eliminar dscuent globaal*/
      /*Reseetar stock si y solo si se eliminó todo el carrito sin haber regitrado nada */
      var DOM = self.DOM, 
          arregloTR,
          index = _INDEX;
  
      _VACIO = true;
      DOM.lblTotal.html("0.00"); 
  
      if (resetearStock == true){   
        arregloTR = DOM.tblDetalle.find("tr:not(.tr-null)").toArray();
        $.each(arregloTR, function(i,o){
          var arregloTD = [].slice.call(o.children),
            $producto = arregloTD[index.producto].dataset.producto,
            $cantidad = arregloTD[index.cantidad].children[0];
            if ($producto != ""){
              modificarStockProducto(self.getProducto($producto), $cantidad.value, "-");
            }
  
        });
      } 
  
      DOM.tblDetalle.html(_tpl8.tblDetalle([]));
    };
  
    var cambiarComprobante = function(tipoComprobante, bloqueComprobante){
      bloqueComprobante[tipoComprobante == "" ? "hide" : "show"]();
    };
  
    var limpiarProveedor = function(DOM){
        DOM.cboTipoDocumento.val("0");
        DOM.txtNumeroDocumento.val(null);
        DOM.blkNumeroDocumento.hide();
        DOM.txtProveedor.val(null);
        DOM.txtDireccion.val(null);
        DOM.txtCorreo.val(null);
        DOM.txtCelular.val(null);
    };
  
    const obtenerProveedor = (codProveedor, DOM) => {
      const { o: objProveedor} = this.getProveedor(codProveedor);
  
      DOM.cboTipoDocumento.val(objProveedor.id_tipo_documento);
  
      if (objProveedor.cod_tipo_documento != "0"){
        DOM.txtNumeroDocumento.val(objProveedor.numero_documento);
        DOM.blkNumeroDocumento.show();
      } else {
        DOM.txtNumeroDocumento.val(null);
        DOM.blkNumeroDocumento.hide();
      }
      DOM.txtProveedor.val(objProveedor?.nombre_contacto);
      DOM.txtDireccion.val(objProveedor?.direccion);
      DOM.txtCelular.val(objProveedor?.celular_contacto);
      DOM.txtCorreo.val(objProveedor?.correo);
    };
  
    const cambiarTipoDocumento = function(tipoDocumento, bloqueNumeroDocumento, txtNumeroDocumento){
      switch(tipoDocumento){
        case "":
          bloqueNumeroDocumento.hide();
        break;
        case "1":
          bloqueNumeroDocumento.show();
          txtNumeroDocumento[0].maxLength = 8;
        break;
        case "6":
          bloqueNumeroDocumento.show();
          txtNumeroDocumento[0].maxLength = 11;
        break;
      }
  
      txtNumeroDocumento[0].value = "";
    };

    const agregarProductoUsandoLectora = (codigoBarra) => {
      const itemProducto =  _data.productos.find(item => {
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
          id_producto: itemProducto.id,
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
        const $precio = $nuevoDetalle.find(".precio input");
        this.DOM.txtLectora.val("");
        $precio.focus();
        $precio.select();
        _VACIO = false;
        return;
      }

      swal("Error", `Producto no encontrado.`);
      this.DOM.txtLectora.val("");
      this.DOM.txtLectora.focus();
    };
  
    const cambiarTarjeta = function(tipoPago, bloqueTarjetas){
        bloqueTarjetas[(tipoPago == "T") ? "show" : "hide"]();
    };
  
    const buscarProducto  = ($tr) => {
      _TR_BUSCAR = $tr;
      this.DOM.mdlBuscarProducto.modal("show");
    };
  
    var seleccionarProductoBuscar = function(itemProducto){
      var index = _INDEX,
          arregloTD = [].slice.call(_TR_BUSCAR.children),
          objProducto = itemProducto.o,
          $producto = arregloTD[index.producto],
          $precio = arregloTD[index.precio_unitario].children[0],
          $marca = arregloTD[index.marca],
          $cantidad = arregloTD[index.cantidad].children[0], /*cantidad*/
          //$descuento = arregloTD[index.descuento],
          $subtotal = arregloTD[index.subtotal],
          valorPrecio = "0.00",
          cantidadDefault;
  
      self.DOM.mdlBuscarProducto.modal("hide");
  
      /*Si hubo un producto seleccionad con anteiorirdad regresar sus datos a como estaban antes de cagarla.*/
      if ($producto.dataset.producto != ""){
        modificarStockProducto(self.getProducto($producto.dataset.producto), $cantidad.value, "-");  
      }
  
      $producto.dataset.producto = objProducto.id;
      $producto.innerHTML = '<span>'+objProducto.nombre_producto+'</span>';
      $marca.innerHTML = objProducto.marca;
      $precio.value = valorPrecio;
  
      cantidadDefault = 1;
      $cantidad.value = cantidadDefault;
  
      //$cantidad.dataset.minstock = objProducto.stock;
      $precio.focus();
      $precio.select();
      //$fecha_vencimiento.setSelectionRange(0, $fecha_vencimiento.value.length);
  
      modificarStockProducto(itemProducto, cantidadDefault, "+");
      modificarSubTotalDetalle( valorPrecio, $subtotal);
  
      _TR_BUSCAR = null;
    };
  
    var agregarFilaDetalle = function(dataFila){
      var DOM = self.DOM;
  
      if (!dataFila){
        dataFila = {
          id_producto: null,
          marca : "",
          lote: "",
          precio_unitario: "0.00",
          cantidad: 1,
          subtotal: "0.00"
        };
      }
  
      const $nuevoDetalle = $(_tpl8.tblDetalle(dataFila));
      DOM.tblDetalle[!_VACIO ? "append" : "html"]($nuevoDetalle);
      $nuevoDetalle.find(".pointer").click();
      _VACIO = false;
      return dataFila;
    };
  
    const realizarBusquedaProducto = function(cadena){
      var DOM = self.DOM;
      /*
      if (cadena == ""){
        //DOM.blkListaProductos.html('<div class="alert alert-info"><strong>Realice la búsqueda del producto a vender.</strong></div>');
        return;
      }
      */
      if (cadena == "" || cadena.length >= 3){
        var parametrosBusqueda = [{
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
      }
    };
  
    var eliminarFilaDetalle = function($tr){
      var tblDetalle = self.DOM.tblDetalle,
          index = _INDEX,
          arregloTD = [].slice.call($tr.children),
          cantidad = arregloTD[index.cantidad].children[0].value,
          itemProducto = self.getProducto(arregloTD[index.producto].dataset.producto),
          arregloTR;
  
      $tr.remove();
  
      arregloTR= tblDetalle.find("tr:not(.tr-null)").toArray();
  
      if (itemProducto.i >= 0){
        modificarStockProducto(itemProducto,cantidad, "-");  
      }
  
      var numFila = arregloTR.length;
      if (numFila <= 0){
        _VACIO = true;
        tblDetalle.html(_tpl8.tblDetalle());
      }
  
      modificarTotalGeneral(arregloTR);
    };
  
    var modificarStockProducto = function(itemProducto, cantidad, tipo){
      /*tipo == + / -*/
        var objProducto = itemProducto.o,
            stock = objProducto.stock,
            nuevoStock  = parseInt(stock) + parseInt(cantidad  * (tipo == "+" ? 1 : -1)),
            exceso = 0;
  
        /*
  
        if (nuevoStock < 0){
          exceso = nuevoStock * -1;
          nuevoStock = 0;  
        }
  
        */
  
        _data.productos[itemProducto.i].stock = nuevoStock;
        return {viejo: stock, exceso: exceso, nuevo: nuevoStock};
    };
  
    var modificarCantidadDetalle = function($tr, $cantidad_, $precio_){
      /*obtener producto, actualizarse la cantidad, mdoifcar subtotal, modificar gran sub total*/
      var index = _INDEX,
          arregloTD = [].slice.call($tr.children),
          $cantidad = ($cantidad_ == null ? arregloTD[index.cantidad].children[0] : $cantidad_),
          cantidadAnterior = $cantidad.dataset.preval, 
          cantidadNueva = $cantidad.value,
          itemProducto = self.getProducto(arregloTD[index.producto].dataset.producto),
          objProducto = itemProducto.o,
          $subtotal = arregloTD[index.subtotal],
          $precio_unitario = ($precio_ == null ? arregloTD[index.precio_unitario].children[0] : $precio_),
          valorPrecio = parseFloat($precio_unitario.value).toFixed(2),
          cantidadVender = cantidadNueva - cantidadAnterior,
          stockModificado = modificarStockProducto(itemProducto, cantidadVender, "+"),
          subtotal;
  
          /*
          if (stockModificado.nuevo < 0){
            cantidadNueva = cantidadNueva - stockModificado.exceso;
            $cantidad.value = cantidadNueva;
          }
          */
  
          subtotal = valorPrecio * cantidadNueva;
          $cantidad.dataset.preval = cantidadNueva;
          modificarSubTotalDetalle( subtotal, $subtotal);
    };
  
    var modificarSubTotalDetalle = function(subtotal, $subtotal){
        $subtotal.innerHTML =  parseFloat(subtotal).toFixed(2);
        modificarTotalGeneral();
    };  
  
    var modificarTotalGeneral = function(arregloTR){
      /*recorret todos los TR, obtener ultimos valores (subtotal), sumarlos
          obtener DescuentoTotal
          obtener Total
          */
        var DOM = self.DOM,
            total = 0.00;
  
        if (!arregloTR){
            arregloTR = DOM.tblDetalle.find("tr:not(.tr-null)").toArray();
        }
  
        for (var i = arregloTR.length - 1; i >= 0; i--) {
          total += parseFloat(arregloTR[i].children[_INDEX.subtotal].innerHTML);
        };
  
        //DOM.lblDescuento.html();
        DOM.lblTotal.html(parseFloat(total).toFixed(2));
    };
  
    const grabarCompra = ()=>{
      const objCompra = verificarCompra(),
          fnConfirm = async (isConfirm) => {
            if (isConfirm){
                try {
                  const cabecera = objCompra.datos.cabecera,
                        detalle  = objCompra.datos.detalle;
  
                  if (_SAVING == true){
                    return;
                  }
  
                  _SAVING = true;
  
                  const sentData = {
                    id_tipo_comprobante : cabecera.idTipoComprobante,
                    numero_comprobante: cabecera.numeroComprobante,
                    id_proveedor: cabecera.idProveedor,
                    tipo_pago: cabecera.tipoPago,
                    tipo_tarjeta: cabecera.tipoTarjeta,
                    fecha_compra : cabecera.fechaCompra,
                    hora_compra: cabecera.horaCompra,
                    id_sucursal : cabecera.idSucursal,
                    importe_total: cabecera.importeTotal,
                    observaciones: cabecera.observaciones,
                    guias_remision: cabecera.guiasRemision,
                    productos: detalle
                  };
              
                  const {data} = MODO === '+' 
                                  ? await apiAxios.post('compras', sentData)
                                  : await apiAxios.put(`compras/${COD_COMPRA_EDITAR}`, sentData);
  
                  swal("Éxito", "Registrado con éxito.", "success");
  
                  app.ListarCompras.verDetalle(data.id);
      
                  $("#lblrotuloedicion").empty();
                  COD_COMPRA_EDITAR = null;
  
                  this.DOM.btnCancelarEdicion.hide();
                  this.DOM.cboSucursal.attr("disabled", false);
                  limpiarCompra();
                  MODO = "+";
      
                  //$('.nav-tabs a[href="#tabListadoCompras"]').tab('show');
                  //ListarCompras.listarCompras(data.lista_compras);
                  //app.ListarCompras.obtenerCompras();
                } catch (error) {
                  swal("Error", JSON.stringify(error), "error");
                  console.error(error);
                } finally {
                  _SAVING = false;
                }
            }   
          };
  
      if (!objCompra.rpt){
        swal("Error", objCompra.msj, "error");
        return objCompra;
      }
  
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
    };
  
    const verificarCompra = function(){
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
  
    const verificarCabecera = function(){
      const DOM = self.DOM,
            idTipoComprobante = DOM.cboTipoComprobante.val(),
            numeroComprobante = DOM.txtNumeroComprobante.val(),
            idProveedor = DOM.cboProveedorBuscar.val(),
            idTipoDocumento = DOM.cboTipoDocumento.val(),
            fechaCompra = DOM.txtFechaCompra.val(),
            horaCompra = DOM.txtHoraCompra.val(),
            tipoPago = DOM.radTipoPago[0].checked ? 'E' : 'T', /*0: EFECTIVO, 1: TARJETA*/      
            tipoTarjeta = DOM.radTipoTarjeta[0].checked ? 'C' : 'D',
            idSucursal = DOM.cboSucursal.val(),
            importeTotal = DOM.lblTotal.html(),
            guiasRemision = DOM.txtGuiasRemision.val(),
            observaciones = DOM.txtObservaciones.val();
  
      if (idProveedor == ""){
        return {rpt: false, msj: "Seleccione un proveedor."};
      }
  
      if (fechaCompra == ""){
        return {rpt: false, msj: "Ingrese fecha de compra."};
      }
  
      if (horaCompra == ""){
        return {rpt: false, msj: "Ingrese hora de compra."};
      }
  
      if (idTipoComprobante.length <= 0){
        if (numeroComprobante <= 0 || parseInt(numeroComprobante) <= 0){
          return {rpt: false, msj: "Número de comprobante no válido"};
        }
      }
  
      return {
        rpt: true,
        datos: {
          idTipoComprobante: idTipoComprobante,
          numeroComprobante: numeroComprobante,
          idProveedor: idProveedor,
          idTipoDocumento: idTipoDocumento,
          fechaCompra: fechaCompra,
          horaCompra: horaCompra,
          tipoPago: tipoPago,
          tipoTarjeta: tipoTarjeta,
          idSucursal: idSucursal,
          importeTotal: importeTotal,
          guiasRemision: guiasRemision,
          observaciones: observaciones
        }
      };
    };
  
    const verificarDetalle = function(){
      /*Verificar cada detalle
          producto (cod_producto)
          cantidad > 0,
          descuento (si hay)
          subtotal
          precio ( > 0)
        Detalles < 0 => Error
      */
      var DOM = self.DOM,
          index = _INDEX,
          arregloTR = DOM.tblDetalle.find("tr:not(.tr-null)").toArray(), 
          objDetalles,
          fnVerificarFila = function($tr, n){
            var arregloTD = [].slice.call($tr.children),
                id_producto = arregloTD[index.producto].dataset.producto,
                precio_compra = arregloTD[index.precio_unitario].children[0].value,
                lote = arregloTD[index.lote].children[0].value,
                cantidad = arregloTD[index.cantidad].children[0].value;
  
              if (id_producto == ""){
                return {rpt: false, msj: "Fila "+n+" sin producto válido."};
              }
  
              if (precio_compra <= 0){
                return {rpt: false, msj: "Fila "+n+" sin precio válido (0.00)."};
              }
  
              if (cantidad <= 0){
                return {rpt: false, msj: "Fila "+n+" sin cantidad válida (0)."};
              }
  
              return {rpt: true, 
                  datos: {
                    id_producto: id_producto,
                    precio_unitario: precio_compra,
                    fecha_vencimiento: '0000-00-00',
                    cantidad : cantidad,
                    lote : lote
                  }
                };
          };
  
        if (!arregloTR.length){
          return {rpt: false, msj:"No hay productos agregados a la compra"};
        }
  
        objDetalles = [];
  
        for (var i = 0, len = arregloTR.length; i < len; i++) {
          var res = fnVerificarFila(arregloTR[i], (i+1));
          if (!res.rpt){
            return res;
          }
          objDetalles.push(res.datos);
        };
  
        return {rpt: true, datos: objDetalles};
    };  
  
    const limpiarCompra = function(){
      /*formulario, descuentos, detalle*/ 
      var DOM = self.DOM;
          //nuevoCorrelativo;
  
      DOM.cboTipoDocumento.val("");
      DOM.cboTipoComprobante.val("").change();
      DOM.txtNumeroComprobante.val(null);
      DOM.cboProveedorBuscar.val("").trigger("chosen:updated");
      DOM.cboTipoDocumento.val("0");
      DOM.blkNumeroDocumento.hide();
      DOM.txtProveedor.val(null);
      DOM.txtDireccion.val(null);
      DOM.txtCelular.val(null);
      DOM.txtCorreo.val(null);
      DOM.txtObservaciones.val(null),
      DOM.txtGuiasRemision.val(null);
  
      DOM.radTipoPago[0].checked = true;
      DOM.radTipoTarjeta[0].checked = true;
      DOM.blkTipoTarjeta.hide();
  
      eliminarTodoCarrito();
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
                    $("#lblrotuloedicion").html("EDITANDO COMPRA: "+cabecera.x_cod_transaccion);
  
                    COD_COMPRA_EDITAR = cabecera.cod_transaccion;
  
                    DOM.cboProveedorBuscar.val(cabecera.cod_proveedor).change().trigger("chosen:updated");
  
                    DOM.cboTipoComprobante.val(cabecera.cod_tipo_comprobante);//.change();
  
                    console.log(cabecera);
  
                    if (cabecera.cod_tipo_comprobante != ""){
                      DOM.txtNumeroComprobante.val(cabecera.comprobante);
                      DOM.blkComprobante.show();
                    } else {
                      DOM.blkComprobante.hide();
                    }
                    
                    DOM.txtFechaCompra.val(cabecera.fecha_transaccion);
  
                    if (cabecera.tipo_tarjeta !=  null){
                      DOM.radTipoTarjeta[0].checked = cabecera.tipo_tarjeta == "C";
                      DOM.blkTipoTarjeta.show();  
                    } else {
                      DOM.blkTipoTarjeta.hide();  
                    }
                    
                    DOM.cboSucursal.attr("disabled", true);
  
                    eliminarTodoCarrito(MODO == "+");
  
                    for (var i = 0, len = detalle.length; i < len ;i++) {
                      var objDetalle = detalle[i];
                       agregarFilaDetalle({
                          id_producto: objDetalle.id_producto,
                          nombre_producto: objDetalle.nombre_producto,
                          img_url: objDetalle.img_url,
                          precio_unitario: objDetalle.precio_unitario,
                          cantidad : objDetalle.cantidad,
                          subtotal: objDetalle.subtotal
                        });
                    };
  
                    DOM.lblTotal.html(cabecera.importe_total_venta);
  
                   $('.nav-tabs a[href="#tabRegistrarCompras"]').tab('show');
                   DOM.btnCancelarEdicion.show();
               
                  }else{                  
                    console.error(datos.msj);
                  }
            };
  
        new _Ajxur.Api({
          modelo: "Compra",
          metodo: "leerCompraEditar",
          data_in : {
            p_codTransaccion : cod_transaccion
          }
        },fn);
    };
  
    this.cancelarEdicion = function(){
      COD_COMPRA_EDITAR = null;
      MODO = "+";
      $("#lblrotuloedicion").empty();
      self.DOM.cboSucursal.attr("disabled",false);       
      self.DOM.btnCancelarEdicion.hide();
  
      limpiarCompra();
    };
  
    this.limpiarCompra = limpiarCompra;

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

      console.log({$tr});

      _data.productos = _data.productos.map( p => {
        if (p.id === idSeleccionado){
          return {
            ...p, seleccionado: !estaSeleccionado
          }
        }
        return p;
      });

      console.log({d: _data.productos.filter(p=>p.seleccionado).length});

      $("#lblSeleccionados").html(_data.productos.filter(p=>p.seleccionado).length);
    };

    this._agregarProductosAlDetalle = () => {
      const productosSeleccionados = _data.productos
                                        .filter(p => p.seleccionado)
                                        .map( itemProducto => {
                                          return {
                                            id_producto: itemProducto.id,
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
  