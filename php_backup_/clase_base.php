<?php
// ============================================================
// CLASE_BASE.PHP — Clase base para formularios libres
//
// Los formularios "libres" (no CRUD automatico) extienden esta clase.
// Proporciona acceso a $this->db (la conexion global).
// ============================================================

class Base
{
    protected $db;
    protected $usuario_activo;

    public function __construct()
    {
        $this->db = $GLOBALS['db'];
    }
}

// Compatibilidad con formularios antiguos
class clase_base extends Base
{
}
