
    ajax: {
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showlistarcreditousuario')}}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                      buscar: params.term
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
            return $('<div><b>CRÉDITO:</b> '+state.tipocredito+''+
                     '<div><b>ESTADO:</b> '+state.estado+' <b>CÓDIGO:</b> '+state.creditocodigo+'</div>'+
                     '<div><b>CLIENTE:</b> '+state.clienteidentificacion+' - '+state.clienteapellidos+', '+state.clientenombre+'</div>'+
                     '<div><b>MONTO:</b> '+state.monedasimbolo+' '+state.creditomonto+'</div>');
        },
        templateSelection: function (repo) {
            if (!repo.id) {
                return repo.text;
            }
            return $('<span>'+repo.text+'</span>');
        },