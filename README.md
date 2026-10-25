# Módulos de gestión del centro médico

Aplicación web de gestión de centro médico construida en **PHP 8** con **PDO/MySQL**
y organizada con el patrón **Modelo-Vista-Controlador (MVC)**.

El proyecto contiene tres módulos con operaciones **CRUD** completas:

- **Pacientes**: alta, consulta, edición y eliminación de pacientes.
- **Usuarios**: alta, consulta, edición y eliminación de usuarios del sistema,
  con almacenamiento seguro de contraseñas mediante `password_hash`.
- **Médicos**: alta, consulta, edición y eliminación de médicos.

Todos los módulos se ejecutan sobre la base de datos `centromedico` e incorporan
los controles de seguridad exigidos por el estándar de codificación del proyecto:
consultas parametrizadas, validación de la entrada en el controlador, validación
del dominio en los modificadores del modelo y escapado de la salida en las vistas.

---

## 1. Requisitos

| Elemento | Versión / detalle |
|---|---|
| PHP | 8.2 (probado con PHP 8.2.12 de XAMPP) |
| Extensiones | `pdo_mysql`, `mbstring` |
| Base de datos | MySQL / MariaDB 10.4 o superior |
| Servidor web | Servidor integrado de PHP (`php -S`) o Apache de XAMPP |
| Navegador | Cualquier navegador moderno |

No se requieren dependencias externas: no hay `composer.json` ni carpeta `vendor/`.

---

## 2. Estructura del proyecto

```text
centro_medico/
├── config/
│   └── config.php                 Constantes de configuración, rutas y autocarga PSR-4
├── infraestructura/
│   └── BaseDeDatos.php            Conexión PDO (singleton) y consultas parametrizadas
├── modelos/
│   ├── Paciente.php               Modelo del paciente: atributos, validación y CRUD
│   ├── Usuario.php                Modelo del usuario: atributos, validación y CRUD
│   └── Medico.php                 Modelo del médico: atributos, validación y CRUD
├── controladores/
│   ├── PacienteControlador.php    Lee la petición HTTP y despacha la acción de pacientes
│   ├── UsuarioControlador.php     Lee la petición HTTP y despacha la acción de usuarios
│   └── MedicoControlador.php      Lee la petición HTTP y despacha la acción de médicos
├── vistas/
│   ├── pacientes/                 Vistas del módulo Pacientes
│   │   ├── registro.php
│   │   ├── edicion.php
│   │   ├── listado.php
│   │   ├── exito.php
│   │   └── confirmacion.php
│   ├── usuarios/                  Vistas del módulo Usuarios
│   │   ├── registro.php
│   │   ├── edicion.php
│   │   ├── listado.php
│   │   ├── exito.php
│   │   └── confirmacion.php
│   └── medicos/                   Vistas del módulo Médicos
│       ├── registro.php
│       ├── edicion.php
│       ├── listado.php
│       ├── exito.php
│       └── confirmacion.php
├── database/
│   └── centromedico.sql           Script de creación de la base de datos y tablas
├── public/
│   ├── index.php                  Único punto de entrada; enruta por ?modulo= y ?accion=
│   └── estilos.php                Entrega la hoja de estilos de `assets/css/`
├── assets/
│   └── css/estilos.css            CSS propio, mínimo y legible
├── .env.example                   Plantilla de variables de entorno
├── .gitignore
└── README.md
```

Los **namespaces equivalen a paquetes**: cada carpeta es un paquete y cada
clase vive en el namespace que corresponde a su carpeta.

| Namespace | Carpeta | Clases |
|---|---|---|
| `App\Infraestructura` | `infraestructura/` | `BaseDeDatos` |
| `App\Modelos` | `modelos/` | `Paciente`, `Usuario`, `Medico` |
| `App\Controladores` | `controladores/` | `PacienteControlador`, `UsuarioControlador`, `MedicoControlador` |
| `App\Vistas` | `vistas/` | Vistas HTML5 (plantillas) |

---

## 3. Configuración de la base de datos

Las credenciales **no están en el código de las clases**: se leen desde el archivo
`.env` de la raíz del proyecto (excluido del repositorio por `.gitignore`).
Como plantilla se incluye `.env.example`.

Para configurar el entorno local:

1. Copiar `.env.example` como `.env`.
2. Ajustar los valores reales del entorno.

```dotenv
BD_SERVIDOR=127.0.0.1
BD_PUERTO=3306
BD_USUARIO=root
BD_CLAVE=
BD_ESQUEMA=centromedico
BD_CHARSET=utf8mb4
BD_MODO_SQL=STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION
```

