@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Aprobaciones</span>
        </div>
    </div>
    <div class="tabs-container" id="tab-solicitud">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-solicitud-0">Pre-Aprobados</a></li>
            <li><a href="#tab-solicitud-1">Aprobados</a></li>
            <li><a href="#tab-solicitud-2">Desembolsados</a></li>
        </ul>
        <div class="tab">
            <div id="tab-solicitud-0" class="tab-content" style="display: block;">
              <div id="cont-tabla-preaprobado">
                @include('app.sistema.tabla',[
                    'tabla' => 'tabla-preaprobados',
                    'script' => 'scriptsapp2',
                    'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoaprobacion/show-creditodesembolsado?view=preaprobado'),
                    'thead' => [
                        ['data' => 'Fecha Pre-Aprobado'],
                        ['data' => 'Monto'],
                        ['data' => 'Cuotas'],
                        ['data' => 'Fecha de Inicio'],
                        ['data' => 'Frecuencia'],
                        ['data' => 'Tasa'],
                        ['data' => 'Interes'],
                        ['data' => 'Asesor'],
                        ['data' => 'Cliente'],
                        ['data' => 'Estado', 'width' => '10px'],
                        ['data' => '', 'width' => '10px']
                    ],
                    'tbody' => [
                        ['data' => 'fecharegistro'],
                        ['data' => 'monto'],
                        ['data' => 'numerocuota'],
                        ['data' => 'fechainicio'],
                        ['data' => 'frecuencia_nombre'],
                        ['data' => 'tipotasa'],
                        ['data' => 'tasa'],
                        ['data' => 'asesor_nombre'],
                        ['data' => 'cliente'],
                        ['data' => 'estado'],
                        ['render' => 'opcion'],
                    ],
                    'tfoot' => [
                        ['input' => ''],
                        ['input' => ''],
                        ['input' => ''],
                        ['input' => 'date'],
                        [
                            'input' => 'select', 
                            'option' => 
                            [
                                ['id' => '1', 'nombre' => 'Diario'],
                                ['id' => '2', 'nombre' => 'Semanal'],
                                ['id' => '3', 'nombre' => 'Quincenal'],
                                ['id' => '4', 'nombre' => 'Mensual'],
                                ['id' => '4', 'nombre' => 'Programado']
                            ]
                        ],
                        [
                            'input' => 'select', 
                            'option' => 
                            [
                                ['id' => '1', 'nombre' => 'Fija'],
                                ['id' => '2', 'nombre' => 'Efectiva'],
                            ]
                        ],
                        ['input' => ''],
                        ['input' => 'text'],
                        ['input' => 'text'],
                        ['input' => ''],
                        ['input' => ''],
                    ]
                ])
              </div>
              <div id="cont-resultado-preaprobado"></div>
            </div>

            <div id="tab-solicitud-1" class="tab-content" style="display: none;">
              <div id="cont-tabla-aprobado">
                @include('app.sistema.tabla',[
                    'tabla' => 'tabla-aprobados',
                    'script' => 'scriptsapp3',
                    'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoaprobacion/show-creditodesembolsado?view=aprobado'),
                    'thead' => [
                        ['data' => 'Fecha Aprobado'],
                        ['data' => 'Monto'],
                        ['data' => 'Cuotas'],
                        ['data' => 'Fecha de Inicio'],
                        ['data' => 'Frecuencia'],
                        ['data' => 'Tasa'],
                        ['data' => 'Interes'],
                        ['data' => 'Asesor'],
                        ['data' => 'Cliente'],
                        ['data' => 'Estado', 'width' => '10px'],
                        ['data' => '', 'width' => '10px']
                    ],
                    'tbody' => [
                        ['data' => 'fecharegistro'],
                        ['data' => 'monto'],
                        ['data' => 'numerocuota'],
                        ['data' => 'fechainicio'],
                        ['data' => 'frecuencia_nombre'],
                        ['data' => 'tipotasa'],
                        ['data' => 'tasa'],
                        ['data' => 'asesor_nombre'],
                        ['data' => 'cliente'],
                        ['data' => 'estado'],
                        ['render' => 'opcion'],
                    ],
                    'tfoot' => [
                        ['input' => ''],
                        ['input' => ''],
                        ['input' => ''],
                        ['input' => 'date'],
                        [
                            'input' => 'select', 
                            'option' => 
                            [
                                ['id' => '1', 'nombre' => 'Diario'],
                                ['id' => '2', 'nombre' => 'Semanal'],
                                ['id' => '3', 'nombre' => 'Quincenal'],
                                ['id' => '4', 'nombre' => 'Mensual'],
                                ['id' => '4', 'nombre' => 'Programado']
                            ]
                        ],
                        [
                            'input' => 'select', 
                            'option' => 
                            [
                                ['id' => '1', 'nombre' => 'Fija'],
                                ['id' => '2', 'nombre' => 'Efectiva'],
                            ]
                        ],
                        ['input' => ''],
                        ['input' => 'text'],
                        ['input' => 'text'],
                        ['input' => ''],
                        ['input' => ''],
                    ]
                ])
              </div>
              <div id="cont-resultado-aprobado"></div>
            </div>

            <div id="tab-solicitud-2" class="tab-content" style="display: none;">
              <div id="cont-tabla-desembolsado">
                @include('app.sistema.tabla',[
                    'tabla' => 'tabla-desembolsados',
                    'script' => 'scriptsapp4',
                    'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoaprobacion/show-creditodesembolsado?view=desembolsado'),
                    'thead' => [
                        ['data' => 'Fecha Desembolsado'],
                        ['data' => 'Monto'],
                        ['data' => 'Cuotas'],
                        ['data' => 'Fecha de Inicio'],
                        ['data' => 'Frecuencia'],
                        ['data' => 'Tasa'],
                        ['data' => 'Interes'],
                        ['data' => 'Asesor'],
                        ['data' => 'Cliente'],
                        ['data' => 'Estado', 'width' => '10px'],
                        ['data' => '', 'width' => '10px']
                    ],
                    'tbody' => [
                        ['data' => 'fecharegistro'],
                        ['data' => 'monto'],
                        ['data' => 'numerocuota'],
                        ['data' => 'fechainicio'],
                        ['data' => 'frecuencia_nombre'],
                        ['data' => 'tipotasa'],
                        ['data' => 'tasa'],
                        ['data' => 'asesor_nombre'],
                        ['data' => 'cliente'],
                        ['data' => 'estado'],
                        ['render' => 'opcion'],
                    ],
                    'tfoot' => [
                        ['input' => ''],
                        ['input' => ''],
                        ['input' => ''],
                        ['input' => 'date'],
                        [
                            'input' => 'select', 
                            'option' => 
                            [
                                ['id' => '1', 'nombre' => 'Diario'],
                                ['id' => '2', 'nombre' => 'Semanal'],
                                ['id' => '3', 'nombre' => 'Quincenal'],
                                ['id' => '4', 'nombre' => 'Mensual'],
                                ['id' => '4', 'nombre' => 'Programado']
                            ]
                        ],
                        [
                            'input' => 'select', 
                            'option' => 
                            [
                                ['id' => '1', 'nombre' => 'Fija'],
                                ['id' => '2', 'nombre' => 'Efectiva'],
                            ]
                        ],
                        ['input' => ''],
                        ['input' => 'text'],
                        ['input' => 'text'],
                        ['input' => ''],
                        ['input' => ''],
                    ]
                ])
              </div>
              <div id="cont-resultado-desembolsado"></div>
            </div>
        </div>
    </div> 
