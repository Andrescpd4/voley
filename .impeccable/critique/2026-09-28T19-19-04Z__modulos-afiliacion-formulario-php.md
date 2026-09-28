---
target: Modulo afiliacion
total_score: 13
max_score: 40
na_heuristics: 
p0_count: 2
p1_count: 2
target_identity: "file:C:\\xampp\\htdocs\\voley\\modulos\\afiliacion\\formulario.php"
target_fingerprint: "sha256:20339dd09ae9c2a6a6e3e4ad1441d32b19eaf63a1cb1de660620b3a3800227bd"
target_path: "C:\\xampp\\htdocs\\voley\\modulos\\afiliacion\\formulario.php"
timestamp: 2026-09-28T19-19-04Z
slug: modulos-afiliacion-formulario-php
---
# Critique — Módulo afiliación (modulos/afiliacion/formulario.php)

Method: dual-agent (A: ses_f168ceaacffeKL5L5WfLcoK9Dh · B: ses_f168ceaa1ffe97BvFQ2PEtURz5)

## Design Health Score (Nielsen 0-4, modo Operate, 10/10 aplican)

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 2 | Envío a revisión termina en toast 3.5s sin cambio de tab ni qué sigue |
| 2 | Match System / Real World | 1 | Jerga de BD expuesta al padre: borrador, pendiente_revision, requiere_info |
| 3 | User Control and Freedom | 1 | Sin Editar/Continuar desde Mis Solicitudes cuando devuelven el trámite |
| 4 | Consistency and Standards | 1 | Sec.1 header #1e1328 + text-black ilegible; confirm() nativo convive con SweetAlert cargado |
| 5 | Error Prevention | 1 | Defaults que mienten: TI y Femenino preseleccionados, form novalidate |
| 6 | Recognition Rather Than Recall | 2 | Prellenado del acudiente bien; pero hay que recordar el ID oculto para adjuntar docs |
| 7 | Flexibility and Efficiency | 2 | Filtros admin bien; sin debounce usado, sin acciones en lote |
| 8 | Aesthetic and Minimalist Design | 1 | Tab1 ~25 inputs en un scroll; columna Observaciones entierra lo urgente |
| 9 | Error Recovery | 1 | Toast genérico sin bad_fields ni foco; errMode throw ante i18n legacy |
| 10 | Help and Documentation | 1 | Solo un alert-info; nada sobre docs obligatorios, estados o qué sigue |
| **Total** | | **13/40** | **Poor** |

## Design Specificity Verdict

LLM: intercambiable con barniz voley. Lo propio del dominio se reduce a categoría con rango de edad, "Niño/Niña", EPS/SISBEN e iconos. Sin posición, sede/horarios, talla de uniforme, foto ni consentimientos deportivos. Cambiando Deportista→Estudiante funciona igual en un colegio.
Deterministic scan: `detect --json modulos/afiliacion` exit 0, `[]` — 0 findings. La herramienta no ve este tipo de problema de dominio.
Corrección a Assessment A con evidencia de B: `js/datatable/spanish.json` SÍ existe en disco, pero con claves legacy 1.x (`sProcessing`, `oPaginate`) frente a DataTables 2.2.2 del CDN — probable caída parcial al inglés, a verificar en browser.
Visual overlays: no disponibles (sin browser automation en esta corrida); sin overlays ni consola que reportar.

## Overall Impression

El trámite funciona como back-office, no como inscripción de un padre apurado: la puerta de documentos nace cerrada, los defaults corrompen datos, y el éxito no se celebra. Lo mejor es la infraestructura de estado unificada (badges + progreso + fichas completas). La mayor oportunidad: un flujo inscribir→adjuntar→enviar sin entender qué es un borrador.

## What's Working

1. Prellenado del acudiente (nombre+documento readonly, contacto editable) — evita redigitar identidad.
2. Lenguaje de estado unificado `afiliacionBadgeEstado` + `afiliacionBarraProgreso` con colores del tema en Tab2 y Tab3.
3. Fichas completas reutilizables ver-antes-de-dictaminar en ambos roles.

## Priority Issues

### [P0] Documentos secuestrados tras el borrador
Why: rompe inscribir→adjuntar→enviar; el bloqueo se descubre tras 25 campos; abandono en móvil.
Fix: si `reg_deportista_id==0`, autoguardar borrador silencioso y abrir el modal; cambiar el alert-info a "Puede adjuntar ahora".
Suggested command: /impeccable shape

### [P0] Defaults que mienten (TI / Femenino)
Why: dato médico/legal corrupto si el padre no toca los selects.
Fix: placeholder vacío en ambos selects, exigirlos en modo enviar, mapear F/M/OTRO explícito en fichas.
Suggested command: /impeccable harden

### [P1] Envío sin cierre
Why: viola peak-end; toast 3.5s sin resumen ni folio; genera doble-envíos y llamadas.
Fix: confirmación previa con Swal + éxito con resumen (nombre, docs X/Y) y botón "Ver mi solicitud" que active tab-mis-link y limpie el form.
Suggested command: /impeccable clarify

### [P1] Tabla admin con i18n legacy
Why: JSON legacy 1.x con DataTables 2.2.2 + `errMode:'throw'` = controles en inglés o excepción visible.
Fix: reemplazar `url` por objeto `language` inline en español o migrar el JSON a claves v2; probar con red bloqueada.
Suggested command: /impeccable harden

### [P2] Tab2 espejo sin salida
Why: con `requiere_info` el padre ve el motivo pero no puede actuar; columna Observaciones desperdicia 1/6 de la tabla.
Fix: botón `Continuar` que cargue el registro en Tab1 y salte a él; observaciones a tooltip/truncate + modal.
Suggested command: /impeccable layout

## Persona Red Flags

Jordan (primerizo): 25 campos sin stepper, jerga borrador/requiere_info, modal docs desacoplado, categoría vacía hasta que responde `listar_categorias`. Abandona en Sec.1.
Sam (accesibilidad): `text-black` sobre `#1e1328` (~1.5:1), 25 pares label/input sin `for`, icon-only con solo `title` sin `aria-label`, input file `d-none` trampa de teclado, toasts sin `aria-live`, `confirm()` fuera del foco del modal, `btn-sm` <44px.
Casey (móvil): sin autoguardado (Limpiar borra todo sin confirmar), CTAs al fondo del scroll, 7-8 columnas con `scrollX`, `modal-lg/xl`, foto 12MP rebota contra 10MB sin aviso previo.

## Minor Observations

- `afiliacionDebounce` definida y nunca usada; Toastify con fallback a `alert()`.
- Endpoints inconsistentes (`rows` vs `data`).
- `th` sin `scope="col"` en ambas tablas; fichas detalle sin `th`.
- `console.error` vuelca `responseText` íntegro; `registro.php:13` interpola `$persona_id_sesion` (intval-ado, migrar a placeholder).
- `depGenero` colapsa OTRO a Masculino; `filtro_estado_admin` mezcla borradores internos con solicitudes reales.

## Questions to Consider

- Si el acudiente quiere inscribir en 3 minutos desde el celular, ¿por qué debe entender qué es un borrador antes de adjuntar?
- ¿Qué pierde el club si todo borrador completo se auto-envía con un solo "Inscribir a mi hijo/a"?
- Con 200 solicitudes de un torneo en un fin de semana, ¿qué colapsa primero: el admin con un modal por dictamen o la tabla sin acciones en lote?
