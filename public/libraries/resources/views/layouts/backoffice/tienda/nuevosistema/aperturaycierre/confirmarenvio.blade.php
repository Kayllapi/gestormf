<form @include('app.nuevosistema.submit', ['method' => 'PUT',
      'view' => 'confirmarenvio',
      'id'   => '$s_aperturacierre->id'])>
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Caja</label>
                <select id="idcaja" disabled>
                    <option></option>
                    @foreach($s_cajas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Monto asignado</label>
                <input type="number" value="{{ $s_aperturacierre->montoasignar }}" id="montoasignar" disabled/>
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
        </div>
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Esta seguro de Confirmar!
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn mx-btn-post">Confirmar</button>
        </div>
    </div> 

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
