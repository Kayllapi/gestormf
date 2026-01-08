@if(isset($json))  
    <?php 
    $jsondata = explode(':',$json); 
    $urltienda = '';
    $modulo = $json;
    if(count($jsondata)>1){
        $urltienda = $jsondata[0];
        $modulo = $jsondata[1];
    }
    ?>
    @if($urltienda=='tienda')
      @if($modulo=='producto')
          $.getJSON('{{url('public/backoffice/tienda/'.$tienda->id.'/sistema_json/'.$modulo.'_'.Auth::user()->idsucursal.'.json')}}?token='+Math.floor((Math.random() * 100) + 1)).done(function(data) {
      @else
          $.getJSON('{{url('public/backoffice/tienda/'.$tienda->id.'/sistema_json/'.$modulo.'.json')}}?token='+Math.floor((Math.random() * 100) + 1)).done(function(data) {
      @endif
    @else
        $.getJSON('{{url('public/nuevosistema/librerias/json/'.$modulo.'.json')}}?token='+Math.floor((Math.random() * 100) + 1)).done(function(data) {
    @endif
        const responseData = data.data;
        const json_data = (idUbicacion) => {
            return responseData.filter(
                (ubicacion) => ubicacion.id === idUbicacion,
            )[0] || {};
        }
        $('{{$input}}').val(json_data({{$val}}))
    });
@endif