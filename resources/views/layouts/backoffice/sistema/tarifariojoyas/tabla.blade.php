<div class="modal-header">
    <h5 class="modal-title">
      Valorización de tipos de oro para Joyas
      <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/tarifariojoyas/create?view=registrar')}}'})">
        <i class="fa-solid fa-plus"></i> Registrar
      </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>

<div class="modal-body">
    @include('app.nuevosistema.tabla',[
      'tabla' => '#tabla-tarifariojoyas',
      'route' => url('backoffice/'.$tienda->id.'/tarifariojoyas/show_table'),
      'type' => 'GET',
      'thead' => [
          ['data' => 'Tipo de Oro' ],
          ['data' => 'Precio x Gramo (S/.)' ],
          ['data' => 'Cobertura (%)' ],
          ['data' => 'F. Actualización' ],
          ['data' => ''],
      ],
      'tbody' => [
          ['data' => 'tipo','type'=>'text'],
          ['data' => 'precio','type'=>'text'],
          ['data' => 'cobertura','type'=>'text'],
          ['data' => 'fecha','type'=>'date'],
          ['data' => 'opcion','type'=>'btn'],
      ]
    ])
</div>

