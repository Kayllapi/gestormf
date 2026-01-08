    <style>
      .agencia_logo {
          width: 100%;
          height: 50px;
          text-align: center;
          margin-bottom:5px;
      }
      .agencia_logo > img {
          display: block;
          max-width: 100%;
          height: 50px;
      }
      .agencia_nombrecomercial {
          font-size: 13px;
          text-align: center;
          font-weight: bold;
      }
      .agencia_ruc {
          text-align: center;
          font-weight: bold;
      }
      .agencia_direccion {
          text-align: center;
          font-weight: bold;
      }
      .agencia_ubigeo {
          text-align: center;
          margin-bottom:5px;
          font-weight: bold;
      }
    </style>

    @if(isset($logo))
    <?php $logo1 = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/sistema/'.$logo ?>
    <?php $logo2 = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/logo/'.$logo ?>
    @if(file_exists($logo1) && $logo!='')
        <div class="agencia_logo"><img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$logo) }}"></div>
    @elseif(file_exists($logo2) && $logo!='')
        <div class="agencia_logo"><img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$logo) }}"></div>
    @endif
    @endif
    @if(isset($nombrecomercial))
    <div class="agencia_nombrecomercial">{{ strtoupper($nombrecomercial) }}</div>
    @endif
    @if(isset($ruc))
    <div class="agencia_ruc">RUC: {{ $ruc }}</div>
    @endif
    @if(isset($direccion))
    <div class="agencia_direccion">{{ strtoupper($direccion) }}</div>
    @endif
    @if(isset($ubigeo))
    <div class="agencia_ubigeo">{{ strtoupper($ubigeo) }}</div>
    @endif