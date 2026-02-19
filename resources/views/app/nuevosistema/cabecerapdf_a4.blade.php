
  <header>
    <div style="float:left;">{{ $tienda->nombre }} | {{ $tienda->nombreagencia }}</div> {{ Auth::user()->codigo }} | {{ date('d-m-Y H:iA') }}
  </header>
  <footer>
    <p class="page">Página </p>
  </footer>
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
        font-size:15px !important;
        font-weight: bold;
        margin:5px;
        text-align:right;
        padding:5px;
    }

    /** Definir las reglas del pie de página **/
    footer {
        position: fixed; 
        bottom: 0cm; 
        left: 0.7cm; 
        right: 0.7cm;
        height: 1cm;

        /** Estilos extra personales **/
        color: #000;
        text-align: center;
        line-height: 0.4cm;
        font-size:11px;
    }
    /** Definir las reglas de numeracion de página **/ 
    footer > .page:after { content: counter(page, decimal-leading-zero); }
    .page {
        float: right;
    }
  </style>