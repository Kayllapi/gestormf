@extends('layouts.backoffice.master')
@section('cuerpobackoffice') 
    <div class="dashboard-list-box fl-wrap">
      <div class="dashboard-header fl-wrap mx-dashboard-list">
           <div class="mx-header-title">Mis Ofertas Reservadas</div>
           <div class="header-search vis-header-search mx-header-search">
             <form action="{{ url('backoffice/reservaoferta') }}" method="GET">
               <div class="header-search-input-item">
                   <input type="text" value="{{ isset($_GET['search_input']) ? $_GET['search_input'] : '' }}" name="search_input" placeholder="Buscar..."/>
               </div>
               <button class="header-search-button mx-header-search-button" type="submit">Buscar</button>
             </form>
           </div>
       </div>
      @foreach($ofertas as $value)
        <div class="dashboard-list">
            <div class="dashboard-message">
                <span class="new-dashboard-item">
                  <i class="fa fa-calendar"></i> {{ date_format(date_create($value->fecharegistro), 'd-m-Y h:i A') }}     
                </span>
                <div class="dashboard-listing-table-image">
                    <?php $rutaimagen = getcwd().'/public/backoffice/tienda/'.$value->idtienda.'/oferta/'.$value->imagen; ?>
                   @if(file_exists($rutaimagen) AND $value->imagen!='')
                       <img src="{{ url('redimensionar/tienda/oferta/225/180/'.$value->idtienda.'/'.$value->imagen) }}" style="height: 180px;">
                   @else
                       <img src="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}" style="height: 180px;">
                   @endif
                </div>
                <div class="dashboard-listing-table-text">
                    <h4>{{ $value->nombre }}</h4>
                    <span class="dashboard-listing-table-address">
                      <i class="fa fa-home"></i>{{ $value->tiendanombre }} - {{ $value->categorianombre }}<br>
                      <i class="fa fa-phone"></i> {{ $value->tiendanumerotelefono }} <br>
                      <i class="fa fa-map-marker"></i> &nbsp;{{ $value->tiendadireccion }}
                    </span>
                    <span class="listing-rating card-popup-rainingvis fl-wrap">
                      <i class="fa fa-ticket-alt"></i> {{ $value->reservaofertacantidad }} Cupones de Oferta    
                    </span>
                    <ul class="dashboard-listing-table-opt  fl-wrap">
                      @if($value->idestadooferta==1)
                      <li><a href="{{ url('backoffice/reservaoferta/'.$value->idreservaoferta.'/edit?view=editcodeqr') }}"><i class="fa fa-qrcode"></i> Escanear QR</a></li>
                      <li><a href="{{ url('backoffice/reservaoferta/'.$value->idreservaoferta.'/edit?view=eliminar') }}" class="del-btn"><i class="fa fa-trash"></i> Eliminar Reserva</a></li>
                      @endif
                  </ul>
                </div>
            </div>
        </div>
      @endforeach
    </div>
@endsection
    @section('scriptsbackoffice')
    <style>
    .new-dashboard-item {
        background-color: transparent;
        border: 1px solid #1877b7;
        color: #1877b7;
    }
    .listing-rating i {
        color: #1baf5a;
    }
    </style>
    @endsection