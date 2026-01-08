 <div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Bienes </span>
    <a class="btn btn-warning" href="javascript:;" onclick="relacion_index()"><i class="fa fa-angle-right"></i> Agregar</a></a>
  </div>
</div>
@include('app.sistema.tabla',[
  'tabla' => 'tabla-generalrelacions',
  'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-relaciongeneral'),
  'data' => [
      'idusuario' => $prestamocredito->idcliente
  ],
  'thead' => [
      ['data' => 'Persona'],
      ['data' => 'Tipo de Relación'],
  ],
  'tbody' => [
      ['data' => 'persona'],
      ['data' => 'tiporelacion'],
  ],
  'tfoot' => [
      ['input' => ''],
      ['input' => '']
  ]
])