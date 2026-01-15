
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
          margin:5px;
          /** Estilos extra personales **/
          color: #000;
          text-align: center;
          line-height: 0.6cm;
          font-size:18px !important;
          font-weight: bold;
          border-bottom: 2px solid #144081; 
          text-align:right;
          padding:5px;
      }
      .footer { 
          position: fixed; 
          bottom: 0px; 
          left: 0.7cm;
          right: 0.7cm;
          height: 25px;   
          margin:5px;
          border-top: 2px solid #144081;
      }
      .page {
          float: right;
      }
      .footer .page:after { content: counter(page, decimal-leading-zero); }
  </style>