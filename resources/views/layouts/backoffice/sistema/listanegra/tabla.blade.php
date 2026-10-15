<div class="modal-header">
    <h5 class="modal-title" style="width: 100%;">
        Lista Negra
        <a href="javascript:;" 
            class="btn btn-primary" 
            onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/listanegra/create?view=registrar')}}'})">
            <i class="fa-solid fa-plus"></i> Registrar
        </a>
        <a href="javascript:;" 
            class="btn btn-info" 
            style="margin-left: 70%;"
            onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/listanegra/create?view=reporte')}}',size:'modal-fullscreen'})">
            <i class="fa-solid fa-file-pdf"></i> Reporte
        </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    @include('app.nuevosistema.tabla',[
        'tabla' => '#tabla-listanegra',
        'route' => url('backoffice/'.$tienda->id.'/listanegra/show_table'),
        'type' => 'GET',
        'scrollY' => 'calc(-198px + 100vh)',
        'thead' => [
            ['data' => 'RUC/DNI/CE'],
            ['data' => 'Cliente'],
            ['data' => 'Motivo <span style="background-color: #ffb2b2 !important; color: #c40000 !important; text-decoration: underline;font-weight: bold;">(Si es Fraude o Lavado de Activos, no indicar al cliente)</span>', 'html' => true],
            ['data' => 'Registrado por'],
            ['data' => 'Fecha'],
            ['data' => 'Agencia'],
            ['data' => ''],
        ],
        'tbody' => [
            ['data' => 'identificacion','type'=>'code'],
            ['data' => 'cliente','type'=>'text'],
            ['data' => 'motivo','type'=>'text'],
            ['data' => 'usuariocodigo','type'=>'text'],
            ['data' => 'fecha','type'=>'date'],
            ['data' => 'agencia','type'=>'text'],
            ['data' => 'opcion','type'=>'btn'],
        ],
        'tfoot' => [
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => ''],
        ]
    ])
</div>

