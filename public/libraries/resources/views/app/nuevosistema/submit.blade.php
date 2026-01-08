    action="javascript:;" 
    onsubmit="callback({
        @if($method=='POST')
        route: '{{$_GET['url_sistema']}}/{{ $tienda->id }}/{{$_GET['name_modulo']}}',
        @elseif($method=='PUT')
        route:  '{{$_GET['url_sistema']}}/{{ $tienda->id }}/{{$_GET['name_modulo']}}/{{ $id }}',
        @elseif($method=='DELETE')
        route:  '{{$_GET['url_sistema']}}/{{ $tienda->id }}/{{$_GET['name_modulo']}}/{{ $id }}',
        @endif
        method: '{{$method}}',
        @if(isset($carga))
        carga: '{{$carga}}',
        @endif
        @if(isset($carga))
        id: '{{$carga}}',
        @endif
        data: {
            view: '{{$view}}',
            @if(isset($data))
                @foreach($data as $key => $value)
                '{{$key}}': '{{$value}}',
                @endforeach
            @endif
            @if(isset($function))
                @foreach($function as $key => $value)
                '{{$key}}': {{$value}},
                @endforeach
            @endif
        }
    },
    function(resultado){
        modulo_actualizar('{{$_GET['name_modulo']}}');                                                      
    },this)"