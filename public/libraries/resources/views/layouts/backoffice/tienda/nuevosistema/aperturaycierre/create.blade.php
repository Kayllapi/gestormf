<form @include('app.nuevosistema.submit',['method'=>'POST','view'=>'registrar'])>
  <div class="row">
    <div class="col-6">
          <label>Caja *</label>
          <select id="idcaja">
              <option></option>
              @foreach($s_cajas as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
          <div class="row">
                    <div class="col-md-6">
                        <label>Monto a asignar en Soles *</label>
                        <input type="number" value="0.00" id="montoasignar" step="0.01" min="0"/>
                    </div>
                    <div class="col-md-6">
                        <label>Monto a asignar en Dolares *</label>
                        <input type="number" value="0.00" id="montoasignar_dolares" step="0.01" min="0"/>
                    </div>
                </div>
    </div>
    <div class="col-6">
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
     <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>


</form> 

<script>
$("#idcaja").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    /*$.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/aperturaycierre/')}}/"+e.currentTarget.value,
        type:'GET',
        data: {
            view : 'saldoanterior'
       },
       success: function (respuesta){
          $("#saldoanterior").val(respuesta.saldoactual);
       }
     })*/
});

$("#idusers").select2({
    placeholder: "-- Seleccionar --"
}).val({{Auth::user()->id}}).trigger("change");

$("#idusersresponsable").select2({
    placeholder: "-- Seleccionar --"
}).val({{Auth::user()->id}}).trigger("change");
</script>
