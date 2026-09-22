<div class="row">
    <div class="col-md-6">
        <h5 class="card-title">Fuentes</h5>
        <div class="mb-3">
            <label for="fuente_base" class="form-label">Fuente Base (cuerpo)</label>
            <input type="text" class="form-control" id="fuente_base" name="fuente_base" placeholder="Ej: Inter, Roboto, system-ui" value="System UI,Helvetica Neue,Arial,sans-serif">
            <div class="form-text">Ingrese una lista de fuentes separadas por comas. El navegador usará la primera disponible.</div>
        </div>
        <div class="mb-3">
            <label for="fuente_titulo" class="form-label">Fuente para Títulos</label>
            <input type="text" class="form-control" id="fuente_titulo" name="fuente_titulo" placeholder="Ej: Inter, Roboto, serif" value="System UI,Helvetica Neue,Arial,sans-serif">
            <div class="form-text">Se aplica a h1-h6, .h1, etc.</div>
        </div>
    </div>
    <div class="col-md-6">
        <h5 class="card-title">Tamaño y Espaciado de Texto</h5>
        <div class="mb-3">
            <label for="tamaño_base" class="form-label">Tamaño Base (rem)</label>
            <input type="number" class="form-control" id="tamaño_base" name="tamaño_base" step="0.01" min="0.5" max="3" value="1">
            <div class="form-text">Define el tamaño base del texto (1rem = 16px por defecto).</div>
        </div>
        <div class="mb-3">
            <label for="line_height" class="form-label">Altura de Línea</label>
            <input type="number" class="form-control" id="line_height" name="line_height" step="0.1" min="1" max="3" value="1.5">
            <div class="form-text">Valor unitario (ej: 1.5).</div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Actualizar variables CSS en tiempo real
    document.getElementById('fuente_base').addEventListener('change', function() {
        var valor = this.value.trim();
        if (valor) {
            document.documentElement.style.setProperty('--bs-font-sans-serif, "' + valor + '"');
            // Actually need quotes:
            document.documentElement.style.setProperty('--bs-font-sans-serif', '"' + valor + '"');
        } else {
            document.documentElement.style.removeProperty('--bs-font-sans-serif');
        }
    });
    document.getElementById('fuente_titulo').addEventListener('change', function() {
        var valor = this.value.trim();
        if (valor) {
            document.documentElement.style.setProperty('--bs-font-serif', '"' + valor + '"');
        } else {
            document.documentElement.style.removeProperty('--bs-font-serif');
        }
    });
    document.getElementById('tamaño_base').addEventListener('input', function() {
        var valor = this.value;
        document.documentElement.style.setProperty('--bs-font-base-size', valor + 'rem');
    });
    document.getElementById('line_height').addEventListener('input', function() {
        var valor = this.value;
        document.documentElement.style.setProperty('--bs-line-height', valor);
    });
</script>