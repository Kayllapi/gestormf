<div class="table-responsive">
<table class="table" id="table-cobranzacancelada">
    <thead style="background: #31353d; color: #fff;">
     <tr>
         <td style="padding: 10px 5px;text-align: center;">Código</td>
         <td style="padding: 10px 5px;text-align: center;width:100px;">Fecha de Recaudación</td>
         <td style="padding: 10px 5px;text-align: center;">Efectivo</td>
         <td style="padding: 10px 5px;text-align: center;">Depósito</td>
         <td style="padding: 10px 5px;text-align: center;width:100px;">Responsable (Cajero)</td>
         <td style="text-align: center;width:10px;">Estado</td>
         <td style="text-align: center;width:10px;"></td>
     </tr>
    </thead>
    <tbody>
    <?php
      $total_efectivo = 0;
      $total_deposito = 0;
    ?>
    @foreach($s_prestamo_ahorrorecaudacionlibre as $value)  
        <?php
        $color = '';
        $btn_anular = '';
        if($idaperturacierre==$value->idaperturacierre){
            $color = 'background-color: #f1c40f;';
            if($value->idestadorecaudacion==2){
                $btn_anular = '<li><a href="javascript:;" onclick="pagolibre_anular('.$value->id.')"><i class="fa fa-ban"></i> Anular</a></li>';
            }elseif($value->idestadorecaudacion==3){
                $color = 'background-color: #8d8a82;';
            }
        }
        ?>
        <tr style="{{$color}}" idprestamo_ahorroretirolibre="{{$value->id}}">
            <td style="text-align: center;">{{str_pad($value->codigo, 8, "0", STR_PAD_LEFT)}}</td>
            <td style="text-align: center;">
              {{date_format(date_create($value->fecharegistro), "d/m/Y")}}<br>
              {{date_format(date_create($value->fecharegistro), "h:i:s A")}}
            </td>
            <td style="text-align: right;">{{$value->monto_efectivo}}</td>
            <td style="text-align: right;">{{$value->monto_deposito}}</td>
            <td style="text-align: center;">{{$value->cajero_nombre}}</td>
            <td>
                @if($value->idestadorecaudacion==1)
                    <span class="badge badge-pill badge-info"><i class="fa fa-sync"></i> Pendiente</span>
                @elseif($value->idestadorecaudacion==2)
                    <span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span>
                @elseif($value->idestadorecaudacion==3)
                    <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>
                @endif
            </td>
            <td>
                <div class="header-user-menu menu-option" id="menu-opcion-pagolibre">
                    <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                    <ul>  
                        <li><a href="javascript:;" onclick="pagolibre_ticket({{$value->id}})"><i class="fa fa-receipt"></i> Ticket</a></li>
                        <?php echo $btn_anular ?>
                    </ul>
                </div>
            </td>
        </tr>
        <?php
        $total_efectivo = $total_efectivo+$value->monto_efectivo;
        $total_deposito = $total_deposito+$value->monto_deposito;
        ?>
    @endforeach
    <thead style="background: #31353d; color: #fff;">
     <tr>
         <td></td>
         <td style="padding: 10px 5px;text-align: right;">TOTAL</td>
         <td style="padding: 10px 5px;text-align: right;">{{number_format($total_efectivo, 2, '.', '')}}</td>
         <td style="padding: 10px 5px;text-align: right;">{{number_format($total_deposito, 2, '.', '')}}</td>
         <td></td>
         <td></td>
         <td></td>
     </tr>
    </thead>
    </tbody>
</table>
</div>
<script>
$("div#menu-opcion-pagolibre").on("click", function () {
    $("ul",this).toggleClass("hu-menu-vis");
    $("i",this).toggleClass("fa-angle-up");
});
</script>