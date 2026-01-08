@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuración de Comida',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])
<div class="tabs-container" id="tab-menuconfiguracioncomida">
    <ul class="tabs-menu">
        <li class="current"><a href="#tab-menuconfiguracioncomida-0">General</a></li>
        <li><a href="#tab-menuconfiguracioncomida-1">Pisos</a></li>
    </ul>
    <div class="tab">
        <div id="tab-menuconfiguracioncomida-0" class="tab-content" style="display: block;">
        </div>

        <div id="tab-menuconfiguracioncomida-1" class="tab-content" style="display: none;">
            <div id="cont-resultado-piso"></div>
        </div>
    </div>
</div>
@endsection
@section('subscripts')
<script>
  tab({click:'#tab-menuconfiguracioncomida'});
</script>
<!-- Mesas -->
<script>
    index_piso();
    // Pisos
    function index_piso() {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/0/edit?view=comida_indexpiso',result:'#cont-resultado-piso'});
    }
    function registrar_piso(idtienda) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/0/edit?view=comida_registrarpiso',result:'#cont-resultado-piso'});
    }
    function editar_piso(idtienda, idpiso) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/'+idpiso+'/edit?view=comida_editarpiso',result:'#cont-resultado-piso'});
    }
    function anular_piso(idtienda, idpiso) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/'+idpiso+'/edit?view=comida_anularpiso',result:'#cont-resultado-piso'});
    }
    // Fin Pisos
  
    // Ambientes
    function index_ambiente(idtienda, idpiso) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/0/edit?view=comida_indexambiente&idpiso='+idpiso,result:'#cont-resultado-piso'});
    }
    function registrar_ambiente(idtienda, idpiso) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/0/edit?view=comida_registrarambiente&idpiso='+idpiso,result:'#cont-resultado-piso'});
    }
    function editar_ambiente(idtienda, idpiso, idambiente) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/'+idambiente+'/edit?view=comida_editarambiente&idpiso='+idpiso,result:'#cont-resultado-piso'});
    }
    function anular_ambiente(idtienda, idpiso, idambiente) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/'+idambiente+'/edit?view=comida_anularambiente&idpiso='+idpiso,result:'#cont-resultado-piso'});
    }
    // Fin Ambientes
  
    // Mesas
    function index_mesa(idtienda, idpiso, idambiente) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/0/edit?view=comida_indexmesa&idpiso='+idpiso+'&idambiente='+idambiente,result:'#cont-resultado-piso'});
    }
    function registrar_mesa(idtienda, idpiso, idambiente) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/0/edit?view=comida_registrarmesa&idpiso='+idpiso+'&idambiente='+idambiente,result:'#cont-resultado-piso'});
    }
    function editar_mesa(idtienda, idpiso, idambiente, idmesa) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/'+idmesa+'/edit?view=comida_editarmesa&idpiso='+idpiso+'&idambiente='+idambiente,result:'#cont-resultado-piso'});
    }
    function anular_mesa(idtienda, idpiso, idambiente, idmesa) {
      pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id) }}/configuracion/'+idmesa+'/edit?view=comida_anularmesa&idpiso='+idpiso+'&idambiente='+idambiente,result:'#cont-resultado-piso'});
    }
    // Fin Mesas
</script>
@endsection