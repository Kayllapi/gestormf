<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Pisos</span>
      <a class="btn btn-warning" href="javascript:;" onclick="registrar_piso({{ $tienda->id }})"><i class="fa fa-angle-right"></i> Registrar</a>
    </div>
</div>
@include('app.sistema.tabla',[
    'tabla' => 'tabla-pisos',
    'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/show-indexpiso'),
    'thead' => [
        ['data' => 'Piso'],
        ['data' => 'Ambientes/Mesas'],
        ['data' => 'Estado', 'width' => '10px'],
        ['data' => '', 'width' => '10px']
    ],
    'tbody' => [
        ['data' => 'piso'],
        ['data' => 'ambientes'],
        ['data' => 'estado'],
        ['render' => 'opcion'],
    ],
    'tfoot' => [
        ['input' => ''],
        ['input' => ''],
        ['input' => ''],
        ['input' => ''],
    ]
])