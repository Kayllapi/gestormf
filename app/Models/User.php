<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'fechaeliminado',
      'fechamodificacion',
      'iduser_modificacion',
      'nombre',
      'apellidopaterno',
      'apellidomaterno',
      'nombrecompleto',
      'razonsocial',
      'identificacion',
      'email',
      'email_verified_at',
      'usuario',
      'clave',
      'password',
      'numerotelefono',
      'direccion',
      'imagen',
      'iduserspadre',
      'iduserspatrocinador',
      'idubigeo',
      'idtipopersona',
      'idtipousuario',
      'idtienda',
      'idestadousuario',
      'idestado'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
  
    // ROLES
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
  
    public function authorizeRoles($roles,$idtienda=0)
    {
        if($idtienda!=0){
            abort_unless($this->hasAnyRole($roles,$idtienda), 401);
        }
        return true;
    }
    public function hasAnyRole($roles,$idtienda)
    {
        if ($this->hasRole($roles,$idtienda)) {
            return true; 
        }   
        return false;
    }
    public function hasRole($role,$idtienda)
    {

        $list_vista = explode('/',$role);
        $role = $list_vista[0].'/{idtienda}/'.$list_vista[2];
        if(Auth::user()->id==1){
            if($this->roles()
                ->join('rolesmodulo','rolesmodulo.idroles','roles.id')
                ->join('modulo','modulo.id','rolesmodulo.idmodulo')
                ->where('modulo.vista',$role)
                ->where('modulo.idestado',1)
                ->first()) {
                return true;
            }
        }else{
            if($this->roles()
                ->join('users','users.id','role_user.user_id')
                ->join('usersrolesmodulo','usersrolesmodulo.idusers','users.id')
                ->join('modulo','modulo.id','usersrolesmodulo.idmodulo')
                ->where('modulo.vista',$role)
                ->where('modulo.idestado',1)
                ->where('users.idtienda',$idtienda)
                ->first()) {
                return true;
            }
        }
        return false;
    }
}
