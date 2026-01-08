<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Relaciones</span>
    <a class="btn btn-warning" href="javascript:;" onclick="relacion_create()"><i class="fa fa-angle-right"></i> Registrar</a>
    <a class="btn btn-success" href="javascript:;" onclick="relacion_generalindex()"><i class="fa fa-angle-left"></i> Atras</a>
  </div>
</div>
@include('app.sistema.tabla',[
  'tabla' => 'tabla-relacions',
  'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-relacion'),
  'data' => [
      'idusuario' => $prestamocredito->idcliente
  ],
  'thead' => [
      ['data' => 'Persona'],
      ['data' => 'Tipo de Relación'],
      ['data' => '', 'width' => '10px']
  ],
  'tbody' => [
      ['data' => 'persona'],
      ['data' => 'tiporelacion'],
      [
          'data' => '', 
          'content' => [
              [
                  'tipo' => 'onclick',
                  'route' => 'relacion_agregar({idrelacion})',
                  'icon' => 'fa fa-check', 
                  'nombre' => 'Agregar',
              ]
          ]
      ]
  ],
  'tfoot' => [
      ['input' => ''],
      ['input' => ''],
      ['input' => '']
  ]
])

<script>
  // Guarda el relacion en la tabla s_prestamo_creditorelacion
  function relacion_agregar(idrelacion){
    callback({
      route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
      method: 'POST',
      data:   {
        view: 'agregar-relacion',
        idrelacion: idrelacion,
        idcredito: {{ $prestamocredito->id }}
      }
    },
    function(resultado){
      console.log('relacion');
    })
  }
</script>