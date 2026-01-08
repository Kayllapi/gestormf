@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Editar Solicitud de Crédito</span>
          <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
        </div>
    </div>
    <form class="js-validation-signin px-30" action="javascript:;"
          onsubmit="callback({
                                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{ $prestamosolicitud->id }}',
                                method: 'PUT',
                                data:   {
                                    view: 'editar',
                                    cronograma: selectCronograma()
                                }
                            },
                            function(resultado){
                                location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud') }}';
                            },this)">
        <div class="profile-edit-container">
            <div class="custom-form">
              <div class="row">
                <div class="col-sm-6">
                    <hr>
                    <h3 style="font-size: 15px; background: #31353d; color: #fff; padding: 0.625em;">CRÉDITO</h3>
                    <div id="cont-alert">
                    </div>
                    <label>Monto *</label>
                    <input type="number" id="monto" value="{{ $prestamosolicitud->monto }}" min="0" step="0.01" onchange="creditoCalendario()" onclick="creditoCalendario()"/>
                    <label>Nº de Cuotas *</label>
                    <input type="number" id="numerocuota" value="{{ $prestamosolicitud->numerocuota }}" min="1" step="1" onchange="creditoCalendario()" onclick="creditoCalendario()"/>
                    <label>Fecha de Inicio *</label>
                    <input type="date" id="fechainicio" value="{{ $prestamosolicitud->fechainicio }}" onchange="creditoCalendario()" onclick="creditoCalendario()"/>
                    <label>Frecuencia *</label>
                    <select id="idprestamo_frecuencia" onchange="selectNumeroDias(), creditoCalendario()">
                        @foreach($frecuencias as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                    <div id="cont-numerodias" style="display: none">
                        <label>Nº de Dias *</label>
                        <input type="number" id="numerodias" value="{{ $prestamosolicitud->numerodias }}" min="0" step="1" onchange="creditoCalendario()"/>
                    </div>
                    <label>Excluir Días:</label>
                    <label>
                        <input type="checkbox" id="excluirsabado" value="si" onclick="creditoCalendario()"> Sábados
                    </label>
                    <label>
                        <input type="checkbox" id="excluirdomingo" value="si" onclick="creditoCalendario()"> Domingos
                    </label>
                    <label>
                        <input type="checkbox" id="excluirferiados" value="si" onclick="creditoCalendario()"> Feriados
                    </label>
                    <label>Tasa % *</label>
                    <input type="number" id="tasa" value="{{ $prestamosolicitud->tasa }}" onclik="creditoCalendario()" onchange="creditoCalendario()"/>
                    <label>Cuota a Pagar</label>
                    <input type="text" id="cuota" value="{{ $prestamosolicitud->cuota }}" disabled/>
                    <label>Interes</label>
                    <input type="text" id="interes" value="{{ $prestamosolicitud->interes }}" disabled/>
                    <label>Monto + Interes</label>
                    <input type="text" id="montointeres" value="{{ $prestamosolicitud->montointeres }}" disabled/>
                </div>
                <div class="col-sm-6" id="cont-creditocalendario">
                    <table class="table">
                        <thead style='background: #31353d; color: #fff;'>
                            <tr>
                                <td>Nº</td>
                                <td>F.Vecimiento</td>
                                <td>Capital</td>
                                <td>Interes</td>
                                <td>Cuota</td>
                                <td>T.Saldo</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prestamosolicituddetalle as $value)
                            <tr>
                                <td>{{ $value->orden }}</td>
                                <td>{{ $value->fechavencimiento }}</td>
                                <td>{{ $value->capital }}</td>
                                <td>{{ $value->interes }}</td>
                                <td>{{ $value->cuota }}</td>
                                <td>{{ $value->saldo }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
              </div>
            </div>
        </div>
        <div class="profile-edit-container">
            <div class="custom-form">
                <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
            </div>
        </div>
    </form>                             
@endsection
@section('subscripts')
<script>
    // Selectores checked
    @if ($prestamosolicitud->excluirsabado == "si")
        $('#excluirsabado').prop("checked", true);
    @endif
    @if ($prestamosolicitud->excluirdomingo == "si")
        $('#excluirdomingo').prop("checked", true);
    @endif
    @if ($prestamosolicitud->excluirferiado == "si")
        $('#excluirferiado').prop("checked", true);
    @endif

    $('#idprestamo_frecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --'
    }).val({{ $prestamosolicitud->idprestamo_frecuencia }}).trigger('change');

    // Mostrando numero de dias en frecuencia programado
    function selectNumeroDias() {
        var frecuencia = $('#idprestamo_frecuencia').val();
        // frecuencia programado
        if (frecuencia == 5) {
            $('#cont-numerodias').css({'display':'block'});
        } else {
            $('#numerodias').val(0);
            $('#cont-numerodias').css('display', 'none');
        }
    }
    //fin

    // Calculando credito en calendario
    function creditoCalendario() {
        $('#cont-creditocalendario').html('');
        $('#cont-alert').html('');
        // Validaciones de datos
        var monto = $('#monto').val();
        var numerocuota = $('#numerocuota').val();
        var idprestamo_frecuencia = $('#idprestamo_frecuencia').val();
        var numerodias = $('#numerodias').val();
        var tasa = $('#tasa').val();
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-creditocalendario') }}",
            type: 'GET',
            data: {
                monto: monto,
                numerocuota: numerocuota,
                fechainicio: $('#fechainicio').val(),
                idprestamo_frecuencia: idprestamo_frecuencia,
                numerodias: numerodias,
                excluirsabado: $('#excluirsabado:checked').val(),
                excluirdomingo: $('#excluirdomingo:checked').val(),
                excluirferiado: $('#excluirferiado:checked').val(),
                tasa: tasa
            },
            success: function (res) {
                $('#cuota').val(res['cuota']);
                $('#interes').val(res['interes']);
                $('#montointeres').val(res['montointeres']);
                $('#cont-creditocalendario').append(res['html']);
                $('#cont-alert').html(`<div class="alert alert-danger" role="alert" style="color: #f3efef;">
                                          <span>${res['mensaje']}</span>
                                       </div>`);
            }
        });
    }
    // fin
  
    // Capturando cronograma de pago
    function selectCronograma() {
        let data = [];
        $("#table-creditocalendario tbody tr").each(function(e) {
            let count = $(this).attr('count');
            data.push({
                orden: $(this).attr('orden'),
                fechavencimiento: $(this).attr('fechavencimiento'),
                capital: $(this).attr('capital'),
                interes: $(this).attr('interes'),
                cuota: $(this).attr('cuota'),
                saldo: $(this).attr('saldo')
            });
        });
        return JSON.stringify(data);
    }
    // fin

    // Funcion para reutilizar ruta de show
    function ajaxSelect2(showRoute) {
        let ajax  = {
            url:      "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/') }}" + showRoute,
            dataType: 'json',
            delay:    250,
            data: (params) => {
                return {
                    buscar: params.term
                };
            },
            processResults: (data) => {
                return {
                    results: data
                };
            },
            cache: true
        };
        return ajax;
    }
    //fin
</script>
@endsection