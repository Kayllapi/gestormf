<div class="row">
    @if(configuracion($tienda->id,'comida_cantidadmesa')['resultado']=='CORRECTO')
        <?php $fechaactual  = new DateTime(Carbon\Carbon::now()->format("Y-m-d H:i:s")); ?>
        @for($i=1; $i <= configuracion($tienda->id,'comida_cantidadmesa')['valor']; $i++)
            <?php
                    $ordenpedido = DB::table('s_comida_ordenpedido')
                                ->join('users as mesero','mesero.id','s_comida_ordenpedido.idresponsable')
                                ->where('s_comida_ordenpedido.idtienda', $tienda->id)
                                ->where('s_comida_ordenpedido.idestado', 1)
                                ->where('s_comida_ordenpedido.idestadoordenpedido', 1)
                                ->where('s_comida_ordenpedido.numeromesa', $i)
                                ->select(
                                    's_comida_ordenpedido.*',
                                    'mesero.nombre as meseronombre',
                                )
                                ->first();
                    $numcolor = '';
                    $idordenpedido = 0;
                    $tiempoxminuto = 0;
                    $mesa_estado = '';
                    $style = 'line-height: 4.7;';
                    if($ordenpedido!=''){
                        $numcolor = '90';
                        $idordenpedido = $ordenpedido->id;
                        $style = 'line-height: 2;';
                      
                        //tiempo
                      
                        $fecharegistro = new DateTime($ordenpedido->fecharegistro);
                        $intvl = $fechaactual->diff($fecharegistro);
                        $hora = $intvl->h>0 ? $intvl->h.($intvl->h==1 ? " hora y ":" horas y "):'';
                        $minuto = $intvl->i>0 ? $intvl->i.($intvl->i==1 ? " minuto ":" minutos "):'0 minutos';
                        $tiempo = $hora.$minuto;
                      
                        $mesa_estado = '<div class="mesa_tiempo">Hace '.$tiempo.'</div><div class="mesa_mesero">'.$ordenpedido->meseronombre.'</div>';
                    }
                  
                    $background = 'background-color:#31353d'.$numcolor.';';
                    if(configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'){
                        $background = 'background-color:'.configuracion($tienda->id,'sistema_color')['valor'].$numcolor.';';
                    }
            ?>
            <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="cont_mesa" onclick="cargar_pedido('<?php echo str_pad($i, 2, "0", STR_PAD_LEFT) ?>','{{$idordenpedido}}')" style="<?php echo $background ?>">
                    <div class="mesa_numero" style="<?php echo $style ?>">Mesa <?php echo str_pad($i, 2, "0", STR_PAD_LEFT) ?></div>
                    <?php echo $mesa_estado ?>
                </div>
            </div>
        @endfor
    @endif
</div>
<script>
  
</script>