@extends('layouts.backoffice.master')
@section('cuerpobackoffice')  

<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anuncios</span>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th width="10px"></th>
            <th>Nombre</th>
          </tr>
        </thead>
        <tbody>
          @foreach($anuncios as $value)
            <tr>
              <td>
                @if($value->imagen!='')
                <img src="{{ url('public/backoffice/consumidor/anuncio/'.$value->imagen) }}" class="td-imagen">
                @endif
              </td>
              <td style="white-space: inherit;">
                <div class="td-nombre">{{ $value->nombre }}</div>
                @if($value->idestadoanuncio==1)
                  <a class="btn btn-warning td-novisto" href="{{ url('backoffice/consumidor/adminanuncio/create') }}"><i class="fa fa-eye"></i> Ver Anuncio</a>
                @else
                  <div class="td-badge td-cont-visto"><span class="badge badge-pill badge-success td-visto"><i class="fa fa-check"></i> Visto</span></div> 
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
    </table>
</div> 
<style>
  .td-imagen {
      height: 40px;
  }
  .td-nombre {
      float: left;padding-top: 13px;
  }
  .td-cont-visto {
      padding: 0px;float: right;width: inherit;
  }
  .td-visto {
      font-size: 14px;border-radius: 20px;padding: 10px;padding-left: 40px;padding-right: 40px;font-weight: normal;
  }
  .td-novisto {
     border-radius: 22px;float: right;
  }
  
  @media only screen and (max-width: 1064px){
      .td-imagen {
          height: 74px;
          margin: 5px;
      }
      .td-nombre {
          padding-top: 8px;
          padding-bottom: 8px;
          width:100%;
      }
      .td-cont-visto {
          float: left;    
          margin-bottom: 8px;
      }
      .td-novisto {
          float: left;
          margin-bottom: 8px;
      }
  }

</style>
@endsection