var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Producto",
  _IMAGEN = 0, /*0: default, 1: misma, 2: nueva*/
  DT = null,
  maxCantidadImg = 10,
  _CATEGORIAS = [],
  arImg = [],
  _cache_busqueda = null;

app.init = function(){
  this.setDOM();
  this.setEventos();
  this.setTemplate();

  this.obtenerMarcas();
  this.obtenerTipoCategorias();
  this.obtenerUnidadMedidas();
  this.listar();
};

app.setDOM = function(){
  var DOM = {};

  DOM.listado = $("#listado");
  DOM.modal = $("#mdlRegistro");
  DOM.frmGrabar = $("#frmgrabar");
  DOM.mdlHeader = DOM.modal.find(".modal-header h3");

  DOM.cboEmpresaEspecial = DOM.frmGrabar.find("#cboempresaespecial");
  DOM.txtCodigoUnico = DOM.frmGrabar.find("#txtcodigounico");
  DOM.txtNombre = DOM.frmGrabar.find("#txtnombre");
  DOM.txtDescripcion = DOM.frmGrabar.find("#txtdescripcion");
  DOM.txtPrecioUnitario = DOM.frmGrabar.find("#txtpreciounitario");
  DOM.cboMarca = DOM.frmGrabar.find("#cbomarca");
  DOM.cboTipo = DOM.frmGrabar.find("#cbotipo");
  DOM.cboCategoria = DOM.frmGrabar.find("#cbocategoria");
  DOM.cboUnidadMedida = DOM.frmGrabar.find("#cbounidadmedida");
  DOM.cboPresentacion = DOM.frmGrabar.find("#cbopresentacion");
  DOM.txtTallas = DOM.frmGrabar.find("#txttallas");

  DOM.tabImgProductos = DOM.frmGrabar.find("#tabImgProductos");
  DOM.tabContent = DOM.frmGrabar.find(".tab-content-imagenes");
  DOM.cboImagenPrincipal = DOM.frmGrabar.find("#cboimagenprincipal");

  //DOM.txtImgUrl = DOM.frmGrabar.find("#txtimgurl");
  //DOM.imgUrl = DOM.frmGrabar.find("#imgurl");
  //DOM.spnBorrarProducto = DOM.frmGrabar.find(".borrar-img-producto");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;


  DOM.txtPrecioUnitario.on("change",function(e){
    const precio = parseFloat(DOM.txtPrecioUnitario.val()).toFixed(2);
    DOM.txtPrecioUnitario.val(precio);
  });

  DOM.txtPrecioUnitario.on("click",function(e){
    DOM.txtPrecioUnitario.select();
  });

  DOM.modal.on("hidden.bs.modal",function(e){
    e.preventDefault();
    self.limpiar();
    DT.fnFilter(_cache_busqueda);
  });

  DOM.modal.on("shown.bs.modal",function(e){
    e.preventDefault();
    _cache_busqueda = $("#DataTables_Table_0_filter").find("input[type=search]").val();
  });

  DOM.cboTipo.on("change", function(e){
    self.obtenerCategorias(this.value);
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
          $(".confirm").attr('disabled', 'disabled');
          if (isConfirm){
            self.grabar();
          }
      });
  });

  DOM.tabContent.on("change", ".on-cambiar-imagen", function ({currentTarget : input}) {
    app.cambiarImagen(input);
  });

  DOM.tabContent.on("click", ".borrar-img-producto", function ({currentTarget : a}) {
    app.cancelarImagen($(a).parents(".tab-pane"));
  });

  DOM.tabContent.on("click", ".on-imagen-defecto", function ({currentTarget : a}) {
    app.imagenDefecto($(a).parents(".tab-pane"));
  });
};

app.setTemplate = function(){
  var tpl8 = {};
  tpl8.listado = Handlebars.compile($("#tpl8Listado").html());
  tpl8.combo = Handlebars.compile($("#tpl8Combo").html());
  tpl8.tabPane = Handlebars.compile($("#tpl8TabPane").html());
  tpl8.tab = Handlebars.compile($("#tpl8Tab").html());
  this.tpl8 = tpl8;
};

app.limpiar = function(){
  var DOM = this.DOM;
  DOM.frmGrabar[0].reset();
  DOM.cboUnidadMedida.val("NIU");

  _ACCION = "agregar";
  _TEMPID = -1;
};

app.agregar = function(){
  _ACCION = "agregar";
  _TEMPID = -1;
  this.DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());
  this.llenarTabs();
};

