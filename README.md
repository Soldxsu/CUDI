# Manual de Usuario - Sistema CUDI
## Sistema de Gestión de Disposición Áulica

### Tabla de Contenidos
1. [Introducción](#introducción)
2. [Acceso al Sistema](#acceso-al-sistema)
3. [Navegación Principal](#navegación-principal)
4. [Crear Nueva Tarjeta](#crear-nueva-tarjeta)
5. [Gestionar Tarjetas Existentes](#gestionar-tarjetas-existentes)
6. [Duplicar Tarjetas](#duplicar-tarjetas)
7. [Eliminar Tarjetas](#eliminar-tarjetas)
8. [Gestión de Profesores](#gestión-de-profesores)
9. [Validaciones y Restricciones](#validaciones-y-restricciones)
10. [Solución de Problemas](#solución-de-problemas)

---

## Introducción

El Sistema CUDI es una herramienta de gestión académica que permite administrar la disposición de aulas, asignando materias, profesores y horarios de manera eficiente. El sistema previene conflictos de horarios y garantiza el uso óptimo de los recursos educativos.

### Características Principales
- **Calendario visual** para gestión de tarjetas
- **Validación automática** de conflictos de aulas
- **Duplicación inteligente** de tarjetas
- **Gestión de profesores** integrada
- **Operaciones sin recarga** de página (AJAX)

---

## Acceso al Sistema

1. **Iniciar Sesión**
   - Accede a la URL del sistema CUDI
   - Ingresa tus credenciales de usuario
   - Haz clic en "Iniciar Sesión"

2. **Navegación**
   - Una vez autenticado, serás dirigido al calendario principal
   - Verás el mes actual con todas las tarjetas de disposición

---

## Navegación Principal

### Calendario
- **Vista mensual**: Muestra todos los días del mes actual
- **Navegación**: Usa los botones "Anterior" y "Siguiente" para cambiar de mes
- **Tarjetas**: Cada día puede contener múltiples tarjetas de disposición
- **Colores**: Las tarjetas tienen diferentes colores según su estado:
  - **Verde**: Tarjeta activa
  - **Azul**: Tarjeta duplicada
  - **Otros colores**: Estados específicos

### Controles Superiores
- **Filtros**: Permite filtrar tarjetas por criterios específicos
- **Botones de acción**: Acceso rápido a funciones principales

---

## Crear Nueva Tarjeta

### Paso 1: Abrir Modal
1. Haz clic en el botón **"+"** en cualquier día del calendario
2. Se abrirá el modal "Nueva Tarjeta de Disposición"

### Paso 2: Completar Información

#### Cantidad de Estudiantes
- **Campo obligatorio**
- Ingresa el número de estudiantes que asistirán
- Este dato determina qué aulas están disponibles

#### Fecha
- Se completa automáticamente con el día seleccionado
- Puedes modificarla si es necesario

#### Turno
- Selecciona el turno deseado:
  - Mañana (08:00:00 - 12:00:00)
  - Tarde (13:00:00 - 17:00:00)
  - Noche (18:00:00 - 22:00:00)

#### Horario
- Se filtran automáticamente según el turno seleccionado
- Muestra los horarios disponibles para ese turno

#### Materia
- Usa el campo de búsqueda para encontrar la materia
- Escribe parte del nombre y selecciona de la lista

#### Profesor
- **Profesor actual**: Si la materia ya tiene un profesor asignado, se muestra automáticamente
- **Cambiar profesor**: Haz clic en "Cambiar Profesor" para seleccionar otro
- **Agregar profesor**: Si no hay profesor asignado, usa "Agregar Profesor"

#### Aula
- Se filtran automáticamente según:
  - Cantidad de estudiantes (capacidad suficiente)
  - Disponibilidad en el horario seleccionado

### Paso 3: Guardar
1. Verifica que todos los campos estén completos
2. Haz clic en **"Crear Tarjeta"**
3. El sistema validará la información y creará la tarjeta

---

## Gestionar Tarjetas Existentes

### Ver Detalles
1. Haz clic en cualquier tarjeta del calendario
2. Se abrirá un modal con toda la información:
   - Fecha (formato dd/mm/aaaa)
   - Turno y horario
   - Materia y profesor
   - Aula y capacidad
   - Cantidad de estudiantes

### Editar Tarjeta
1. En el modal de detalles, haz clic en **"Editar"**
2. Modifica los campos necesarios
3. Haz clic en **"Actualizar Tarjeta"**

---

## Duplicar Tarjetas

### Función de Duplicación
La duplicación permite crear una copia exacta de una tarjeta para otra fecha, manteniendo todos los datos (turno, horario, materia, profesor, aula, cantidad de estudiantes).

### Proceso de Duplicación
1. **Seleccionar tarjeta**: Haz clic en la tarjeta que deseas duplicar
2. **Abrir duplicación**: En el modal de detalles, haz clic en **"Duplicar"**
3. **Seleccionar fecha**: 
   - Aparecerá un modal con un selector de fecha
   - Solo puedes seleccionar fechas futuras
   - La fecha mínima es el día actual
4. **Confirmar**: Haz clic en **"Duplicar"**

### Validaciones Automáticas
El sistema verificará automáticamente:
- **Disponibilidad de aula**: El aula no debe estar ocupada en la nueva fecha/turno/horario
- **Fecha válida**: No se puede duplicar en fechas pasadas

### Manejo de Conflictos
Si el aula ya está ocupada, el sistema mostrará un mensaje específico:
> "El aula [nombre] ya está ocupada el [fecha] en el turno [turno] horario [horario]. Para duplicar debe cambiar el turno, horario o seleccionar otro día."

**Opciones de solución:**
- Seleccionar otra fecha
- Cambiar el turno en la tarjeta original antes de duplicar
- Cambiar el horario en la tarjeta original antes de duplicar

---

## Eliminar Tarjetas

### Proceso de Eliminación
1. **Seleccionar tarjeta**: Haz clic en la tarjeta que deseas eliminar
2. **Confirmar eliminación**: En el modal de detalles, haz clic en **"Eliminar"**
3. **Confirmación**: Aparecerá un diálogo de confirmación
4. **Procesar**: Haz clic en **"Aceptar"** para confirmar la eliminación

### Características
- **Eliminación inmediata**: Sin recarga de página
- **Confirmación obligatoria**: Previene eliminaciones accidentales
- **Feedback inmediato**: Mensaje de confirmación

---

## Gestión de Profesores

### Agregar Nuevo Profesor
1. En el formulario de tarjeta, haz clic en **"Agregar Profesor"**
2. Completa la información:
   - Nombre completo
   - Email (opcional)
   - Teléfono (opcional)
3. Haz clic en **"Guardar"**

### Cambiar Profesor de Materia
1. En el formulario de tarjeta, haz clic en **"Cambiar Profesor"**
2. Selecciona el nuevo profesor de la lista
3. El cambio se aplicará automáticamente a la materia

### Eliminar Profesor
1. En la lista de profesores, haz clic en el ícono de eliminar (🗑️)
2. Confirma la eliminación
3. **Nota**: Solo se pueden eliminar profesores no asignados a materias activas

---

## Validaciones y Restricciones

### Política de Aulas
**Regla fundamental**: Una aula solo puede ser utilizada una vez por día/turno/horario específico.

### Validaciones Automáticas
1. **Capacidad de aula**: Debe ser suficiente para la cantidad de estudiantes
2. **Disponibilidad de horario**: El aula no debe estar ocupada
3. **Fechas válidas**: No se permiten fechas pasadas para nuevas tarjetas
4. **Campos obligatorios**: Todos los campos requeridos deben estar completos

### Mensajes de Error
El sistema proporciona mensajes específicos para cada tipo de error:
- Conflictos de aula
- Capacidad insuficiente
- Campos faltantes
- Fechas inválidas

---

## Solución de Problemas

### Problemas Comunes

#### "No puedo duplicar una tarjeta"
**Posibles causas:**
- El aula ya está ocupada en la fecha/turno/horario seleccionado
- La fecha seleccionada es anterior al día actual

**Solución:**
- Selecciona otra fecha
- Cambia el turno o horario de la tarjeta original
- Verifica que la fecha sea futura

#### "No aparecen aulas disponibles"
**Posibles causas:**
- La cantidad de estudiantes excede la capacidad de las aulas
- Todas las aulas están ocupadas en ese horario

**Solución:**
- Reduce la cantidad de estudiantes
- Selecciona otro horario o turno
- Verifica la disponibilidad en otra fecha

#### "Los botones no funcionan"
**Posibles causas:**
- Error de JavaScript en el navegador
- Conexión de red interrumpida

**Solución:**
- Recarga la página (F5)
- Verifica tu conexión a internet
- Limpia la caché del navegador

### Contacto de Soporte
Para problemas técnicos o consultas adicionales, contacta al administrador del sistema.

---

## Consejos de Uso

### Mejores Prácticas
1. **Planificación anticipada**: Crea las tarjetas con suficiente antelación
2. **Verificación de datos**: Revisa siempre la información antes de guardar
3. **Uso de duplicación**: Aprovecha la función de duplicación para materias recurrentes
4. **Gestión de profesores**: Mantén actualizada la información de contacto

### Atajos Útiles
- **F5**: Recargar página
- **Esc**: Cerrar modales abiertos
- **Tab**: Navegar entre campos del formulario

---

*Manual de Usuario - Sistema CUDI v1.0*  
*Última actualización: Agosto 2025*