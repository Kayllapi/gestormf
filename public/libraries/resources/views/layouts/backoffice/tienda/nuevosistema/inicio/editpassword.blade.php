<form @include('app.nuevosistema.submit',['method'=>'PUT','view'=>'editpassword','id'=>$usuario->id])>
    <div class="profile-edit-container">
        <div class="custom-form no-icons">
            <div class="pass-input-wrap fl-wrap">
                <label>Contraseña Actual *</label>
                
                <input type="password" class="pass-input" value="" id="antpassword"/>
            </div>
            <div class="pass-input-wrap fl-wrap">
                <label>Nueva Contraseña *</label>
                
                <input type="password" class="pass-input" value="" id="password"/>
            </div>
            <div class="pass-input-wrap fl-wrap">
                <label>Confirmar Nueva Contraseña *</label>
                
                <input type="password" class="pass-input" value="" id="password_confirmation"/>
            </div>
            <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
        </div>
    </div> 	
</form>