@endsection
@section('subscripts')
<script>
tab({click:'#tab-solicitud'});
</script>
<script>
  function index() {
    $('#tabla-preaprobados').DataTable().ajax.reload();
    $('#tabla-aprobados').DataTable().ajax.reload();
    $('#tabla-desembolsados').DataTable().ajax.reload();
    $('#cont-tabla-preaprobado, #cont-tabla-aprobado, #cont-tabla-desembolsado').css('display','block');
    $('#cont-resultado-preaprobado, #cont-resultado-aprobado, #cont-resultado-desembolsado').html('');
  }

  // Preaprobados
  function aprobar_preaprobado(idtienda,idcredito) {
      $('#cont-tabla-preaprobado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamoaprobacion/'+idcredito+'/edit?view=aprobar',result:'#cont-resultado-preaprobado'});
  }
  function desaprobar_preaprobado(idtienda,idcredito) {
      $('#cont-tabla-preaprobado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamoaprobacion/'+idcredito+'/edit?view=desaprobar',result:'#cont-resultado-preaprobado'});
  }
  function detalle_preaprobado(idtienda,idcredito) {
      $('#cont-tabla-preaprobado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamoaprobacion/'+idcredito+'/edit?view=detalle',result:'#cont-resultado-preaprobado'});
  }

  // Aprobados
  function detalle_aprobado(idtienda,idcredito) {
      $('#cont-tabla-aprobado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamoaprobacion/'+idcredito+'/edit?view=detalle',result:'#cont-resultado-aprobado'});
  }

  // Desembolsados
  function detalle_desembolsado(idtienda,idcredito) {
      $('#cont-tabla-desembolsado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamoaprobacion/'+idcredito+'/edit?view=detalle',result:'#cont-resultado-desembolsado'});
  }
</script>
@endsection