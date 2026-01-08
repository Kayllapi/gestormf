    <style>
      html, body {
          padding-top: 15px;
      }
      /* PDF A4 */
      .header {  
          height: 50px; 
          padding-bottom:5px;
          margin-top: -15px;
          border-bottom: 2px solid <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>; 
      }
      .header_agencia_logo {
          height: 50px;
          text-align: center;
          float: left;
          margin-right:10px;
      }
      .header_agencia_logo > img {
          display: block;
          max-width: 100%;
          height: 50px;
      }
      .header_agencia_informacion {
          float: right;
          width: 80%;
          text-align: right;
      }
      .header_agencia_nombrecomercial {
          font-size: 13px;
          font-weight: bold;
      }
      .header_agencia_ruc {
      }
      .header_agencia_direccion {
      }
      .header_agencia_ubigeo {
      }
      .header_agencia_telefono {
      }
    </style>
  <div class="header">
    <?php $logo1 = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$logo ?>
    <?php $logo2 = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/logo/'.$logo ?>
    @if(file_exists($logo1) && $logo!='')
        <div class="header_agencia_logo"><img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$logo) }}"></div>
    @elseif(file_exists($logo2) && $logo!='')
        <div class="header_agencia_logo"><img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$logo) }}"></div>
    @endif
    <div class="header_agencia_informacion">
        <div class="header_agencia_nombrecomercial">{{ strtoupper($nombrecomercial) }}</div>
        @if(isset($ruc))
        <div class="header_agencia_ruc">RUC: {{ $ruc }}</div>
        @endif
        <div class="header_agencia_direccion">{{ strtoupper($direccion) }}</div>
        <div class="header_agencia_ubigeo">{{ strtoupper($ubigeo) }}</div>
        @if(!isset($ruc))
        <div class="header_tienda_telefono">TELÉFONO DE OFICINA: {{ strtoupper($tienda->numerotelefono) }}</div>
        @endif
    </div>
  </div>