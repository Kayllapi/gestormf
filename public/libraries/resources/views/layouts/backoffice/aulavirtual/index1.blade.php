@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>CURSOS Y CAPACITACIONES</span>
    </div>
</div>
<div class="row">
<div class="container">
  
    <?php $i = 0 ?>
    <?php $num = 0 ?>
    <?php $numant = 0 ?>
    @foreach($cursos as $value)
        @if($i==$num)
        <div class="team-holder fl-wrap">
        <?php $numant = $num+2 ?>
        <?php $num = $num+3 ?>
        @endif
        <?php 
        $counttemas = DB::table('cursomodulo')
            ->join('cursomodulotema','cursomodulotema.idcursomodulo','cursomodulo.id')
            ->where('idcurso',$value->id)
            ->count();
        ?>
        <div class="team-box">
          <div class="cont-box">
            <div class="team-photo">
                <a href="{{ url('backoffice/aulavirtual/'.$value->id.'/edit?view=detalle') }}">
                <?php $rutaimagen = getcwd().'/public/backoffice/usuario/'.$value->idusers.'/aulavirtual/'.$value->imagen; ?>
                @if(file_exists($rutaimagen))
                    <img src="{{ url('public/backoffice/usuario/'.$value->idusers.'/aulavirtual/'.$value->imagen) }}" class="respimg">
                @else
                    <img src="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}" class="respimg">
                @endif
                </a>
            </div>
            <div class="team-info">
                <h3><a href="{{ url('backoffice/aulavirtual/'.$value->id.'/edit?view=detalle') }}" style="color: #ffffff;">{{ $value->nombre }}</a></h3>
                <h4><i class="fa fa-video"></i> {{ $counttemas }} videos</h4>
            </div>
          </div>
        </div>
        @if($i==$numant)
        </div>
        @endif
        <?php $i = $i+1 ?>
    @endforeach    
</div>
</div>
@endsection