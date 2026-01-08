<div class="modal-header">
    <h5 class="modal-title">
      Registro de Agencias 
      <a href="javascript:;" class="sistema-font" onclick="actualizar_tabla('agencia')"><i class="fa-solid fa-arrows-rotate"></i></a>
      <a href="javascript:;" 
            class="btn btn-primary" 
            onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/agencia/create?view=registrar')}}'})">
            <i class="fa-solid fa-plus"></i> Registrar
        </a>
    </h5>
</div>

    @include('app.nuevosistema.tabla',[
        'tabla' => '#tabla-agencia',
        'route' => url('public/backoffice/tienda/'.$tienda->id.'/sistema_json/agencia.json'),
        'thead' => [
            ['data' => 'Nombre Agencia'],
            ['data' => 'Dirección (Domicilio Fiscal)'],
            ['data' => ''],
        ],
        'tbody' => [
            ['data' => 'nombrecomercial','type'=>'text'],
            ['data' => 'direccion','type'=>'text'],
            ['data' => 'opcion','type'=>'btn'],
        ],
        'tfoot' => [
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => ''],
        ]
    ])