 <div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Laborales</span>
      <a class="btn btn-warning" href="javascript:;" onclick="laboral_create()"><i class="fa fa-angle-right"></i> Registrar</a></a>
    <a class="btn btn-success" href="javascript:;" onclick="laboral_generalindex()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
</div>    

@include('app.sistema.tabla',[
  'tabla' => 'tabla-laborals',
  'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/usuario/show-laboral'),
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
      ['data' => '', 'width' => '10px']
  ],
  'tbody' => [
      ['data' => 'fuenteingreso'],
      ['data' => 'actividad'],
      ['data' => 'laboradesde'],
      ['data' => 'ingresomensual'],
      ['data' => 'ubigeo'],
      ['data' => 'direccion'],
      ['data' => 'referencia'],
      [
          'data' => '', 
          'content' => [
              [
                  'tipo' => 'onclick',
                  'route' => 'laboral_agregar({idlaboral})',
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
      ['input' => ''],
      ['input' => ''],
      ['input' => '']
  ]
])

<script>
  // Guarda el labor en la tabla s_prestamo_creditolabor
  function laboral_agregar(idlabor){
    callback({
      route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
      method: 'POST',
      data:   {
        view: 'agregar-labor',
        idlabor: idlabor,
        idcredito: {{ $prestamocredito->id }}
      }
    },
    function(resultado){
      console.log('bien');
    })
  }
</script>