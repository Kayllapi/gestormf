<div class="mb-1 mt-1 d-flex flex-wrap justify-content-between align-items-center" style="background-color: #bcbcbc;color: #000;font-weight: bold;">
  <span>
    <span class="badge" style="background-color: #bcbcbc;color: #000;">DATOS DE PRÉSTAMO</span>
    @if($aviso_credito)
      <span class="badge" style="background-color: #ffc9ca;color: #93222c;">{{ $aviso_credito }}</span>
    @endif
  </span>
  <span class="badge" style="background-color: #bcbcbc;color: #000;">DESEMBOLSADO EN AGENCIA: <span style="font-weight: normal;background-color: #E8E585;">{{ $credito->agencia_desembolso ?: '-' }}</span></span>
</div>
