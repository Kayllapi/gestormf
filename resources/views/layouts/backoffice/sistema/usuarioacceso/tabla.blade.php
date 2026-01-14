
<div class="modal-header">
    <h5 class="modal-title">
        Usuarios
        <a href="javascript:;" class="sistema-font" onclick="actualizar_tabla('usuarioacceso')"><i class="fa-solid fa-arrows-rotate"></i></a>
        <a href="javascript:;" 
            class="btn btn-primary" 
            onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuarioacceso/create?view=registrar&modulo=usuarioacceso')}}'})">
            <i class="fa-solid fa-plus"></i> Registrar
        </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    @include('app.nuevosistema.tabla',[
        'tabla' => '#tabla-usuarioacceso',
        'route' => url('public/backoffice/tienda/'.$tienda->id.'/sistema_json/usuarioacceso.json'),
        'thead' => [
            ['data' => 'Código'],
            ['data' => 'RUC/DNI'],
            ['data' => 'Apellidos y Nombres'],
            ['data' => 'Usuario'],
            ['data' => 'Empresa'],
            ['data' => 'Agencia'],
            ['data' => 'Cargo'],
            ['data' => 'Estado'],
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
                'data' => 'json:estado',
                'type' => 'select',
            ],
            ['type' => ''],
        ]
    ])
</div>