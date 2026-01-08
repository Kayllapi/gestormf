@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Registrar Saldo de Usuario',
    'botones'=>[
        'atras:/usuariosaldo: Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/usuariosaldo',
        method: 'POST',
        data:{
            view: 'registrar',
            idtienda: '{{ $tienda->id }}'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuariosaldo') }}';                                                                            
    },this)">
           <div class="row">
               <div class="col-md-6">
                     <label>Usuario *</label>
                         <div class="row">
                            <div class="col-md-9">
                                <select id="idcliente">
                                    <option></option>
                                 </select>
                            </div>
                            <div class="col-md-3">
                                <a href="javascript:;" id="modal-registrarcliente" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                            </div>
                         </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                       <div class="col-md-4">
                         <label>Monto *</label>
                            <input type="number" id="monto"  step="0.01" min="0" />
                       </div>
                      <div class="col-md-8">
                        <label>Motivo *</label>  
                             <input type="text" id="motivo"/>  
                      </div>
                    </div>
                </div>
           </div>
    <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>                             
@endsection
@section('subscripts')
@include('app.modal_usuario_registrar',[
    'nombre'            =>'Registrar Usuario',
    'modal'             =>'registrarcliente',
    'idusuario'         =>'idcliente',
    'usuariodireccion'  =>'direccion',
    'usuarioubigeo'     =>'idubigeo'
])
<script>
$('#idcliente').select2({
    @include('app.select2_cliente')
});
</script>
@endsection
