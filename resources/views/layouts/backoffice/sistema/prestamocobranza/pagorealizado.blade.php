<table class="table" id="table-cobranzacancelada">
    <thead style="background: #31353d; color: #fff;">
     <tr>
         <td style="padding: 8px;text-align: center;">Código</td>
         <td style="padding: 8px;text-align: center;">Fecha de Pago</td>
         <td style="padding: 8px;text-align: center;">Efectivo</td>
         <td style="padding: 8px;text-align: center;">Depósito</td>
         <td style="padding: 8px;text-align: center;">Responsable</td>
         <td style="padding: 8px;text-align: center;width:10px;">Estado</td>
         <td style="padding: 8px;text-align: center;width:10px;"></td>
     </tr>
    </thead>
    <tbody>
    <?php
      $total_efectivo = 0;
      $total_deposito = 0;
      $i = 0;
    ?>
    @foreach($prestamocobranzas as $value)  
    <?php
                //$monto = $value->cronograma_pagado;
                /*if($value->cronograma_idtipopago==1){
                    $monto = number_format($value->cronograma_pagado, 2, '.', '');
                }elseif($value->cronograma_idtipopago==2){
                    $monto = number_format($value->cronograma_pagado, 2, '.', '');
                }*/
                $classname = '';
                $btn_anular = '';
                if(modulo($tienda->id,Auth::user()->id,'cobranza_anular')['resultado']=='CORRECTO' or $idaperturacierre==$value->s_idaperturacierre){
                //if($idaperturacierre==$value->s_idaperturacierre){
                    $classname = 'mx-table-warning';
                    if($i==0 && $value->idestadocobranza==2){
                        $btn_anular = '<li><a href="javascript:;" onclick="anular_pagorealizado('.$value->id.')"><i class="fa fa-ban"></i> Anular</a></li>';
                        $i++;
                    }
                }
              
                $opcion = '<li><a href="javascript:;" onclick="ticket_pagorealizado('.$value->id.')"><i class="fa fa-receipt"></i> Ticket</a></li>
                                  <li><a href="javascript:;" onclick="detalle_pagorealizado('.$value->id.')"><i class="fa fa-list"></i> Detalle</a></li>
                                  '.$btn_anular;
    ?>
    <tr class="{{$classname}}" idcobranza="{{$value->id}}">
        <td style="padding: 8px;text-align: center;">{{str_pad($value->codigo, 8, "0", STR_PAD_LEFT)}}</td>
        <td style="padding: 8px;text-align: center;">{{date_format(date_create($value->fecharegistro), "d/m/Y h:i:s A")}}</td>
        <td style="padding: 8px;text-align: right;">{{$value->cronograma_pagado}}</td>
        <td style="padding: 8px;text-align: right;">{{$value->cronograma_deposito}}</td>
        <td style="padding: 8px;text-align: center;">{{$value->cajero_nombre}}</td>
        <td>
                @if($value->idestadocobranza==1)
                    <span class="badge badge-pill badge-info"><i class="fa fa-sync"></i> Pendiente</span>
                @elseif($value->idestadocobranza==2)
                    <span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span>
                @elseif($value->idestadocobranza==3)
                    <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>
                @endif
        </td>
        <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>  
                <?php echo $opcion ?>
                </ul>
            </div>
        </td>
    </tr>
    <?php
      $total_efectivo = $total_efectivo+$value->cronograma_pagado;
      $total_deposito = $total_deposito+$value->cronograma_deposito;
    ?>
    @endforeach
    <thead style="background: #31353d; color: #fff;">
     <tr>
         <td></td>
         <td></td>
         <td style="padding: 8px;text-align: right;">{{number_format($total_efectivo, 2, '.', '')}}</td>
         <td style="padding: 8px;text-align: right;">{{number_format($total_deposito, 2, '.', '')}}</td>
         <td></td>
         <td></td>
         <td></td>
     </tr>
    </thead>
    </tbody>
</table>
</div>
<script>
        $("div#menu-opcion").on("click", function () {
            //$("div#menu-opcion > ul").removeClass("hu-menu-vis");
            $("ul",this).toggleClass("hu-menu-vis");
            $("i",this).toggleClass("fa-angle-up");
        });
</script>
