    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showlistarproducto')}}",
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
        var urlimagen = '{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}';
        if(state.imagen!=null){
            urlimagen = '{{ url('public/backoffice/tienda') }}/'+state.idtienda+'/producto/40/'+state.imagen;
        }
        return $('<div>'+
                 '<div style="background-image: url('+urlimagen+');'+
                            'background-repeat: no-repeat;'+
                            'background-size: contain;'+
                            'background-position: center;'+
                            'width: 40px;'+
                            'height: 40px;'+
                            'float: left;'+
                            'margin-right: 5px;'+
                            'margin-top: -5px;">'+
                          '</div><div>'+state.nombre+'</div><div>'+state.unidadmedida+' - '+state.precioalpublico+'</div>');
    },
    templateSelection: function (repo) {
        if (!repo.id) {
            return repo.text;
        }
        if(repo.codigo!=''){
            if(repo.codigo==undefined){
                return $('<span>'+repo.text+'</span>');
            }
            return $('<span>'+repo.codigo+' - '+repo.nombre+' / '+repo.unidadmedida+'</span>');
        }else{
            return $('<span>'+repo.nombre+' / '+repo.unidadmedida+'</span>');
        }
    },