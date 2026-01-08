@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Aperturas y Cierres de Caja</span>
      @if($caja['resultado']=='ABIERTO')
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/create?view=cajaauxiliar') }}"><i class="fa fa-angle-right"></i> Aperturar Caja Auxiliar</a></a>
      @endif
      @if(modulo($tienda->id,Auth::user()->id,'apertura')['resultado']=='CORRECTO')
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/create?view=caja') }}"><i class="fa fa-angle-right"></i> Aperturar Caja</a></a>
      @endif
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Responsable</th>
        <th>Recepción</th>
        <th>Caja</th>
        <th colspan="2">Apertura</th>
        <th colspan="2">Cierre</th>
        <th>Fecha de Apertura</th>
        <th>Fecha de Cierre</th>
        <th width="10px">Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['usersresponsable','usersrecepcion','cajanombre'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura')
    ])
    <tbody>
        <?php $tipocierrecaja =  configuracion($tienda->id,'prestamo_tipocierrecaja')['valor'] ?>
        @foreach($s_aperturacierres as $value)
        <tr>
          <td>{{$value->usersresponsableapellidos}}, {{$value->usersresponsablenombre}}</td>
          <td>{{$value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
          <td>{{$value->cajanombre}}
          @if($value->idtipocaja==2)
            <br>(CAJA AUXILIAR)
          @endif
          </td>
          @if($value->config_sistema_moneda_usar==1)
          <td style="background-color: #28a745;color: #fff;padding: 5px;width:90px;">
            {{$moneda_soles->simbolo}} {{$value->montoasignar}}</td>
          <td style="background-color: #28a745;color: #fff;width:90px;"></td>
          @elseif($value->config_sistema_moneda_usar==2)
          <td style="background-color: #28a745;color: #fff;padding: 5px;">
            {{$moneda_dolares->simbolo}} {{$value->montoasignar_dolares}}</td>
          <td style="background-color: #28a745;color: #fff;"></td>
          @elseif($value->config_sistema_moneda_usar==3)
          <td style="background-color: #28a745;color: #fff;padding: 5px;">
            {{$moneda_soles->simbolo}} {{$value->montoasignar}}</td>
          <td style="background-color: #28a745;color: #fff;padding: 5px;">
            {{$moneda_dolares->simbolo}} {{$value->montoasignar_dolares}}</td>
          @endif
          @if($value->config_sistema_moneda_usar==1)
          <td style="background-color: #007bff;color: #fff;padding: 5px;width:90px;">
            @if($tipocierrecaja==3)
            {{$moneda_soles->simbolo}} {{$value->montocierre_recibido}}
            @else
            {{$moneda_soles->simbolo}} {{$value->montocierre}}
            @endif
          </td>
          <td style="background-color: #007bff;color: #fff;width:90px;"></td>
          @elseif($value->config_sistema_moneda_usar==2)
          <td style="background-color: #007bff;color: #fff;padding: 5px;">
            @if($tipocierrecaja==3)
            {{$moneda_dolares->simbolo}} {{$value->montocierre_recibido_dolares}}
            @else
            {{$moneda_dolares->simbolo}} {{$value->montocierre_dolares}}
            @endif
          </td>
          <td style="background-color: #007bff;color: #fff;"></td>
          @elseif($value->config_sistema_moneda_usar==3)
          <td style="background-color: #007bff;color: #fff;padding: 5px;">
            @if($tipocierrecaja==3)
            {{$moneda_soles->simbolo}} {{$value->montocierre_recibido}}
            @else
            {{$moneda_soles->simbolo}} {{$value->montocierre}}
            @endif
          </td>
          <td style="background-color: #007bff;color: #fff;padding: 5px;">
            @if($tipocierrecaja==3)
            {{$moneda_dolares->simbolo}} {{$value->montocierre_recibido_dolares}}
            @else
            {{$moneda_dolares->simbolo}} {{$value->montocierre_dolares}}
            @endif
          </td>
          @endif
          <td>{{$value->fechaconfirmacion!=''?date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i:s A"):'---'}}</td>
          <td>{{$value->fechacierre!=''?date_format(date_create($value->fechacierre),"d/m/Y h:i:s A"):'---'}}</td>
          <td>
            @if($value->idestadoaperturacierre==1 && $value->idusersresponsable==Auth::user()->id)
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-sync-alt"></i> Apertura en Proceso</span></div>
            @elseif($value->idestadoaperturacierre==2 && ($value->idusersresponsable==Auth::user()->id || $value->idusersrecepcion==Auth::user()->id) && $value->fechaconfirmacion=='')
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Apertura Pendiente</span></div> 
            @elseif($value->idestadoaperturacierre==2 && ($value->idusersresponsable==Auth::user()->id || $value->idusersrecepcion==Auth::user()->id) && $value->fechaconfirmacion!='')
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Aperturado</span></div>
            @elseif($value->idestadoaperturacierre==3 && ($value->idusersresponsable==Auth::user()->id || $value->idusersrecepcion==Auth::user()->id) && $value->fechacierreconfirmacion=='')
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Cierre Pendiente</span></div>
            @elseif($value->idestadoaperturacierre==3 && ($value->idusersresponsable==Auth::user()->id || $value->idusersrecepcion==Auth::user()->id) && $value->fechacierreconfirmacion!='')
              <div class="td-badge"><span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Cerrado</span></div>
            @endif
          </td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    @if($value->idestadoaperturacierre==1 && $value->idusersresponsable==Auth::user()->id)
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-check"></i> Confirmar Apertura</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar Apertura</a></li>
                    @elseif($value->idestadoaperturacierre==2 && $value->idusersresponsable==Auth::user()->id && $value->idusersrecepcion!=Auth::user()->id && $value->fechaconfirmacion=='')
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=anularapertura') }}"><i class="fa fa-ban"></i> Cancelar Apertura</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=detalleapertura') }}"><i class="fa fa-list-alt"></i> Detalle de Apertura</a></li>
                    @elseif($value->idestadoaperturacierre==2 && $value->idusersrecepcion==Auth::user()->id && $value->fechaconfirmacion=='')
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=confirmarrecepcion') }}"><i class="fa fa-check"></i> Recepcionar</a></li>
                    @elseif($value->idestadoaperturacierre==2 && $value->idusersresponsable==Auth::user()->id && $value->idusersrecepcion!=Auth::user()->id && $value->fechaconfirmacion!='')
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=detallediario') }}"><i class="fa fa-list-alt"></i> Detalle del Día</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=detalleapertura') }}"><i class="fa fa-list-alt"></i> Detalle de Apertura</a></li>
                    @elseif($value->idestadoaperturacierre==2 && $value->idusersrecepcion==Auth::user()->id && $value->fechaconfirmacion!='')
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=confirmarcierre') }}"><i class="fa fa-check"></i> Cerrar Caja</a></li>
                    @if($value->idusersresponsable==Auth::user()->id)
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=detallediario') }}"><i class="fa fa-list-alt"></i> Detalle del Día</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=detalleapertura') }}"><i class="fa fa-list-alt"></i> Detalle de Apertura</a></li>
                    @endif
                    @elseif($value->idestadoaperturacierre==3 && $value->idusersresponsable==Auth::user()->id && $value->fechacierreconfirmacion=='')
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=confirmarrecepcioncierre') }}"><i class="fa fa-check"></i> Revisar Cierre</a></li>
                    @elseif($value->idestadoaperturacierre==3 && ($value->idusersresponsable==Auth::user()->id || $value->idusersrecepcion==Auth::user()->id) && $value->fechacierreconfirmacion!='')
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=detallediario') }}"><i class="fa fa-list-alt"></i> Detalle del Día</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=detalleapertura') }}"><i class="fa fa-list-alt"></i> Detalle de Apertura</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=detallecierre') }}"><i class="fa fa-list-alt"></i> Detalle de Cierre</a></li>
                    @elseif($value->idestadoaperturacierre==3 && ($value->idusersresponsable==Auth::user()->id || $value->idusersrecepcion==Auth::user()->id) && $value->fechacierreconfirmacion=='')
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=detallediario') }}"><i class="fa fa-list-alt"></i> Detalle del Día</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/cajaapertura/'.$value->id.'/edit?view=detalleapertura') }}"><i class="fa fa-list-alt"></i> Detalle de Apertura</a></li>
                    @endif
                </ul>
            </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $s_aperturacierres->links('app.tablepagination', ['results' => $s_aperturacierres]) }}
@endsection
