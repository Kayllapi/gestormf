 <div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Bienes </span>
    <a class="btn btn-warning" href="javascript:;" onclick="bien_index()"><i class="fa fa-angle-right"></i> Agregar</a></a>
  </div>
</div>
@include('app.sistema.tabla',[
  'tabla' => 'tabla-generalbiens',
  'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-biengeneral'),
  'data' => [
      'idusuario' => $prestamocredito->idcliente
  ],
  'thead' => [
    ['data' => 'Tipo de Bien'],
    ['data' => 'Descripción'],
    ['data' => 'Valor Estimado'],
  ],
  'tbody' => [
    ['data' => 'tipobien'],
    ['data' => 'descripcion'],
    ['data' => 'valorestimado'],
  ],
  'tfoot' => [
      ['input' => ''],
      ['input' => ''],
      ['input' => '']
  ]
])