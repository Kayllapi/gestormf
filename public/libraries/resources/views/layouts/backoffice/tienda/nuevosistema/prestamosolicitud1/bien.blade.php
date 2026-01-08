<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Bienes</span>
    <a class="btn btn-warning" href="javascript:;" onclick="bien_create()"><i class="fa fa-angle-right"></i> Registrar</a>
    <a class="btn btn-success" href="javascript:;" onclick="bien_generalindex()"><i class="fa fa-angle-left"></i> Atras</a>
  </div>
</div>
@include('app.sistema.tabla',[
  'tabla' => 'tabla-biens',
  'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-bien'),
  'data' => [
      'idusuario' => $prestamocredito->idcliente
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
      [
          'data' => '', 
          'content' => [
              [
                  'tipo' => 'onclick',
                  'route' => 'bien_agregar({idbien})',
                  'icon' => 'fa fa-check', 
                  'nombre' => 'Agregar',
              ]
          ]
      ]
  ],
  'tfoot' => [
      ['input' => ''],
      ['input' => ''],
      ['input' => ''],
      ['input' => '']
  ]
])

<script>
  // Guarda el bien en la tabla s_prestamo_creditobien
  function bien_agregar(idbien){
    callback({
      route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
      method: 'POST',
      data:   {
        view: 'agregar-bien',
        idbien: idbien,
        idcredito: {{ $prestamocredito->id }}
      }
    },
    function(resultado){
      console.log('bien');
    })
  }
</script>