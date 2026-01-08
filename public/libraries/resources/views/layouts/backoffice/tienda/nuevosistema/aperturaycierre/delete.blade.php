<form @include('app.nuevosistema.submit',['method' => 'DELETE',
      'view'  => 'eliminar',
      'id'    => $s_aperturacierre->id])>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> Esta seguro Elimar la Apertura de la Caja de <b>"{{ $s_aperturacierre->cajanombre }} "</b>!.
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn mx-btn-post"><i class="fa fa-trash"></i> Eliminar</button>
        </div>
    </div> 
</form>                             
