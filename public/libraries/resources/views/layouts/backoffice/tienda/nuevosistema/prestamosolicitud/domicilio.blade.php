 <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Domicilios</span>
      @if($estado!='lectura')
      <a class="btn btn-warning" href="javascript:;" onclick="domicilio_create()"><i class="fa fa-angle-right"></i> Registrar</a></a>
      @endif
    </div>
</div>

@include('app.sistema.tabla',[
      'tabla' => 'tabla-generaldomicilios',
      'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-domicilio'),
      'data' => [
          'idtienda' => $tienda->id,
          'idprestamo_credito' => $prestamocredito->id,
          'estado' => $estado
      ],
      'thead' => [
          ['data' => 'Ubigeo'],
          ['data' => 'Dirección'],
          ['data' => 'Referencia'],
          ['data' => 'Reside Desde'],
          ['data' => 'Hora de Ubicación'],
          ['data' => 'Propiedad'],
          ['data' => '', 'width' => '10px']
      ],
      'tbody' => [
          ['data' => 'ubigeonombre'],
          ['data' => 'direccion'],
          ['data' => 'referencia'],
          ['data' => 'residesde'],
          ['data' => 'horaubicacion'],
          ['data' => 'tipopropiedad'],
          ['render' => 'opcion'],
      ],
      'tfoot' => [
          ['input' => ''],
          ['input' => ''],
          ['input' => ''],
          ['input' => ''],
          ['input' => ''],
          ['input' => '']
      ]
  ])

