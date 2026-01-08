@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<section class="scroll-con-sec hero-section" data-scrollax-parent="true" id="sec1">
    <div class="bg"  data-bg="{{ url('public/backoffice/sistema/banner-1.png') }}" data-scrollax="properties: { translateY: '200px' }"></div>
    <div class="overlay"></div>
    <div class="hero-section-wrap fl-wrap">
        <div class="container">
            <div data-v-04cc2f02="" class="landing__categories">
            <?php
            $modulos = DB::table('modulo')
              ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
              ->join('roles','roles.id','rolesmodulo.idroles')
              ->join('role_user','role_user.role_id','roles.id')
              ->where('role_user.user_id',Auth::user()->id)
              ->where('modulo.idmodulo',7)
              ->where('modulo.idestado',1)
              ->select('modulo.*')
              ->orderBy('modulo.orden','asc')
              ->get();
            ?>
            @foreach($modulos as $value)
              
                <?php 
                $rutaimagen = getcwd().'/public/backoffice/sistema/modulo/'.$value->imagen; 
                if(file_exists($rutaimagen) AND $value->imagen!=''){
                    $urlimagen = url('public/backoffice/sistema/modulo/'.$value->imagen);
                }else{
                    $urlimagen = url('public/backoffice/sistema/sin_imagen_redondo.png');
                }
                ?>
                <a href="javascript:;" onclick="ir_submodulo({{ $value->id }})" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-modulo">
                  <img data-v-b789b216="" src="{{$urlimagen}}" class="category-bubble-icon">
                  <h2 data-v-b789b216="" class="category-bubble-title">{{ $value->nombre }}</h2>
                </a>
              
                    <?php
                    $submodulos = DB::table('modulo')
                      ->join('rolesmodulo','rolesmodulo.idmodulo','modulo.id')
                      ->join('roles','roles.id','rolesmodulo.idroles')
                      ->join('role_user','role_user.role_id','roles.id')
                      ->where('role_user.user_id',Auth::user()->id)
                      ->where('modulo.idmodulo',$value->id)
                      ->where('modulo.idestado',1)
                      ->select('modulo.*')
                      ->orderBy('modulo.orden','asc')
                      ->get();
                    $ii = 0;
                    ?>
                    @foreach($submodulos as $subvalue)
                        <?php 
                        $rutasubimagen = getcwd().'/public/backoffice/sistema/modulo/'.$subvalue->imagen; 
                        if(file_exists($rutasubimagen) AND $subvalue->imagen!=''){
                            $urlsubimagen = url('public/backoffice/sistema/modulo/'.$subvalue->imagen);
                        }else{
                            $urlsubimagen = url('public/backoffice/sistema/sin_imagen_redondo.png');
                        }
                        ?>
                        @if($ii==0)
                        <a href="javascript:;" id="cont-submodulo{{ $value->id }}" onclick="ir_modulo()" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" style="display:none;">
                          <img data-v-b789b216="" src="{{url('public/backoffice/sistema/modulo/atras.png')}}" class="category-bubble-icon">
                          <h2 data-v-b789b216="" class="category-bubble-title">Atras</h2>
                        </a>
                        @endif
                        <?php $href = str_replace('{idtienda}', $idtienda, $subvalue->vista); ?>
                        <a href="{{ url($href) }}" id="cont-submodulo{{ $value->id }}" data-v-b789b216="" data-v-04cc2f02="" class="category-bubble cont-submodulo" style="display:none;">
                          <img data-v-b789b216="" src="{{$urlsubimagen}}" class="category-bubble-icon">
                          <h2 data-v-b789b216="" class="category-bubble-title">{{ $subvalue->nombre }}</h2>
                        </a>
                        <?php $ii++ ?>
                    @endforeach
            @endforeach
            </div>
        </div>
    </div>
</section>

                    
<style>
  section.hero-section {
      padding: 100px 0 100px;
  }
  .landing__categories[data-v-04cc2f02] {
      justify-content: center;
      flex-direction: row;
      display: flex;
      flex-wrap: wrap;
      margin: 0 auto;
  }
  .landing__categories>.category-bubble[data-v-04cc2f02] {
      margin: 12.5px;
  }
  .category-bubble[data-v-b789b216] {
      background-color: #fff;
      border-radius: 5px;
      cursor: pointer;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      box-shadow: 0 2px 17px 0 #000000;
      height: 114.2px;
      width: 114.2px;
      text-align: center;
      transition: .3s;
      text-decoration: none;
      color: #000;
      font-weight: 300;
      margin: 20px 0 -10px;
  }
  .category-bubble .category-bubble-icon[data-v-b789b216] {
      margin-top: 10px;
      height: 60px;
  }
  .category-bubble .category-bubble-title[data-v-b789b216] {
      line-height: 16px;
      margin-top: 3px;
      font-weight: 300;
      /*width: 73px;*/
      margin-left: auto;
      margin-right: auto;
      overflow: hidden;
      font-size: 14px;
  }
  .category-bubble[data-v-b789b216]:hover {
      height: 130px;
      width: 130px;
      margin-left: 4.5px;
      margin-right: 4.5px;
      margin-top: -4px;
  }
  .category-bubble:hover .category-bubble-icon[data-v-b789b216] {
      height: 80px;
  }
  @media only screen and  (max-width: 440px) {
      .category-bubble[data-v-b789b216] {
          height: 90px;
          width: 90px;
      }
      .category-bubble .category-bubble-icon[data-v-b789b216] {
          margin-top: 13px;
          height: 40px;
      }
      .category-bubble[data-v-b789b216]:hover {
          height: 90px;
          width: 90px;
      }
      .category-bubble .category-bubble-icon[data-v-b789b216]:hover {
          margin-top: 13px;
          height: 40px;
      }
      section.hero-section {
          padding: 30px 0 30px;
      }
      .landing__categories>.category-bubble[data-v-04cc2f02] {
          margin: 8px;
      }
      .category-bubble .category-bubble-title[data-v-b789b216] {
          font-size: 12px;
      }
  }
</style>
@endsection
@section('subscripts')
<script>
    function ir_submodulo(idmodulo){
        $('a#cont-submodulo'+idmodulo).css('display','block');
        $('.cont-modulo').css('display','none');
    }
    function ir_modulo(){
        $('.cont-modulo').css('display','block');
        $('.cont-submodulo').css('display','none');
    }
</script>
@endsection