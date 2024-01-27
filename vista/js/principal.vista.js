var app = {},
  _PRODUCTOS_PAGINA = screen.width >= 900 ? 8 : 6,
  _PRODUCTOS = [],
  _ACTUAL_PAG = 0,
  _FINAL_PAG = 0,
  _PRODUCTO_INFO_CACHE = [],
  _CATEGORIAS = [];

app.init = function(){
  this.setDOM();
  this.setTemplates();
  this.setEventos();
  this.obtenerDataCatalogo();
  this.listarProductos();
};

app.setDOM = function(){
  var DOM = Util.preDOM2DOM($(".main-content"), 
                    [ {"txtBuscar":"#txtbuscar"},
                      {"cboFiltroTipo":"#cbofiltrotipo"},
                      {"cboFiltroCategoria":"#cbofiltrocategoria"},
                      {"blkListaProductos": "#blklistaproductos"},
                      {"mdlDetalle": "#mdlDetalle"},
                      {"imgProductoPrincipal": "#imgproductoprincipal"},
                      {"blkInformacion": "#blkinformacionproducto"},
                      {"blkCarruselImg": "#blkcarruselimg"}
                      ]);  

  DOM.paginacion = $(".pagination");

  this.DOM = DOM;
};

app.setTemplates = function(){
  this.tpl8 = Util.Templater($("script[type=handlebars-x]"));
};

app.setEventos  = function(){
  var self = this,
      DOM = self.DOM;

  DOM.txtBuscar.on("keyup", function(e){
    self.listarProductos();
  });

  DOM.cboFiltroTipo.on("change", function(){
    self.cargarCategorias(this.value);
    self.listarProductos();
  });

  DOM.cboFiltroCategoria.on("change", function(){
    self.listarProductos();
  });

  DOM.blkCarruselImg.on("click",".item", function(e){
    self.seleccionarImagenInfo(this.children[0]);
  });
  /*
  self.DOM.frmIniciar.on("submit", function(e){
    e.preventDefault();
    self.iniciarSesion();
  });*/
};

app.cargarCategorias = function(codTipo){
    var self = app, DOM = self.DOM;
    if (codTipo == ""){
      self.DOM.cboFiltroCategoria.html(app.tpl8.Combo([]));
      return;
    }
    self.DOM.cboFiltroCategoria.html(app.tpl8.Combo(ArrayUtils.conseguirTodos(_CATEGORIAS,"cod_tipo_categoria", codTipo)));  
  };

app.limpiar = function(){
  this.DOM.frmIniciar[0].reset();
};

app.obtenerDataCatalogo = function(){
  var self = this,
      DOM = self.DOM,
      tpl8 = self.tpl8.Combo,
      fn = function (xhr){
        var datos = xhr.datos;
          if (datos.rpt) {
            var data  = datos.data;
            DOM.cboFiltroTipo.html(tpl8(data.tipos));
            _CATEGORIAS = data.categorias;
            DOM.cboFiltroCategoria.html(tpl8([])); //data.categorias
          }else{
            swal("Error", datos.msj, "error");
          }
      };

  new Ajxur.Api({
    modelo: "Producto",
    metodo: "obtenerDataMantenimiento"
  },fn);
};


app.listarProductos = function(){
  var self = this,
      DOM = self.DOM,
      fn = function (xhr){
        var datos = xhr.datos;
          if (datos.rpt) {
            self.renderProductos(datos.data, DOM);
          }else{
            swal("Error", datos.msj, "error");
          }
      };

  new Ajxur.Api({
    modelo: "Producto",
    metodo: "obtenerProductosCatalogo",
    data_in: {
      p_codTipoCategoria: DOM.cboFiltroTipo.val(),
      p_codCategoriaProducto: DOM.cboFiltroCategoria.val()
    },
    data_out : [DOM.txtBuscar.val()]
  },fn);
};

app.renderProductos = function(datos, DOM){
  _PRODUCTOS = datos;
  var tpl8Paginacion = this.tpl8.Paginacion,
      tpl8Listado = this.tpl8.ListaProducto,
      cantidadProd = _PRODUCTOS.length,
      paginas  = Math.ceil(cantidadProd / _PRODUCTOS_PAGINA),
      arPaginas = [];


  for (var i = 2; i <= paginas; i++) {
      arPaginas.push(i);
  };

  if (_FINAL_PAG != paginas){
    DOM.paginacion.html(tpl8Paginacion(arPaginas));
    _FINAL_PAG = paginas;
  }

  this.setPagina(1);
  //DOM.blkListaProductos.html(tpl8Listado(this.obtenerProductosXPagina(1)));  
  /*datos en _PRODUCTOS*/

};

