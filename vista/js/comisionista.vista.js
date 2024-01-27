var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Comisionista",
  DT = null,
  _productos = [],
  _productosagregados = [], //usado para los combos de productos.
  _FILAS = 0;

app.init = function(){
  this.setDOM();
  this.setEventos();
  this.setTemplate();

  this.listar();
  this.obtenerData();
};

app.setDOM = function(){
  var DOM = {};

  DOM.listado = $("#listado");
  DOM.modal = $("#mdlRegistro");
  DOM.frmGrabar = $("#frmgrabar");
  DOM.mdlHeader = DOM.modal.find(".modal-header h3");

  DOM.txtNumeroDocumento = DOM.frmGrabar.find("#txtnumerodocumento");
  DOM.txtNombres = DOM.frmGrabar.find("#txtnombres");
  DOM.txtCelular = DOM.frmGrabar.find("#txtcelular");
  DOM.txtCorreo = DOM.frmGrabar.find("#txtcorreo");

  DOM.modalProductos = $("#mdlProductos");
  DOM.frmGrabarProductos = $("#frmgrabarproductos");
  DOM.tblProductosComision = $("#tblproductoscomision");
  DOM.txtComisionista = $("#txtcomisionista");
  DOM.btnAgregarProductoComision = $("#btnagregarproducto");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.modal.on("hidden.bs.modal",function(e){
    self.limpiar();
  });

  DOM.frmGrabar.on("submit", function(e){
    e.preventDefault();
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
        },
        function(isConfirm){ 
          if (isConfirm){
            self.grabar();
          }
      });
  });

  var fSoloNumeros = function(e){ return Util.soloNumeros(e);};
  DOM.txtNumeroDocumento.on("keypress", fSoloNumeros);
  DOM.txtCelular.on("keypress", fSoloNumeros);

  var fSoloLetras = function(e){ return Util.soloLetras(e);};
  DOM.txtNombres.on("keypress", fSoloLetras);

  DOM.modalProductos.on("hidden.bs.modal",function(e){
      _TEMPID = -1;
  });

  DOM.tblProductosComision.on("click","tr button.eliminar", function(e){
    e.preventDefault();
    self.eliminarProductoComision(this.parentElement.parentElement);
  });

  DOM.tblProductosComision.on("change","tr select.productos", function(e){
    var valor = this.value;
    e.preventDefault();
    console.log("change");
    if (checkSiExiste(valor)){
      this.dataset.cod="";
      this.value ="";
      swal("Error", "Producto ya seleccionado.", "error");
      return;  
    }
    gestionarProductoArregloTmp(this.dataset.cod, valor);
    this.dataset.cod = valor;
  });

  DOM.btnAgregarProductoComision.on("click", function(e){
    e.preventDefault();
    self.agregarNuevoProductoComisionista();
  });

  DOM.frmGrabarProductos.on("submit", function(e){
    e.preventDefault();
    self.grabarProductosComisionista();   
  });

};

var gestionarProductoArregloTmp = function(codQuitar, codAgregar){
  var ar = [],
      objProducto;
  for (var i = 0, len =  _productosagregados.length; i <len;i++) {
    var item = _productosagregados[i];
    if (item.cod_producto != codQuitar){
      ar.push(item);
    }
    if (item.cod_producto == codAgregar){
      objProducto = item;  
    }
  };

  if (objProducto){
    ar.push(objProducto);
  }
  _productosagregados = ar;
  return ar;
};

var checkSiExiste = function(codVerificar){
  var existe = false;

  for (var i = 0, len =  _productosagregados.length; i <len;i++) {
    var item = _productosagregados[i];
    if (item.cod_producto == codVerificar){
      existe = true;
      break;
    }
  };

  return existe;
};

app.setTemplate = function(){
  var tpl8 = {};
  tpl8.listado = Handlebars.compile($("#tpl8Listado").html());
  tpl8.producto_comision = Handlebars.compile($("#tpl8ProductoComision").html());
  this.tpl8 = tpl8;
};

app.limpiar = function(){
  var DOM = this.DOM;
  DOM.frmGrabar[0].reset();

  _ACCION = "agregar";
  _TEMPID = -1;
};

app.agregar = function(){
  _ACCION = "agregar";
  _TEMPID = -1;
  this.DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());
};

app.editar = function(cod){
  _ACCION = "editar";
  _TEMPID = cod;
  var DOM = this.DOM,
    fn = function (xhr){
      var datos = xhr.datos;
      if (datos.rpt) {      
        var data = datos.data;
        DOM.txtNumeroDocumento.val(data.numero_documento);
        DOM.txtNombres.val(data.nombres);
        DOM.txtCelular.val(data.celular);
        DOM.txtCorreo.val(data.correo);

      }else{
        console.error(datos.msj);
      }
  };

  DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());
  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "leerDatos",
    data_in : {
      p_codComisionista: cod
    }
  },fn);
};


app.eliminar = function(cod){
  var DOM = this.DOM,
    fn = function (xhr){
      var datos = xhr.datos;
      if (datos.rpt) {      
         swal("Exito", datos.msj, "success");
         app.listar();
      }else{
        console.error(datos.msj);
      }
  };

  swal({
          title: "Confirme",
          text: "¿Esta seguro que desea eliminar el registro?",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'Si',
          cancelButtonText: "No",
          closeOnConfirm: true,
          closeOnCancel: true,
          imageUrl: "../images/pregunta.png"
        },
        function(isConfirm){ 
          if (isConfirm){
              new Ajxur.Api({
                modelo: _CLASE,
                metodo: "eliminar",
                data_in : {
                  p_codComisionista: cod
                }
              },fn);
          }
      });
};

