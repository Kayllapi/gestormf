
  <header>
    <div style="float:left;font-size:18px;">{{ $tienda->nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <div class="footer">
    <p class="page">Página </p>
  </div>
  <style>
    
      header {
          position: fixed;
          top: 0cm;
          left: 0.7cm;
          right: 0.7cm;
          height: 0.6cm;
          /** Estilos extra personales **/
          color: #676869;
          text-align: center;
          line-height: 0.6cm;
          font-size:18px !important;
          font-weight: bold;
          border-bottom: 2px solid #144081; 
          margin:5px;
          text-align:right;
          padding:5px;
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
          border-top: 2px solid #31353d;
      }
      .page {
          float: center;
      }
      .footer .page:after { content: counter(page, decimal-leading-zero); }
  </style>