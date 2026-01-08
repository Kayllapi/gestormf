@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Cajas',
    'botones'=>[
        'registrar:/'.$tienda->id.'/caja/create: Registrar'
    ]
])
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Nombre</th>
        @if(configuracion($tienda->id,'sistema_moneda_usar')['valor']==1)
        <th>Total S/.</th>
        @elseif(configuracion($tienda->id,'sistema_moneda_usar')['valor']==2)
        <th>Total $</th>
        @elseif(configuracion($tienda->id,'sistema_moneda_usar')['valor']==3)
        <th>Total S/.</th>
        <th>Total $</th>
        @else
        <th>Total S/.</th>
        @endif
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['nameBox'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/caja')
    ])
    <tbody>
        @foreach($box as $value)
        <tr>
          <td>{{$value->nombre}}</td>
          <?php $saldo = 0 ?>
          @if(configuracion($tienda->id,'sistema_moneda_usar')['valor']==1)
          <td>{{ $saldo = efectivocaja($tienda->id,$value->id,1)['total'] }}</td>
          @elseif(configuracion($tienda->id,'sistema_moneda_usar')['valor']==2)
          <td>{{ $saldo = efectivocaja($tienda->id,$value->id,2)['total'] }}</td>
          @elseif(configuracion($tienda->id,'sistema_moneda_usar')['valor']==3)
          <td>{{ $saldo = efectivocaja($tienda->id,$value->id,1)['total'] }}</td>
          <td>{{ $saldo = saldo.efectivocaja($tienda->id,$value->id,2)['total'] }}</td>
          @else
          <td>{{ $saldo = efectivocaja($tienda->id,$value->id,1)['total'] }}</td>
          @endif
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/caja/'.$value->id.'/edit?view=editar') }}" id="btneliminar1"><i class="fa fa-edit"></i> Editar</a></li>
                    @if($saldo<=0)
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/caja/'.$value->id.'/edit?view=eliminar') }}" id="btneliminar1"><i class="fa fa-trash"></i> Eliminar</a></li>
                    @endif
                </ul>
            </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $box->links('app.tablepagination', ['results' => $box]) }}
@endsection
