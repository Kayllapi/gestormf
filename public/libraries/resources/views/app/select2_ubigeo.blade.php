    ajax: {
        url:"{{url('inicio/showlistarubigeo')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },

        processResults: function (data) {
           return {
              results: $.map(data, function (item) {
                 return {
                    id: item.id,
                    text: item.text,
                    ubicacion: item.distrito+', '+item.provincia+', '+item.departamento
                 }
              })
           };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2