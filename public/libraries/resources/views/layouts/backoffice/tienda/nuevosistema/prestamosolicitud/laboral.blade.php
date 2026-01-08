 <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Laborales</span>
      @if($estado!='lectura')
      <a class="btn btn-warning" href="javascript:;" onclick="laboral_create()"><i class="fa fa-angle-right"></i> Registrar</a></a>
      @endif
    </div>
</div>    
@include('app.sistema.tabla',[
      'tabla' => 'tabla-generallaborals',
      'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-laboral'),
      'data' => [
          'idtienda' => $tienda->id,
          'idprestamo_credito' => $prestamocredito->id,
          'estado' => $estado
      ],
      'thead' => [
          ['data' => 'Fuente de Ingreso'],
          ['data' => 'Giro'],
          ['data' => 'Actividad'],
          ['data' => 'Ingreso Mensual'],
          ['data' => 'Labora Desde'],
          ['data' => 'Ubigeo'],
          ['data' => 'Dirección'],
          ['data' => 'Referencia'],
          ['data' => '', 'width' => '10px']
      ],
      'tbody' => [
          ['data' => 'fuenteingreso'],
          ['data' => 'giro'],
          ['data' => 'actividad'],
          ['data' => 'ingresomensual'],
          ['data' => 'laboradesde'],
          ['data' => 'ubigeo'],
          ['data' => 'direccion'],
          ['data' => 'referencia'],
          ['render' => 'opcion'],
      ],
      'tfoot' => [
          ['input' => ''],
          ['input' => ''],
          ['input' => ''],
          ['input' => ''],
          ['input' => ''],
          ['input' => ''],
          ['input' => ''],
          ['input' => ''],
          ['input' => '']
      ]
  ])

