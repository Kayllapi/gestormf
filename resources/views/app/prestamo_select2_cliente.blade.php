    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showlistarprestamousuario')}}",
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
    placeholder: "-- Seleccionar --",
    minimumInputLength: 2,
    allowClear: true,
    @if(!isset($idasesor))
    templateResult: function (state) {
        if (!state.id) {
            return state.text;
        }
        return $('<div>'+
                 '<div>CLIENTE: '+state.text+'</div>'+
                 '<div>ASESOR: '+state.asesor+'</div>');
    }
    @endif