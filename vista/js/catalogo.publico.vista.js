var app = {},
  _PRODUCTOS_PAGINA = screen.width >= 900 ? 8 : 6,
  _PRODUCTOS = [],
  _ACTUAL_PAG = 0,
  _FINAL_PAG = 0,
  _PRODUCTO_INFO_CACHE = [],
  _CATEGORIAS = [];

let debounceTimer;

app.init = function(){
  this.setDOM();
  this.setTemplates();
  this.setEventos();
  this.obtenerDataCatalogo();
  this.listarProductos();
};

app.setDOM = function(){
  const DOM = Util.preDOM2DOM($(".main-content"), 
                    [ {"txtBuscar":"#txtbuscar"},
                      {"cboFiltroMarca":"#cbofiltromarca"},
                      {"cboFiltroTipo":"#cbofiltrotipo"},
                      {"cboFiltroCategoria":"#cbofiltrocategoria"},
                      {"blkListaProductos": "#blklistaproductos"},
                      {"mdlDetalle": "#mdlDetalle"},
                      {"imgProductoPrincipal": "#imgproductoprincipal"},
                      {"blkInformacion": "#blkinformacionproducto"},
                      {"blkCarruselImg": "#blkcarruselimg"}
                      ]);  

  DOM.paginacion = $(".pagination");
  DOM.lblCargando = $("#lbl-cargando");

  this.DOM = DOM;
};

app.setTemplates = function(){
  this.tpl8 = Util.Templater($("script[type=handlebars-x]"));
};

app.setEventos  = function(){
  var self = this,
      DOM = self.DOM;

  DOM.txtBuscar.on("keyup", () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        this.listarProductos();
    }, 1500); 
  });

  DOM.cboFiltroMarca.on("change", function(){
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

  DOM.paginacion.on("click", ".pags a", ({currentTarget}) => {
    const url = currentTarget?.dataset.url;
    if (url === ""){
      return;
    }
    const params = new URL(url).searchParams;
    this.listarProductos(params.get("page"));
  })
};

app.cargarCategorias = function(codTipo){
    var self = app;
    if (codTipo == ""){
      self.DOM.cboFiltroCategoria.html(app.tpl8.Combo([]));
      return;
    }
    self.DOM.cboFiltroCategoria.html(app.tpl8.Combo(ArrayUtils.conseguirTodos(_CATEGORIAS,"id_tipo_categoria", codTipo)));  
  };

app.limpiar = function(){
  this.DOM.frmIniciar[0].reset();
};

app.obtenerDataCatalogo = async function(){
  try {
    const tpl8 = app.tpl8.Combo;
    const { data } = await apiAxios.get(`productos-catalogo-util`);
    this.DOM.cboFiltroTipo.html(tpl8(data.tipos));
    _CATEGORIAS = data.categorias;
    this.DOM.cboFiltroCategoria.html(tpl8([])); //data.categorias
    this.DOM.cboFiltroMarca.html(tpl8(data.marcas));

  } catch (error) {
    swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
    console.error(error);
  }
};

app.listarProductos = async function(pagina = 1){
  this.DOM.lblCargando.show();
  this.DOM.blkListaProductos.css({opacity : ".5"});
  try {
    const params = new URLSearchParams({
      q : this.DOM.txtBuscar.val().trim(),
      id_categoria: this.DOM.cboFiltroCategoria.val(),
      id_tipo_categoria : this.DOM.cboFiltroTipo.val(),
      id_marca : this.DOM.cboFiltroMarca.val(),
      page : pagina
    });

    const { data } = await apiAxios.get(`productos-catalogo?${params.toString()}`);
    this.renderProductos(data, this.DOM);

  } catch (error) {
    swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
    console.error(error);
  } finally {
    this.DOM.lblCargando.hide();
    this.DOM.blkListaProductos.css({opacity : "1"});
  }
};

app.renderProductos = function(dataPaginacion, DOM){
  const tpl8Paginacion = this.tpl8.Paginacion,
        tpl8Listado = this.tpl8.ListaProducto;
  const {
    data,
    links
  } = dataPaginacion;

  DOM.paginacion.html(tpl8Paginacion(links));
  DOM.blkListaProductos.html(tpl8Listado(data));
};

app.obtenerProducto = async function(codProducto){
  const objProducto = ArrayUtils.conseguir(_PRODUCTO_INFO_CACHE, "id", codProducto);
  if (objProducto != -1){
    this.renderProductoInfo(objProducto);
    return;
  }

  try {
    const { data } = await apiAxios.get(`productos-catalogo/${codProducto}`);
    this.renderProductoInfo(data);
    _PRODUCTO_INFO_CACHE.push(data);
  } catch (error) {
    swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
    console.error(error);
  }
};

app.seleccionarImagenInfo = function($img){
  var DOM = this.DOM;
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

    console.log({objProducto, firstItem});

    if (firstItem.length > 0){
      firstItem.click();
    } else {
      DOM.imgProductoPrincipal[0].src = "../imagenes/productos/default_producto.jpg";  
    }

    DOM.mdlDetalle.modal("show");
};

app.verPrecio = function(thisBTN){
  $(thisBTN).prev().show();
};


$(document).ready(function(){
  app.init();
});

