<div class="modal-header">
    <h5 class="modal-title">
      Clientes/Garantes Global
      <a href="javascript:;" 
         class="btn btn-primary" 
         onclick="modal({route:'{{url('backoffice/'.$tienda->id.'/usuario/create?view=registrar&modulo=usuario')}}'})">
        <i class="fa-solid fa-plus"></i> Registrar
      </a>
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-6">
            <div class="row">
                <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA</label>
                <div class="col-sm-9">
                    <select class="form-control" id="idagencia">
                        <option></option>
                        <option value="0" selected>TODA LAS AGENCIAS</option>
                        @foreach($agencias as $value)
                            <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-body">
    <div id="cont_loading"></div>
    <div id="cont-usuario">
        @include('app.nuevosistema.tabla',[
            'tabla' => '#tabla-usuario',
            // 'route' => url('backoffice/'.$tienda->id.'/usuarioglobal/show_table'),
            'route' => url('backoffice/'.$tienda->id.'/usuarioglobal/show_table'),
            'type' => 'GET',
            'scrollY' => 'calc(-196px + 100vh)',
            'thead' => [
                ['data' => 'Código'],
                ['data' => 'RUC/DNI/CE'],
                ['data' => 'Cliente'],
                ['data' => 'Distrito - Provincia - Departamento	'],
                ['data' => 'Tipo de Persona'],
                ['data' => 'Tipo Documento'],
                ['data' => 'Último Crédito'],
                ['data' => 'Días sin crédito'],
                ['data' => ''],
            ],
            'tbody' => [
                ['data' => 'codigo','type'=>'code'],
                ['data' => 'identificacion','type'=>'code'],
                ['data' => 'cliente','type'=>'text'],
                ['data' => 'ubigeo','type'=>'text'],
                ['data' => 'persona','type'=>'text'],
                ['data' => 'tipodocumento','type'=>'text'],
                ['data' => 'ultimacredito','type'=>'text'],
                ['data' => 'dias','type'=>'num'],
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
                ['type' => ''],
                ['type' => ''],
            ]
        ])
    </div>
</div>
<script>
    sistema_select2({ input:'#idagencia',val:'{{$tienda->id}}' });

    $("#idagencia").on("change", function(e) {
        filtro();
    });

    filtro();
    function filtro(){
        load('#cont_loading');
        $('#cont-usuario').addClass('d-none');

        var root = '{{url('backoffice/'.$tienda->id.'/usuarioglobal/show_table')}}?idagencia='+$('#idagencia').val();
        $('#tabla-usuario').DataTable().ajax.url(root).load();

        $('#tabla-usuario').on('xhr.dt', function(e, settings, json, xhr){
            $('#cont_loading').html('');
            $('#cont-usuario').removeClass('d-none');
        });
    }
</script>