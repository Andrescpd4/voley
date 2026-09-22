<div class="row">
    <div class="col-md-12">
        <h5 class="card-title">Modo Oscuro</h5>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode" value="1">
            <label class="form-check-label" for="dark_mode">
                Activar modo oscuro (añade la clase <code>.dark</code> al elemento <code><html></code>)
            </label>
        </div>
        <div class="alert alert-info">
            <strong>Nota:</strong> El modo oscuro solo funcionará si ha definido variables CSS alternativas dentro de <code>.dark</code> en el CSS generado. Actualmente, el sistema añade la clase <code>.dark</code> cuando está activo, pero no genera automáticamente colores oscuros. Puede usar el selector <code>.dark</code> en su CSS personalizado o esperar a una futura versión que genere variables alternativas.
        </div>
    </div>
</div>

<script type="text/javascript">
    document.getElementById('dark_mode').addEventListener('change', function() {
        if (this.checked) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    });
</script>