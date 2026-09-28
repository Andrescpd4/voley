Para esta primera etapa necesitamos priorizar una versión funcional que podamos poner en operación en el menor tiempo posible.
1. Usuarios y perfiles
El sistema debe permitir la creación de usuarios sin una limitación práctica de cantidad y manejar inicialmente los siguientes roles:
Administrador Voley+
Entrenador
Padre, madre o acudiente
Un acudiente podrá tener uno o más deportistas asociados a su cuenta.
Cada perfil deberá visualizar únicamente la información que corresponda a su rol.
2. Proceso de afiliación e inscripción del deportista
El padre, madre o acudiente debe poder realizar directamente desde la aplicación el proceso de afiliación del niño o niña al club.
Se debe crear un Formulario de Afiliación Voley+, diligenciable desde el perfil del acudiente, que incluya como mínimo:
Nombres y apellidos del deportista
Tipo y número de documento
Fecha de nacimiento
Categoría
EPS
Información de contacto
Dirección
Datos del padre, madre o acudiente
Contacto de emergencia
Información relevante requerida por el club
Observaciones
Documentación requerida
El formulario debe permitir guardar información parcialmente y continuar posteriormente si aún está incompleto.
Al finalizar deberá pasar a revisión administrativa y manejar estados como:
Borrador/incompleto
Pendiente de revisión
Requiere información adicional
Aprobado
No aprobado
Activo
Inactivo
Desde administración debemos poder revisar la solicitud completa, dejar observaciones, devolverla para corrección y finalmente aprobar la afiliación del deportista.
Una vez aprobada, la información deberá convertirse automáticamente en la ficha activa del deportista, evitando volver a registrar datos manualmente.
3. Documentación
Desde administración debemos poder configurar cuáles documentos son obligatorios para realizar la afiliación.
El acudiente deberá poder cargarlos directamente desde su perfil.
Cada documento deberá manejar estados:
Pendiente
En revisión
Aprobado
Requiere corrección/rechazado
Debe permitir dejar observaciones y solicitar nuevamente el documento cuando corresponda.
Necesitamos adicionalmente una visualización rápida que permita identificar deportistas con documentación completa y aquellas que aún tengan pendientes.
4. Autorizaciones y firma digital
Dentro del mismo proceso de afiliación necesitamos incluir las autorizaciones requeridas por Voley+, para que el padre, madre o acudiente pueda leerlas, aceptarlas y firmarlas digitalmente desde la aplicación.
Inicialmente debemos contemplar, entre otras:
Autorización para tratamiento de datos personales.
Autorización de tratamiento de datos del menor.
Autorización para uso de imagen, fotografía y video.
Autorización para publicación de contenido en redes sociales y medios institucionales de Voley+.
Consentimientos y autorizaciones generales requeridos por el club.
Autorizaciones adicionales que posteriormente podamos parametrizar.
Necesitamos que estas autorizaciones sean parametrizables, es decir, que administración pueda crear una nueva autorización o actualizar su contenido sin requerir un desarrollo adicional cada vez.
Cada aceptación o firma debe conservar trazabilidad, incluyendo como mínimo:
Nombre del acudiente que autoriza.
Documento del acudiente.
Deportista relacionado.
Documento/autorización aceptada.
Versión del documento firmado.
Fecha y hora.
Estado de aceptación.
Evidencia de firma digital/electrónica utilizada.
El sistema debe permitir consultar posteriormente exactamente qué versión del documento fue aceptada por cada acudiente.
Si una autorización cambia sustancialmente y administración determina que requiere nueva aceptación, el sistema deberá permitir solicitar nuevamente la firma a los padres.
5. Estado general del proceso de inscripción
Sería importante que el acudiente pueda visualizar el avance de su proceso, por ejemplo:
Inscripción Voley+ – 75 % completada
Indicando claramente qué tiene pendiente:
Información por completar.
Documento faltante.
Documento rechazado.
Autorización pendiente de firma.
Solicitud pendiente de aprobación.
Administración igualmente debe poder visualizar rápidamente cuáles deportistas tienen el proceso completo y cuáles presentan pendientes.
6. Control de asistencia
El entrenador deberá poder crear o seleccionar la clase/entrenamiento y marcar la asistencia de las deportistas:
Asistió
No asistió
Excusa
Llegada tarde
La información debe quedar histórica y permitirnos consultar porcentaje o consolidado de asistencia por deportista y por periodo.
7. Torneos, eventos y salidas
Desde administración debemos poder crear un evento indicando como mínimo:
Nombre del torneo o actividad.
Fecha.
Lugar.
Horario.
Recomendaciones/información adicional.
Deportistas convocadas.
8. Autorizaciones para torneos y salidas
Cuando exista una salida, torneo, competencia o actividad que requiera autorización, el acudiente deberá recibir el documento directamente desde su usuario.
La autorización debe mostrar toda la información del evento y permitir al acudiente seleccionar:
Autorizo.
No autorizo.
Cuando corresponda, debe permitir igualmente realizar firma electrónica/digital.
Debe quedar trazabilidad de:
Deportista.
Acudiente que respondió.
Decisión.
Fecha y hora.
Documento autorizado.
Evento relacionado.
Desde administración necesitamos visualizar fácilmente qué deportistas ya cuentan con autorización y cuáles se encuentran pendientes.
9. Comunicados
Necesitamos una sección para publicar comunicados dirigidos a:
Todo el club.
Una categoría determinada.
Un grupo seleccionado.
Una deportista/acudiente específico.
En comunicaciones importantes necesitamos contar con confirmación de lectura para conocer cuáles padres ya visualizaron la información.
De ser posible, dejar preparada la estructura para generar notificaciones cuando exista:
Nuevo comunicado.
Documento pendiente.
Documento rechazado.
Autorización pendiente.
Convocatoria a torneo.
Cambio de horario.
Solicitud aprobada.
10. Panel administrativo
Crear un tablero básico que nos permita visualizar rápidamente:
Deportistas activas.
Nuevas solicitudes de afiliación.
Afiliaciones pendientes de aprobación.
Documentación pendiente.
Autorizaciones pendientes de firma.
Asistencia.
Próximos eventos/torneos.
Permisos de salida pendientes.
11. Historial del deportista
Cada deportista debe contar con una ficha donde posteriormente podamos consultar en un solo lugar:
Información personal.
Acudientes.
Documentos.
Estado de afiliación.
Autorizaciones firmadas.
Historial de asistencia.
Eventos y torneos.
Permisos.
Comunicaciones relevantes.
12. Seguridad y manejo de información
Debemos garantizar que un padre o acudiente únicamente pueda consultar información correspondiente a sus hijos asociados.
Los entrenadores tendrán acceso únicamente a la información necesaria para desarrollar sus funciones y los administradores tendrán acceso general según los permisos asignados.
Al manejar información y documentación de menores de edad, necesitamos especial control sobre accesos, modificaciones, descargas y autorizaciones, manteniendo trazabilidad de las acciones realizadas.
Para esta primera entrega quiero que nos concentremos exclusivamente en estos puntos.
Funcionalidades como pagos, mensualidades, facturación, evaluaciones deportivas, estadísticas técnicas, inventarios u otros desarrollos adicionales podemos manejarlos posteriormente como una segunda fase.
Por favor revisa inicialmente qué funcionalidades o componentes de nuestra aplicación actual podemos reutilizar para agilizar el desarrollo y evitar construir nuevamente procesos que ya tengamos disponibles.
La prioridad sería desarrollar en este orden:
Usuarios y roles.
Afiliación y ficha del deportista.
Documentación.
Autorizaciones y firma digital.
Aprobación administrativa.
Asistencia.
Eventos y permisos.
Comunicados.
Panel administrativo.

----------------------ABSTRACCION------------------
Agruparemos tus 12 requerimientos en 5 Macro-Módulos para facilitar el desarrollo modular y evitar el alto acoplamiento:

Módulo Core (Base y Seguridad): Gestión de Usuarios, Roles (Admin, Entrenador, Acudiente), Perfiles, y Aislamiento de Datos (SGSI para menores de edad). Reutilizaremos casi al 100% tu motor actual de roles y permisos.

Módulo de Admisiones (El Onboarding): Formulario de afiliación (guardado parcial), carga paramétrica de documentos obligatorios, cálculo de estado general (ej. 75%) y flujo de revisión administrativa (Borrador -> Pendiente -> Aprobado).

Módulo Legal & Compliance: Motor paramétrico de autorizaciones (Tratamiento de datos, Imagen, etc.), firmas digitales/electrónicas, versionamiento de documentos legales y trazabilidad de auditoría.

Módulo Operativo (Cancha): Ficha 360° del deportista, control de asistencia, creación de eventos/torneos y gestión de permisos de salida (con firma digital del acudiente).

Módulo de Comunicaciones & Control: Dashboard administrativo, comunicados segmentados, acuses de recibo y notificaciones push/email.