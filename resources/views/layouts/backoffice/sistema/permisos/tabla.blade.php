<div class="modal-header">
    <h5 class="modal-title">
        Cargos/Permisos
        <a href="javascript:;" 
            class="btn btn-primary" 
            onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/permisos/create?view=registrar')}}'})">
            <i class="fa-solid fa-plus"></i> Registrar
        </a>        
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    @include('app.nuevosistema.tabla',[
        'tabla' => '#tabla-permisos',
        'route' => url('backoffice/'.$tienda->id.'/permisos/show_table'),
        'type' => 'GET',
        'thead' => [
            ['data' => 'Rango' ],
            ['data' => 'Nombre' ],
            ['data' => ''],
        ],
        'tbody' => [
            ['data' => 'rango','type'=>'text'],
            ['data' => 'nombre','type'=>'text'],
            ['data' => 'opcion','type'=>'btn'],
        ],
        'tfoot' => [
            ['type' => ''],
            ['type' => 'text'],
            ['type' => ''],
        ]
    ])
</div>


