const StockProductos = function({$, tpl8, sucursales}){
  const data = {
    tipos: [],
    categorias : [],
    productosStock : []
  };
  let sucursalSeleccionada = null,
      DT = null;
      _productosNoRepetidos = [];
  
    this.init = function(){
      this.setDOM();
      this.setEventos();

      const idSucursal = document.getElementById("cbosucursal").value;
      sucursalSeleccionada = sucursales.find ( item => item.id == idSucursal);

      this.obtenerTipoCategorias();
      this.obtenerCategorias();
      this.obtenerProductosStock();
    };
  
    this.setData = function({key, value}){
      data[key] = value;
    };
  
    this.getData = function(){
      return data;
    };

    this.actualizarListaStockProducto = function(){
      _productosNoRepetidos = listaNoRepetidos();
    };
  
    this.actualizarLista = function(_dataProductos){
      _data.producto_stock = _dataProductos;
      self.actualizarListaStockProducto();
      self.listarProductos(self.DOM);
    };
  
    this.setDOM = function(){
      this.DOM = Util.preDOM2DOM($, 
                      [{"chkNoRepetir": "#chknorepetir"},
                        {"cboTipo": "#cbotipostock"},
                        {"cboCategoria": "#cbocategoriastock"},
                        {"tblStock": "#tblstock"}
                        ]);  
    };
  
    this.setEventos = function(){
      const DOM = this.DOM,
            fnListarProductos = (e) => {
              this.listarProductos(DOM);
            },
            fnListarProductosTipo = (e) => {
              fnListarProductos(e);
              this.cargarCategorias(e.currentTarget.value);
            };
  
      DOM.chkNoRepetir.on("change", fnListarProductos);
      DOM.cboTipo.on("change", fnListarProductosTipo);
      DOM.cboCategoria.on("change", fnListarProductos);
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

    this.obtenerProductosStock =  async  function () {
      try {
        const { data } = await apiAxios.get(`almacen/productos-stock/${sucursalSeleccionada?.id}`);
        this.setData({key: "productosStock", value: data});
        this.actualizarListaStockProducto();
        this.listarProductos();

      } catch (error) {
        console.error(error);
      }
    };
  
    this.cargarCategorias = function(codTipo){
      var DOM = this.DOM;
      if (codTipo == ""){
        DOM.cboCategoria.html(tpl8.Combo([]));
        return;
      }
      DOM.cboCategoria.html(tpl8.Combo(
          data.categorias.filter(item => {
            return item.id_tipo_categoria == codTipo
          })
      ));  
    };
  
    this.listarProductos = function(){
      const DOM = this.DOM,
        listaAFiltrar = DOM.chkNoRepetir[0].checked 
                        ? _productosNoRepetidos 
                        : data.productosStock,
        /*Zona de filtro*/
        listarFiltrada = this.filtrarListaProductos(listaAFiltrar, DOM.cboTipo.val() ?? "", DOM.cboCategoria.val() ?? "");
  
      if (DT) {DT.fnDestroy(); DT = null;}
      DOM.tblStock.html(tpl8.Stock(listarFiltrada));
      if (listarFiltrada.length > 0){
        DT = DOM.tblStock.find("table").dataTable({
                "aaSorting": [[0, "asc"]],
                responsive:true
              });
      }
    };
  
    this.filtrarListaProductos = function(lista, idTipo, idCategoria){
        return lista.filter(item=>{
          return (idTipo == "" ? true : item.producto.categoria.id_tipo_categoria == idTipo) &&
                (idCategoria == "" ? true : item.producto.id_categoria_producto == idCategoria);
        });
    };
  
    const listaNoRepetidos = function(){
      const lista = data.productosStock;
      let   listaNueva = [],
            lastProducto = null,
            lastStock,
            lastArregloPrecios;
      const fnProducto = function(objProducto, esProductoNuevo){
            //Genera un LastProducto.
              if (esProductoNuevo){
                lastProducto = objProducto;
                lastArregloPrecios = [];
                lastStock = 0;
              }
              lastStock = parseInt(lastStock) + parseInt(objProducto.stock);     
              lastArregloPrecios.push({stock: objProducto.stock, precio: objProducto.precio});
            },
            fnAgregarProductoNuevo = function(lastProducto){
              //Agregar el producto al arreglo ListaNueva
              let lastRotulo = "";
    
              for (let j = lastArregloPrecios.length - 1; j >= 0; j--) {
                  const objRotuloPrecio = lastArregloPrecios[j];
                  lastRotulo += "S/"+objRotuloPrecio.precio+" ("+objRotuloPrecio.stock+")";
                  if (j > 0){
                    lastRotulo += ", ";
                  }
              };
    
              listaNueva.push({
                id_producto : lastProducto.id_producto,
                producto: lastProducto.producto,
                stock: lastStock,
                rotulo: lastRotulo,
              });
            };
  
      if (!lista.length){
        return listaNueva;
      }

      let objProducto;
      for (let i = 0;  i < lista.length; i++) {
        objProducto = lista[i];
        if (lastProducto == null){
          fnProducto(objProducto, true);
          continue;
        }

        if (lastProducto?.producto?.nombre == objProducto?.producto?.nombre){
          /*Es un producto repetido.*/
          fnProducto(objProducto, false);
        } else {
          /*Agregar*/
          fnAgregarProductoNuevo(lastProducto);
          /*Es un producto nuevo*/
          fnProducto(objProducto, true);
        }
      };
      fnAgregarProductoNuevo(objProducto);
  
      return listaNueva;
    };

    this.actualizarListaProductos = function ({idSucursal}) {
      sucursalSeleccionada = {id: idSucursal};
      this.obtenerProductosStock();
    };
  
    return this.init();
  };
  