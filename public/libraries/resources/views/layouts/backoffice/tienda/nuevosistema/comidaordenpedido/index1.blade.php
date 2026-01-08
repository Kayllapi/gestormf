@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Orden de Pedido',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])

        @foreach ($pisos as $valuepiso)
          <?php
            $ambientes = DB::table('s_comida_ambiente')
              ->where([
                ['s_comida_mesa.idtienda', $tienda->id],
                ['s_comida_mesa.idpiso', $valuepiso->id]
              ])
              ->get();
          ?>
          @if ($valuepiso->id == 1)
            <div id="tab-ordenpedido-pisos-{{ $valuepiso->id }}" class="tab-content" style="display: block;">
                @if (count($ambientes) != 1)
                <div class="tabs-container" id="tab-ordenpedido-ambientes{{ $valuepiso->id }}">
                    <ul class="tabs-menu">
                        @foreach ($ambientes as $item_ambiente)
                          @if ($item_ambiente->id == 1)
                            <li class="current"><a href="#tab-ordenpedido-ambientes-{{ $item_ambiente->id }}">{{ $item_ambiente->nombre }}</a></li>
                          @else
                            <li><a href="#tab-ordenpedido-ambientes-{{ $item_ambiente->id }}">{{ $item_ambiente->nombre }}</a></li>
                          @endif
                        @endforeach
                    </ul>
                    <div class="tab">
                        @foreach ($ambientes as $item_ambiente)
                          <?php
                              $mesas = DB::table('s_comida_mesa')
                                ->join('s_comida_piso', 's_comida_piso.id', 's_comida_mesa.idpiso')
                                ->join('s_comida_ambiente', 's_comida_ambiente.id', 's_comida_mesa.idambiente')
                                ->where([
                                  ['s_comida_mesa.idtienda', $tienda->id],
                                  ['s_comida_mesa.idpiso', $valuepiso->id],
                                  ['s_comida_mesa.idambiente', $item_ambiente->id],
                                ])
                                ->select(
                                  's_comida_mesa.*',
                                  's_comida_piso.nombre as nombre_piso',
                                  's_comida_ambiente.nombre as nombre_ambiente'
                                )
                                ->orderBy('s_comida_mesa.idpiso','asc')
                                ->orderBy('s_comida_mesa.idambiente','asc')
                                ->orderBy('s_comida_mesa.numero_mesa','asc')
                                ->get();
                          ?>
                          @if ($item_ambiente->id == 1)
                            <div id="tab-ordenpedido-ambientes-{{ $item_ambiente->id }}" class="tab-content" style="display: block;">
                                <div class="profile-edit-container">
                                    <div class="statistic-container fl-wrap">
                                        @foreach ($mesas as $item_mesa)
                                          <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comidaordenpedido/1/edit?view=ordenpedido') }}" class="statistic-item-wrap"> 
                                              <div class="statistic-item gradient-bg fl-wrap">
                                                  <i class="fa fa-utensils"></i>
                                                  <div class="statistic-item-numder">Mesa</div>
                                                  <h5>Nro {{ $item_mesa->numero_mesa }}</h5>
                                              </div>
                                          </a>
                                        @endforeach
                                    </div>  
                                </div>
                            </div>
                          @else
                            <div id="tab-ordenpedido-ambientes-{{ $item_ambiente->id }}" class="tab-content" style="display: none;">
                                <div class="profile-edit-container">
                                    <div class="statistic-container fl-wrap">
                                        @foreach ($mesas as $item)
                                          <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comidaordenpedido/1/edit?view=ordenpedido') }}" class="statistic-item-wrap"> 
                                              <div class="statistic-item gradient-bg fl-wrap">
                                                  <i class="fa fa-utensils"></i>
                                                  <div class="statistic-item-numder">Mesa</div>
                                                  <h5>Nro {{ $item->numero_mesa }}</h5>
                                              </div>
                                          </a>
                                        @endforeach
                                    </div>  
                                </div>
                            </div>
                          @endif
                        @endforeach
                    </div>
                </div>
                @else
                  <div class="profile-edit-container">
                      <div class="statistic-container fl-wrap">
                          @foreach ($mesas as $item)
                            <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comidaordenpedido/1/edit?view=ordenpedido') }}" class="statistic-item-wrap"> 
                                <div class="statistic-item gradient-bg fl-wrap">
                                    <i class="fa fa-utensils"></i>
                                    <div class="statistic-item-numder">Mesa</div>
                                    <h5>Nro {{ $item->numero_mesa }}</h5>
                                </div>
                            </a>
                          @endforeach
                      </div>  
                  </div>
                @endif
            </div>
          @else
            <div id="tab-ordenpedido-pisos-{{ $valuepiso->id }}" class="tab-content" style="display: none;">
                @if (count($ambientes) != 1)
                <div class="tabs-container" id="tab-ordenpedido-ambientes{{ $valuepiso->id }}">
                    <ul class="tabs-menu">
                        @foreach ($ambientes as $item_ambiente)
                          @if ($item_ambiente->id == 1)
                            <li class="current"><a href="#tab-ordenpedido-ambientes-{{ $item_ambiente->id }}">{{ $item_ambiente->nombre }}</a></li>
                          @else
                            <li><a href="#tab-ordenpedido-ambientes-{{ $item_ambiente->id }}">{{ $item_ambiente->nombre }}</a></li>
                          @endif
                        @endforeach
                    </ul>
                    <div class="tab">
                        @foreach ($ambientes as $item_ambiente)
                          <?php
                              $mesas = DB::table('s_comida_mesa')
                                ->join('s_comida_piso', 's_comida_piso.id', 's_comida_mesa.idpiso')
                                ->join('s_comida_ambiente', 's_comida_ambiente.id', 's_comida_mesa.idambiente')
                                ->where([
                                  ['s_comida_mesa.idtienda', $tienda->id],
                                  ['s_comida_mesa.idpiso', $valuepiso->id],
                                  ['s_comida_mesa.idambiente', $item_ambiente->id],
                                ])
                                ->select(
                                  's_comida_mesa.*',
                                  's_comida_piso.nombre as nombre_piso',
                                  's_comida_ambiente.nombre as nombre_ambiente'
                                )
                                ->orderBy('s_comida_mesa.idpiso','asc')
                                ->orderBy('s_comida_mesa.idambiente','asc')
                                ->orderBy('s_comida_mesa.numero_mesa','asc')
                                ->get();
                          ?>
                          @if ($item_ambiente->id == 1)
                            <div id="tab-ordenpedido-ambientes-{{ $item_ambiente->id }}" class="tab-content" style="display: block;">
                                <div class="profile-edit-container">
                                    <div class="statistic-container fl-wrap">
                                        @foreach ($mesas as $item_mesa)
                                          <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comidaordenpedido/1/edit?view=ordenpedido') }}" class="statistic-item-wrap"> 
                                              <div class="statistic-item gradient-bg fl-wrap">
                                                  <i class="fa fa-utensils"></i>
                                                  <div class="statistic-item-numder">Mesa</div>
                                                  <h5>Nro {{ $item_mesa->numero_mesa }}</h5>
                                              </div>
                                          </a>
                                        @endforeach
                                    </div>  
                                </div>
                            </div>
                          @else
                            <div id="tab-ordenpedido-ambientes-{{ $item_ambiente->id }}" class="tab-content" style="display: none;">
                                <div class="profile-edit-container">
                                    <div class="statistic-container fl-wrap">
                                        @foreach ($mesas as $item)
                                          <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comidaordenpedido/1/edit?view=ordenpedido') }}" class="statistic-item-wrap"> 
                                              <div class="statistic-item gradient-bg fl-wrap">
                                                  <i class="fa fa-utensils"></i>
                                                  <div class="statistic-item-numder">Mesa</div>
                                                  <h5>Nro {{ $item->numero_mesa }}</h5>
                                              </div>
                                          </a>
                                        @endforeach
                                    </div>  
                                </div>
                            </div>
                          @endif
                        @endforeach
                    </div>
                </div>
                @else
                  <div class="profile-edit-container">
                      <div class="statistic-container fl-wrap">
                          @foreach ($mesas as $item)
                            <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comidaordenpedido/1/edit?view=ordenpedido') }}" class="statistic-item-wrap"> 
                                <div class="statistic-item gradient-bg fl-wrap">
                                    <i class="fa fa-utensils"></i>
                                    <div class="statistic-item-numder">Mesa</div>
                                    <h5>Nro {{ $item->numero_mesa }}</h5>
                                </div>
                            </a>
                          @endforeach
                      </div>  
                  </div>
                @endif
            </div>
          @endif
        @endforeach

@endsection
@section('subscripts')
<script>
</script>
@endsection