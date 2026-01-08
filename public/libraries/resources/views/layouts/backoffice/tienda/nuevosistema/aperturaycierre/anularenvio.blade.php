<form @include('app.nuevosistema.submit',['method'=>'PUT',
    'view'  => 'anularenvio',
    'id'    =>  $s_aperturacierre->id])>
    <div class="row">
       <div class="col-md-6">
          <label>Caja</label>
          <select id="idcaja" disabled>
              <option></option>
              @foreach($s_cajas as $value)
              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
              @endforeach
          </select>
          <div class="row">
              <div class="col-md-6">
                  <label>Monto asignado en Soles</label>
                  <input type="number" value="{{ $s_aperturacierre->montoasignar }}" id="montoasignar" disabled/>
              </div>
              <div class="col-md-6">
                  <label>Monto asignado en Dolares</label>
                  <input type="number" value="{{ $s_aperturacierre->montoasignar_dolares }}" id="montoasignar_dolares" disabled/>
              </div>
          </div>
       </div>
       <div class="col-md-6">
          <label>Persona responsable *</label>
          <select id="idusersresponsable" disabled>
              <option></option>
              @foreach($users as $value)
              <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
              @endforeach
          </select>
          <label>Persona asignado</label>
          <select id="idusers" disabled>
              <option></option>
              @foreach($users as $value)
              <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
              @endforeach
          </select>
       </div>
     </div>
  <button type="submit" class="btn mx-btn-post">Anular Envio</button>
</form>
<script>
$("#idcaja").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$s_aperturacierre->s_idcaja}}).trigger("change");

$("#idusers").select2({
    placeholder: "---  Seleccionar ---"
}).val({{$s_aperturacierre->idusersrecepcion}}).trigger("change");

$("#idusersresponsable").select2({
    placeholder: "-- Seleccionar --"
}).val({{$s_aperturacierre->idusersresponsable}}).trigger("change");
</script>
