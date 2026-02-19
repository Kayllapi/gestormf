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
        color: #0f0f0f;
        text-align: center;
        line-height: 0.6cm;
        font-size:15px !important;
        font-weight: bold;
        margin:5px;
        text-align:right;
        padding:5px;
    }
    footer {
        position: fixed; 
        bottom: 0cm; 
        left: 0.7cm; 
        right: 0.7cm;
        height: 1cm;
        color: #000;
        text-align: center;
        line-height: 0.4cm;
        font-size:12px;
    }
    footer > .page:after { content: counter(page, decimal-leading-zero); }
    .page {
        float: right;
    }
</style>