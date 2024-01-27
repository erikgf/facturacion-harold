<?php 

/** Incluye PHPExcel */
require_once '../negocio/util/Funciones.php';           
require_once '../datos/config_facturacion.php';
require_once '../negocio/Cotizacion.clase.php';

  $codTransaccion =  isset($_GET["p_t"]) ? $_GET["p_t"] : '';

  if ($codTransaccion == ""){
    echo("ID de Cotización no enviado.");
    exit;
  }

  try {
    $objTransaccion = new Transaccion();
    $objTransaccion->setCodTransaccion($codTransaccion);
    $dataCotizacion = $objTransaccion->obtenerCotizacionDataPDF();
    if ($dataCotizacion["rpt"] == false){
      print($dataCotizacion["msj"]);
      exit;
    }

    $data = $dataCotizacion["data"];
    $cabecera = $data["cabecera"];
    $detalle = $data["detalle"];

  } catch (Exception $e) {
    var_dump($e);
    exit;
  }
 ?>

<html lang="es">
<head>
  <title>Cotización N°: <?php echo $cabecera["correlativo"]; ?></title>
  <style type="text/css">
    :root{
      --primary-color: rgb(28, 188,224);
      --secondary-color: rgb(230, 186, 61);
    }

    body {
          width: 100%;
          height: 100%;
          margin: 0;
          padding: 0;
          background-color: #FAFAFA;
          font: 10pt "Arial";
          -webkit-print-color-adjust:exact;
      }

      * {
          box-sizing: border-box;
          -moz-box-sizing: border-box;
      }

      .page {
          width: 210mm;
          min-height: 297mm;
          padding: 20mm 15mm;
          margin: 10mm auto;
          border: 1px #D3D3D3 solid;
          border-radius: 5px;
          background: white;
          box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
          position: relative;
      }
      .subpage {
          padding: 1cm;
          border: 5px red solid;
          height: 257mm;
          outline: 2cm #FFEAEA solid;
      }
      
      @page {
          size: A4;
          margin: 0;
      }

      @media print {
          html, body {
              width: 210mm;
              height: 297mm;        
          }
          .page {
              margin: 0;
              border: initial;
              border-radius: initial;
              width: initial;
              box-shadow: initial;
              background: initial;
              position:relative;
              page-break-after: always; 
          }

          .btnimprimir{
            display: none;
          }

      }


      .btnimprimir{
         position: absolute;
         left: 50%;
         top: 2cm;
      }

    .flex{
      display: flex;
    }

    .flex-auto{
      flex: auto;
    }

    .logo{
      width: 5cm;
      height: 2.5cm;
    }

    .titulo_bloque_izquierda{
      flex: auto;
    }

    .fila-cabecera{
      padding: .15cm 0px;
      font-size: 11pt;
    }

    .text-right{
      text-align: right;
    }

    .titulo-izquierda{
      text-align: left;
      font-weight: bold;
      color: var(--primary-color);
      font-size: 17pt;
      margin-bottom: .15cm;
    }

    .subtitulo-izquierda{
      font-size: 11pt;
    } 

    .separador{
      margin-top: .75cm;
      margin-bottom: .5cm;
      color: var(--primary-color);
      height: .05cm;
      border: none;
    }

    .etiqueta{
      font-size: 10pt;
      color: var(--primary-color);
      margin-bottom: .2cm;
    }

    .top-border-colored{
      background-color: var(--primary-color);
      height: .75cm;
      position: absolute;
      top:0;
      left:0;
      width: 100%;
    }

    .titulo-centrado{
      font-size: 18pt;
      color: var(--primary-color);
      text-align: center;
      width: 100%;
      margin-top: 1.5cm;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
      margin-bottom: 20px;
    }

    table thead th{
      background-color: var(--primary-color);
      color: white;
      padding: 5px;
      font-size: 8pt;
      border: solid 1px white;
    }

    table tbody td{
      vertical-align: top;
      padding: 7.5px;
      font-size: 8pt;
      text-align: center
    }

    .td-item{
      width: 1.2cm;
    }

    .td-producto{
    }

    .td-marca{
      width: 3.5cm;
    }

    .td-cantidad{
      width: 2.1cm;
    }

    .td-preciounitario{
      width: 2.25cm;
    }

    .td-monto{
      width: 2.25cm;
    }

    #triangle-bottomleft {
      width: 0;
      height: 0;
      position: absolute;
      left: 6.5cm;
      bottom: 0;
      border-top: 153px solid white;
      border-left: 153px solid transparent;  
    }

    #triangle-bottomleftmini {
      width: 0;
      height: 0;
      position: absolute;
      left: 9cm;
      bottom: 0;
      border-top: 56px solid var(--secondary-color);
      border-left: 57px solid transparent;
    }
      
    .bottom-border-colored-left{
      position: absolute !important;
      background-color: var(--primary-color);
      bottom: 0;
      left: 0;
      width: 50%;
      color: white;
      padding: 12px;
    }

    .bottom-border-colored-right{
      bottom: 0;
      position: absolute !important;
      right: 0;
      width: 50%;
      background-color: var(--secondary-color);
      color: white;
      padding: .3cm;
      text-align: center;
    }

    .text-left {
      text-align: left;
    }

  </style>
