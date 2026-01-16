<div class="modal-header">
    <h5 class="modal-title">
      Copia de Seguridad
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row mt-1 justify-content-center">
                        <div class="col-sm-12 col-md-2">
                            <a href="{{ route('bd.descargar') }}" class="btn btn-info">
                                <i class="fa-solid fa-database"></i> BASE DE DATOS
                            </a>
                            <a href="https://github.com/Kayllapi/gestormf/archive/refs/heads/main.zip"
                                class="btn btn-success"
                                download>
                                <i class="fa-solid fa-code"></i> SISTEMA
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script></script>