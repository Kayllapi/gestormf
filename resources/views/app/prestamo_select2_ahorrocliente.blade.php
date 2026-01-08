
        ajax: {
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showlistarahorrousuario')}}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                  buscar: params.term,
                  @if(isset($idasesor))
                  idasesor: {{$idasesor}}
                  @endif
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        placeholder: "--  Seleccionar Producto --",
        allowClear: true,
        minimumInputLength: 2,
        templateResult: function (state) {
            if (!state.id) {
                return state.text;
            }
            return $('<div><b>AHORRO:</b> '+state.tipoahorro+'</div>'+
                     '<div><b>ESTADO:</b> '+state.estado+' <b>CÓDIGO:</b> '+state.ahorrocodigo+'</div>'+
                     '<div><b>CLIENTE:</b> '+state.clienteidentificacion+' - '+state.clienteapellidos+', '+state.clientenombre+'</div>'+
                     '<div><b>MONTO A AHORRAR:</b> '+state.monedasimbolo+' '+state.ahorromonto+'</div>');
        },
        templateSelection: function (repo) {
            if (!repo.id) {
                return repo.text;
            }
            return $('<span>'+repo.text+'</span>');
        },