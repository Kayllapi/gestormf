
<div class="row">
    <div class="col-sm-3">    
        <div class="list-single-main-wrapper fl-wrap">
            <div class="breadcrumbs gradient-bg fl-wrap">
              <span>Créditos</span>
            </div>
        </div>
        <a class="btn btn-success" href="javascript:;" onclick="expedientedetalle_editar({{$prestamocredito->id}})" style="margin-bottom: 5px;float: left;width: 100%;"><i class="fa fa-edit"></i> Expediente</a>
        <div class="table-responsive">
        <table class="table">
          <tbody>
            @foreach($prestamocreditos as $valuedetalle)
            <tr>
                <td>
                  <b>Código:</b> {{$valuedetalle['creditocodigo']}}<br>
                  <b>Desembolso:</b> {{$valuedetalle['creditodesembolso']}}<br>
                  <b>Fecha:</b> {{$valuedetalle['creditofechadesembolso']}}<br>
                </td>
                <td>
                  <a class="btn btn-warning" href="javascript:;" onclick="expedientedetalle_index({{$valuedetalle['idcredito']}})"><i class="fa fa-angle-right"></i> Mostrar</a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
    <div class="col-sm-9">  
        <div id="cont-expedientedetalle"></div>   
    </div>
</div>
<script>
expedientedetalle_editar({{$prestamocredito->id}})
</script>
<style>
  .cont_expediente {
    background-color: #32353c;
    padding: 5px;
    border-radius: 5px;
    line-height: 4;
    color: #fff;
  }
</style>