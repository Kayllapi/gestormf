<table class="table" id="table-cobranzacancelada">
    <thead style="background: #31353d; color: #fff;">
     <tr>
         <td style="padding: 10px 5px;text-align: center;">Código</td>
         <td style="padding: 10px 5px;text-align: center;">Fecha de Retiro</td>
         <td style="padding: 10px 5px;text-align: center;">Efectivo</td>
         <td style="padding: 10px 5px;text-align: center;">Responsable</td>
         <td style="text-align: center;width:10px;">Estado</td>
         <td style="padding: 1px;width:10px;">
           <a class="btn btn-warning" href="javascript:;" onclick="retirolibre_registrar({{$s_prestamo_ahorro->id}})"><i class="fa fa-angle-right"></i> Registrar</a>
         </td>
     </tr>
    </thead>
    <tbody>
    <?php
      $total_efectivo = 0;
      $total_deposito = 0;
    ?>
    @foreach($s_prestamo_ahorroretirolibre as $value)  
        <?php
        $color = '';
        $btn_anular = '';
        if($idaperturacierre==$value->s_idaperturacierre){
            $color = 'background-color: #f1c40f;';
            if($value->idestadoahorroretirolibre==2){
                $btn_anular = '<li><a href="javascript:;" onclick="retirolibre_anular('.$value->id.')"><i class="fa fa-ban"></i> Anular</a></li>';
            }elseif($value->idestadoahorroretirolibre==3){
                $color = 'background-color: #8d8a82;';
            }
        }
        ?>
        <tr style="{{$color}}" idprestamo_ahorroretirolibre="{{$value->id}}">
            <td style="text-align: center;">{{str_pad($value->codigo, 8, "0", STR_PAD_LEFT)}}</td>
            <td style="text-align: center;">{{date_format(date_create($value->fecharetiro), "d/m/Y h:i:s A")}}</td>
            <td style="text-align: right;">{{$value->monto_efectivo}}</td>
            <td style="text-align: center;">{{$value->cajero_nombre}}</td>
            <td>
                @if($value->idestadoahorroretirolibre==1)
                    <span class="badge badge-pill badge-info"><i class="fa fa-sync"></i> Pendiente</span>
                @elseif($value->idestadoahorroretirolibre==2)
                    <span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span>
                @elseif($value->idestadoahorroretirolibre==3)
                    <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>
                @endif
            </td>
            <td>
                <div class="header-user-menu menu-option" id="menu-opcion">
                    <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                    <ul>  
                        <li><a href="javascript:;" onclick="retirolibre_ticket({{$value->id}})"><i class="fa fa-receipt"></i> Ticket</a></li>
                        <?php echo $btn_anular ?>
                    </ul>
                </div>
            </td>
        </tr>
        <?php
        $total_efectivo = $total_efectivo+$value->monto_efectivo;
        ?>
    @endforeach
    <thead style="background: #31353d; color: #fff;">
     <tr>
         <td></td>
         <td style="padding: 10px 5px;text-align: right;">TOTAL</td>
         <td style="padding: 10px 5px;text-align: right;">{{number_format($total_efectivo, 2, '.', '')}}</td>
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
            $("ul",this).toggleClass("hu-menu-vis");
            $("i",this).toggleClass("fa-angle-up");
        });
</script>