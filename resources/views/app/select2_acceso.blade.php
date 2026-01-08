    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showlistaracceso')}}",
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
    placeholder: "-- Seleccionar --",
    minimumInputLength: 2,
    allowClear: true