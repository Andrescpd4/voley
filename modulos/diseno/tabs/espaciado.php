<div class="row">
    <div class="col-md-12">
        <h5 class="card-title">Escala de Espaciado</h5>
        <p class="text-muted">Define el valor base para márgenes y paddings (en rem). Las clases como .m-1, .p-2 usarán este múltiplo.</p>
        <div class="mb-3">
            <label for="escala_espaciado" class="form-label">Escala de Espaciado (rem)</label>
            <input type="number" class="form-control" id="escala_espaciado" name="escala_espaciado" step="0.01" min="0.5" max="2" value="1">
            <div class="form-text">Ej: 1 = 1rem, 0.5 = 0.5rem, 1.5 = 1.5rem.</div>
        </div>
    </div>
</div>

<script type="text/javascript">
    document.getElementById('escala_espaciado').addEventListener('input', function() {
        var valor = this.value;
        document.documentElement.style.setProperty('--bs-spacing-unit', valor + 'rem');
    });
</script>