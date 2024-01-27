var app = {},
  _TEMPID = -1,
  _ROTULO = "",
  _ACCION = "agregar",
  _CLASE = "Cotizacion",
  DT = null,
  _POSTREGISTRADO = false,
  _MSJ = "",
  _SAVING = false,
  ULTIMO_CORRELATIVO = 1;

var RegistrarCotizaciones = function($contenedor, _tpl8){
  var _Util = Util,
      _ArrayUtils = ArrayUtils,
      _INDEX = {
        "eliminar": 0,
        "producto": 1,
        "marca" : 2,
        fecha_vencimiento: 3,
        lote: 4,
        "precio_unitario": 5,
        "cantidad": 6,
        "subtotal": 7
      },
      _VACIO = true,
      _TR_BUSCAR = null,
      _Ajxur = Ajxur,
      _data = {
        productos: [],
        clientes : [],
        tipo_categorias : [],
        categoria_productos : []
      },
      _descuentos_realizados = [],
      NOMBRE_LOCALSTORAGE = "___jp",  
      NS_DIASCREDITO = "__dc",
      NS_DIASVALIDEZ = "__dv",
      NS_DIASENTREGA = "__de",
      NS_COSTODELIVERY = "__cd",
      NS_COTIZACION = "__cotizacion",
      MODO = "+",
      ARREGLO_DESCUENTOS_TEMPORAL_EDITANDO = [],
      COD_TRANSACCION_EDITAR = null;
      self = this;

  this.init = function(){
    this.setDOM();
    this.setEventos();
    this.setDefaultData();
    this.obtenerData();
  };

  this.getData = function(){
    return _data;
  };

  this.setProductos = function(_productos){
    _data.productos = _productos;
  };

  this.getDescuentosRealizados = function(){
    return _descuentos_realizados;
  }

  this.setDOM = function(){
    var DOM = _Util.preDOM2DOM($contenedor, 
                    [{"txtSerie": "#txtserie"},
                      {"txtCorrelativo": "#txtcorrelativo"},
                      {"cboClienteBuscar": "#cboclientebuscar"},
                      {"cboTipoDocumento": "#cbotipodocumento"},
                      {"blkNumeroDocumento": "#blknumerodocumento"},
                      {"txtNumeroDocumento": "#txtnumerodocumento"},
                      {"txtRazonSocial": "#txtrazonsocial"},
                      {"txtCliente": "#txtcliente"},
                      {"txtApellidos": "#txtapellidos"},
                      {"txtDireccion": "#txtdireccion"},
                      {"txtCelular": "#txtcelular"},
                      {"txtCorreo": "#txtcorreo"},
                      {"txtCondicionDiasCredito": "#txtcondiciondiascredito"},
                      {"txtCondicionDiasValidez": "#txtcondiciondiasvalidez"},
                      {"txtCondicionDiasEntrega": "#txtcondiciondiasentrega"},
                      {"txtCondicionDelivery": "#txtcondiciondelivery"},
                      {"txtObservaciones" : "#txtobservaciones"},
                      {"txtFechaCotizacion": "#txtfechacotizacion"},
                      {"btnAgregarProducto": "#btnagregarproducto"},
                      {"tblDetalle": "#tbldetallebody"},
                      {"lblTotal": "#lbltotal"},
                      {"btnCancelarEdicion": "#btncancelaredicion"},
                      {"btnGuardar": "#btnguardar"},
                      {"mdlBuscarProducto": "#mdlBuscarProducto"},
                      {"txtBuscar":"#txtbuscar"},
                      {"cboTipo":"#cbofiltrotipo"},
                      {"cboCategoria":"#cbofiltrocategoria"},
                      {"blkListaProductos" : "#blklistaproductos"}
                      ]);  

      this.DOM = DOM;
  };

  this.setEventos = function(){
    var self = this,
        DOM = self.DOM;

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

    DOM.btnAgregarProducto.on("click", function(){
      agregarFilaDetalle();
    });

    DOM.tblDetalle.on("click", "tr .pointer", function(e){
      buscarProducto(this.parentElement);
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

    DOM.btnGuardar.on("click", function(e){
      e.preventDefault();
      if (_SAVING == false){
        grabarCotizacion();  
      }
      
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

      var maxstock = this.dataset.maxstock ? this.dataset.maxstock : "";

        if (maxstock != "" && parseInt(valor) > this.dataset.maxstock){
          this.value = this.dataset.preval;
          return;
        }

        if (parseInt(valor) <= 0){
          this.value = 1;
        }

        modificarCantidadDetalle($tr, this);
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

    DOM.tblDetalle.on("click", "tr .descuento a.descuento-cancelar", function(e){
      cancelarDescuento("detalle",[].slice.call(this.parentElement.parentElement.children));
    });

    DOM.mdlBuscarProducto.on("shown.bs.modal", function(e){
      var txtBuscar = DOM.txtBuscar,
          txtBuscarVal = txtBuscar.val();

      txtBuscar.focus();
      txtBuscar[0].setSelectionRange(0, txtBuscarVal.length);
      realizarBusquedaProducto(txtBuscarVal);
    });

    DOM.blkListaProductos.on("click", "tr", function(e){
      var itemProducto = self.getProducto(this.dataset.id);
      if (itemProducto.i != -1 && _TR_BUSCAR != null){
        seleccionarProductoBuscar(itemProducto);
      }
    });

    DOM.btnCancelarEdicion.on("click", function(){
      self.cancelarEdicion();
    });

  };

  var cargarCategorias = function(codTipo){
    var DOM = self.DOM;
    if (codTipo == ""){
      DOM.cboCategoria.html(_tpl8.Combo([]));
      return;
    }
    DOM.cboCategoria.html(_tpl8.Combo(ArrayUtils.conseguirTodos(_data.categoria_productos,"cod_tipo_categoria", codTipo)));  
  };

  var reafirmarStock = function(){
    var DOM = self.DOM,
        arrDataProductos = _data.productos,
        arregloTR = DOM.tblDetalle.find("tr:not(.tr-null)").toArray();

      swal("Exito", "Stock actualizado...", "success");
        for (var i = 0; i < arregloTR.length; i++) {
          var $tds = [].slice.call(arregloTR[i].children),
              cod_producto = $tds[_INDEX.producto].dataset.producto,
              cantidad = $tds[_INDEX.cantidad].children[0].value;

            if (cod_producto != undefined && cod_producto != ""){
              var item = self.getProducto(cod_producto);
              _data.productos[item.i].stock = item.o.stock - cantidad;
            }
        };
  };    

  var equilibrarMontoPago = function(txtAccion, montoTotal){
    /*Obtener monto 1 y monto 2,*/
    var DOM = self.DOM,
        $efectivo = DOM.txtMontoEfectivo,
        efectivo = $efectivo.val(),
        $tarjeta = DOM.txtMontoTarjeta,
        tarjeta = $tarjeta.val(),
        total = (montoTotal == undefined ? parseFloat(DOM.lblTotal.html())  : montoTotal);

        if (txtAccion == "T"){
          if (tarjeta == "0.00" || tarjeta > total){
            efectivo = "0.00";
            tarjeta = total;
          } else {
            efectivo = total - tarjeta;
          }
        } else {
            efectivo = total;
          if (efectivo == "0.00" || efectivo > total){
            tarjeta = "0.00";
          } else {
            tarjeta = total - efectivo;
          }
        }
        cambiarTarjeta( (tarjeta > 0 ? "T" : "E"), DOM.blkTipoTarjeta);

        $tarjeta.val(parseFloat(tarjeta).toFixed(2));
        $efectivo.val(parseFloat(efectivo).toFixed(2));
  };


  this.getProducto = function(cod_producto){
    return _ArrayUtils.conseguirPID(_data.productos, "cod_producto", cod_producto);
  };

  this.setDataProductos = function(_dataProductos){
    _data.productos = _dataProductos;
  }

  this.obtenerDataProductos = function(_eliminarCarrito, _fn){
    var eliminarCarrito = _eliminarCarrito == undefined ? true : _eliminarCarrito,
        self = this, 
         DOM  = self.DOM,
          fn = function (xhr){
              var datos = xhr.datos,
                arregloTR;
                if (datos.rpt) {
                  self.setDataProductos(datos.data);
                  if (eliminarCarrito)
                    eliminarTodoCarrito();
                  if (_fn != undefined){
                    _fn()
                  }
                  //arregloTR= DOM.tblDetalle.find("tr:not(.tr-null)").toArray();
                  /*Cambiar sucursal implica cambiar productos y limpiar el carrito de venta*/
                }else{
                  console.error(datos.msj);
                }
          };

      new _Ajxur.Api({
        modelo: "Cotizacion",
        metodo: "obtenerDataProductos",
      },fn);
  };

  this.obtenerData = function(){
     var self = this, 
         DOM  = self.DOM,
          fn = function (xhr){
              var datos = xhr.datos;

                if (datos.rpt) {
                  $.each(datos.data,function(key,o){
                    _data[key] = o;
                  });

                  llenarClientesBuscar(DOM.cboClienteBuscar);
                  DOM.cboTipo.html(_tpl8.Combo(_data.tipo_categorias));

                  var correlativoPrevio = _data.correlativo_previo,
                      seriePrevio = localStorage.getItem(NOMBRE_LOCALSTORAGE+NS_COTIZACION+"serie");

                  if (seriePrevio == undefined || seriePrevio == null || seriePrevio == "null"){                    
                    seriePrevio = "001";
                    localStorage.setItem(NOMBRE_LOCALSTORAGE+NS_COTIZACION+"serie", seriePrevio);
                  }

                  DOM.txtCorrelativo.val(_Util.completarNumero(correlativoPrevio,6));
                  DOM.txtSerie.val(seriePrevio);

                  ULTIMO_CORRELATIVO = correlativoPrevio;

                }else{
                  console.error(datos.msj);
                }
          };

      new _Ajxur.Api({
        modelo: "Cotizacion",
        metodo: "obtenerData"
      },fn);
  };

  this.setDefaultData = function(){
    let dias_credito = localStorage.getItem(NOMBRE_LOCALSTORAGE+NS_DIASCREDITO);
    let dias_validez = localStorage.getItem(NOMBRE_LOCALSTORAGE+NS_DIASVALIDEZ);
    let dias_entrega = localStorage.getItem(NOMBRE_LOCALSTORAGE+NS_DIASENTREGA);
    let costo_delivery = localStorage.getItem(NOMBRE_LOCALSTORAGE+NS_COSTODELIVERY);

    this.DOM.txtCondicionDiasCredito.val(dias_credito);
    this.DOM.txtCondicionDiasValidez.val(dias_validez);
    this.DOM.txtCondicionDiasEntrega.val(dias_entrega);
    this.DOM.txtCondicionDelivery.val(costo_delivery);

  };

  var eliminarTodoCarrito = function(resetearStock){
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
      });
    }  
    DOM.tblDetalle.html(_tpl8.tblDetalle([]));
    _descuentos_realizados = [];
  };

  var llenarClientesBuscar = function($cboClienteBuscar){
    $cboClienteBuscar.html(_tpl8.cboClientesBuscar(_data.clientes)).val("0").chosen();
  };

  var cambiarComprobante = function(tipoComprobante, bloqueComprobante){
    var noComprobante = tipoComprobante == "";
    if (!noComprobante){
      cargarCorrelativo();
    }
    bloqueComprobante[noComprobante ? "hide" : "show"]();
  };


  var cargarCorrelativo = function () {
    var fn = function(xhr){
      var datos = xhr.datos;
      if (datos.rpt){
        //localStorage.setItem(NOMBRE_LOCALSTORAGE+"correlativo", parseInt(datos.nuevo_correlativo));
        self.DOM.txtCorrelativo.val(_Util.completarNumero(datos.nuevo_correlativo,6));
      }
    };

    new _Ajxur.Api({
      modelo: "Cotizacion",
      metodo: "obtenerNumeroComprobante"
    }, fn);
  };

  var obtenerCliente = function(codCliente, DOM){
    var objCliente = _ArrayUtils.conseguir(_data.clientes, "cod_cliente", codCliente);

    DOM.cboTipoDocumento.val(objCliente.cod_tipo_documento);

    if (objCliente.cod_cliente == "0"){
      DOM.txtNumeroDocumento.val(null);
      DOM.blkNumeroDocumento.hide();
      DOM.txtCliente.val(null);
      DOM.txtApellidos.val(null);
      DOM.txtDireccion.val(null);
      DOM.txtCorreo.val(null);
      DOM.txtCelular.val(null);
      DOM.txtRazonSocial.val(null);
      return;
    }

    if (objCliente.cod_tipo_documento != "0"){
      DOM.txtNumeroDocumento.val(objCliente.numero_documento);
      DOM.blkNumeroDocumento.show();
    } else {
      DOM.txtNumeroDocumento.val(null);
      DOM.blkNumeroDocumento.hide();
    }

    if (objCliente.cod_tipo_documento == "6"){
      DOM.txtRazonSocial.parents(".form-group").show();
      DOM.txtRazonSocial.prop("required", true);
        
      DOM.txtCliente.parents(".form-group").hide();
      DOM.txtCliente.prop("required", false);
      DOM.txtApellidos.parents(".form-group").hide();
      DOM.txtApellidos.prop("required", false);
    } else {
      DOM.txtRazonSocial.parents(".form-group").hide();
      DOM.txtRazonSocial.prop("required", false);
        
      DOM.txtCliente.parents(".form-group").show();
      DOM.txtCliente.prop("required", true);
      DOM.txtApellidos.parents(".form-group").show();
      DOM.txtApellidos.prop("required", true);
    }

    DOM.txtCliente.val(objCliente.nombres);
    DOM.txtRazonSocial.val(objCliente.nombres);
    DOM.txtApellidos.val(objCliente.apellidos);
    DOM.txtDireccion.val(objCliente.direccion);
    DOM.txtCelular.val(objCliente.celular);
    DOM.txtCorreo.val(objCliente.correo);
  };

  var cambiarTipoDocumento = function(tipoDocumento, bloqueNumeroDocumento, txtNumeroDocumento){
    let DOM = self.DOM;

    switch(tipoDocumento){
      case "0":
        bloqueNumeroDocumento.hide();
      break;
      case "1":
        bloqueNumeroDocumento.show();
        txtNumeroDocumento[0].maxLength = 8;
      break;
      case "4":
      case "7":
        bloqueNumeroDocumento.show();
        txtNumeroDocumento[0].maxLength = 12;


        DOM.txtCliente.parents(".form-group").show();
        DOM.txtCliente.prop("required", true);
        DOM.txtApellidos.parents(".form-group").show();
        DOM.txtApellidos.prop("required", true);
        DOM.txtRazonSocial.parents(".form-group").hide();
        DOM.txtRazonSocial.prop("required", false);

      break;
      case "6":
        bloqueNumeroDocumento.show();
        txtNumeroDocumento[0].maxLength = 11;


        DOM.txtCliente.parents(".form-group").hide();
        DOM.txtCliente.prop("required", false);
        DOM.txtApellidos.parents(".form-group").hide();
        DOM.txtApellidos.prop("required", false);
        DOM.txtRazonSocial.parents(".form-group").show();
        DOM.txtRazonSocial.prop("required", true);
      break;
    }

    txtNumeroDocumento[0].value = "";
  };

  var cambiarTarjeta = function(tipoPago, bloqueTarjetas){
      bloqueTarjetas[(tipoPago == "T") ? "show" : "hide"]();
  };

  var buscarProducto  = function($tr){
    var DOM = self.DOM;
    _TR_BUSCAR = $tr;
    DOM.mdlBuscarProducto.modal("show");
  };

  var seleccionarProductoBuscar = function(itemProducto){
    var index = _INDEX,
        arregloTD = [].slice.call(_TR_BUSCAR.children),
        objProducto = itemProducto.o,
        $producto = arregloTD[index.producto],
        $marca = arregloTD[index.marca],
        $fecha_vencimiento = arregloTD[index.fecha_vencimiento],
        $lote = arregloTD[index.lote],
        $precio = arregloTD[index.precio_unitario],
        $cantidad = arregloTD[index.cantidad].children[0], /*cantidad*/
        $subtotal = arregloTD[index.subtotal],
        valorPrecio = parseFloat(objProducto.precio_unitario).toFixed(2),
        cantidadDefault,
        subtotal;

    self.DOM.mdlBuscarProducto.modal("hide");

    $producto.dataset.producto = objProducto.cod_producto;
    $producto.innerHTML = '<span>'+objProducto.nombre_producto+'</span>';

    $fecha_vencimiento.innerHTML = objProducto.fecha_vencimiento;
    $producto.dataset.fechavencimiento = objProducto.fecha_vencimiento;

    $lote.innerHTML = objProducto.lote;
    $producto.dataset.lote = objProducto.lote;

    $precio.innerHTML = valorPrecio;

    $marca.innerHTML = objProducto.marca;

    cantidadDefault = 1;
    $cantidad.value = cantidadDefault;

    $cantidad.focus();
    $cantidad.setSelectionRange(0, $cantidad.value.length);

    subtotal = valorPrecio * cantidadDefault;

    modificarSubTotalDetalle( subtotal, $subtotal);
    _TR_BUSCAR = null;
  };

  var agregarFilaDetalle = function(dataFila){
    var DOM = self.DOM;

    if (!dataFila){
      dataFila = {
        cod_producto: null,
        marca: "",
        precio_unitario: "0.00",
        cantidad: 1,
        subtotal: "0.00",
        fecha_vencimiento : "",
        lote: ""
      };
    }

    DOM.tblDetalle[!_VACIO ? "append" : "html"](_tpl8.tblDetalle(dataFila));
    _VACIO = false;
    return dataFila;
  };


  var realizarBusquedaProducto = function(cadena){
    var DOM = self.DOM;
   
    if (cadena == "" || cadena.length >= 3){
      var parametrosBusqueda = [{
          propiedad: "nombre_producto",
          valor: cadena,
          mayusculas : true,
          exactitud : false
        },
        { propiedad : "cod_tipo",
          valor : DOM.cboTipo.val(),
          mayusculas: false,
          exactitud : true
        },
        { propiedad : "cod_categoria",
          valor : DOM.cboCategoria.val(),
          mayusculas: false,
          exactitud : true
        }
      ];
     
     DOM.blkListaProductos.html(_tpl8.ListaProducto(_ArrayUtils.buscarTodos(_data.productos, parametrosBusqueda)));
    }
  };

  var eliminarFilaDetalle = function($tr){
    var tblDetalle = self.DOM.tblDetalle,
        index = _INDEX,
        arregloTD = [].slice.call($tr.children),
        $descuento = arregloTD[index.descuento],
        cantidad = arregloTD[index.cantidad].children[0].value,
        itemProducto = self.getProducto(arregloTD[index.producto].dataset.producto),
        arregloTR;

    $tr.remove();

    arregloTR= tblDetalle.find("tr:not(.tr-null)").toArray();

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

      if (nuevoStock < 0){
        exceso = nuevoStock * -1;
        nuevoStock = 0;  
      }

      _data.productos[itemProducto.i].stock = nuevoStock;
      return {viejo: stock, exceso: exceso, nuevo: nuevoStock};
  };

  var modificarCantidadDetalle = function($tr, $cantidad){
    /*obtener producto, actualizarse la cantidad, mdoifcar subtotal, modificar gran sub total*/
    var index = _INDEX,
        cantidadAnterior = $cantidad.dataset.preval, 
        cantidadNueva = $cantidad.value,
        arregloTD = [].slice.call($tr.children),
        itemProducto = self.getProducto(arregloTD[index.producto].dataset.producto),
        objProducto = itemProducto.o,
        $subtotal = arregloTD[index.subtotal],
        valorPrecio = parseFloat(objProducto.precio_unitario).toFixed(2),
        subtotal;

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
          subtotal = 0.00,
          total = 0.00;


      if (!arregloTR){
          arregloTR = DOM.tblDetalle.find("tr:not(.tr-null)").toArray();
      }

      for (var i = arregloTR.length - 1; i >= 0; i--) {
        subtotal += parseFloat(arregloTR[i].children[_INDEX.subtotal].innerHTML);
      };

      total = parseFloat(subtotal).toFixed(2);
      DOM.lblTotal.html(total);

  };

  var recalcularSubtotalDescuento = function(subtotal, descuentoDataset){
    var nuevoSubtotal = 0.00,
        descuento = 0.00,
        tipo, monto,
        tmpAr;

    if (descuentoDataset == ""){
      return {subtotal: subtotal};
    }

    tmpAr = descuentoDataset.split("_");
    monto = tmpAr[1]; tipo = tmpAr[2]; 

    if (tipo == 'P'){
      descuento = parseFloat(subtotal * (monto / 100)).toFixed(2);
      nuevoSubtotal = subtotal - descuento;
    } else {    
      descuento = monto;
      nuevoSubtotal = subtotal - monto;
      if (nuevoSubtotal < 0){
        descuento = subtotal;
        nuevoSubtotal = "0.00";
      }
    }    
    return {subtotal: nuevoSubtotal, descuento: descuento};
  };

  var grabarCotizacion = function(){
    var objCotizacion = verificarCotizacion(),
        ListarCotizaciones = app.ListarCotizaciones,
        fnConfirm = function(isConfirm){
          if (_SAVING == true){
            return;
          } 

           if (isConfirm){            
              var cabecera = objCotizacion.datos.cabecera,
                  detalle  = objCotizacion.datos.detalle,
                  ListarCotizacionesDOM = ListarCotizaciones.DOM,
                  accion = MODO == "+" ? "grabar" : "editar";

              _SAVING = true;

                 new _Ajxur.Api({
                  modelo: "Cotizacion",
                  metodo: accion,
                  data_in: {
                    p_codTransaccion : COD_TRANSACCION_EDITAR,
                    p_serie : cabecera.serie,
                    p_correlativo : cabecera.correlativo,
                    p_codCliente: cabecera.codCliente,
                    p_codTipoDocumento: cabecera.codTipoDocumento,
                    p_numeroDocumento : cabecera.numeroDocumento,
                    p_numeroCliente: cabecera.numeroCliente,
                    p_razonSocialNombre: cabecera.nombreCliente,
                    p_apellidos: cabecera.apellidosCliente,
                    p_direccionCliente:cabecera.direccionCliente,
                    p_correoEnvio : cabecera.correoEnvio,
                    p_celularCliente : cabecera.celularCliente,
                    p_fechaCotizacion: cabecera.fechaTransaccion,
                    p_importeTotal: cabecera.importeTotalCotizacion,
                    p_detalleCotizacion : JSON.stringify(detalle),
                    p_observaciones : cabecera.observaciones,
                    p_diasCredito : cabecera.diasCredito,
                    p_diasEntrega : cabecera.diasEntrega,
                    p_diasValidez : cabecera.diasValidez,
                    p_costoDelivery : cabecera.costoDelivery
                  },
                  data_out:[ListarCotizacionesDOM.txtFechaInicio.val(), ListarCotizacionesDOM.txtFechaFin.val()]
                }, fn);
              }   
        },
        fn = function(xhr){
          var datos = xhr.datos,
              _cabecera = objCotizacion.datos.cabecera,
              data,
              DOM = self.DOM;

          if (!datos){
            _SAVING = false;
            return;
          }

          if (datos.rpt){
            data = datos.data;

            if (data.clientes.length > 0){
              /*Si hay clientes actualizar.*/
              _data.clientes = data.clientes;
              llenarClientesBuscar(DOM.cboClienteBuscar);
            }


            localStorage.setItem(NOMBRE_LOCALSTORAGE+NS_COTIZACION+"serie", _cabecera.serie);

            localStorage.setItem(NOMBRE_LOCALSTORAGE+NS_DIASCREDITO, _cabecera.diasCredito);
            localStorage.setItem(NOMBRE_LOCALSTORAGE+NS_DIASVALIDEZ, _cabecera.diasValidez);
            localStorage.setItem(NOMBRE_LOCALSTORAGE+NS_DIASENTREGA, _cabecera.diasEntrega);
            localStorage.setItem(NOMBRE_LOCALSTORAGE+NS_COSTODELIVERY, _cabecera.costoDelivery);

            COD_TRANSACCION_EDITAR = null;
            ARREGLO_DESCUENTOS_TEMPORAL_EDITANDO = [];
            DOM.btnCancelarEdicion.hide();
            $("#lblrotuloedicion").html();
            limpiarCotizacion();

            DOM.txtCorrelativo.val(data.nuevo_correlativo);

            if(MODO == "*"){
              app.ListarCotizaciones.verDetalle(data.cod_transaccion);
            }

            swal("Exito", datos.msj, "success");
            $('.nav-tabs a[href="#tabListadoCotizaciones"]').tab('show');
            ListarCotizaciones.listarCotizaciones(data.lista_cotizaciones);
            MODO = "+";

            app.ListarCotizaciones.verComprobante(data.cod_transaccion);
          } else {
            swal("Error", datos.msj, "error");
          }

          _SAVING = false;
        },
        fnError = function(e){
          console.error(e);
          _SAVING = false;
        };


    if (!objCotizacion.rpt){
      swal("Error", objCotizacion.msj, "error");
      return;
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
        }, fnConfirm, fnError);
  };

  var verificarCotizacion = function(){
    var objVerificarCabecera = verificarCabecera(),
        objVerificarDetalle;

    if (!objVerificarCabecera.rpt){
      return objVerificarCabecera;
    }

    objVerificarDetalle = verificarDetalle();
    if (!objVerificarDetalle.rpt){
      return objVerificarDetalle;
    }

    return {rpt: true, datos: {cabecera: objVerificarCabecera.datos, detalle: objVerificarDetalle.datos}};
  };

 
  var verificarCabecera = function(){
    var DOM = self.DOM,
        serie = DOM.txtSerie.val(),
        correlativo = DOM.txtCorrelativo.val(),
        codCliente = DOM.cboClienteBuscar.val(),
        codTipoDocumento = DOM.cboTipoDocumento.val(),
        numeroDocumento = DOM.txtNumeroDocumento.val(),
        nombreCliente = DOM.txtCliente.val(),
        apellidosCliente = DOM.txtApellidos.val(),
        direccionCliente = DOM.txtDireccion.val(),
        correo = DOM.txtCorreo.val(),
        celular = DOM.txtCelular.val(),
        diasCredito = DOM.txtCondicionDiasCredito.val(),
        diasValidez = DOM.txtCondicionDiasValidez.val(),
        diasEntrega = DOM.txtCondicionDiasEntrega.val(),
        costoDelivery = DOM.txtCondicionDelivery.val(),
        fechaTransaccion = DOM.txtFechaCotizacion.val(),
        importeTotalCotizacion = DOM.lblTotal.html(),
        observaciones = DOM.txtObservaciones.val(),
        numeroDocumentoLength;

    if (nombreCliente.length < 0){
      return {rpt: false, msj: "Ingrese nombre de cliente."};
    }

    numeroDocumentoLength = numeroDocumento.length;

    if (codTipoDocumento == '1' && (numeroDocumentoLength != 8 && numeroDocumentoLength != 0)) {
      return {rpt: false, msj: "Ingrese un número de DNI válido."};
    }

    if (codTipoDocumento == '6' && (numeroDocumentoLength != 11 && numeroDocumentoLength != 0)) {
      return {rpt: false, msj: "Ingrese un número de RUC válido."};
    }

    if (fechaTransaccion == ""){
      return {rpt: false, msj: "Ingrese fecha de cotización."};
    }

    return {
      rpt: true,
      datos: {
        serie: serie,
        correlativo: correlativo,
        codTipoDocumento: codTipoDocumento,
        codCliente: codCliente,
        numeroDocumento: numeroDocumento,
        nombreCliente: nombreCliente,
        apellidosCliente: apellidosCliente,
        direccionCliente: direccionCliente,
        correoEnvio: correo,
        celularCliente : celular,
        fechaTransaccion: fechaTransaccion,
        importeTotalCotizacion: importeTotalCotizacion,
        observaciones: observaciones,
        diasCredito : diasCredito,
        diasEntrega: diasEntrega,
        diasValidez: diasValidez,
        costoDelivery: costoDelivery
      }
    };
  };

  var verificarDetalle = function(){
    /*Verificar cada detalle
        producto (cod_producto)
        cantidad > 0,
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
              codProducto = arregloTD[index.producto].dataset.producto,
              fechaVencimiento = arregloTD[index.producto].dataset.fechavencimiento,
              lote = arregloTD[index.producto].dataset.lote,
              cantidad = arregloTD[index.cantidad].children[0].value;

            if (codProducto == ""){
              return {rpt: false, msj: "Fila "+n+" sin producto válido."};
            }

            if (cantidad <= 0){
              return {rpt: false, msj: "Fila "+n+" sin cantidad válida."};
            }

            return {rpt: true, 
              datos: {
                 codProducto: codProducto,
                 cantidad : cantidad,
                 fechaVencimiento: fechaVencimiento,
                 lote: lote                
                }
              };
        };

      if (!arregloTR.length){
        return {rpt: false,msj:"No hay productos agregados a la cotización"};
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

  var limpiarCotizacion = function(){
    /*formulario, descuentos, detalle*/ 
    var DOM = self.DOM;
    DOM.txtCorrelativo.val(_Util.completarNumero(ULTIMO_CORRELATIVO,6));
    DOM.txtSerie.val( localStorage.getItem(NOMBRE_LOCALSTORAGE+NS_COTIZACION+"serie"));

    DOM.cboClienteBuscar.val("0").trigger("chosen:updated");
    DOM.cboTipoDocumento.val("0");
    DOM.blkNumeroDocumento.hide();
    DOM.txtCliente.val(null);
    DOM.txtApellidos.val(null);
    DOM.txtDireccion.val(null);
    DOM.txtCelular.val(null);
    DOM.txtCorreo.val(null);
    DOM.txtObservaciones.val(null);

    eliminarTodoCarrito(MODO == "*");
  };

  this.editar = function(cod_transaccion){
    /*1.- 
    cabecera
    detalle
    imprimir cabecera
    imprimir detalle
      set teb stock
    */
     var self = this, 
         DOM  = self.DOM,
          fn = function (xhr){
              var datos = xhr.datos,
                  cabecera,
                  detalle;

                if (datos.rpt) {  
                  MODO = "*";

                  cabecera = datos.data.cabecera;
                  detalle = datos.data.detalle;
                  $("#lblrotuloedicion").html("EDITANDO COTIZACIÓN: "+cabecera.x_cod_transaccion);

                  COD_TRANSACCION_EDITAR = cabecera.cod_transaccion;

                  DOM.cboClienteBuscar.val(cabecera.cod_cliente).change().trigger("chosen:updated");

                  DOM.txtSerie.val(cabecera.serie);
                  DOM.txtCorrelativo.val(cabecera.correlativo).change();
                  
                  DOM.txtFechaCotizacion.val(cabecera.fecha_transaccion);

                  eliminarTodoCarrito(MODO == "+");

                  for (var i = 0, len = detalle.length; i < len ;i++) {
                    var objDetalle = detalle[i];
                     agregarFilaDetalle({
                        cod_producto: objDetalle.cod_producto,
                        nombre_producto: objDetalle.nombre_producto,
                        precio_unitario: objDetalle.precio_unitario,
                        cantidad: objDetalle.cantidad,
                        subtotal: objDetalle.subtotal,
                        marca: objDetalle.marca,
                        fecha_vencimiento: objDetalle.fecha_vencimiento,
                        lote:  objDetalle.lote
                      });
                  };

                  DOM.lblTotal.html(cabecera.importe_total);

                 $('.nav-tabs a[href="#tabRegistrarCotizaciones"]').tab('show');

                 DOM.txtCondicionDiasEntrega.val(cabecera.condicion_dias_entrega);
                 DOM.txtCondicionDiasValidez.val(cabecera.condicion_dias_validez);
                 DOM.txtCondicionDiasCredito.val(cabecera.condicion_dias_credito);
                 DOM.txtCondicionDelivery.val(cabecera.condicion_delivery);

                 DOM.txtObservaciones.val(cabecera.observaciones);
                 DOM.btnCancelarEdicion.show();
             
                }else{
                  console.error(datos.msj);
                }
          };

      new _Ajxur.Api({
        modelo: "Cotizacion",
        metodo: "leerCotizacionEditar",
        data_in : {
          p_codTransaccion : cod_transaccion
        }
      },fn);
  };

  this.cancelarEdicion = function(){
    COD_TRANSACCION_EDITAR = null;
    ARREGLO_DESCUENTOS_TEMPORAL_EDITANDO = [];
    MODO = "+";
    $("#lblrotuloedicion").empty();
    self.DOM.btnCancelarEdicion.hide();

    limpiarCotizacion();
  };

  this.limpiarCotizacion = limpiarCotizacion;

  return this.init();
};

