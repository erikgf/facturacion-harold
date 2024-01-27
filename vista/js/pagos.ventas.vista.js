var app = {},
  _TEMPID = -1,
  _ROTULO = "",
  _ACCION = "agregar",
  _CLASE = "Venta",
  DT = null,
  _POSTREGISTRADO = false,
  _MSJ = "",
  _SAVING = false,
  ULTIMO_CORRELATIVO = 1;

var RegistrarPagosVentas = function($contenedor, _tpl8){
  var _Util = Util,
      _ArrayUtils = ArrayUtils,
      _VACIO = true,
      _TR_BUSCAR = null,
      _Ajxur = Ajxur,
      _data = {
        productos: [],
        clientes : [],
        tipo_categorias : [],
        categoria_productos : []
      },
      NOMBRE_LOCALSTORAGE = "___jp",
      MODO = "+",
      COD_EDITAR = null;
      self = this;

  this.init = function(){
    this.setDOM();
    this.setEventos();
    this.obtenerData();
  };

  this.getData = function(){
    return _data;
  };


  this.getDescuentosRealizados = function(){
    return _descuentos_realizados;
  }

  this.setDOM = function(){
    var DOM = _Util.preDOM2DOM($contenedor, 
                    [ {"cboComprobanteBuscar" :"#cbocomprobantebuscar"},
                      {"txtNumeroComprobante": "#txtnumerocomprobante"},
                      {"txtNombreCliente": "#txtnombrecliente"},
                      {"txtFechaRegistro": "#txtfecharegistro"},
                      {"txtTotalVenta": "#txttotalventa"},
                      {"txtPendiente": "#txtpendiente"},
                      {"txtFechaPago" : "#txtfechapago"},
                      {"txtPagado": "#txtpagado"},
                      {"txtObservaciones": "#txtobservaciones"},
                      {"btnGuardar" : "#btnguardar"}
                      ]);  

      this.DOM = DOM;
  };

  this.setEventos = function(){
    var self = this,
        DOM = self.DOM;

    DOM.cboComprobanteBuscar.on("change", (e)=>{
      let valor = e.currentTarget.value;
      if (valor == ""){
        this.obtenerVentaPagar(null);
        return;
      }
      this.obtenerVentaPagar(valor);
    });

    DOM.btnGuardar.on("click", function(e){
      e.preventDefault();
      if (_SAVING == false){
        grabarPagoVenta();  
      }
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

            if (cod_producto != undefined && cod_producto != "0000-00-00"){
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
        $credito = DOM.txtMontoCredito,
        credito = $credito.val(),
        total = (montoTotal == undefined ? parseFloat(DOM.lblTotal.html())  : montoTotal);


        switch(txtAccion){
          case "E":
            efectivo = total;            
            tarjeta = "0.00";
            credito = "0.00";
          break;
          case "T":
            efectivo = total - tarjeta - credito;
            if (efectivo < 0.00){
              efectivo = "0.00";
              credito = "0.00";
              tarjeta = total;
            }
          break;
          case "C":
            efectivo = total - tarjeta - credito;
            if (efectivo < 0.00){
              efectivo = "0.00";
              tarjeta = "0.00";
              credito = total;
            }
          break;
        }

       
        cambiarTarjeta( (tarjeta > 0 ? "T" : "E"), DOM.blkTipoTarjeta);

        $tarjeta.val(parseFloat(tarjeta).toFixed(2));
        $efectivo.val(parseFloat(efectivo).toFixed(2));
        $credito.val(parseFloat(credito).toFixed(2));
  };


  this.getProducto = function(codigo_unico_producto){
    return _ArrayUtils.conseguirPID(_data.productos, "codigo_unico_producto", codigo_unico_producto);
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
        modelo: "Venta",
        metodo: "obtenerDataProductos",
        data_in: {
          p_codSucursal: DOM.cboSucursal.val()
        }
      },fn);
  };

  this.obtenerData = function(){
     var self = this, 
         DOM  = self.DOM,
          fn = function (xhr){
              var datos = xhr.datos;
                if (datos.data) {
                  llenarComprobantesAdeudadosBuscar(DOM.cboComprobanteBuscar, datos.data.comprobantes_deuda);
                  return;
                }
                console.error(datos.msj);
          };

      new _Ajxur.Api({
        modelo: "PagoVenta",
        metodo: "obtenerData"
      },fn);
  };


  this.obtenerVentaPagar = function(cod_venta){
    if (cod_venta == null){
      this.renderVentaPagar(null);
      return;
    }

     var self = this, 
         DOM  = self.DOM,
          fn = function (xhr){
              var datos = xhr.datos;
                if (datos) {
                  self.renderVentaPagar(datos);
                  return;
                }
                console.error(datos.msj);
          };

      new _Ajxur.Api({
        modelo: "PagoVenta",
        metodo: "obtenerVentaPagar",
        data_in: {
          p_codVenta: cod_venta
        }
      },fn);
  };

  this.renderVentaPagar = function(data = null){
    let DOM = this.DOM;
    if (!data){
      DOM.txtNumeroComprobante.val("");
      DOM.txtFechaRegistro.val("");
      DOM.txtNombreCliente.val("");
      DOM.txtTotalVenta.val("");
      DOM.txtPendiente.val("");
      return;
    }

    DOM.txtNumeroComprobante.val(data.numero_comprobante);
    DOM.txtFechaRegistro.val(data.fecha_registro);
    DOM.txtNombreCliente.val(data.cliente);
    DOM.txtTotalVenta.val(data.importe_total_venta);
    DOM.txtPendiente.val(data.deuda);
    DOM.txtFechaPago.select();
  };

  var llenarComprobantesAdeudadosBuscar = function($cboComprobanteBuscar, comprobantes_deuda){
    $cboComprobanteBuscar.html(_tpl8.CboComprobantesBuscar(comprobantes_deuda)).val("0").chosen();
  };

  var grabarPagoVenta = function(){
    var objVenta = verificarPagoVenta(),
        ListarPagosVentas = app.ListarPagosVentas,
        fnConfirm = function(isConfirm){
          if (_SAVING == true){
            return;
          } 

           if (isConfirm){            
              var cabecera = objVenta.datos.cabecera,
                  detalle  = objVenta.datos.detalle,
                  ListarPagosVentasDOM = ListarPagosVentas.DOM,
                  accion = MODO == "+" ? "grabar" : "editar";

              _SAVING = true;

                 new _Ajxur.Api({
                  modelo: "PagoVenta",
                  metodo: accion,
                  data_in: {
                    p_codVenta : cabecera.codVenta,
                    p_fechaPago : cabecera.fechaPago,
                    p_pagado : cabecera.pagado,
                    p_observaciones : cabecera.observaciones
                  },
                  data_out:[ListarPagosVentasDOM.txtFechaInicio.val(), ListarPagosVentasDOM.txtFechaFin.val()]
                }, fn);
              }   
        },
        fn = function(xhr){
          var datos = xhr.datos,
              _cabecera = objVenta.datos.cabecera,
              data,
              DOM = self.DOM;

          if (!datos){
            _SAVING = false;
            return;
          }

          if (datos.rpt){
            data = datos.data;

            if (data.comprobantes_deuda.length > 0){
              llenarComprobantesAdeudadosBuscar(DOM.cboComprobanteBuscar, data. comprobantes_deuda);
            }

            COD_EDITAR = null;
            $("#lblrotuloedicion").html();
            limpiarPagoVenta();

            if(MODO == "*"){
              app.ListarPagosVentas.verDetalle(data.cod_pago_venta);
            }

            swal("Exito", datos.msj, "success");
            $('.nav-tabs a[href="#tabListadoPagosVentas"]').tab('show');
            ListarPagosVentas.listarVentas(data.lista);
            MODO = "+";

          } else {
            swal("Error", datos.msj, "error");
          }

          _SAVING = false;
        },
        fnError = function(e){
          console.error(e);
          _SAVING = false;
        };


    if (!objVenta.rpt){
      swal("Error", objVenta.msj, "error");
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

  var verificarPagoVenta = function(){
    var objVerificarCabecera = verificarCabecera(),
        objVerificarDetalle;

    if (!objVerificarCabecera.rpt){
      return objVerificarCabecera;
    }


    return {rpt: true, datos: {cabecera: objVerificarCabecera.datos}};
  };

  var verificarCabecera = function(){
    var DOM = self.DOM,
        codComprobante = DOM.cboComprobanteBuscar.val(),
        fechaPago = DOM.txtFechaPago.val(),
        pagado  = DOM.txtPagado.val(),
        observaciones = DOM.txtObservaciones.val();

    if (pagado <= 0.00) {
      return {rpt: false, msj: "No se puede pagar un monto igual o menor a 0."};
    }

    if (fechaPago == ""){
      return {rpt: false, msj: "Ingrese fecha de pago de venta."};
    }

    return {
      rpt: true,
      datos: {
        codVenta: codComprobante,
        fechaPago: fechaPago,
        pagado: pagado,
        observaciones: observaciones
      }
    };
  };

  var limpiarPagoVenta = function(){
    /*formulario, descuentos, detalle*/ 
    var DOM = self.DOM;
    DOM.cboComprobanteBuscar.val("0").trigger("chosen:updated");

    DOM.txtNumeroComprobante.val("");
    DOM.txtFechaRegistro.val("");
    DOM.txtNombreCliente.val("");
    DOM.txtTotalVenta.val("");
    DOM.txtPendiente.val("");

    DOM.txtObservaciones.val("");
    DOM.txtPagado.val("");
    DOM.txtFechaPago.val("");
  };

  /*
  this.editar = function(cod_pago_venta){
     var self = this, 
         DOM  = self.DOM,
          fn = function (xhr){
              var datos = xhr.datos,
                  cabecera,
                  detalle;

                if (datos.rpt) {  
                  MODO = "*";

                  cabecera = datos.data.cabecera;
                  $("#lblrotuloedicion").html("EDITANDO PAGO VENTA: "+cabecera.x_cod);

                  COD_EDITAR = cabecera.cod_pago_venta;

                  DOM.cboComprobanteBuscar.val(cabecera.cod_venta).change().trigger("chosen:updated");
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

                  _descuentos_realizados = [];

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

                     if (objDetalle.codigo_descuento != ""){
                        _descuentos_realizados.push({codigo: objDetalle.cod_descuento});
                     };
                  };

                  DOM.lblSubTotal.html(cabecera.importe_total_venta);

                  if (cabecera.cod_descuento_global != null){
                      DOM.txtDescuentoGlobal[0].dataset.id =
                              cabecera.cod_descuento_global+"_"+cabecera.monto_descuento+"_"+cabecera.tipo_descuento+"_"+cabecera.rotulo_descuento+"_"+cabecera.codigo_descuento_global;

                      DOM.txtDescuentoGlobal.html(cabecera.rotulo_descuento+'<br><small>'+cabecera.codigo_descuento_global+'</small><br><a class="descuento-cancelar" href="javascript:;" style="font-size: 14px;">Cancelar</a>');         
                      DOM.lblDescuento.html(cabecera.total_descuentos);

                      _descuentos_realizados.push({codigo: cabecera.codigo_descuento_global});
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

                 $('.nav-tabs a[href="#tabRegistrarPagosVentas"]').tab('show');

                 DOM.btnCancelarEdicion.show();
             
                }else{
                  console.error(datos.msj);
                }
          };

      new _Ajxur.Api({
        modelo: "PagoVenta",
        metodo: "leerEditar",
        data_in : {
          p_codPagoVenta : cod_pago_venta
        }
      },fn);
  };

  this.cancelarEdicion = function(){
    COD_EDITAR = null;
    MODO = "+";
    $("#lblrotuloedicion").empty();
    self.DOM.btnCancelarEdicion.hide();

    limpiarPagoVenta();
  };
  */
  
  this.limpiarPagoVenta = limpiarPagoVenta; 

  return this.init();
};

var ListarPagosVentas = function($contenedor, _tpl8){
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
    this.obtenerVentas();
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
      self.obtenerVentas();      
    });
  };

  this.obtenerVentas = function(){
    var   self = this, 
          DOM  = self.DOM,
          fn = function (xhr){
              var datos = xhr.datos;
                if (datos.rpt) {
                  self.listarVentas(datos.data);
                }else{
                  console.error(datos.msj);
                }
          };


      new _Ajxur.Api({
        modelo: "PagoVenta",
        metodo: "obtenerListaVentas",
        data_out: [DOM.txtFechaInicio.val(), DOM.txtFechaFin.val()]
      },fn);
  };


  this.listarVentas = function(dataVentas){
    var DOM = this.DOM;
     if (DT) {DT.fnDestroy(); DT = null;}
      DOM.tblLista.html(_tpl8.ListaVentas({admin : ___ad == 1 ? 1 : null, data: dataVentas}));
      if (dataVentas.length > 0){
        DT = $(".tablalista").dataTable({
                "aaSorting": [[0, "desc"]],
                responsive: true
              });
      }
  };

  this.gestionarVoucher = function(cod_venta,  rotulo){
    _TEMPID = cod_venta;
    app.mdlVoucher.modal("show");
    app.mdlVoucher.find(".rotuloVenta").html(rotulo);
    app.frmVoucher[0].reset();
  };


  this.gestionarComisionista = function(cod_venta, rotulo ){
    _TEMPID = cod_venta;
    app.mdlComisionar.modal("show");
    app.mdlComisionar.find(".rotuloVenta").html(rotulo);
  };


  this.anular = function(codTransaccion){
    var self = this,
        DOM = self.DOM,
        fn = function(xhr){
          var datos = xhr.datos;
          if (datos.rpt){
            swal("Éxito", datos.msj, "success");
            self.listarVentas(datos.data);
          }
        },
        fnConfirm= function(rpta){
           if(rpta){
            new _Ajxur.Api({
              modelo: "Venta",
              metodo: "eliminarVenta",
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
            renderVenta(datos.data);
          }
        },
        fnError = function(e){
          swal("Error",e,"error");
        };

    new _Ajxur.Api({
              modelo: "Venta",
              metodo: "leerVenta",
              data_in: {
                p_codTransaccion: codTransaccion
              }
            },fn,fnError);
  };

  var renderVenta = function(dataVenta){
    var mdlDetalleVenta = $("#mdlDetalleVenta"),
        cabecera = dataVenta.cabecera;

    mdlDetalleVenta.modal("show");
    mdlDetalleVenta.find("h3").html("Venta: "+cabecera.cod_transaccion+" - "+cabecera.cliente+" - Doc.: "+cabecera.numero_documento);
    mdlDetalleVenta.find(".modal-body").html(_tpl8.DetalleVenta(dataVenta));

    mdlDetalleVenta = null;
  };

  this.verComprobante = function(codTransaccion){
     var str = "../controlador/imprimir.comprobante.pdf.php?"+
                    "p_t="+codTransaccion;
                    
     window.open(str,'_blank'); 
  };

  return this.init();
};

app.init = function(){
  this.$tabRegistrarPagosVentas = $("#tabRegistrarPagosVentas");
  this.$tabListadoPagosVentas = $("#tabListadoPagosVentas");

  /*Modal Detalle*/
  this.tpl8 = Util.Templater($("script[type=handlebars-x]"));

  this.RegistrarPagosVentas = new RegistrarPagosVentas(this.$tabRegistrarPagosVentas, this.tpl8);
  this.ListarPagosVentas = new ListarPagosVentas(this.$tabListadoPagosVentas, this.tpl8);

  this.setEventos();
//  this.initRegistrar();
 // this.initLista();
};

app.setEventos  = function(){
  var self = this;

};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});

