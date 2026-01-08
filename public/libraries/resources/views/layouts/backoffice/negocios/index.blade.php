@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<style>
table.width200,table.rwd_auto {border:1px solid #4db7fe;width:100%;margin:0 0 50px 0}
		.width200 th,.rwd_auto th {    background: #4db7fe;padding: 5px;text-align: center;color: white;}
		.width200 td,.rwd_auto td {border-bottom:1px solid #ccc;padding:10px;text-align:center}
		.width200 tr:last-child td, .rwd_auto tr:last-child td{border:0}
		
	.rwd {width:100%;overflow:auto;}
		.rwd table.rwd_auto {width:auto;min-width:100%}
			.rwd_auto th,.rwd_auto td {white-space: nowrap;}
			
	@media only screen and (max-width: 760px), (min-width: 768px) and (max-width: 1024px)  
	{
	
		table.width200, .width200 thead, .width200 tbody, .width200 th, .width200 td, .width200 tr { display: block; }
		
		.width200 thead tr { position: absolute;top: -9999px;left: -9999px; }
		
		.width200 tr { border: 1px solid #ccc; }
		
		.width200 td { border: none;border-bottom: 1px solid #ccc; position: relative;padding-left: 50%;text-align:left }
		
		.width200 td:before {  position: absolute; top: 6px; left: 6px; width: 45%; padding-right: 10px; white-space: nowrap;}
		
		.width200 td:nth-of-type(1):before { content: "Nombre"; }
		.width200 td:nth-of-type(2):before { content: "Apellidos"; }
		.width200 td:nth-of-type(3):before { content: "Cargo"; }
		.width200 td:nth-of-type(4):before { content: "Twitter"; }
		.width200 td:nth-of-type(5):before { content: "ID"; }
		
		.descarto {display:none;}
		.fontsize {font-size:10px}
	}
	
	/* Smartphones (portrait and landscape) ----------- */
	@media only screen and (min-width : 320px) and (max-width : 480px) 
	{
		body { width: 320px; }
		.descarto {display:none;}
	}
	
	/* iPads (portrait and landscape) ----------- */
	@media only screen and (min-width: 768px) and (max-width: 1024px) 
	{
		body { width: 495px; }
		.descarto {display:none;}
		.fontsize {font-size:10px}
	}
  .btn {
    padding: 5px 10px;
    border-radius: 6px;
    background: #4db7fe;
    color: #ffffff;
    font-weight: 600;
  }
</style>

<div class="dashboard-list-box fl-wrap">
   <div class="dashboard-header fl-wrap mx-dashboard-list">
       <div class="mx-header-title">Tiendas/Negocios</div>
   </div>
   
      <table class="rwd_auto fontsize">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Monto</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th></th>
          </tr>
        </thead>
        <tbody> 
          <?php $contar = 1;?>
          @foreach($planadquirido as $value)
            <tr>
              <td>{{$contar}}</td>
              <td>{{$value->tiendanombre}}</td>
              <td>{{$value->costo}} </td>
              <td>{{date_format(date_create($value->fechapago),'d-m-Y h:i:s A')}} </td>
              <td>@if($value->idestado==1) Pendiente @else Confirmado @endif</td>
              <td>@if($value->idestado==1) <a href="{{ url('backoffice/negocios/'.$value->id.'/edit?view=confirmar') }}" class="btn">Confirmar</a> @endif </td>
            </tr>
          <?php $contar++;?>
          @endforeach

        </tbody>
      </table>

 </div>                        
@endsection
@section('scriptsbackoffice')
<style>
  .dashboard-listing-table-opt li a.del-btn{
    background-color: #ff3c3c;
  }
.new-dashboard-item {
    background-color: transparent;
    border: 1px solid #1877b7;
    color: #1877b7;
    z-index: 0;
}
.mx-dashboard-list {
    border-top-right-radius: 5px;
    border-top-left-radius: 5px;
    background-color: #4db7fe;
}
.mx-header-search{
    top: 0px;
    margin: auto;
    float: right;
}
.mx-header-title{
    color: #fff;
    float: left;
    font-size: 18px;
    line-height: 2;
}
.mx-header-search-button {
    background: #2f3b59;
}
</style>
@endsection