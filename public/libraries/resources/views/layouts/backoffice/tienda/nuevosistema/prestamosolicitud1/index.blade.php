@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Solicitudes de Crédito</span>
    <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/create') }}"><i class="fa fa-angle-right"></i> Registrar</a>
  </div>
</div>
<div class="tabs-container" id="tab-solicitud">
  <ul class="tabs-menu">
    <li><a href="#tab-solicitud-0">Pendientes</a></li>
    <li><a href="#tab-solicitud-1">Pre-Aprobados</a></li>
    <li><a href="#tab-solicitud-2">Aprobados</a></li>
    <li><a href="#tab-solicitud-3">Desembolsados</a></li>
  </ul>
  <div class="tab">
      <div id="tab-solicitud-0" class="tab-content" style="display: block;">
        <div id="cont-tabla-pendiente">
          @include('app.sistema.tabla',[
              'tabla' => 'tabla-pendientes',
              'script' => 'scriptsapp1',
              'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-creditodesembolsado?view=pendiente'),
              'thead' => [
                  ['data' => 'Fecha de degistro'],
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
                  [
                      'data' => 'idprestamo_tipotasa', 
                      'render' => 'lista', 
                      'renderdata' => [
                          [
                              'id' => '1', 
                              'nombre' => 'Fija', 
                          ],
                          [
                              'id' => '2', 
                              'nombre' => 'Efectiva', 
                          ]
                      ]
                  ],
                  ['data' => 'tasa'],
                  ['data' => 'asesor_nombre'],
                  ['data' => 'cliente'],
                  [
                      'data' => 'idestado', 
                      'render' => 'estado', 
                      'renderdata' => [
                          [
                              'id' => '1', 
                              'class' => 'success', 
                              'icon' => 'fa fa-check', 
                              'nombre' => 'Activado', 
                          ],
                          [
                              'id' => '2', 
                              'class' => 'dark', 
                              'icon' => 'fa fa-ban', 
                              'nombre' => 'Anulado', 
                          ]
                      ]
                  ],
                  [
                      'data' => 'idestado', 
                      'content' => [
                          [
                              'id' => '1', 
                              'tipo' => 'onclick',
                              'route' => 'editar_pendiente({idtienda},{idcredito})',
                              'icon' => 'fa fa-edit', 
                              'nombre' => 'Editar', 
                          ],
                          [
                              'id' => '1', 
                              'tipo' => 'onclick',
                              'route' => 'preaprobar_pendiente({idtienda},{idcredito})',
                              'icon' => 'fa fa-check', 
                              'nombre' => 'Pre-Aprobar', 
                          ],
                          [
                              'id' => '1', 
                              'tipo' => 'onclick',
                              'route' => 'anular_pendiente({idtienda},{idcredito})',
                              'icon' => 'fa fa-ban', 
                              'nombre' => 'Anular', 
                          ],
                          [
                              'id' => '2', 
                              'tipo' => 'onclick',
                              'route' => 'detalle_pendiente({idtienda},{idcredito})',
                              'icon' => 'fa fa-list', 
                              'nombre' => 'Detalle', 
                          ]
                      ]
                  ]
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
        <div id="cont-resultado-pendiente"></div>
      </div>

      <div id="tab-solicitud-1" class="tab-content" style="display: none;">
        <div id="cont-tabla-preaprobado">
          @include('app.sistema.tabla',[
              'tabla' => 'tabla-preaprobados',
              'script' => 'scriptsapp2',
              'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-creditodesembolsado?view=preaprobado'),
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
                  ['data' => 'fechapreaprobado'],
                  ['data' => 'monto'],
                  ['data' => 'numerocuota'],
                  ['data' => 'fechainicio'],
                  ['data' => 'frecuencia_nombre'],
                  [
                      'data' => 'idprestamo_tipotasa', 
                      'render' => 'lista', 
                      'renderdata' => [
                          [
                              'id' => '1', 
                              'nombre' => 'Fija', 
                          ],
                          [
                              'id' => '2', 
                              'nombre' => 'Efectiva', 
                          ]
                      ]
                  ],
                  ['data' => 'tasa'],
                  ['data' => 'asesor_nombre'],
                  ['data' => 'cliente'],
                  [
                      'data' => 'idestado', 
                      'render' => 'estado', 
                      'renderdata' => [
                          [
                              'id' => '1', 
                              'class' => 'success', 
                              'icon' => 'fa fa-check', 
                              'nombre' => 'Activado', 
                          ],
                          [
                              'id' => '2', 
                              'class' => 'dark', 
                              'icon' => 'fa fa-ban', 
                              'nombre' => 'Anulado', 
                          ]
                      ]
                  ],
                  [
                      'data' => '', 
                      'content' => [
                          [
                              'tipo' => 'onclick',
                              'route' => 'detalle_preaprobado({idtienda},{idcredito})',
                              'icon' => 'fa fa-list', 
                              'nombre' => 'Detalle', 
                          ]
                      ]
                  ]
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

      <div id="tab-solicitud-2" class="tab-content" style="display: none;">
        <div id="cont-tabla-aprobado">
          @include('app.sistema.tabla',[
              'tabla' => 'tabla-aprobados',
              'script' => 'scriptsapp3',
              'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-creditodesembolsado?view=aprobado'),
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
                  ['data' => 'fechaaprobado'],
                  ['data' => 'monto'],
                  ['data' => 'numerocuota'],
                  ['data' => 'fechainicio'],
                  ['data' => 'frecuencia_nombre'],
                  [
                      'data' => 'idprestamo_tipotasa', 
                      'render' => 'lista', 
                      'renderdata' => [
                          [
                              'id' => '1', 
                              'nombre' => 'Fija', 
                          ],
                          [
                              'id' => '2', 
                              'nombre' => 'Efectiva', 
                          ]
                      ]
                  ],
                  ['data' => 'tasa'],
                  ['data' => 'asesor_nombre'],
                  ['data' => 'cliente'],
                  [
                      'data' => 'idestado', 
                      'render' => 'estado', 
                      'renderdata' => [
                          [
                              'id' => '1', 
                              'class' => 'success', 
                              'icon' => 'fa fa-check', 
                              'nombre' => 'Activado', 
                          ],
                          [
                              'id' => '2', 
                              'class' => 'dark', 
                              'icon' => 'fa fa-ban', 
                              'nombre' => 'Anulado', 
                          ]
                      ]
                  ],
                  [
                      'data' => '', 
                      'content' => [
                          [
                              'tipo' => 'onclick',
                              'route' => 'detalle_aprobado({idtienda},{idcredito})',
                              'icon' => 'fa fa-list', 
                              'nombre' => 'Detalle', 
                          ]
                      ]
                  ]
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
      <div id="tab-solicitud-3" class="tab-content" style="display: none;">
        <div id="cont-tabla-desembolsado">
          @include('app.sistema.tabla',[
              'tabla' => 'tabla-desembolsados',
              'script' => 'scriptsapp4',
              'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-creditodesembolsado?view=desembolsado'),
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
                  ['data' => 'fechadesembolsado'],
                  ['data' => 'monto'],
                  ['data' => 'numerocuota'],
                  ['data' => 'fechainicio'],
                  ['data' => 'frecuencia_nombre'],
                  [
                      'data' => 'idprestamo_tipotasa', 
                      'render' => 'lista', 
                      'renderdata' => [
                          [
                              'id' => '1', 
                              'nombre' => 'Fija', 
                          ],
                          [
                              'id' => '2', 
                              'nombre' => 'Efectiva', 
                          ]
                      ]
                  ],
                  ['data' => 'tasa'],
                  ['data' => 'asesor_nombre'],
                  ['data' => 'cliente'],
                  [
                      'data' => 'idestado', 
                      'render' => 'estado', 
                      'renderdata' => [
                          [
                              'id' => '1', 
                              'class' => 'success', 
                              'icon' => 'fa fa-check', 
                              'nombre' => 'Activado', 
                          ],
                          [
                              'id' => '2', 
                              'class' => 'dark', 
                              'icon' => 'fa fa-ban', 
                              'nombre' => 'Anulado', 
                          ]
                      ]
                  ],
                  [
                      'data' => '', 
                      'content' => [
                          [
                              'tipo' => 'onclick',
                              'route' => 'detalle_desembolsado({idtienda},{idcredito})',
                              'icon' => 'fa fa-list', 
                              'nombre' => 'Detalle', 
                          ]
                      ]
                  ]
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
  // Tabulador de pestañas
  tab({click:'#tab-solicitud'});
</script>
<script>
  function index() {
    $('#tabla-pendientes').DataTable().ajax.reload();
    $('#tabla-preaprobados').DataTable().ajax.reload();
    $('#tabla-aprobados').DataTable().ajax.reload();
    $('#tabla-desembolsados').DataTable().ajax.reload();
    $('#cont-tabla-pendiente, #cont-tabla-preaprobado, #cont-tabla-aprobado, #cont-tabla-desembolsado').css('display','block');
    $('#cont-resultado-pendiente, #cont-resultado-preaprobado, #cont-resultado-aprobado, #cont-resultado-desembolsado').html('');
  }

  // Pendientes
  function editar_pendiente(idtienda,idcredito) {
      $('#cont-tabla-pendiente').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamosolicitud/'+idcredito+'/edit?view=editar',result:'#cont-resultado-pendiente'});
  }
  function preaprobar_pendiente(idtienda,idcredito) {
      $('#cont-tabla-pendiente').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamosolicitud/'+idcredito+'/edit?view=preaprobar',result:'#cont-resultado-pendiente'});
  }
  function anular_pendiente(idtienda,idcredito) {
      $('#cont-tabla-pendiente').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamosolicitud/'+idcredito+'/edit?view=anular',result:'#cont-resultado-pendiente'});
  }
  function detalle_pendiente(idtienda,idcredito) {
      $('#cont-tabla-pendiente').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamosolicitud/'+idcredito+'/edit?view=detalle',result:'#cont-resultado-pendiente'});
  }

  // Preaprobados
  function detalle_preaprobado(idtienda,idcredito) {
      $('#cont-tabla-preaprobado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamosolicitud/'+idcredito+'/edit?view=detalle',result:'#cont-resultado-preaprobado'});
  }

  // Aprobados
  function detalle_aprobado(idtienda,idcredito) {
      $('#cont-tabla-aprobado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamosolicitud/'+idcredito+'/edit?view=detalle',result:'#cont-resultado-aprobado'});
  }

  // Desembolsados
  function detalle_desembolsado(idtienda,idcredito) {
      $('#cont-tabla-desembolsado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/prestamosolicitud/'+idcredito+'/edit?view=detalle',result:'#cont-resultado-desembolsado'});
  }
</script>
@endsection