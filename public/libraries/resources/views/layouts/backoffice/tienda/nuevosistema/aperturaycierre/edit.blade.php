<form @include('app.nuevosistema.submit',['method' => 'PUT',
          'view' => 'editar',
            'id' => $s_aperturacierre->id])>
          <div class="row">
             <div class="col-md-6">
                <label>Caja *</label>
                <select id="idcaja">
                    <option></option>
                    @foreach($s_cajas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Saldo anterior</label>
                <input type="text" id="saldoanterior" placeholder="---" readonly/>
                <label>Monto a asignar *</label>
                <input type="number" value="{{ $s_aperturacierre->montoasignar }}" id="montoasignar" step="0.01" min="0"/>
             </div>
             <div class="col-md-6">
                <label>Persona responsable *</label>
                <select id="idusersresponsable" disabled>
                    <option></option>
                    @foreach($users as $value)
                    <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Persona a asignar *</label>
                <select id="idusers">
                    <option></option>
                    @foreach($users as $value)
                    <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
                    @endforeach
                </select>
             </div>
           </div>
  <button type="submit" class="btn mx-btn-post"> Editar</button>
</form>      
<script>
$("#idcaja").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/aperturaycierre/')}}/"+e.currentTarget.value,
        type:'GET',
        data: {
            view : 'saldoanterior'
       },
       success: function (respuesta){
          $("#saldoanterior").val(respuesta.saldoactual);
       }
     })
}).val({{$s_aperturacierre->s_idcaja}}).trigger("change");

$("#idusers").select2({
    placeholder: "---  Seleccionar ---"
}).val({{$s_aperturacierre->idusersrecepcion}}).trigger("change");

$("#idusersresponsable").select2({
    placeholder: "-- Seleccionar --"
}).val({{$s_aperturacierre->idusersresponsable}}).trigger("change");

</script>
