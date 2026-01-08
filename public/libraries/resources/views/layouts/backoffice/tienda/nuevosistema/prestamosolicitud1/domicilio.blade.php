 <div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Domicilios Actuales</span>
    <a class="btn btn-warning" href="javascript:;" onclick="domicilio_create()"><i class="fa fa-angle-right"></i> Registrar</a>
    <a class="btn btn-success" href="javascript:;" onclick="domicilio_generalindex()"><i class="fa fa-angle-left"></i> Atras</a>
  </div>
</div>
@include('app.sistema.tabla',[
  'tabla' => 'tabla-domicilios',
  'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-domicilio'),
  'data' => [
      'idusuario' => $prestamocredito->idcliente
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
      [
          'data' => '', 
          'content' => [
              [
                  'tipo' => 'onclick',
                  'route' => 'domicilio_agregar({iddomicilio})',
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
      ['input' => ''],
      ['input' => ''],
      ['input' => '']
  ]
])

<script>
  // Guarda el domicilio en la tabla s_prestamo_creditodomicilio
  function domicilio_agregar(iddomicilio){
    callback({
      route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
      method: 'POST',
      data:   {
        view: 'agregar-domicilio',
        iddomicilio: iddomicilio,
        idcredito: {{ $prestamocredito->id }}
      }
    },
    function(resultado){
      console.log('bien');
    })
  }
</script>