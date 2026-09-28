<?php
// afiliacion/acciones.php - Backend del modulo de afiliacion (Patron Libre con Traits)
// Controla las 3 pestañas: Registro (Acudiente), Mis Solicitudes (Acudiente), Gestion (Admin)

require_once __DIR__ . '/../../php/clase_base.php';
require_once __DIR__ . '/clases/afiliacion_helpers.php';
require_once __DIR__ . '/clases/afiliacion_acudiente.php';
require_once __DIR__ . '/clases/afiliacion_admin.php';

class Formulario extends Base
{
    // Cargar los 3 traits de funcionalidad
    use afiliacion_helpers;
    use afiliacion_acudiente;
    use afiliacion_admin;
}

// Despachar la accion solicitada via constante ACCION (definida en descarga.php)
$accion = ACCION;
$f = new Formulario();
$f->$accion();