<form id="form-diseno" method="POST" action="">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="disenoTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="colores-tab" data-bs-toggle="tab" data-bs-target="#colores" type="button" role="tab" aria-controls="colores" aria-selected="true">Colores</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tipografia-tab" data-bs-toggle="tab" data-bs-target="#tipografia" type="button" role="tab" aria-controls="tipografia" aria-selected="false">Tipografía</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="espaciado-tab" data-bs-toggle="tab" data-bs-target="#espaciado" type="button" role="tab" aria-controls="espaciado" aria-selected="false">Espaciado</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="avanzado-tab" data-bs-toggle="tab" data-bs-target="#avanzado" type="button" role="tab" aria-controls="avanzado" aria-selected="false">Avanzado</button>
        </li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content" id="disenoTabContent">
        <div class="tab-pane fade show active" id="colores" role="tabpanel" aria-labelledby="colores-tab">
            <?php include_once 'tabs/colores.php'; ?>
        </div>
        <div class="tab-pane fade" id="tipografia" role="tabpanel" aria-labelledby="tipografia-tab">
            <?php include_once 'tabs/tipografia.php'; ?>
        </div>
        <div class="tab-pane fade" id="espaciado" role="tabpanel" aria-labelledby="espaciado-tab">
            <?php include_once 'tabs/espaciado.php'; ?>
        </div>
        <div class="tab-pane fade" id="avanzado" role="tabpanel" aria-labelledby="avanzado-tab">
            <?php include_once 'tabs/avanzado.php'; ?>
        </div>
    </div>
    <!-- Hidden fields for AJAX submit? We'll handle via JS -->
    <input type="hidden" name="accion" value="guardar">
    <!-- The actual submit will be done via gcGuardarTema -->
    <div class="mt-3">
        <button type="button" class="btn btn-primary" onclick="gcGuardarTema(getFormData())">
            Guardar Tema
        </button>
    </div>
</form>