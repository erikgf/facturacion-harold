var App;
const app = function() {
    this.productos = [];
    this.productosFiltrados = [];

    this.init = () => {
      this.obtenerDataProductos();
      this.setDOM();
      this.setEventos();
    };


    this.setDOM =  () => {
      this.tpl8 = Util.Templater($("script[type=handlebars-x]"));
      this.tblListado = $("#tbl-listado");
      this.btnImprimir = $("#btn-imprimir");
      this.btnAgregar = $("#btn-agregar");
      this.mdlRegistro = $("#mdlBuscarProducto");
      this.txtBuscar = $("#txt-buscar");
      this.lblSeleccionados = $("#lbl-seleccionados");
      this.blkListaProductos = $("#blk-listaproductos");
      this.btnConfirmarProductos = $("#btn-confirmarproductos");
    };

    this.setEventos = () =>{

      this.btnAgregar.on("click", (e) => {
        e.preventDefault();
        this.mdlRegistro.modal("show");
      });


      this.mdlRegistro.on("show.bs.modal", (e) => {
        this.listarProductosSeleccionables(true);
      });

      this.txtBuscar.on("keyup", (e)=>{
        this.listarProductosSeleccionables();
      });

      this.tblListado.on("click", ".btn-quitar", function(e){
        e.preventDefault();
        $(this).parents("tr").remove();
      }); 

      this.blkListaProductos.on("click", "tr:not(.tr-null)", (e) => {
        this._seleccionarProductoBuscar($(e.currentTarget));
      });

      this.btnConfirmarProductos.on("click", (e)=>{
        e.preventDefault();
        this.cargarProductosSeleccionadosTabla();
      });

      this.btnImprimir.on("click", (e) => {
        e.preventDefault();
        this.imprimir();
      });

    };

    this.obtenerDataProductos = async () => {
      try {
        const { data } = await apiAxios.get(`productos`);

        this.productosFiltrados = data;
      } catch (error) {
        swal("Error", "Error al obtener los productos.", "error");
        console.error(error);
      }
    };

    this.listarProductosSeleccionables = (limpiarSeleccion = false) => {
      const cadenaBuscar = this.txtBuscar.val();

      if (limpiarSeleccion){
        this.productosFiltrados = this.productosFiltrados.map( p => {
          return {...p, seleccionado: false};
        });
        this.lblSeleccionados.html("0");
      }

      const productosFiltrados = this.productosFiltrados.filter(p => {
        return p.producto.includes(cadenaBuscar);
      });

      this.blkListaProductos.html(this.tpl8.ListaProducto(productosFiltrados));
      
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

      this.productosFiltrados = this.productosFiltrados.map( p => {
        if (p.id == idSeleccionado){
          return {
            ...p, seleccionado: !estaSeleccionado
          }
        }
        return p;
      });

      this.lblSeleccionados.html(this.productosFiltrados.filter(p=>p.seleccionado).length);
    };

    this.cargarProductosSeleccionadosTabla = () => {
      const productosSeleccionados = this.productosFiltrados.filter(p=>p.seleccionado);
      console.log({productosSeleccionados})
      this.tblListado.find("tbody").html(this.tpl8.Listado(productosSeleccionados));
      this.mdlRegistro.modal("hide");
    };

    this.imprimir = () => {

      const IDS = this.tblListado.find("tbody tr").toArray().map(tr => {
        return {
          id: tr.dataset.id,
          cantidad: tr.children[1].children[0].value
        }
      });
      
      if (!IDS.length){
        alert("No hay registros que imprimir.");
        return;
      }

      const sentData = {
        ids : IDS,
        key : JSON.parse(localStorage.getItem(SESSION_NAME)).token
      };
      


      Util.downloadPDFUsingPost({
        url: "../impresiones/productos.etiquetas.pdf.php",
        variableName: "p_data", 
        JSONData: JSON.stringify(sentData)
      });
    };

    return this.init();
};



$(document).ready(function(){
    new AccesoAuxiliar(()=>{
      App = new app();      
    });
  });
  