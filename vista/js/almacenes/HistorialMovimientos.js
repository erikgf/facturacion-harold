const HistorialMovimientos = function({$, tpl8, sucursales}){
  const data = {
    tipos: [],
    categorias : [],
    historialProductos : []
  };
  let sucursalSeleccionada = null,
      DT = null;
      _productosNoRepetidos = [];

    var _Util = Util,
        _ArrayUtils = ArrayUtils,
        _Ajxur = Ajxur,
        _productosTransferencia = [],
        _codProductosTransferir = [],
        _PRODUCTO_TRANSFERIR = null,
        self = this;
  
    /*
    this.init = function(){
      this.setDOM();
      this.setEventos();
      this.llenarData();
    };
    */

    this.init = function(){
      this.setDOM();
      this.setEventos();

      console.log({})

      const idSucursal = document.getElementById("cbosucursal").value;
      sucursalSeleccionada = sucursales.find ( item => item.id == idSucursal);

      this.obtenerTipoCategorias();
      this.obtenerCategorias();
      //this.obtenerHistorialProductos();
    };
  
    this.setData = function({key, value}){
      data[key] = value;
    };
  
    this.getData = function(){
      return data;
    };

    this.setDOM = function(){
      this.DOM = _Util.preDOM2DOM($, 
                      [ {"cboFiltroMovimiento": "#cbofiltromovimiento"},
                        {"cboTipo": "#cbotipohistorial"},
                        {"cboCategoria": "#cbocategoriahistorial"},
                        {"btnTransferencia":"#btntransferencia"},
                        {"txtFechaDesde":"#txtfechainicio"},
                        {"txtFechaHasta":"#txtfechafin"},
                        {"btnBuscarFecha":"#btnbuscarfecha"},
                        {"blkAlertMovimiento":"#blkalertmovimiento"},
                        {"tblHistorial": "#tblhistorial"},
                        {"mdlMovimiento": "#mdlMovimiento"},
                        {"frmGrabar":"#frmgrabar"},
                        {"cboTipoMovimiento":"#cbotipomovimiento"},
                        {"cboSucursalActual":"#cbosucursalactual"},
                        {"cboProductos":"#cboproductos"},
                        {"txtPrecio":"#txtprecio"},
                        {"txtStockActual":"#txtstockactual"},
                        {"txtCantidad":"#txtcantidad"},
                        {"blkAlert": "#blkalert"},
                        {"mdlTransferencia": "#mdlTransferencia"},
                        {"frmGrabarTrans": "#frmgrabartransferencia"},
                        {"cboAlmacenOrigen": "#cboalmacenorigen"},
                        {"cboProductoOrigen": "#cboproductoorigen"},
                        {"txtStockActualTrans": "#txtstockactualtrans"},
                        {"btnMover": "#btnmover"},
                        {"cboAlmacenDestino": "#cboalmacendestino"},
                        {"tblTransferencia": "#tbltransferencia"}
                      ]);
    };

    const hacerBrillarBoton = ()=>{
      this.DOM.btnBuscarFecha.attr('style',
            "background-color: #4F99C6 !important; border-color: #6FB3E0 !important; color: #574c4c !important"
          );
      setTimeout(()=>{
        this.DOM.btnBuscarFecha.attr("style", "");
      }, 600);
    };
  
    this.setEventos = function(){
      var self = this,
          DOM = self.DOM;
  
      DOM.cboFiltroMovimiento.on("change", hacerBrillarBoton);
      DOM.cboTipo.on("change", (e)=>{
        cargarCategorias(e.currentTarget.value);
        hacerBrillarBoton();
      });
      DOM.cboCategoria.on("change", hacerBrillarBoton);
  
      DOM.mdlMovimiento.on("shown.bs.modal", function(e){
        limpiarModal(DOM);
      });
  
      DOM.cboProductos.on("change", function(e){
        if (this.value == "" || DOM.txtPrecio.val() == ""){
          DOM.txtStockActual.val("");
          DOM.txtStockActual.data("i","");
          return;
        }
        obtenerStockActual(DOM);
      });
  
      DOM.txtPrecio.on("change", function(e){
        if (this.value == "" || DOM.cboProductos.val() == ""){
          DOM.txtStockActual.val("");
          DOM.txtStockActual.data("i","");
          return;
        }
        obtenerStockActual(DOM);
      });
  
      DOM.frmGrabar.on("submit", function(e){
        e.preventDefault();
        self.guardarMovimiento();
      });
  
      DOM.btnBuscarFecha.on("click", (e) => {
        this.obtenerHistorialProductos();
      });
  
      DOM.cboSucursalActual.on("change", function(e){
        app.cboSucursal.val(this.value);
        app.obtenerDataSoloProductos();
      });
  
      DOM.btnTransferencia.on("click",function(e){
        e.preventDefault();
        DOM.mdlTransferencia.modal("show");
      });
  
      DOM.mdlTransferencia.on("shown.bs.modal", function(e){
        cargarProductosTransferencia(self.DOM.cboAlmacenOrigen.val());
      });
  
      DOM.mdlTransferencia.on("hidden.bs.modal", function(e){
        cargarProductosTransferencia(self.DOM.cboAlmacenOrigen.val());
      });
  
      DOM.cboAlmacenOrigen.on("change", function(e){
       cargarProductosTransferencia(this.value);
      });
  
      DOM.cboProductoOrigen.on("change", function(e){
       seleccionarProductoTransferencia(this.value);
      });
  
      DOM.tblTransferencia.on("change", "tr td input", function(e){
        limitarProductoTransferencia(this);
      });
  
  
      DOM.btnMover.on("click", function(e){
        e.preventDefault();
        moverProductoTransferencia(_PRODUCTO_TRANSFERIR);
      });
  
      DOM.frmGrabarTrans.on("submit", function(e){
        e.preventDefault();
        guardarTransferencia();
      });
    };

    this.obtenerTipoCategorias = async  function () {
      try {
        const { data } = await apiAxios.get(`tipo-categorias`);
        this.setData({key: "tipos", value: data});
        this.DOM.cboTipo.html(tpl8.Combo(data));
      } catch (error) {
        console.error(error);
      }
    };

    this.obtenerCategorias =  async  function () {
      try {
        const { data } = await apiAxios.get(`categorias`);
        this.setData({key: "categorias", value: data});
        this.DOM.cboCategoria.html(tpl8.Combo([]));
      } catch (error) {
        console.error(error);
      }
    };

    this.obtenerHistorialProductos =  async  function () {
      try {
        const sentData = {
          fecha_inicio: this.DOM.txtFechaDesde.val(),
          fecha_fin: this.DOM.txtFechaHasta.val()
        };
        const paramsData = new URLSearchParams(sentData);

        const { data } = await apiAxios.get(`almacen/historial-productos/${sucursalSeleccionada?.id}?${paramsData.toString()}`);
        this.setData({key: "historialProductos", value: data});
        this.listarMovimientos(data);

      } catch (error) {
        console.error(error);
      }
    };

    this.listarMovimientos = function() {
      const DOM = this.DOM;
      const lista  = data.historialProductos;
        if (DT) {DT.fnDestroy(); DT = null;}

        DOM.tblHistorial.html(tpl8.Historial(lista));
        if (lista.length > 0){
          DT = DOM.tblHistorial.find("table").dataTable({
          "aaSorting": [[0, "asc"]],
          responsive:true
          });
        }
    }

    const cargarCategorias = (idTipoCategoria) => {
      if (idTipoCategoria == ""){
        this.DOM.cboCategoria.html(tpl8.Combo([]));
        return;
      }

      this.DOM.cboCategoria.html(tpl8.Combo(
        data.categorias.filter(item=>{
          return item.id_tipo_categoria == idTipoCategoria
        })
      ));
      this.DOM.cboCategoria.val("");
    };
   

    this.actualizarLista = function(_dataProductos){
      self.setHistorialProductos(_dataProductos);
      self.listarMovimientos(self.DOM);
    };
  
    var seleccionarProductoTransferencia = function(codProducto){
      var objProductoBuscado,
          txtStockActualTrans = self.DOM.txtStockActualTrans[0],
          stock;
  
      if (codProducto == ""){
        objProductoBuscado = -1;
      } else {
        objProductoBuscado = _ArrayUtils.buscar(_productosTransferencia, [{
            propiedad: "cod_producto",
            valor : codProducto
          }]
        );
      }
  
      if (objProductoBuscado == -1){
        _PRODUCTO_TRANSFERIR = null;
        stock = "";
      } else {
        _PRODUCTO_TRANSFERIR = objProductoBuscado;
        stock = objProductoBuscado.stock;
      }
  
      txtStockActualTrans.value = stock;
    };
  
    var limitarProductoTransferencia = function($cantidadMover){
      var stockIngresado = parseInt($cantidadMover.value),
          maxStock = parseInt($cantidadMover.dataset.maxstock),
          valorIngresado = 1;
  
      if (stockIngresado == ""){
        valorIngresado = 1;
      } else if (stockIngresado > maxStock){
        valorIngresado = maxStock;
      } else if (stockIngresado < 1){
        valorIngresado = 1;
      } else {
        valorIngresado = stockIngresado;
      }
  
      $cantidadMover.value = valorIngresado;
    };
  
    var moverProductoTransferencia = function(objProducto){
      var DOM = self.DOM,
          tblTransferencia = DOM.tblTransferencia;
  
          if (objProducto == null){
            return;
          }
          //check if exists
          if (_ArrayUtils.conseguir(_codProductosTransferir, "cod_producto", objProducto.cod_producto) != -1){
            alert("Producto ya está agregado.");
            return;
          }
  
          tblTransferencia.prepend(_tpl8.Transferencia([objProducto]));
          _codProductosTransferir.push({cod_producto: objProducto.cod_producto});
          DOM.cboProductoOrigen.val("").trigger("chosen:updated");
          DOM.txtStockActualTrans.val("");
  
          _PRODUCTO_TRANSFERIR = null;
          DOM = null;
          tblTransferencia = null;
    };
  
    this.quitarProductoTransferencia = function($tr){
        var cod_producto = $tr.dataset.id,
        objCodProducto;
          
        $tr.remove();
        objCodProducto = _ArrayUtils.conseguirPID(_codProductosTransferir, "cod_producto", cod_producto);
        _codProductosTransferir.splice(objCodProducto.i, 1);
  
    };
  
    var cargarProductosTransferencia = function(codSucursalOrigen){
        var DOM = self.DOM,
          fn = function(xhr){
          var datos = xhr.datos,
              data;
  
          if (datos.rpt){
            data = datos.data;
            _productosTransferencia = data;          
            if (DOM.cboProductoOrigen.data("chosen") == undefined){
              DOM.cboProductoOrigen.html(_tpl8.Producto(data)).chosen();
            } else {
              DOM.cboProductoOrigen.html(_tpl8.Producto(data)).trigger("chosen:updated");
            }
  
            _PRODUCTO_TRANSFERIR = null;
            _codProductosTransferir = [];
            DOM.txtStockActualTrans.val("");
            DOM.tblTransferencia.empty();
          }
          DOM = null;
        };
  
        new _Ajxur.Api({
          "modelo": "Almacen",
          "metodo": "obtenerProductosTransferencia",
          data_in: {
            p_codSucursal : codSucursalOrigen
          }
        }, fn);
    };
  
  
    var guardarTransferencia = function(){
        var DOM = self.DOM,
          cboOrigen = DOM.cboAlmacenOrigen.val(),
          cboDestino = DOM.cboAlmacenDestino.val(),
          tmpTR = [],
          productosTransferir = [],
          fn = function(xhr){
          var datos = xhr.datos,
              data;
  
          if (datos.rpt){
            data = datos.data;
            alert(datos.msj);
            location.reload();
          }
          DOM = null;
        };
  
        if (cboOrigen == cboDestino){
          alert("No se puede seleccionar sucursales iguales.");
          return;
        }
  
        if (_codProductosTransferir.length <= 0){
          alert("No hay productos para transferir.");
          return;
        }
  
        tmpTR = DOM.tblTransferencia.find("tr").toArray();
  
        for (var i = tmpTR.length - 1; i >= 0; i--) {
          var objTR = tmpTR[i];
          productosTransferir.push({cod_sucursal_producto: objTR.dataset.id, cantidad_mover: objTR.children[4].children[0].value});
        };
  
        new _Ajxur.Api({
          "modelo": "AlmacenMovimiento",
          "metodo": "guardarTransferencia",
          data_out: [cboOrigen, cboDestino, JSON.stringify(productosTransferir)]
        }, fn);
  
    };

  
    var fnObtenerHistorialFechas = function(){
      var DOM = self.DOM,
            fn = function (xhr){
                var datos = xhr.datos;
  
                if (datos.rpt) {
                  self.actualizarLista(datos.data);
                }else{
                  console.error(datos.msj);
                }
            };
  
        new Ajxur.Api({
          modelo: "Almacen",
          metodo: "getHistorialProductos",
          data_in: {
            p_codSucursal : app.cboSucursal.val(),
            p_fechaDesde: DOM.txtFechaDesde.val(),
            p_fechaHasta : DOM.txtFechaHasta.val()
          }
        },fn);
    };
  
    this.llenarData = function(){
      var DOM = this.DOM;
      DOM.cboTipo.html(_tpl8.Tipo(_data.tipos));
      DOM.cboCategoria.html(_tpl8.Categoria([]));
      DOM.cboProductos.html(_tpl8.Producto(_data.lista_productos));
  
      this.listarMovimientos(DOM);
    };
  
    this.filtrarListaProductos = function(lista, tipoMovimiento, codTipo, codCategoria){

      return lista.filter(item => {
        const idTipoMovimiento = item.movimiento.charAt(0);
        const idTipoCategoria = item.producto.categoria.id_tipo_categoria;
        const idCategoriaProducto = item.producto.id_categoria_producto;

        return  (idTipoMovimiento == '' || idTipoMovimiento == tipoMovimiento) &&
                (idTipoCategoria == '' || idTipoCategoria == codTipo) &&
                (codCategoria == '' || idCategoriaProducto == codCategoria);
      });


    };
  
    this.eliminar = function(cod_historial){
      var self = this,
          DOM = this.DOM,
          fn = function(xhr){
           var datos = xhr.datos,
               data,
               indiceProductoActualizar,
               objStockProductos = app.StockProductos,
               objProSuc,
               stockProductos;
  
            if (datos.rpt) {
                data = datos.data;
  
                objProSuc = data.obj_producto_sucursal;
                stockProductos = objStockProductos.getStockProductos();
  
                indiceProductoActualizar = _ArrayUtils.buscarTodos(stockProductos,[
                                                                          {
                                                                            propiedad: "cod_producto",
                                                                            valor: objProSuc.cod_producto
                                                                          },
                                                                          { propiedad: "precio",
                                                                            valor: parseFloat(objProSuc.precio).toFixed(2)
                                                                          }],
                                                                          1).i;
  
                objStockProductos.getStockProductos()[indiceProductoActualizar].stock = objProSuc.stock;
  
                objStockProductos.actualizarListaStockProducto();
                objStockProductos.listarProductos();
                self.setHistorialProductos(data.historial_productos);
                self.listarMovimientos(DOM);
  
              _Util.alert(DOM.blkAlertMovimiento, {tipo: "s", mensaje: data.msj});
            } else {
              _Util.alert(DOM.blkAlertMovimiento, {tipo: "e", mensaje: data.msj});
            }
      }, fnError = function(e){
          console.error(e);
      };
  
      new _Ajxur.Api({
         modelo: "AlmacenMovimiento",
         metodo: "eliminarMovimiento",
         data_in : {
          p_codHistorial: cod_historial
         },
         data_out: [DOM.txtFechaDesde.val(), DOM.txtFechaHasta.val()]
      }, fn, fnError);
    };
  
    this.guardarMovimiento = function(){
        var self = this,
            DOM = this.DOM,
            codSucursal = app.cboSucursal.val(),
            codProducto = DOM.cboProductos.val(),
            precio = DOM.txtPrecio.val(),
            cantidad = DOM.txtCantidad.val(),
            fn = function (xhr){
                var datos = xhr.datos,
                    data,
                    indiceProductoActualizar,
                    objStockProductos = app.StockProductos,
                    objProSuc;
                if (datos.rpt){
                   data = datos.data;
                   objProSuc = data.obj_producto_sucursal;
                   indiceProductoActualizar = DOM.txtStockActual.data("i"); 
                   if (indiceProductoActualizar == null || indiceProductoActualizar == ""){
                      objStockProductos.getStockProductos().push(objProSuc);
                   } else { 
                      objStockProductos.getStockProductos()[indiceProductoActualizar].stock = objProSuc.stock;
                   }
  
                   objStockProductos.actualizarListaStockProducto();
                   objStockProductos.listarProductos();
                   self.setHistorialProductos(data.historial_productos);
                   self.listarMovimientos(DOM);
  
                   limpiarModal(DOM);
                  _Util.alert(DOM.blkAlert, {tipo: "s", mensaje: datos.msj});
                } else {
                  _Util.alert(DOM.blkAlert, {tipo: "e", mensaje: datos.msj});
                }
            };          
  
        if (codProducto == ""){
          _Util.alert(DOM.blkAlert, {tipo: "e", mensaje: "Seleccione un producto."});
          return;
        }
  
        if (precio == ""){
          _Util.alert(DOM.blkAlert, {tipo: "e", mensaje: "Escriba un precio de producto."});
          return;
        }
  
        if (cantidad == "" || cantidad <= 0){
          _Util.alert(DOM.blkAlert, {tipo: "e", mensaje: "Ingrese la cantidad de producto."});
          return;
        }
  
        new _Ajxur.Api({
          modelo: "AlmacenMovimiento",
          metodo: "registrarMovimiento",
          data_in : {
            p_tipoMovimiento: DOM.cboTipoMovimiento.val(),
            p_codProducto : codProducto,
            p_precio : precio,
            p_cantidad : cantidad,
            p_codSucursal: codSucursal
          },
          data_out: [DOM.txtFechaDesde.val(), DOM.txtFechaHasta.val()]
        }, fn)
    };
  
    var limpiarModal = function(DOM){
      DOM.frmGrabar[0].reset();
  
      if (DOM.cboProductos.data("chosen") == undefined){
        DOM.cboProductos.chosen();
      }
      DOM.txtStockActual.data("i","");
      DOM.cboProductos.trigger("chosen:updated");
      DOM.cboSucursalActual.val(app.cboSucursal.val());
    };
  
    var obtenerStockActual = function(DOM){
      var listaStock = app.StockProductos.getStockProductos(),
          objProducto = _ArrayUtils.buscarTodos(listaStock, [{
                          propiedad: "cod_producto",
                          valor: DOM.cboProductos.val()
                        },
                        { propiedad: "precio",
                          valor: parseFloat(DOM.txtPrecio.val()).toFixed(2)
                        }],
                        1);
  
      if (!objProducto){
        DOM.txtStockActual.val("0");
        DOM.txtStockActual.data("i","");
      } else{
        DOM.txtStockActual.val(objProducto.item.stock);
        DOM.txtStockActual.data("i",objProducto.i);
      }
    };

    this.actualizarListaProductos = function({idSucursal}){
      sucursalSeleccionada = {id: idSucursal};
      this.obtenerHistorialProductos();
    };
  
    return this.init();
  };
  