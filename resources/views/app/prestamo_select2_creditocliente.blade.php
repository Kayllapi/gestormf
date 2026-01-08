
        ajax: {
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showlistarcreditousuario')}}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                  buscar: params.term,
                  @if(isset($idasesor))
                  idasesor: {{$idasesor}},
                  @endif
                  @if(isset($idestadocredito))
                  idestadocredito: {{$idestadocredito}},
                  idestadodesembolso: 0,
                  @else
                  idestadocredito: 4,
                  idestadodesembolso: 1,
                  @endif
                  @if(isset($idprestamo_estadocredito))
                  idprestamo_estadocredito: {{$idprestamo_estadocredito}},
                  @else
                  idprestamo_estadocredito: 1,
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
            var style_estado = '';
            if(state.estado!='PENDIENTE') {
                style_estado = 'style_estado_cancelado';
            }
            return $('<div class="style_estado '+style_estado+'"><div><b>CRÉDITO:</b> '+state.tipocredito+'</div>'+
                     '<div><b>ESTADO:</b> '+state.estado+' <b>CÓDIGO:</b> '+state.creditocodigo+'</div>'+
                     '<div><b>CLIENTE:</b> '+state.clienteidentificacion+' - '+state.clienteapellidos+', '+state.clientenombre+'</div>'+
                     '<div><b>MONTO:</b> '+state.monedasimbolo+' '+state.creditomonto+'</div></div>');
        },
        templateSelection: function (repo) {
            if (!repo.id) {
                return repo.text;
            }
            return $('<span>'+repo.text+'</span>');
        },