El esquema utilizado es `centromedico` y contiene las tablas `roles`,
`usuarios`, `pacientes` y `medicos`.

Para crear la base de datos y las tablas desde la raíz del proyecto:

```bash
C:\xampp\mysql\bin\mysql.exe -u root -p < database/centromedico.sql
```

---

## 4. Cómo ejecutar

Desde la raíz del proyecto (`centro_medico/`), con el servidor integrado de PHP:

```bash
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public
```

Luego se abre en el navegador la URL del módulo deseado:

| URL | Resultado |
|---|---|
| `http://127.0.0.1:8080/index.php?modulo=pacientes&accion=listado` | Listado de pacientes |
| `http://127.0.0.1:8080/index.php?modulo=usuarios&accion=listado` | Listado de usuarios |
| `http://127.0.0.1:8080/index.php?modulo=medicos&accion=listado` | Listado de médicos |

En Apache (XAMPP) basta con copiar la carpeta `centro_medico` dentro de
`htdocs/`; las rutas de las vistas y de los recursos se calculan en tiempo de
ejecución a partir de `SCRIPT_NAME`, de modo que no hay rutas absolutas fijadas
en el código.

---

## 5. Acciones disponibles

Cada módulo expone las mismas acciones. El parámetro `modulo` indica el módulo
(`pacientes`, `usuarios` o `medicos`) y el parámetro `accion` indica la operación.

| Acción | Método HTTP | Descripción |
|---|---|---|
| `?modulo=X&accion=listado` | GET | Lista todos los registros del módulo (vista inicial por defecto) |
| `?modulo=X&accion=registro` | GET | Muestra el formulario de alta |
| `?modulo=X&accion=registrar` | POST | Registra el registro enviado por el formulario |
| `?modulo=X&accion=edicion&id=N` | GET | Muestra el formulario de edición del registro `N` |
| `?modulo=X&accion=actualizar` | POST | Actualiza el registro cuyo `id` viaja en el formulario |
| `?modulo=X&accion=eliminar&id=N` | GET | Muestra la confirmación del borrado |
| `?modulo=X&accion=eliminar&id=N` | POST | Elimina el registro `N` |

---

## 6. Estándar de codificación aplicado

El código sigue el estándar de codificación definido en el informe técnico de
la evidencia `GA7-220501096-AA1-EV02` (capítulo 4) y su lista de verificación
del anexo B. Correspondencia de las reglas principales:

| Regla | Requisito | Dónde se cumple |
|---|---|---|
| G-03 | Indentación de 4 espacios, sin tabulaciones | Todos los archivos |
| G-08 | `declare(strict_types=1);` inmediatamente después de `<?php` | Todos los archivos PHP |
| G-09 | Todo archivo declara su namespace | Clases y vistas |
| G-12 | Sin etiqueta de cierre `?>` en archivos con sólo PHP | Clases y configuración |
| A-02 | `public/` es la única carpeta expuesta | `public/index.php` y `public/estilos.php` |
| A-04 / VU-01 / VU-02 | Sin SQL ni lógica de negocio en las vistas | `vistas/pacientes/`, `vistas/usuarios/`, `vistas/medicos/` |
| C-01 / C-02 / C-04 | Clases en PascalCase, singular y con el nombre del archivo | `Paciente`, `Usuario`, `Medico`, `BaseDeDatos`, `PacienteControlador`, `UsuarioControlador`, `MedicoControlador` |
| V-01 / V-09 | Variables en camelCase y constantes en UPPER_SNAKE_CASE | Todo el proyecto |
| M-02 | Accesores `getX()` y modificadores `setX()` | `App\Modelos\Paciente`, `App\Modelos\Usuario`, `App\Modelos\Medico` |
| M-05 | El constructor se llama `__construct()` | Todos los modelos |
| E-01 | Orden de miembros: constantes, propiedades, constructor, públicos, protegidos, privados | Todas las clases |
| E-07 / D-01 | Visibilidad y tipo de retorno en cada método | Todas las clases |
| EN-04 | La validación vive en los modificadores | `setNombre()`, `setEmail()`, `setTelefono()`, `setGenero()`, `setFechaNacimiento()`, … |
| B-01 / B-02 | Conexión única y credenciales fuera del código | `App\Infraestructura\BaseDeDatos`, `config/config.php`, `.env` |
| B-06 | Consultas parametrizadas, sin concatenar valores | Todos los SQL de los modelos |
| B-07 | Sin `SELECT *`: las columnas se enumeran | Sentencias de los modelos |
| B-08 | Las escrituras van dentro de una transacción | `registrar()`, `actualizar()`, `eliminar()` |
| B-09 | El SQL se construye en métodos privados dedicados | `construirSqlInsercion()`, `construirSqlActualizacion()`, … |
| S-01 | La entrada se valida en el controlador | `App\Controladores\PacienteControlador`, `UsuarioControlador`, `MedicoControlador` |
| S-04 | La regla de validación se expresa una sola vez | En los `setX()` del modelo |
| S-05 / S-07 | Los errores se registran con `error_log()` y no se muestran al usuario | `registrarError()` de cada controlador |
| SE-03 | Toda salida se escapa con `htmlspecialchars()` | Todas las vistas |
| SE-07 | Credenciales en variables de entorno | `.env` y `config/config.php` |
| DOC-01 / DOC-02 | PHPDoc con `@param`, `@return` y `@throws` | Clases y métodos públicos |
| Q-01 | Todos los archivos pasan `php -l` | Ver sección 7 |