app.editar = async function(idVenta){
  _ACCION = "editar";
  _TEMPID = idVenta;
  const DOM = this.DOM;
  
  DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());

  try {
    const { data } = await apiAxios.get(`productos/${idVenta}`);
    
    DOM.cboEmpresaEspecial.val(data.empresa_especial);
    DOM.txtCodigoUnico.val(data.codigo_generado);
    DOM.txtNombre.val(data.nombre);
    DOM.txtDescripcion.val(data.descripcion);
    DOM.cboMarca.val(data.id_marca);
    DOM.cboTipo.val(data.id_tipo_categoria);
    this.obtenerCategorias(data.id_tipo_categoria, data.id_categoria_producto);

    DOM.txtPrecioUnitario.val(data.precio_unitario);
    DOM.cboUnidadMedida.val(data.id_unidad_medida);
    //DOM.cboPresentacion.val(data.cod_presentacion);
    DOM.txtTallas.val(data.tallas);
    DOM.cboImagenPrincipal.val(data.numero_imagen_principal);

    this.llenarTabs(data.imagenes_procesadas);
   
  } catch (error) {
    swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
    console.error(error);
  }
};

app.eliminar = function(cod){
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
        async function(isConfirm){ 
          $(".confirm").attr('disabled', 'disabled');
          if (isConfirm){
            try {
              await apiAxios.delete(`productos/${cod}`);
              swal("Exito", "Eliminado con éxito.", "success");
              app.listar();
            } catch (error) {
              swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
              console.error(error);
            }
          }
      });
};

app.grabar = async function(){
      const DOM = this.DOM, datosFrm = new FormData();
      const objButtonLoading = new ButtonLoading({$: DOM.frmGrabar.find("button[type=submit]")[0]});
      const objDatosFormulario = Object.fromEntries(new FormData(DOM.frmGrabar[0]));

      datosFrm.append("empresa_especial", objDatosFormulario.cboempresaespecial);
      datosFrm.append("codigo_unico", objDatosFormulario.txtcodigounico);
      datosFrm.append("tallas", objDatosFormulario.txttallas);
      datosFrm.append("nombre", objDatosFormulario.txtnombre);
      datosFrm.append("descripcion", objDatosFormulario.txtdescripcion);
      datosFrm.append("precio_unitario", objDatosFormulario.txtpreciounitario);
      datosFrm.append("id_unidad_medida", objDatosFormulario.cbounidadmedida);
      //datosFrm.append("id_presentacion", objDatosFormulario.cboempresaespecial);
      datosFrm.append("id_marca", objDatosFormulario.cbomarca);
      datosFrm.append("id_categoria_producto", objDatosFormulario.cbocategoria);
      datosFrm.append("numero_imagen_principal", objDatosFormulario.cboimagenprincipal);

      for (let i = maxCantidadImg; i >= 1; i--) {
        const $bloque = DOM.tabContent.find(`[data-id=${i}]`);
        const $inputFile = $bloque.find(".on-cambiar-imagen");
        if ($inputFile.val()){
          datosFrm.append("imagenes[]", $inputFile[0].files[0]);
          datosFrm.append("imagenes_indices[]", i);
        }
      };

      objButtonLoading.start();

      try {
        const headersApiAxios =  {
            headers : { 'Content-Type' : 'multipart/form-data' }
        };
        
        const { data } = _ACCION === 'agregar' 
                            ?   await apiAxios.post(`productos`, datosFrm, headersApiAxios)
                            :   await apiAxios.post(`productos/${_TEMPID}?_method=PUT`, datosFrm, headersApiAxios);

        swal("Éxito", "Registrado correctamente.", "success");
        app.listar();  
        DOM.modal.modal("hide");

        //swal("Éxito", "Eliminado con éxito.", "success");
        //app.listar();
      } catch (error) {
        const { response } = error;
        if (Boolean(response?.data?.message)){
          swal("Error",response.data.message, "error");
          return;
        }
        console.error(error);
      } finally {
        objButtonLoading.finish();
      }
};

app.listar = async function(){
  const DOM = this.DOM, tpl8Listado = this.tpl8.listado;

  try {
    const { data } = await apiAxios.get('productos');
    if (DT) { DT.fnDestroy(); DT = null; }
      DOM.listado.html(tpl8Listado(data));
      DT = DOM.listado.find("table").dataTable({
        "aaSorting": [[0, "DESC"]],
        columnDefs: [{ type: 'num', targets: 4 }],
        responsive:true
      });

  } catch (error) {
    swal("Error", "Ocurrió un problema al cargar los UNIDADES DE MEDIDA.", "error");
    console.error(error);
  }
};

app.obtenerMarcas = async () => {
  const DOM = app.DOM, tpl8 = app.tpl8.combo;

  try {
    const { data } = await apiAxios.get('marcas');
    DOM.cboMarca.html(tpl8({items: data, rotulo: "marca"}));

  } catch (error) {
    swal("Error", "Ocurrió un problema al cargar las MARCAS.", "error");
    console.error(error);
  }
};

