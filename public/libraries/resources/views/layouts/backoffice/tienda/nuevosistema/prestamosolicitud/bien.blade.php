<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Bienes</span>
      @if($estado!='lectura')
      <a class="btn btn-warning" href="javascript:;" onclick="bien_create()"><i class="fa fa-angle-right"></i> Registrar</a></a>
      @endif
    </div>
</div>

@include('app.sistema.tabla',[
      'tabla' => 'tabla-generalbiens',
      'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-bien'),
      'data' => [
          'idtienda' => $tienda->id,
          'idprestamo_credito' => $prestamocredito->id,
          'estado' => $estado
      ],
      'thead' => [
          ['data' => 'Tipo de Bien'],
          ['data' => 'Descripción'],
          ['data' => 'Valor Estimado'],
          ['data' => '', 'width' => '10px']
      ],
      'tbody' => [
          ['data' => 'tipobien'],
          ['data' => 'descripcion'],
          ['data' => 'valorestimado'],
          ['render' => 'opcion'],
      ],
      'tfoot' => [
          ['input' => ''],
          ['input' => ''],
          ['input' => ''],
          ['input' => '']
      ]
  ])

