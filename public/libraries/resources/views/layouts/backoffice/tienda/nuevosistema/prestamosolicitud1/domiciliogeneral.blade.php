 <div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Domicilios </span>
    <a class="btn btn-warning" href="javascript:;" onclick="domicilio_index()"><i class="fa fa-angle-right"></i> Agregar</a></a>
  </div>
</div>
@include('app.sistema.tabla',[
  'tabla' => 'tabla-generaldomicilios',
  'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-domiciliogeneral'),
  'data' => [
      'idusuario' => $prestamocredito->idcliente
  ],
  'thead' => [
      ['data' => 'Ubigeo'],
      ['data' => 'Dirección'],
      ['data' => 'Referencia'],
      ['data' => 'Reside Desde'],
      ['data' => 'Hora de Ubicación'],
      ['data' => 'Propiedad']
  ],
  'tbody' => [
      ['data' => 'ubigeonombre'],
      ['data' => 'direccion'],
      ['data' => 'referencia'],
      ['data' => 'residesde'],
      ['data' => 'horaubicacion'],
      ['data' => 'tipopropiedad']
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