    <style>
      html, body {
          margin: 0px;
          padding: 0px;
          font-size: 10px;
          font-family: <?php echo  configuracion($idtienda,'sistema_tipoletra')['resultado']=='CORRECTO'?configuracion($idtienda,'sistema_tipoletra')['valor']:'Courier New,Courier,monospace' ?>;
      }
      .ticket_contenedor {
          width: <?php echo  configuracion($idtienda,'sistema_anchoticket')['resultado']=='CORRECTO'?configuracion($idtienda,'sistema_anchoticket')['valor'].'cm':'7cm' ?>;
      }
      .tarjetapago_contenedor {
          width: <?php echo  configuracion($idtienda,'prestamo_tarjetapago_anchoimpresion')['resultado']=='CORRECTO'?configuracion($idtienda,'prestamo_tarjetapago_anchoimpresion')['valor'].'cm':'10cm' ?>;
      }
      .contenedor {
          padding: 15px;
      }
      .titulo {
          text-align: center;
          width: 100%;
          font-size: 13px;
          margin-top: 10px;
          margin-bottom: 10px;
          font-weight: bold;
      }
      .tabla {
          width: 100%;
          margin:0px;
          padding:0px;
          border-collapse: collapse;
      }
      .tabla td {
          border: 1px solid <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>60;
          padding:3px;
          text-align:left;
          /*white-space: nowrap;*/
      }
      .tabla_cabera td {
          border: 1px solid <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>;
          background-color: <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>;
          color: #fff;
          font-weight: bold;
          text-align:left;
      }
      .tabla_titulo {
          background-color: <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>40;
          font-weight: bold;
          text-align:left;
      }
      .tabla_resultado td {
          border: 1px solid <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>;
          background-color: <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>;
          color: #fff;
      }
      .tabla_informativa {
          width: 100%;
          border-collapse: collapse;
      }
      .tabla_informativa_subtitulo {
          font-weight: bold;
      }
      .tabla_informativa_punto {
          font-weight: bold;
      }
      .tabla_informativa_descripcion {
          
      }
      .tabla_informativa tr td {
          
      }
      .dato_adicional {
          text-align: center;
          font-weight: bold;
      }
      .dato_firma {
          text-align: center;
          margin-top: 80px;
          font-weight: bold;
      }
      /* OTROS */
      .espacio {
          width: 100%;
          height: 5px;
      }
      .mensaje_alerta {
          background-color: <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>;
          padding:8px;
          text-align: center;
          font-weight: bold;
          color: #fff;
      }
    </style>