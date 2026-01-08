 <div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Labores </span>
    <a class="btn btn-warning" href="javascript:;" onclick="laboral_index()"><i class="fa fa-angle-right"></i> Agregar</a></a>
  </div>
</div>
@include('app.sistema.tabla',[
  'tabla' => 'tabla-generallaborals',
  'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-laboralgeneral'),
  'data' => [
      'idusuario' => $prestamocredito->idcliente
  ],
  'thead' => [
      ['data' => 'Fuente de Ingreso'],
      ['data' => 'Actividad'],
      ['data' => 'Labora Desde'],
      ['data' => 'Ingreso Mensual'],
      ['data' => 'Ubigeo'],
      ['data' => 'Dirección'],
      ['data' => 'Referencia'],
  ],
  'tbody' => [
      ['data' => 'fuenteingreso'],
      ['data' => 'actividad'],
      ['data' => 'laboradesde'],
      ['data' => 'ingresomensual'],
      ['data' => 'ubigeo'],
      ['data' => 'direccion'],
      ['data' => 'referencia'],
  ],
  'tfoot' => [
      ['input' => ''],
      ['input' => ''],
      ['input' => ''],
      ['input' => ''],
      ['input' => ''],
      ['input' => ''],
      ['input' => '']
  ]
])