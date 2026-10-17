
<div class="modal-header">
    <h5 class="modal-title">
        Usuarios
        <a href="javascript:;" 
            class="btn btn-primary" 
            onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuarioacceso/create?view=registrar&modulo=usuarioacceso')}}'})">
            <i class="fa-solid fa-plus"></i> Registrar
        </a>
    </h5>
    <div class="row" style="font-weight: normal; margin-left: -500px;">
        <label class="col-sm-3 col-form-label" style="font-weight: bold;">AGENCIA:</label>
        <div class="col-sm-9">
            <input type="text" class="form-control"
                style="background-color: #d1d1d1 !important;"
                value="{{$tienda->nombreagencia}}" disabled>
        </div>
    </div>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    @include('app.nuevosistema.tabla',[
        'tabla' => '#tabla-usuarioacceso',
        'route' => url('backoffice/'.$tienda->id.'/usuarioacceso/show_table'),
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