app.grabar = function(){
  var DOM = this.DOM,
      fn = function(xhr){
        console.log(xhr);
        var datos = xhr.datos;
        if (datos.rpt){
          swal("Exito", datos.msj, "success");
          DOM.modal.modal("hide");
          app.listar();
        } else {
          swal("Error", datos.msj, "error");
        } 
      };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: _ACCION,
    data_in :  {
      p_numeroDocumento : DOM.txtNumeroDocumento.val(),
      p_nombres : DOM.txtNombres.val(),
      p_celular : DOM.txtCelular.val(),
      p_correo : DOM.txtCorreo.val(),
      p_codComisionista : _TEMPID
    }

  },fn);
};

app.gestionarProductos = function(cod_comisionista, nombres){
  var DOM = this.DOM,
      tpl8 = this.tpl8.producto_comision,
      fn = function (xhr){
        var datos = xhr.datos, data;
          if (datos.rpt) {
            data = datos.data;
            DOM.modalProductos.modal("show");
            if (!data.length){
              DOM.tblProductosComision.html(tpl8({data: []}));  
              _FILAS = 0;
            } else {
              DOM.tblProductosComision.html(tpl8({productos: _productos, data: data}));  
              _FILAS = data.length;
              _productosagregados = data;
            }
          }else{
            console.error(datos.msj);
          }
      };

  DOM.txtComisionista.val(nombres);
  _TEMPID = cod_comisionista;

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "obtenerProductosComisionista",
    data_in: {
      p_codComisionista: cod_comisionista
    }
  },fn);

};


app.eliminarProductoComision = function($tr){
  $tr.remove();
  _FILAS--;
  if (_FILAS == 0){
    this.DOM.tblProductosComision.html(this.tpl8.producto_comision({data: []}));
  }
};

app.agregarNuevoProductoComisionista = function(){
  /*Add una fila*/
  this.DOM.tblProductosComision[_FILAS == 0 ? "html" : "prepend"](this.tpl8.producto_comision({productos: _productos, data: [{cod_producto: null, tipo_movimiento: 'P',  valor_comision: null}]}));
  _FILAS++;
};

app.grabarProductosComisionista = function(){
   var DOM = this.DOM,
      fn = function(xhr){
        var datos = xhr.datos;
        if (datos.rpt){
          swal("Exito", datos.msj, "success");
        } else {
          swal("Error", datos.msj, "error");
        } 
      },
      arregloTR = [].slice.call(DOM.tblProductosComision.find("tr:not(.tr-null)")),
      indices = {
        "producto": 1,
        "tipo": 2,
        "valor": 3
      },
      arregloProductos = [],
      valido = true;

  if (arregloTR.length <= 0){
    return;
  }

  $.each(arregloTR, function(i,$tr){
      var arregloTD = [].slice.call($tr.children),
          cod_producto,
          tipo_comision,
          valor_comision;

      cod_producto = arregloTD[indices.producto].children[0].value;

      if (cod_producto == ""){
        valido = false;
        swal("Error", "No hay producto en el registro N° "+(i+1), "error");
        return;
      }
      tipo_comision = arregloTD[indices.tipo].children[0].value;
      valor_comision = arregloTD[indices.valor].children[0].value;

      if (valor_comision == "" || valor_comision < 0){
        valido = false;
        swal("Error", "El valor de la comisión no es válido, en el registro N° "+(i+1), "error");
        return;
      }

      arregloProductos.push({
        cod_producto: cod_producto,
        tipo_comision: tipo_comision,
        valor_comision : valor_comision
      });
  });

  if (valido == false){
    return;
  }

    swal({
          title: "Confirme",
          text: "¿Esta seguro de grabar los productos del comisionista?",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'Si',
          cancelButtonText: "No",
          closeOnConfirm: false,
          closeOnCancel: true,
          imageUrl: "../images/pregunta.png"
        },
        function(isConfirm){ 
          if (isConfirm){
            new Ajxur.Api({
              modelo: _CLASE,
              metodo: "grabarProductosComisionista",
              data_in :  {
                p_productosComisionista : JSON.stringify(arregloProductos),
                p_codComisionista : _TEMPID
              }

            },fn);
          }
      });

  
};

app.listar = function(){
  var DOM = this.DOM,
      tpl8Listado = this.tpl8.listado,
      tpl8DniClave = this.tpl8.dni_clave;

  var fn = function (xhr){
    var datos = xhr.datos;
      if (datos.rpt) {
        if (DT) { DT.fnDestroy(); DT = null; }
        DOM.listado.html(tpl8Listado(datos.data));
        DT = DOM.listado.find("table").dataTable({
          "aaSorting": [[0, "asc"]]
        });
      }else{
        swal("Error", datos.msj, "error");
      }
  };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "listar"
  },fn);
};


app.obtenerData = function(){
  var fn = function (xhr){
    var datos = xhr.datos;
      if (datos.rpt) {
        _productos = datos.data;        
      }else{
        console.error(datos.msj);
      }
  };

  new Ajxur.Api({
    modelo: "Producto",
    metodo: "obtenerProductos"
  },fn);
};

$(document).ready(function(){
  app.init();
});

