# ManageScheduls

Este es un sistema integral de gestión académica desarrollado bajo (MVC). Permite a los docentes administrar grupos, configurar planes de evaluación y realizar el seguimiento detallado de las calificaciones de sus alumnos.

## 🚀 Características Principales

### 👨‍🏫 Módulo para Docentes
* **Gestión de Grupos:** Creación y edición de grupos (schedules) vinculados a asignaturas específicas.
* **Estructura de Evaluación Dinámica:** Configuración de unidades y actividades con ponderaciones personalizables que suman el 100%.
* **Importación Masiva de Alumnos:** Registro rápido de estudiantes mediante archivos CSV, generando automáticamente credenciales de acceso.
* **Matriz de Calificaciones:** Interfaz para la captura masiva de notas por actividad y asignación de puntos adicionales (bonus).

### 👨‍🎓 Módulo para Alumnos
* **Reporte de Resultados:** Visualización del puntaje obtenido en cada actividad, retroalimentación del docente y calificación final por unidad.
* **Consulta de Perfil:** Acceso a la información personal del usuario logueado.

### 🛠️ Administración General
* **Gestión de Docentes:** CRUD completo con validaciones de seguridad para CURP y RFC.
* **Control de Asignaturas:** Registro de materias y asignación de múltiples profesores a una misma clase.

## 🏗️ Arquitectura del Proyecto

El proyecto sigue una estructura MVC limpia para facilitar el mantenimiento:

* **/controllers:** Contiene la lógica de negocio (Users, Teachers, Subjects, Schedules, Students).
* **/models:** Manejo de datos y transacciones SQL (PDO).
* **/views:** Interfaces de usuario desarrolladas con Bootstrap 4 y PHP.
* **/public:** Archivos estáticos y punto de entrada de la aplicación.

## 🛠️ Tecnologías Utilizadas

* **Lenguaje:** PHP 7.4+.
* **Base de Datos:** MySQL con soporte para transacciones y llaves foráneas.
* **Frontend:** HTML5, CSS3 (Bootstrap 4), JavaScript.
* **Librerías:** FPDF para la generación de horarios en formato PDF.

## 📋 Requisitos de Instalación

1. Clonar el repositorio.
2. Configurar las constantes de la base de datos y la URL raíz en su archivo de configuración global.
3. Importar la base de datos SQL (tablas: `users`, `teachers`, `subjects`, `schedules`, `unidades`, `actividades`, `inscripciones`, `resultados`).
4. Asegurarse de tener habilitado el módulo `mod_rewrite` en Apache para el manejo de URLs amigables.