var ListarCotizaciones = function($contenedor, _tpl8){
  var _Util = Util,
      _ArrayUtils = ArrayUtils,
      _VACIO = true,
      _Ajxur = Ajxur,
      DT = null,
      NOMBRE_LOCALSTORAGE = "___jp",
      self = this;

  this.init = function(){
    this.setDOM();
    this.setEventos();
    this.obtenerCotizaciones();
  };

  this.setDOM = function(){
    var DOM = _Util.preDOM2DOM($contenedor, 
                    [{"txtFechaInicio": "#txtfechainicio"},
                      {"txtFechaFin": "#txtfechafin"},
                      {"btnBuscar":"#btnbuscarfecha"},
                      {"tblLista" : "#tbllista"}
                      ]);  


      this.DOM = DOM;
  };

  this.setEventos = function(){
    var self = this,
        DOM = self.DOM;

    DOM.btnBuscar.on("click", function(e){
      e.preventDefault();
      self.obtenerCotizaciones();      
    });
  };

  this.obtenerCotizaciones = function(){
    var   self = this, 
          DOM  = self.DOM,
          fn = function (xhr){
              var datos = xhr.datos;
                if (datos.rpt) {
                  self.listarCotizaciones(datos.data);
                }else{
                  console.error(datos.msj);
                }
          };

      console.log("objer");

      new _Ajxur.Api({
        modelo: "Cotizacion",
        metodo: "obtenerListaCotizaciones",
        data_out: [DOM.txtFechaInicio.val(), DOM.txtFechaFin.val()]
      },fn);
  };

  this.listarCotizaciones = function(dataCotizaciones){
    var DOM = this.DOM;
     if (DT) {DT.fnDestroy(); DT = null;}
      DOM.tblLista.html(_tpl8.ListaCotizaciones({admin : ___ad == 1 ? 1 : null, data: dataCotizaciones}));
      if (dataCotizaciones.length > 0){
        DT = $(".tablalista").dataTable({
                "aaSorting": [[0, "desc"]],
                responsive: true
              });
      }
  };

  this.gestionarVoucher = function(cod_venta,  rotulo){
    _TEMPID = cod_venta;
    app.mdlVoucher.modal("show");
    app.mdlVoucher.find(".rotuloCotizacion").html(rotulo);
    app.frmVoucher[0].reset();
  };

  this.gestionarComisionista = function(cod_venta, rotulo ){
    _TEMPID = cod_venta;
    app.mdlComisionar.modal("show");
    app.mdlComisionar.find(".rotuloCotizacion").html(rotulo);
  };

  this.anular = function(codTransaccion){
    var self = this,
        DOM = self.DOM,
        fn = function(xhr){
          var datos = xhr.datos;
          if (datos.rpt){
            swal("Éxito", datos.msj, "success");
            self.listarCotizaciones(datos.data);
          }
        },
        fnConfirm= function(rpta){
           if(rpta){
            new _Ajxur.Api({
              modelo: "Cotizacion",
              metodo: "eliminarCotizacion",
              data_in: {
                p_codTransaccion: codTransaccion
              },
              data_out: [DOM.txtFechaInicio.val(), DOM.txtFechaFin.val()]
            },fn);
           }
        };
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

  this.verDetalle = function(codTransaccion){
    var self = this,
        DOM = self.DOM,
        fn = function(xhr){
          var datos = xhr.datos;
          if (datos.rpt){
            renderCotizacion(datos.data);
          }
        },
        fnError = function(e){
          swal("Error",e,"error");
        };

    new _Ajxur.Api({
              modelo: "Cotizacion",
              metodo: "leerCotizacion",
              data_in: {
                p_codTransaccion: codTransaccion
              }
            },fn,fnError);
  };

  var renderCotizacion = function(dataCotizacion){
    var mdlDetalleCotizacion = $("#mdlDetalleCotizacion"),
        cabecera = dataCotizacion.cabecera;

    mdlDetalleCotizacion.modal("show");
    mdlDetalleCotizacion.find("h3").html("Cotizacion: "+cabecera.cod_transaccion+" - "+cabecera.cliente+" - Doc.: "+cabecera.numero_documento);
    mdlDetalleCotizacion.find(".modal-body").html(_tpl8.DetalleCotizacion(dataCotizacion));

    console.log("l");

    mdlDetalleCotizacion = null;
  };

  this.verComprobante = function(codTransaccion){
     var str = "../controlador/imprimir.cotizacion.pdf.php?"+
                    "p_t="+codTransaccion;
                    
     window.open(str,'_blank'); 
  };

  return this.init();
};


app.init = function(){
  this.$tabRegistrarCotizaciones = $("#tabRegistrarCotizaciones");
  this.$tabListadoCotizaciones = $("#tabListadoCotizaciones");

  this.tpl8 = Util.Templater($("script[type=handlebars-x]"));

  this.RegistrarCotizaciones = new RegistrarCotizaciones(this.$tabRegistrarCotizaciones, this.tpl8);
  this.ListarCotizaciones = new ListarCotizaciones(this.$tabListadoCotizaciones, this.tpl8);

  this.setEventos();
};

app.setEventos  = function(){};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  })
});

