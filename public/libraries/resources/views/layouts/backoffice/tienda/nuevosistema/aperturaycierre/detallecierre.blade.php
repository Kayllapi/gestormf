<div class="row">
   <div class="col-md-6">
      <label>Caja</label>
      <input type="text" value="{{ $s_aperturacierre->cajanombre }}" disabled/>
      <div class="row">
          <div class="col-md-6">
              <label>Monto cerrado en Soles</label>
              <input type="number" value="{{ $s_aperturacierre->montocierre }}" disabled/>
          </div>
          <div class="col-md-6">
              <label>Monto cerrado en Dolares</label>
              <input type="number" value="{{ $s_aperturacierre->montocierre_dolares }}" disabled/>
          </div>
      </div>
      <label>Persona asignado</label>
      <input type="text" value="{{ $s_aperturacierre->usersrecepcionapellidos }}, {{ $s_aperturacierre->usersrecepcionnombre }}" disabled/>
   </div>
   <div class="col-md-6">
      <label>Persona responsable</label>
      <input type="text" value="{{ $s_aperturacierre->usersresponsableapellidos }}, {{ $s_aperturacierre->usersresponsablenombre }}" disabled/>
      <label>Fecha de Cierre</label>
      <input type="text" value="{{ date_format(date_create($s_aperturacierre->fechacierre),"d/m/Y h:i:s A") }}" disabled/>
      <label>Fecha de Confirmación de Cierre</label>
      <input type="text" value="{{ date_format(date_create($s_aperturacierre->fechacierreconfirmacion),"d/m/Y h:i:s A") }}" disabled/>
   </div>
 </div>
     