### 6.1 Decisiones de diseño que conviene conocer

1. **Hoja de estilos fuera de `public/`.** La regla A-02 obliga a que `public/`
   sea la única carpeta expuesta, pero la hoja de estilos vive en
   `assets/css/estilos.css`. `public/estilos.php` la entrega con el tipo de
   contenido correcto, de modo que hay **una sola copia** del archivo y la
   carpeta `assets/` nunca queda expuesta por URL.
2. **El borrado se confirma antes de ejecutarse.** Un enlace `GET` nunca
   modifica datos: el enlace *Eliminar* del listado abre la vista
   `confirmacion.php` y el borrado real ocurre únicamente con un `POST`.
3. **Almacenamiento seguro de contraseñas.** El módulo de usuarios utiliza
   `password_hash()` para guardar la clave y la comparación se realizaría con
   `password_verify()` en un futuro flujo de autenticación.
4. **Sin token CSRF.** La protección CSRF tiene sentido cuando existe una
   sesión autenticada que un atacante pueda suplantar. Los módulos actuales
   todavía no tienen autenticación, de modo que un token no aportaría protección
   real; se incorporará cuando exista inicio de sesión, junto con la regla SE-04.

---

## 7. Verificación estática

Antes de integrar los módulos, todos los archivos pasan la validación de sintaxis.
El proyecto cuenta con 25 archivos PHP:

```bash
C:\xampp\php\php.exe -l config/config.php
C:\xampp\php\php.exe -l infraestructura/BaseDeDatos.php
C:\xampp\php\php.exe -l modelos/Paciente.php
C:\xampp\php\php.exe -l modelos/Usuario.php
C:\xampp\php\php.exe -l modelos/Medico.php
C:\xampp\php\php.exe -l controladores/PacienteControlador.php
C:\xampp\php\php.exe -l controladores/UsuarioControlador.php
C:\xampp\php\php.exe -l controladores/MedicoControlador.php
C:\xampp\php\php.exe -l vistas/pacientes/registro.php
C:\xampp\php\php.exe -l vistas/pacientes/edicion.php
C:\xampp\php\php.exe -l vistas/pacientes/listado.php
C:\xampp\php\php.exe -l vistas/pacientes/exito.php
C:\xampp\php\php.exe -l vistas/pacientes/confirmacion.php
C:\xampp\php\php.exe -l vistas/usuarios/registro.php
C:\xampp\php\php.exe -l vistas/usuarios/edicion.php
C:\xampp\php\php.exe -l vistas/usuarios/listado.php
C:\xampp\php\php.exe -l vistas/usuarios/exito.php
C:\xampp\php\php.exe -l vistas/usuarios/confirmacion.php
C:\xampp\php\php.exe -l vistas/medicos/registro.php
C:\xampp\php\php.exe -l vistas/medicos/edicion.php
C:\xampp\php\php.exe -l vistas/medicos/listado.php
C:\xampp\php\php.exe -l vistas/medicos/exito.php
C:\xampp\php\php.exe -l vistas/medicos/confirmacion.php
C:\xampp\php\php.exe -l public/index.php
C:\xampp\php\php.exe -l public/estilos.php
```

El detalle de la prueba funcional (CRUD completo de los tres módulos, casos de
error y estado final de las tablas) queda registrado en `../VERIFICACION.txt`.

---

## Repositorio

Código fuente disponible en:

`https://github.com/Danaji9007/centro-medico-modulo-pacientes`
