    <style>
      html, body {
          margin-top: 38px;
          margin-bottom: 28px;
      }
      /* PDF A4 */
      .header { 
          position: fixed; 
          top: -38px; 
          left: 0px; 
          right: 0px; 
          height: 50px; 
          margin:15px;
          margin-left: 50px;
          margin-right: 50px;
          padding-bottom:5px;
          border-bottom: 2px solid <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>; 
      }
      .footer { 
          position: fixed; 
          left: 0px; 
          bottom: -25px; 
          right: 0px; 
          height: 25px;   
          margin:15px;
          margin-left: 50px;
          margin-right: 50px;
          padding-top:5px;
          border-top: 2px solid <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>; 
      }
      .page {
          float: right;
      }
      .content {
          width:100%;
          margin-left: 50px;
          margin-right: 50px;
      }
      .content_pdf {
          width:100%;
          margin-left: 50px;
          margin-right:-8px;
      }
      .content_pdf table {
          margin:0px;
          padding:0px;
          border-collapse: collapse;
          margin-right: 55px;
      }
      .content_pdf table td {
          padding:3px;
          text-align:left;
      }
      .footer .page:after { content: counter(page, decimal-leading-zero); }
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
          width: 400px;
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
  <div class="footer">
        <p class="page">Página </p>
  </div>