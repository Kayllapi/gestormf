<div class="modal-header">
    <h5 class="modal-title">
        Lista Negra
        <a href="javascript:;" 
            class="btn btn-primary" 
            onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/listanegra/create?view=registrar')}}'})">
            <i class="fa-solid fa-plus"></i> Registrar
        </a>
        <a href="javascript:;" 
            class="btn btn-warning" 
            onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/listanegra/create?view=reporte')}}'})">
            <i class="fa-solid fa-bar"></i> Reporte
        </a>
        
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    @include('app.nuevosistema.tabla',[
        'tabla' => '#tabla-listanegra',
        'route' => url('backoffice/'.$tienda->id.'/listanegra/show_table'),
        'type' => 'GET',
        'thead' => [
            ['data' => 'RUC/DNI/CE'],
            ['data' => 'Cliente'],
            ['data' => 'Motivo'],
            ['data' => 'Fecha'],
            ['data' => 'Estado'],
            ['data' => ''],
        ],
        'tbody' => [
            ['data' => 'identificacion','type'=>'code'],
            ['data' => 'cliente','type'=>'text'],
            ['data' => 'motivo','type'=>'text'],
            ['data' => 'fecha','type'=>'date'],
            ['data' => 'estado','type'=>'text'],
            ['data' => 'opcion','type'=>'btn'],
        ],
        'tfoot' => [
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'date'],
            ['type' => 'text'],
        ]
    ])
</div>


