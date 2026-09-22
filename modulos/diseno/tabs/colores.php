<div class="row">
    <div class="col-md-6">
        <h5 class="card-title">Colores Principales</h5>
        <div class="mb-3">
            <label for="primaria" class="form-label">Primario</label>
            <input type="color" class="form-control form-control-color" id="primaria" name="primaria" value="#405189">
        </div>
        <div class="mb-3">
            <label for="secundaria" class="form-label">Secundario</label>
            <input type="color" class="form-control form-control-color" id="secundaria" name="secundaria" value="#6c757d">
        </div>
        <div class="mb-3">
            <label for="exito" class="form-label">Éxito</label>
            <input type="color" class="form-control form-control-color" id="exito" name="exito" value="#198754">
        </div>
        <div class="mb-3">
            <label for="info" class="form-label">Info</label>
            <input type="color" class="form-control form-control-color" id="info" name="info" value="#0dcaf0">
        </div>
        <div class="mb-3">
            <label for="advertencia" class="form-label">Advertencia</label>
            <input type="color" class="form-control form-control-color" id="advertencia" name="advertencia" value="#ffc107">
        </div>
        <div class="mb-3">
            <label for="peligro" class="form-label">Peligro</label>
            <input type="color" class="form-control form-control-color" id="peligro" name="peligro" value="#dc3545">
        </div>
    </div>
    <div class="col-md-6">
        <h5 class="card-title">Colores Neutros</h5>
        <div class="mb-3">
            <label for="claro" class="form-label">Claro</label>
            <input type="color" class="form-control form-control-color" id="claro" name="claro" value="#f8f9fa">
        </div>
        <div class="mb-3">
            <label for="oscuro" class="form-label">Oscuro</label>
            <input type="color" class="form-control form-control-color" id="oscuro" name="oscuro" value="#212529">
        </div>
        <hr>
        <h5 class="card-title">Escala de Grises (opcional)</h5>
        <p class="text-muted">Deje en blanco para no usar.</p>
        <div class="row">
            <div class="col-6">
                <div class="mb-3">
                    <label for="gris100" class="form-label">Gris 100</label>
                    <input type="color" class="form-control form-control-color" id="gris100" name="gris100" value="#f8f9fa">
                </div>
                <div class="mb-3">
                    <label for="gris200" class="form-label">Gris 200</label>
                    <input type="color" class="form-control form-control-color" id="gris200" name="gris200" value="#e9ecef">
                </div>
                <div class="mb-3">
                    <label for="gris300" class="form-label">Gris 300</label>
                    <input type="color" class="form-control form-control-color" id="gris300" name="gris300" value="#dee2e6">
                </div>
                <div class="mb-3">
                    <label for="gris400" class="form-label">Gris 400</label>
                    <input type="color" class="form-control form-control-color" id="gris400" name="gris400" value="#ced4da">
                </div>
                <div class="mb-3">
                    <label for="gris500" class="form-label">Gris 500</label>
                    <input type="color" class="form-control form-control-color" id="gris500" name="gris500" value="#adb5bd">
                </div>
            </div>
            <div class="col-6">
                <div class="mb-3">
                    <label for="gris600" class="form-label">Gris 600</label>
                    <input type="color" class="form-control form-control-color" id="gris600" name="gris600" value="#6c757d">
                </div>
                <div class="mb-3">
                    <label for="gris700" class="form-label">Gris 700</label>
                    <input type="color" class="form-control form-control-color" id="gris700" name="gris700" value="#495057">
                </div>
                <div class="mb-3">
                    <label for="gris800" class="form-label">Gris 800</label>
                    <input type="color" class="form-control form-control-color" id="gris800" name="gris800" value="#343a40">
                </div>
                <div class="mb-3">
                    <label for="gris900" class="form-label">Gris 900</label>
                    <input type="color" class="form-control form-control-color" id="gris900" name="gris900" value="#212529">
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Actualizar variables CSS en tiempo real al cambiar un color
    var inputsColor = document.querySelectorAll('input[type="color"]');
    for (var i = 0; i < inputsColor.length; i++) {
        inputsColor[i].addEventListener('input', function(event) {
            var nombre = event.target.name;
            var valor = event.target.value;
            document.documentElement.style.setProperty('--bs-' + nombre, valor);
            // También actualizar variables de gris si aplica
            if (nombre.indexOf('gris') === 0) {
                document.documentElement.style.setProperty('--bs-' + nombre, valor);
            }
        });
    }

    // Botón aplicar (guardar) se manejará desde el formulario principal
</script>