<div class="modal-header">
    <h5 class="modal-title">
      Agencias
      <a href="javascript:;" class="sistema-font" onclick="actualizar_tabla('sucursal')"><i class="fa-solid fa-arrows-rotate"></i></a>
      <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/sucursal/create?view=registrar&modulo=sucursal')}}'})">
        <i class="fa-solid fa-plus"></i> Registrar
      </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
    @include('app.nuevosistema.tabla',[
        'tabla' => '#tabla-sucursal',
        'route' => url('public/backoffice/tienda/'.$tienda->id.'/sistema_json/sucursal.json'),
        'thead' => [
            ['data' => 'Nombre Empresa'],
            ['data' => 'Agencia'],
            ['data' => 'Representante'],
            ['data' => 'Dirección'],
            ['data' => 'Telefono'],
            ['data' => 'Tipo Empresa'],
            ['data' => ''],
        ],
        'tbody' => [
            ['data' => 'nombre','type'=>'text'],
            ['data' => 'nombreagencia','type'=>'text'],
            ['data' => 'representante','type'=>'text'],
            ['data' => 'direccion','type'=>'text'],
            ['data' => 'telefono','type'=>'text'],
            ['data' => 'tipoempresa','type'=>'text'],
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

