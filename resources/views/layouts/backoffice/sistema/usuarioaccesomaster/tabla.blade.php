
<div class="modal-header">
    <h5 class="modal-title">
        Gerentes generales
        @if($puede_registrar ?? false)
        <a href="javascript:;" 
            class="btn btn-primary" 
            onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuarioaccesomaster/create?view=registrar&modulo=usuarioaccesomaster')}}'})">
            <i class="fa-solid fa-plus"></i> Registrar
        </a>
        @endif
    </h5>
    <div class="row" style="font-weight: normal; margin-left: -500px;">
        <label class="col-sm-3 col-form-label" style="font-weight: bold;">AGENCIA:</label>
        <div class="col-sm-9">
            <input type="text" class="form-control"
                style="background-color: #d1d1d1 !important;"
                value="Todas las agencias" disabled>
        </div>
    </div>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    <script>
        $(document).off('init.dt.usuarioaccesomaster', '#tabla-usuarioaccesomaster')
            .one('init.dt.usuarioaccesomaster', '#tabla-usuarioaccesomaster', function (event, settings) {
                var tabla = new $.fn.dataTable.Api(settings);
                $(tabla.column(7).footer()).find('select[buscador="true"]')
                    .attr('id', 'filtro-estado-usuarioaccesomaster')
                    .find('option[value=""]').val('TODO').text('TODO');
                sistema_select2({ input: '#filtro-estado-usuarioaccesomaster', val: 'TODO' });
            });
    </script>
    @include('app.nuevosistema.tabla',[
        'tabla' => '#tabla-usuarioaccesomaster',
        'route' => url('backoffice/'.$tienda->id.'/usuarioaccesomaster/show_table'),
        'type' => 'GET',
        'scrollY' => 'calc(-196px + 100vh)',
        'thead' => [
            ['data' => 'Código'],
            ['data' => 'RUC/DNI'],
            ['data' => 'Apellidos y Nombres'],
            ['data' => 'Usuario'],
            ['data' => 'Empresa'],
            ['data' => 'Agencia'],
            ['data' => 'Cargo'],
            ['data' => 'Estado de Acceso de Usuario'],
            ['data' => ''],
        ],
        'tbody' => [
            ['data' => 'codigo','type'=>'text'],
            ['data' => 'identificacion','type'=>'text'],
            ['data' => 'cliente','type'=>'text'],
            ['data' => 'usuario','type'=>'text'],
            ['data' => 'empresa','type'=>'text'],
            ['data' => 'agencia','type'=>'text'],
            ['data' => 'cargo','type'=>'text'],
            ['data' => 'idestadousuario','type'=>'badge'],
            ['data' => 'opcion','type'=>'btn'],
        ],
        'tfoot' => [
            ['type' => ''],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            [
                'data' => [
                    [
                        'id' => '1',
                        'text' => 'ACTIVO'
                    ],
                    [
                        'id' => '2',
                        'text' => 'DESHABILITADO'
                    ]
                ],
                'type' => 'select',
            ],
            ['type' => ''],
        ]
    ])
</div>