app.obtenerProductosXPagina = function(numero_pagina){
  var productosCut = [],
      _desde = (numero_pagina * _PRODUCTOS_PAGINA) - _PRODUCTOS_PAGINA,
      _hasta = numero_pagina * _PRODUCTOS_PAGINA - 1;

  if (!_PRODUCTOS) { return [];}

  if (_hasta >= _PRODUCTOS.length){
    _hasta = _PRODUCTOS.length - 1;
  }

  for (var i = _desde; i <= _hasta; i++) {
      productosCut.push(_PRODUCTOS[i]);
  };

  return productosCut;
};

app.setPagina= function(numero_pagina){
  var DOM = this.DOM;

  DOM.blkListaProductos.html(this.tpl8.ListaProducto(this.obtenerProductosXPagina(numero_pagina)));

  if (_ACTUAL_PAG == numero_pagina)
    return;

  if (numero_pagina == 1){
    DOM.paginacion.find(".pag-first").addClass("disabled");
  } else {
    DOM.paginacion.find(".pag-first").removeClass("disabled");
  }

  if (numero_pagina == _FINAL_PAG){
    DOM.paginacion.find(".pag-last").addClass("disabled");
  } else {
    DOM.paginacion.find(".pag-last").removeClass("disabled");
  }

  var pags = DOM.paginacion.find(".pags");

  pags.removeClass("active");
  pags.eq(numero_pagina - 1).addClass("active");
  pags.eq(numero_pagina - 1 + _FINAL_PAG).addClass("active");
  _ACTUAL_PAG = numero_pagina;
};

app.previousPagina = function(){
  if (_ACTUAL_PAG == 1){
    return;
  }

  this.setPagina(_ACTUAL_PAG - 1);

};

app.nextPagina = function(){
  if (_ACTUAL_PAG == _FINAL_PAG){
    return;
  }

  this.setPagina(_ACTUAL_PAG + 1);
};


app.obtenerProducto = function(codProducto){
  var self = this,
      objProducto = ArrayUtils.conseguir(_PRODUCTO_INFO_CACHE, "id", codProducto),
      fn = function (xhr){
            var datos = xhr.datos,
                objProducto = datos.data;
                if (datos.rpt) {
                  self.renderProductoInfo(objProducto);
                  _PRODUCTO_INFO_CACHE.push(objProducto);
                }else{
                  swal("Error", datos.msj, "error");
                }
            };

  this.DOM.mdlDetalle.modal("show");

  if (objProducto != -1){
    self.renderProductoInfo(objProducto);
    return;
  }

  new Ajxur.Api({
    modelo: "Producto",
    metodo: "obtenerInformacion",
    data_in: {
      p_codProducto: codProducto
    }
  },fn);


};

app.seleccionarImagenInfo = function($img){
  var DOM = this.DOM;
  /*
  if (!$img){
    DOM.imgProductoPrincipal[0].src = "../imagenes/productos/default_producto.jpg";
    return;
  }
  */
  DOM.imgProductoPrincipal[0].src = $img.src;
  DOM.blkCarruselImg.find("img").removeClass("img-thumbnail-selected");
  $img.classList.add("img-thumbnail-selected");


};

app.renderProductoInfo = function(objProducto){
  var self = this,
      DOM = self.DOM,
      tpl8 = self.tpl8,
      firstItem;

    DOM.blkInformacion.html(tpl8.Info(objProducto));
    DOM.blkCarruselImg.html(tpl8.Items(objProducto.imagenes));

    firstItem = DOM.blkCarruselImg.find(".item").eq(0);

    if (firstItem.length > 0){
      firstItem.click();
    } else {
      DOM.imgProductoPrincipal[0].src = "../imagenes/productos/default_producto.jpg";  
    }
};

app.verPrecio = function(thisBTN){
  $(thisBTN).prev().show();
};


$(document).ready(function(){
  new AccesoAuxiliar(()=>{
   // app.init();
  });
  /*
  if (!MOSTRAR_PRECIOS && localStorage.getItem("___jp_mas18") == null){
    swal({
          title: "Verificación de edad",
          text: "CONFIRMA TU EDAD PARA VER EL CONTENIDO.",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'SOY MAYOR DE 18',
          cancelButtonText: "SOY MENOR DE 18",
          closeOnConfirm: false,
          closeOnCancel: true,
          imageUrl: "../images/mas18.png"
        },
        function(isConfirm){ 
          if (isConfirm){
            localStorage.setItem("___jp_mas18", true);
            swal.close();
            app.init();
          } else{
            location.href = "error403.php";
          }
      });
  } else {
     app.init();
  }
  */
});