</head>
<body>
  <div class="book">
    <a href="#" class="btnimprimir" onclick="print();">Imprimir</a>
      <div class="page">
        <div class="top-border-colored"></div>
        <div class="flex" >
          <div class="flex-auto">
            <div class="titulo-izquierda"><?php echo F_RAZON_SOCIAL_COTIZACION; ?></div>
            <div class="subtitulo-izquierda">RUC: <?php echo F_RUC; ?></div>
            <div class="subtitulo-izquierda"><?php echo F_DIRECCION_COTIZACION; ?></div>
            <div class="subtitulo-izquierda"><?php echo F_TELEFONO_COTIZACION; ?></div>
          </div>
          <img class="logo" src="../imagenes/logo.jpeg"/>
        </div>

        <hr class="separador">

        <div class="flex" >
          <div class="flex-auto">
            <div class="etiqueta">Datos del Cliente:</div>
            <div class="subtitulo-izquierda"><?php echo $cabecera["nombre_cliente"]; ?></div>
            <div class="subtitulo-izquierda"><?php echo $cabecera["direccion_cliente"] ? $cabecera["direccion_cliente"] : "-"; ?></div>
            <br>
            <div class="etiqueta">Núm. Contacto:</div>
            <div class="subtitulo-izquierda"><?php echo $cabecera["numero_contacto"]; ?></div>
            <br>
            <div class="etiqueta">Correo:</div>
            <div class="subtitulo-izquierda"><?php echo $cabecera["correo"]; ?></div>
          </div>
          <div class="flex-auto">
            <div class="etiqueta">Fecha de la Cotización:</div>
            <div class="subtitulo-izquierda"><?php echo $cabecera["fecha_emision"]; ?></div>
          </div>
        </div>

        <h4 class="titulo-centrado">COTIZACIÓN N° <?php echo $cabecera["correlativo"] ?></h4>

        <table>
          <thead>
            <tr>
                <th scope="col" class="td-item">Item</th>
                <th class="td-producto" scope="col">Descripción</th>
                <th class="td-marca" scope="col" >Marca</th>
                <th class="td-cantidad" scope="col" style="">Cant.</th>
                <th class="td-preciounitario" scope="col" style="">Precio Unit.</th>
                <th class="td-monto" scope="col" style="">Monto</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($detalle as $key => $registro_detalle) :  ?>
            <tr>
              <td><?php echo $registro_detalle["item"]; ?></td>
              <td class="text-left"><?php echo $registro_detalle["nombre_producto"]; ?>
                  <br> 
                  <?php echo $registro_detalle["fecha_vencimiento"] == "0000-00-00" ? "" : " <b>FV:</b> ".$registro_detalle["fecha_vencimiento"]; ?>
                  <?php echo $registro_detalle["lote"] == "" ? "" : " <b>Lote:</b>  ".$registro_detalle["lote"]; ?>
              </td>
              <td class="text-left"><?php echo $registro_detalle["marca"]; ?></td>
              <td><?php echo $registro_detalle["cantidad_item"]; ?></td>
              <td><?php echo $registro_detalle["precio_unitario"]; ?></td>
              <td><?php echo $registro_detalle["subtotal"]; ?></td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>

        <div class="flex" style="padding-top:24px;font-size:12pt">
          <div class="flex-auto text-right"><b>Subtotal: </b>S/ <?php echo $cabecera["subtotal"]; ?></div>
        </div>
        <div class="flex" style="padding-top:24px;font-size:12pt">
          <div class="flex-auto text-right"><b>I.G.V. 18.0%: </b>S/ <?php echo $cabecera["igv"]; ?></div>
        </div>
        <div class="flex" style="padding-top:24px; font-size:16pt">
          <div class="flex-auto text-right"><b>Total: S/ <?php echo $cabecera["total"]; ?></b></div>
        </div>

        <style type="text/css">
        .li-condiciones{
          font-size:11pt;
          padding:2.5px;
        }

        .condiciones-forma-pago{
         font-size:12.5pt;font-weight:bold; margin-top:5px;padding-bottom:5px;
        }

        .rotulo-cta-bcp{
          position: absolute;
          left: 5.8cm;
          font-size: 9.5pt;
          text-align: center;
          bottom: .25cm;
          width: 4cm;
        }
        </style>

        <div class="bottom-border-colored-left">
          <div class="condiciones-forma-pago">Condiciones y forma de pago</div>
          <div class="li-condiciones">* Crédito: <?php echo $cabecera["condicion_dias_credito"]; ?> días</div>
          <div class="li-condiciones">* Validez: <?php echo $cabecera["condicion_dias_validez"]; ?> días</div>
          <div class="li-condiciones">* Entrega: <?php echo $cabecera["condicion_dias_entrega"]; ?> días</div>
          <div class="li-condiciones">* Delivery: <?php echo $cabecera["condicion_delivery"] === "0" ? $cabecera["condicion_delivery"]." días" : "Gratis" ?> </div>
          <div class="rotulo-cta-bcp">
            <div style="padding: 2.5px;">CTA. CTE. BCP</div>
            <div style="font-size: 11pt;"><?php echo $cabecera["cta_bcp"] ?></div>
          </div>
          
        </div>
        <div id="triangle-bottomleft"></div>
        <div id="triangle-bottomleftmini"></div>
        <div class="bottom-border-colored-right">
          <div>CTA. CCI.</div>
          <div style="font-size: 12pt;"><?php echo $cabecera["cta_bcp_cci"] ?></div>
        </div>
      </div>
  </div>

  <script type="text/javascript">
    setTimeout(function(){
      print();
    }, 1000);
  </script>
</body>
</html>