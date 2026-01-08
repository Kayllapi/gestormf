function cambiar_sucursal(idsucursal){
    $.ajax({
        url:"{{url('backoffice/'.$tienda->id.'/inicio/show_cambiarsucursal')}}",
        type:'GET',
        data: {
            idsucursal : idsucursal,
        },
        success: function (respuesta){
            location.reload();
        }
    })
}

function actualizar_tabla(tabla){
    load('#load-tabla');
    $.ajax({
        url:"{{url('backoffice/'.$tienda->id.'/inicio/show_actualizartabla')}}",
        type:'GET',
        data: {
            tabla : tabla,
        },
        success: function (respuesta){
            $('#load-tabla').html('');
            $('#tabla-'+tabla).DataTable().ajax.reload();
        }
    })
}  
mostrar_apertura();
function mostrar_apertura(){
    load('#cont-apertura-mostrar');
    $.ajax({
        url:"{{url('backoffice/'.$tienda->id.'/inicio/show_mostrarapertura')}}",
        type:'GET',
        success: function (respuesta){
            $('#cont-apertura-mostrar').html(respuesta['apertura']);
        }
    })
}
mostrar_sucursales();
function mostrar_sucursales(){
    load('#cont-sucursal-mostrar');
    $.ajax({
        url:"{{url('backoffice/'.$tienda->id.'/inicio/show_mostrarsucursales')}}",
        type:'GET',
        success: function (respuesta){
            $('#cont-sucursal-mostrar').html(respuesta['sucursales']);
        }
    })
}