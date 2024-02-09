const ProductTablePicker = function({
    _productos : [], 
    _tipoFlujo, // "-" / "+"
    _$tblBuscar,
    _$mdlBuscar,
    _$txtBuscar,
    _$blkProductos,
    _$lblTotal,
    _tpl8
}){
    let _VACIO = true;
    const _INDEX = {
      "eliminar": 0,
      "producto": 1,
      "marca" : 2,
      "lote": 3,
      "precio_unitario": 4,
      "cantidad": 5,
      "subtotal": 6
    };

    const init = () => {
        this._productos = _productos;
        this._tipoFlujo = _tipoFlujo;
        this._$tblBuscar = _$tblBuscar;
        this._$mdlBuscar = _$mdlBuscar;
        this._$txtBuscar = _$txtBuscar;
        this._$blkProductos = _$blkProductos;
        this._$lblTotal = _$lblTotal;

        return this;
    };

    this.prepararAgregarProductos = () => {
        this._productos = this._productos.map( p => {
          return {
            ...p, seleccionado: false
          }
        })
  
        this._$mdlBuscar.modal("show");
        this._$txtBuscar.val("");
        this._$txtBuscar.focus();
        this._$txtBuscar.select();
  
        realizarBusquedaProducto("");
    };

    this.getProducto = function(id_producto){
      return ArrayUtils.conseguirPID(this._productos, "id", id_producto);
    };

    const agregarFilaDetalle = (dataFila) => {
      if (!dataFila){
        dataFila = {
          id_producto: null,
          marca : "",
          lote: "",
          precio_unitario: "0.00",
          cantidad: 1,
          subtotal: "0.00"
        };
      }
  
      const $nuevoDetalle = $(_tpl8.tblDetalle(dataFila));
      this._$tblDetalle[!_VACIO ? "append" : "html"]($nuevoDetalle);
      $nuevoDetalle.find(".pointer").click();
      _VACIO = false;
      return dataFila;
    };

    const realizarBusquedaProducto = function(cadena){
        var DOM = self.DOM;
        /*
        if (cadena == ""){
          //DOM.blkListaProductos.html('<div class="alert alert-info"><strong>Realice la búsqueda del producto a vender.</strong></div>');
          return;
        }
        */
        if (cadena == "" || cadena.length >= 3){
          var parametrosBusqueda = [{
              propiedad: "nombre_producto",
              valor: cadena,
              mayusculas : true,
              exactitud : false
            },
            { propiedad : "id_tipo_categoria",
              valor : DOM.cboTipo.val(),
              mayusculas: false,
              exactitud : true
            },
            { propiedad : "id_categoria",
              valor : DOM.cboCategoria.val(),
              mayusculas: false,
              exactitud : true
            }
          ];
  
        const productos = ArrayUtils.buscarTodos(this._productos, parametrosBusqueda);
        this._$blkProductos.html(_tpl8.ListaProducto(productos));
        }
    };

    const modificarTotalGeneral = function(arregloTR){
      /*recorrer todos los TR, obtener ultimos valores (subtotal), sumarlos
          obtener DescuentoTotal
          obtener Total
          */
        let total = 0.00;
  
        if (!arregloTR){
            arregloTR = this._$tblDetalle.find("tr:not(.tr-null)").toArray();
        }
  
        for (let i = arregloTR.length - 1; i >= 0; i--) {
          total += parseFloat(arregloTR[i].children[_INDEX.subtotal].innerHTML);
        };
  
        //DOM.lblDescuento.html();
        this._$lblTotal.html(parseFloat(total).toFixed(2));
    };

    const eliminarFilaDetalle = ($tr) => {
      const index = _INDEX,
          arregloTD = [].slice.call($tr.children),
          cantidad = arregloTD[index.cantidad].children[0].value,
          itemProducto = this.getProducto(arregloTD[index.producto].dataset.producto);
      const arregloTR = this._$tblDetalle.find("tr:not(.tr-null)").toArray();
  
      $tr.remove();
  
      if (itemProducto.i >= 0){
        modificarStockProducto(itemProducto,cantidad, "-");  
      }
  
      const numFila = arregloTR.length;
      if (numFila <= 0){
        _VACIO = true;
        this._$tblDetalle.html(_tpl8.tblDetalle());
      }
  
      modificarTotalGeneral(arregloTR);
    };

    const modificarStockProducto = (itemProducto, cantidad, tipo) => {
      /*tipo == + / -*/
        const objProducto = itemProducto.o;
        const stock = objProducto.stock;
        const nuevoStock  = parseInt(stock) + parseInt(cantidad  * (tipo == "+" ? 1 : -1));
        let exceso = 0;
  
        if (nuevoStock < 0){
          exceso = nuevoStock * -1;
          nuevoStock = 0;  
        }
  
        this._productos[itemProducto.i].stock = nuevoStock;
        return {viejo: stock, exceso: exceso, nuevo: nuevoStock};
    };
    
    const modificarCantidadDetalle = function($tr, $cantidad_, $precio_){
      /*obtener producto, actualizarse la cantidad, mdoifcar subtotal, modificar gran sub total*/
      const index = _INDEX,
          $cantidad = ($cantidad_ == null ? arregloTD[index.cantidad].children[0] : $cantidad_),
          cantidadAnterior = $cantidad.dataset.preval, 
          cantidadNueva = $cantidad.value,
          arregloTD = [].slice.call($tr.children);
  
      const itemProducto = this.getProducto(arregloTD[index.producto].dataset.producto),
          $subtotal = arregloTD[index.subtotal],
          $precio_unitario = ($precio_ == null ? arregloTD[index.precio_unitario].children[0] : $precio_),
          valorPrecio = parseFloat($precio_unitario.value).toFixed(2),
          cantidadVender = cantidadNueva - cantidadAnterior,
          stockModificado = modificarStockProducto(itemProducto, cantidadVender, _tipoFlujo);
  
      if (_tipoFlujo === "-"){
        if (stockModificado.nuevo < 0){
          cantidadNueva = cantidadNueva - stockModificado.exceso;
          $cantidad.value = cantidadNueva;
        }
      }

      const subtotal = valorPrecio * cantidadNueva;
      $cantidad.dataset.preval = cantidadNueva;
      modificarSubTotalDetalle( subtotal, $subtotal);
      /*
      const index = _INDEX,
          cantidadAnterior = $cantidad.dataset.preval, 
          cantidadNueva = $cantidad.value,
          arregloTD = [].slice.call($tr.children);

      var $cantidad = ($cantidad_ == null ? arregloTD[index.cantidad].children[0] : $cantidad_),
          itemProducto = self.getProducto(arregloTD[index.producto].dataset.producto),
          $subtotal = arregloTD[index.subtotal],
          $precio_unitario = ($precio_ == null ? arregloTD[index.precio_unitario].children[0] : $precio_),
          valorPrecio = parseFloat($precio_unitario.value).toFixed(2),
          cantidadVender = cantidadNueva - cantidadAnterior,
          stockModificado = modificarStockProducto(itemProducto, cantidadVender, "+"),
          subtotal;
  
          //if (stockModificado.nuevo < 0){
          //  cantidadNueva = cantidadNueva - stockModificado.exceso;
          //  $cantidad.value = cantidadNueva;
          //}
  
          subtotal = valorPrecio * cantidadNueva;
          $cantidad.dataset.preval = cantidadNueva;
          modificarSubTotalDetalle( subtotal, $subtotal);
          */
    };

    const modificarPUDetalle = function($tr, $precio){
      /*obtener producto, actualizarse la cantidad, mdoifcar subtotal, modificar gran sub total*/
      const index = _INDEX,
            precioNuevo = $precio.value,
            arregloTD = [].slice.call($tr.children);
  
      const $subtotal = arregloTD[index.subtotal],
            $cantidad = arregloTD[index.cantidad].children[0],
            valorCantidad = parseFloat($cantidad.value).toFixed(2);
          
      const subtotal = precioNuevo * valorCantidad;
      $precio.dataset.preval = precioNuevo;
      modificarSubTotalDetalle( subtotal, $subtotal);
    };
  
    const modificarSubTotalDetalle = function(subtotal, $subtotal){
        $subtotal.innerHTML =  parseFloat(subtotal).toFixed(2);
        modificarTotalGeneral();
    };  
  
    return init();
};
