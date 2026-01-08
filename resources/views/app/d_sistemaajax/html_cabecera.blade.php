<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>{{$nombre}}</span>
      @if(isset($botones))
      @foreach($botones as $value)
      <?php 
      $data = explode(':',$value);
      $btnclass = '';
      $iconclass = '';
      if($data[0]=='registrar'){
          $btnclass = 'btn-warning';
          $iconclass = 'fa-angle-right';
      }
      elseif($data[0]=='atras'){
          $btnclass = 'btn-success';
          $iconclass = 'fa-angle-left';
      }
      ?>
      <a class="btn {{ $btnclass }}" href="javascript:;" onclick="pagina({route:'backoffice/tienda/sistema{{$data[1]}}',result:'#mx-subcuerpo'})"><i class="fa {{ $iconclass }}"></i> {{ $data[2] }}</a></a>
      @endforeach
      @endif
    </div>
</div>