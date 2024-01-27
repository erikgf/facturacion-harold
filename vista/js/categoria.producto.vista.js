var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "CategoriaProducto",
  DT = null,
  _cache_busqueda = null;

app.init = function(){
  this.setDOM();
  this.setEventos();
  this.setTemplate();

  this.obtenerData();
  this.listar();
};

app.setDOM = function(){
  var DOM = {};

  DOM.listado = $("#listado");
  DOM.modal = $("#mdlRegistro");
  DOM.frmGrabar = $("#frmgrabar");
  DOM.mdlHeader = DOM.modal.find(".modal-header h3");

  DOM.txtNombre = DOM.frmGrabar.find("#txtnombre");
  DOM.txtDescripcion = DOM.frmGrabar.find("#txtdescripcion");
  DOM.cboTipo = DOM.frmGrabar.find("#cbotipo");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.modal.on("hidden.bs.modal",function(e){
    self.limpiar();
    DT.fnFilter(_cache_busqueda);
  });

  DOM.modal.on("shown.bs.modal",function(e){
    e.preventDefault();
    DOM.txtNombre.focus();
    _cache_busqueda = $("#DataTables_Table_0_filter").find("input[type=search]").val();

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
};

app.setTemplate = function(){
  var tpl8 = {};
  tpl8.listado = Handlebars.compile($("#tpl8Listado").html());
  tpl8.combo = Handlebars.compile($("#tpl8Combo").html());
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
  this.DOM.mdlHeader.html((_ACCION+" CATEGORÍA DE PRODUCTO").toUpperCase());
};

app.editar = async function(cod){
  _ACCION = "editar";
  _TEMPID = cod;
  const DOM = this.DOM;

  DOM.mdlHeader.html((_ACCION+" CATEGORÍA DE PRODUCTO").toUpperCase());

  try {
      const response = await apiAxios.get(`categorias/${cod}`);
      const { data } = response;

      DOM.txtDescripcion.val(data.descripcion);
      DOM.txtNombre.val(data.nombre);
      DOM.cboTipo.val(data.tipo_categoria?.id)

  } catch (error) {
      swal("Error", JSON.stringify(error), "error");
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
          if (isConfirm){
            try {
              await apiAxios.delete(`categorias/${cod}`);
              swal("Exito", "Eliminado con éxito.", "success");
              app.listar();
            
            } catch (error) {
              swal("Error", JSON.stringify(error), "error");
              console.error(error);
            }
          }
      });

};

app.grabar = async function(){
  const DOM = this.DOM;
  try {
    const sentData = {
      nombre: DOM.txtNombre.val(),
      descripcion: DOM.txtDescripcion.val(),
      id_tipo_categoria : DOM.cboTipo.val()
    };
    _ACCION === 'agregar' 
          ? await apiAxios.post('categorias', sentData)
          : await apiAxios.put(`categorias/${_TEMPID}`, sentData);

    swal("Exito", "Registrado con éxito.", "success");
    DOM.modal.modal("hide");
    app.listar();

  } catch (error) {
    swal("Error", JSON.stringify(error), "error");
    console.error(error);
  }
};

app.obtenerData = async function(){
  const DOM = this.DOM,
      tpl8 = this.tpl8.combo;

  try {
      const response = await apiAxios.get('tipo-categorias');
      const { data } = response;
      DOM.cboTipo.html(tpl8({items: data.map(r => {
        return {
          codigo: r.id,
          nombre: r.nombre
        }
      }), rotulo: "tipo de categoría"}));

  } catch (error) {
      swal("Error", JSON.stringify(error), "error");
      console.error(error);
  }
};

app.listar = async function(){
  const DOM = this.DOM,
      tpl8Listado = this.tpl8.listado;
  try {
    const response = await apiAxios.get('categorias');
    const { data } = response;
    if (DT) { DT.fnDestroy(); DT = null; }
    DOM.listado.html(tpl8Listado(data.map(r => {
      return {
        ...r,
        tipo_categoria: r.tipo_categoria?.nombre
      }
    })));
    DT = DOM.listado.find("table").dataTable({
      "aaSorting": [[0, "asc"]]
    });

  } catch (error) {
      swal("Error", JSON.stringify(error), "error");
      console.error(error);
  }
};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});

