<div class="modal-header">
    <h5 class="modal-title">
      | CLIENTES |
      <a href="javascript:;" class="sistema-font" onclick="actualizar_tabla('usuario')"><i class="fa-solid fa-arrows-rotate"></i></a>
      <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuario/create?view=registrar&modulo=usuario')}}'})">
        <i class="fa-solid fa-plus"></i> Registrar
      </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
    @include('app.nuevosistema.tabla',[
        'tabla' => '#tabla-usuario',
        'route' => url('public/backoffice/tienda/'.$tienda->id.'/sistema_json/usuario.json'),
        'thead' => [
            ['data' => 'Código'],
            ['data' => 'RUC/DNI/CE'],
            ['data' => 'Cliente'],
            ['data' => 'Distrito - Provincia - Departamento	'],
            ['data' => 'Tipo de Persona'],
            ['data' => 'Tipo Documento'],
            ['data' => ''],
        ],
        'tbody' => [
            ['data' => 'codigo','type'=>'code'],
            ['data' => 'identificacion','type'=>'code'],
            ['data' => 'cliente','type'=>'text'],
            ['data' => 'ubigeo','type'=>'text'],
            ['data' => 'persona','type'=>'text'],
            ['data' => 'tipodocumento','type'=>'text'],
            ['data' => 'opcion','type'=>'btn'],
        ],
        'tfoot' => [
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            ['type' => 'text'],
            [
                'data' => [
                    [
                        'id' => 1,
                        'text' => 'NATURAL'
                    ],
                    [
                        'id' => 2,
                        'text' => 'JURÍDICA'
                    ]
                ],
                'type' => 'select',
            ],
            [
                'data' => [
                    [
                        'id' => 1,
                        'text' => 'DNI'
                    ],
                    [
                        'id' => 2,
                        'text' => 'RUC'
                    ],
                    [
                        'id' => 3,
                        'text' => 'CE'
                    ]
                ],
                'type' => 'select',
            ],
            ['type' => ''],
        ]
    ])
</div>