@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Transferencia de Productos</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Código</th>
        <th >Tienda Origen</th>
        <th>Tienda Destino</th>
        <th>Motivo</th>
        <th>Fecha Solicitud</th>
        <th>Fecha Envio</th>
        <th>Fecha Recepción</th>
        <th>Transferencia</th>
        <th>Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
   
    <tbody>
        @foreach($productotransferencias as $value)
        <tr>
         <td>{{ str_pad($value->codigo, 6, "0", STR_PAD_LEFT) }}</td>
          <td>{{ $value->tienda_origen_nombre }} {{ $value->idusersorigen!=0?'('.$value->user_origen_nombre.')':'' }}</td>
          <td>{{ $value->tienda_destino_nombre }} {{ $value->idusersdestino!=0?'('.$value->user_destino_nombre.')':'' }}</td>
         <td>{{ $value->motivo }}</td>
           <td>{{ ($value->idestadotransferencia==1 or $value->idestadotransferencia==2 or $value->idestadotransferencia==3)?date_format(date_create($value->fechasolicitud), 'd/m/Y - h:i A'):'---' }}</td>
              <td>{{ ($value->idestadotransferencia==2 or $value->idestadotransferencia==3)?date_format(date_create($value->fechaenvio), 'd/m/Y - h:i A'):'---' }}</td>
              <td>{{ $value->idestadotransferencia==3?date_format(date_create($value->fecharecepcion), 'd/m/Y - h:i A'):'---' }}</td>
           <td>
                    @if($value->idestadotransferencia==1)
                        <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Solicitud</span></div> 
                    @elseif($value->idestadotransferencia==2)
                        <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-share"></i> Envio</span></div>
                    @elseif($value->idestadotransferencia==3)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Recepcionado</span></div>
                    @endif 
              </td>
           <td>
                    @if($value->idestadotransferencia==1)
                        @if($value->id_tienda_destino)
                            @if($value->idestado==1)
                                <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-sync-alt"></i> En Proceso</span></div> 
                            @elseif($value->idestado==2)
                                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div>
                            @endif
                        @else
                            @if($value->idestado==1)
                            <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-sync-alt"></i> En Proceso</span></div> 
                            @elseif($value->idestado==2)
                            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
                            @endif
                        @endif
                    @elseif($value->idestadotransferencia==2)
                        @if($value->id_tienda_destino) 
                            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
                        @else
                            <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span></div> 
                        @endif
                    @elseif($value->idestadotransferencia==3)
                        <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                    @endif 
                        
              </td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                   @if($value->idestadotransferencia==1)
                        @if($value->idtiendaorigen)
                         @if($value->idestado==1)
                                <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=confirmar') }}">
                                 <i class="fa fa-check"></i> Confirmar</a></li>
                               <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=edit') }}">
                                 <i class="fa fa-edit"></i> Editar</a></li>
                               <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=detalle') }}">
                                 <i class="fa fa-list-alt"></i> Detalle</a></li>
                               <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=eliminar') }}">
                                 <i class="fa fa-trash"></i> Eliminar</a></li> 
                         @elseif($value->idestado==2)
                               <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=edit') }}">
                                 <i class="fa fa-share"></i> Enviar</a></li>
                               <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=rechazar') }}">
                                 <i class="fa fa-ban"></i> Rechazar</a></li>
                               <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=detalle') }}">
                                 <i class="fa fa-list-alt"></i> Detalle</a></li>
                         @endif
                        @elseif($value->id_tienda_destino)
                            @if($value->idestado==1)  
                               <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=confirmar') }}">
                                  <i class="fa fa-check"></i> Confirmar</a></li>
                                <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=edit') }}">
                                  <i class="fa fa-edit"></i> Editar</a></li>
                                <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=detalle') }}">
                                  <i class="fa fa-list-alt"></i> Detalle</a></li>
                                <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=eliminar') }}">
                                  <i class="fa fa-trash"></i> Eliminar</a></li> 
                            @elseif($value->idestado==2)
                            <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=detalle') }}">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                            @endif
                        @endif
                    @elseif($value->idestadotransferencia==2)
                        @if($value->id_tienda_destino)
                            <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=edit') }}">
                              <i class="fa fa-check"></i> Recepcionar</a></li>
                            <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=rechazar') }}">
                              <i class="fa fa-ban"></i> Rechazar</a></li>
                            <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=detalle') }}">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                        @else
                            <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=detalle') }}">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                            
                        @endif
                    @elseif($value->idestadotransferencia==3)
                            <li><a  href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=detalle') }}">
                              <i class="fa fa-list-alt"></i> Detalle</a></li>
                            <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productotransferencia/'.$value->id.'/edit?view=ticket') }}">
                              <i class="fa fa-receipt"></i> Ticket</a></li>
                    @endif 
                  
                   
                </ul>
            </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
  {{ $productotransferencias->links('app.tablepagination', ['results' => $productotransferencias]) }}

</div>
@endsection