app.obtenerTipoCategorias = async () => {
  const DOM = app.DOM, tpl8 = app.tpl8.combo;

  try {
    const { data } = await apiAxios.get('tipo-categorias');
    DOM.cboTipo.html(tpl8({items: data, rotulo: "tipo de categoría"}));

  } catch (error) {
    swal("Error", "Ocurrió un problema al cargar los TIPOS DE CATEGORÍA.", "error");
    console.error(error);
  }
};

app.obtenerCategorias = async ( idTipoCategoria, idCategoria = null) => {
  const DOM = app.DOM, tpl8 = app.tpl8.combo;

  if (idTipoCategoria == ""){
    DOM.cboCategoria.empty();
    return;
  }

  try {
    const { data } = await apiAxios.get(`categorias/tipo/${idTipoCategoria}`);
    DOM.cboCategoria.html(tpl8({items: data, rotulo: "categoría"}));

    if (idCategoria){
      DOM.cboCategoria.val(idCategoria);
    }

  } catch (error) {
    swal("Error", "Ocurrió un problema al cargar los TIPOS DE CATEGORÍA.", "error");
    console.error(error);
  }
};

app.obtenerUnidadMedidas = async () => {
  const DOM = app.DOM, tpl8 = app.tpl8.combo;

  try {
    const { data } = await apiAxios.get('unidad-medidas');
    DOM.cboUnidadMedida.html(tpl8({items: data.map(r => {
      return {id: r.id, nombre: r.descripcion}
    }), rotulo: "unidad de medida"}));
    DOM.cboUnidadMedida.val("NIU");

  } catch (error) {
    swal("Error", "Ocurrió un problema al cargar los UNIDADES DE MEDIDA.", "error");
    console.error(error);
  }
};

app.llenarTabs = function(dataImagenes){
  const DOM = this.DOM,
      tpl8 = this.tpl8,
      tabImgProductos = DOM.tabImgProductos;

  /*Dataimagenes seria un arr => [numero_imagen, img_url, es_principal (active per default)*/
  const tabs = [], tabPanes = [], numeroImagen = DOM.cboImagenPrincipal.val();
  const cantidadImagenes = dataImagenes?.length;
  const contieneImagenes = dataImagenes && Boolean(cantidadImagenes);

  for (let i = 1; i <= maxCantidadImg; i++) {
      if (contieneImagenes){
        const objImagen = dataImagenes.find(item => item.numero_imagen === i);
        if (objImagen){
          const objImagenNI = objImagen.numero_imagen;
          const esActive =  objImagenNI == numeroImagen ? true : null;
          tabs.push({id: i, is_active: esActive});
          tabPanes.push({id: objImagenNI, img_url: objImagen.img_url, is_active: esActive});
          dataImagenes.splice(j,1);
          continue;
        }
      }

      const esActive = numeroImagen == i;
      tabs.push({id: i, is_active: esActive});
      tabPanes.push({id: i, img_url: "../imagenes/productos/default_producto.jpg", is_active: esActive});
  };
/*

  if (dataImagenes){
    for (var i = 1; i <= dataImagenes.length; i++) {
      var objDI = dataImagenes[i];
        tabs.push({i: i, is_active: objDI.es_principal});
        tabPanes.push({i: i, img_url: objDI.img_url, is_active: objDI.es_principal});
    };
  }
*/
  tabImgProductos.html(tpl8.tab(tabs));
  DOM.tabContent.html(tpl8.tabPane(tabPanes));
};

app.cambiarImagen = function(input){
   const $this = $(input),
        parent = $this.parents(".tab-pane"),
        spnBorrarProducto = parent.find(".borrar-img-producto"),
        imgUrl = parent.find("img");

    if (input.files && input.files[0]) {        
      //var num = id.substr(id.length - 1);
      const reader = new FileReader();
      reader.onload = function(e) {
        imgUrl.attr('src', e.target.result);
        //_IMAGEN = 2;
        spnBorrarProducto.show();
      };
      reader.readAsDataURL(input.files[0]);
    }
};

app.setImagenAccion = function($bloque, tipoAccion){
  //TipoAccion: 0 =>setDefecto, 1=>AnteriorImagen
  //const parent = $(a).parent(),
  const imgUrl = $bloque.find("img"),
        spnBorrarProducto = $bloque.find(".borrar-img-producto"),
        txtImg = $bloque.find(".on-cambiar-imagen");
  let src;
  //_IMAGEN = tipoAccion;
  imgUrl[0].dataset.imagen = tipoAccion;

  if (tipoAccion == 0){
    src = "../imagenes/productos/default_producto.jpg";
  } else {
    src = imgUrl[0].dataset.original;
  }

  imgUrl[0].src = src;
  txtImg.val("");
  spnBorrarProducto.hide();
}

app.cancelarImagen = function($bloque){
  this.setImagenAccion($bloque, 1);
};

app.imagenDefecto = function($bloque){
  this.setImagenAccion($bloque, 0);
};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});


