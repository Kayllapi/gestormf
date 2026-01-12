<div class="modal-header">
    <h5 class="modal-title">
        Compra y Venta de Bienes
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-1">
                <h5 class="modal-title text-center">
                    ACTIVOS
                </h5>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-2">
                            <form action="javascript:;" id="form_movimientointernodinero_retiro1"> 
                                <div class="modal-body">
                                    <div class="row ">
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-primary" onclick="valid_registro_retiro1()">
                                                Resgistrar Compra <br> de Activo
                                            </button>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label" style="text-align: right;">Agencia</label>
                                                <div class="col-sm-7">
                                                    <select class="form-control" id="id_agencia_compra">
                                                        <option></option>
                                                        @foreach($agencias as $value)
                                                            <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <button type="button" class="btn btn-primary" onclick="valid_registro_retiro1()">
                                                        Buscar
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label" style="text-align: right;">Periodo</label>
                                                <div class="col-sm-3">
                                                    <input type="date" class="form-control" id="fecha_inicio_compra" value="{{ date('Y-m-d') }}"> 
                                                </div>
                                                <label class="col-sm-1 col-form-label" style="text-align: center;">al</label>
                                                <div class="col-sm-3">
                                                    <input type="date" class="form-control" id="fecha_fin_compra" value="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="col-sm-2">
                                                    <input type="checkbox" name="check_compra" id="check_compra">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body" style="overflow-y: scroll;height: 150px;padding: 0;margin-top: 5px;overflow-x: scroll;">
                        </div>
                        <div class="modal-body">
                            <button type="button" class="btn  big-btn  color-bg flat-btn" style="background-color: #144081;color: #fff;margin-bottom: 5px;">
                                Eliminar
                            </button>
                            <button type="button" class="btn  big-btn  color-bg flat-btn" style="background-color: #144081;color: #fff;margin-bottom: 5px;">
                                Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-1">
                <h5 class="modal-title text-center">
                    VENTA
                </h5>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-2">
                            <form action="javascript:;" id="form_movimientointernodinero_retiro1"> 
                                <div class="modal-body">
                                    <div class="row ">
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-primary" onclick="valid_registro_retiro1()">
                                                Resgistrar Venta <br> de Activo
                                            </button>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label" style="text-align: right;">Agencia</label>
                                                <div class="col-sm-7">
                                                    <select class="form-select" id="id_agencia_venta">
                                                        <option></option>
                                                        @foreach($agencias as $value)
                                                            <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <button type="button" class="btn btn-primary" onclick="valid_registro_retiro1()">
                                                        Buscar
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-3 col-form-label" style="text-align: right;">Periodo</label>
                                                <div class="col-sm-3">
                                                    <input type="date" class="form-control" id="fecha_inicio_venta" value="{{ date('Y-m-d') }}"> 
                                                </div>
                                                <label class="col-sm-1 col-form-label" style="text-align: center;">al</label>
                                                <div class="col-sm-3">
                                                    <input type="date" class="form-control" id="fecha_fin_venta" value="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="col-sm-2">
                                                    <input type="checkbox" name="check_venta" id="check_venta">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body" style="overflow-y: scroll;height: 150px;padding: 0;margin-top: 5px;overflow-x: scroll;">
                        </div>
                        <div class="modal-body">
                            <button type="button" class="btn  big-btn  color-bg flat-btn" style="background-color: #144081;color: #fff;margin-bottom: 5px;">
                                Eliminar
                            </button>
                            <button type="button" class="btn  big-btn  color-bg flat-btn" style="background-color: #144081;color: #fff;margin-bottom: 5px;">
                                Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    sistema_select2({ input:'#id_agencia_compra' });
    sistema_select2({ input:'#id_agencia_venta' });
</script>