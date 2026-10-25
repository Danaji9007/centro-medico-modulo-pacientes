# Módulo de pacientes — Centro médico

Módulo web de una aplicación de gestión de centro médico, construido en **PHP 8**
con **PDO/MySQL** y organizado con el patrón **Modelo-Vista-Controlador (MVC)**.

El módulo permite **registrar, consultar, actualizar y eliminar (CRUD)** pacientes
sobre la tabla `PACIENTE` de la base de datos `clinica_salud`, e incorpora los
controles de seguridad exigidos por el estándar de codificación del proyecto:
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
│   └── Paciente.php               Modelo del paciente: atributos, validación y CRUD
├── controladores/
│   └── PacienteControlador.php    Lee la petición HTTP y despacha la acción
├── vistas/
│   └── pacientes/
│       ├── registro.php           Formulario de alta
│       ├── edicion.php            Formulario de edición (prellenado con ?id=)
│       ├── listado.php            Tabla con todos los pacientes
│       ├── exito.php              Confirmación de la operación
│       └── confirmacion.php       Confirmación del borrado
├── public/
│   ├── index.php                  Único punto de entrada; enruta por ?accion=
│   └── estilos.php                Entrega la hoja de estilos de `assets/css/`
├── assets/
│   └── css/estilos.css            CSS propio, mínimo y legible
├── .gitignore
└── README.md
```

Los **namespaces equivalen a paquetes**: cada carpeta es un paquete y cada
clase vive en el namespace que corresponde a su carpeta.

| Namespace | Carpeta | Clases |
|---|---|---|
| `App\Infraestructura` | `infraestructura/` | `BaseDeDatos` |
| `App\Modelos` | `modelos/` | `Paciente` |
| `App\Controladores` | `controladores/` | `PacienteControlador` |
| `App\Vistas` | `vistas/` | Vistas HTML5 (plantillas) |

---

## 3. Configuración de la base de datos

Las credenciales **no están en el código de las clases**: se declaran como
constantes en `config/config.php`, archivo que reside fuera de `public/`.

```php
const BD_SERVIDOR = '127.0.0.1';
const BD_PUERTO = 3306;
const BD_USUARIO = 'root';
const BD_CLAVE = '';
const BD_ESQUEMA = 'clinica_salud';
const BD_CHARSET = 'utf8mb4';
```

La tabla utilizada es `PACIENTE`, con las columnas `id_paciente`, `nombre`,
`apellido`, `numero_identificacion`, `fecha_nacimiento`, `genero`, `direccion`,
`telefono`, `email`, `tipo_sangre` y `alergias`.

> Para una instalación propia, basta con cambiar esas constantes. Si el servidor
> de base de datos usa credenciales reales, conviene moverlas a un archivo `.env`
> (ya está excluido por `.gitignore`) y leerlas desde `config/config.php`.

---

## 4. Cómo ejecutar

Desde la raíz del proyecto (`centro_medico/`), con el servidor integrado de PHP:

```bash
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public
```

Luego se abre en el navegador:

| URL | Resultado |
|---|---|
| `http://127.0.0.1:8080/index.php?accion=listado` | Listado de pacientes |
| `http://127.0.0.1:8080/index.php?accion=registro` | Formulario de alta |

En Apache (XAMPP) basta con copiar la carpeta `centro_medico` dentro de
`htdocs/`; las rutas de las vistas y de los recursos se calculan en tiempo de
ejecución a partir de `SCRIPT_NAME`, de modo que no hay rutas absolutas fijadas
en el código.

---

## 5. Acciones disponibles

| Acción | Método HTTP | Descripción |
|---|---|---|
| `?accion=listado` | GET | Lista todos los pacientes (vista inicial por defecto) |
| `?accion=registro` | GET | Muestra el formulario de alta |
| `?accion=registrar` | POST | Registra el paciente enviado por el formulario |
| `?accion=edicion&id=N` | GET | Muestra el formulario de edición del paciente `N` |
| `?accion=actualizar` | POST | Actualiza el paciente cuyo `id` viaja en el formulario |
| `?accion=eliminar&id=N` | GET | Muestra la confirmación del borrado |
| `?accion=eliminar&id=N` | POST | Elimina el paciente `N` |

---

## 6. Cómo probar cada operación

### 6.1 Registrar (alta)

1. Abrir `http://127.0.0.1:8080/index.php?accion=registro`.
2. Completar nombre, apellido y número de identificación (campos obligatorios).
3. Pulsar **Registrar paciente**.
4. Resultado esperado: la vista de confirmación *"El paciente fue registrado
   correctamente"* y el paciente aparece en el listado.

### 6.2 Consultar (listado)

1. Abrir `http://127.0.0.1:8080/index.php?accion=listado`.
2. Resultado esperado: una tabla con `id`, nombre, apellido, identificación,
   fecha de nacimiento, género, teléfono y correo electrónico de cada paciente.

### 6.3 Actualizar (edición)

1. Desde el listado, pulsar **Editar** en la fila del paciente.
2. Cambiar, por ejemplo, el teléfono y pulsar **Guardar cambios**.
3. Resultado esperado: vista de confirmación y el nuevo teléfono en el listado.

### 6.4 Eliminar (borrado)

1. Desde el listado, pulsar **Eliminar** en la fila del paciente.
2. El sistema muestra la vista de confirmación con los datos del paciente.
3. Pulsar **Eliminar paciente**.
4. Resultado esperado: vista de confirmación y el paciente desaparece del listado.

### 6.5 Casos de error esperados

| Caso | Resultado esperado |
|---|---|
| Correo electrónico con formato inválido | Mensaje *"El correo electrónico no tiene un formato válido"* y **no** se inserta el registro |
| Número de identificación repetido | Mensaje *"Ya existe un paciente registrado con ese número de identificación"* y **no** se inserta el registro |
| Nombre o apellido vacío | Mensaje *"El nombre del paciente es obligatorio"* y no se inserta el registro |
| Teléfono con caracteres no permitidos | Mensaje de validación del teléfono y no se inserta el registro |

### 6.6 Verificación directa en la base de datos

```bash
C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 -P 3306 \
  -e "SELECT COUNT(*) AS filas FROM clinica_salud.PACIENTE;"
C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 -P 3306 \
  -e "SELECT * FROM clinica_salud.PACIENTE;"
```

---

## 7. Estándar de codificación aplicado

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
| A-04 / VU-01 / VU-02 | Sin SQL ni lógica de negocio en las vistas | `vistas/pacientes/` |
| C-01 / C-02 / C-04 | Clases en PascalCase, singular y con el nombre del archivo | `Paciente`, `BaseDeDatos`, `PacienteControlador` |
| V-01 / V-09 | Variables en camelCase y constantes en UPPER_SNAKE_CASE | Todo el proyecto |
| M-02 | Accesores `getX()` y modificadores `setX()` | `App\Modelos\Paciente` |
| M-05 | El constructor se llama `__construct()` | `App\Modelos\Paciente` |
| E-01 | Orden de miembros: constantes, propiedades, constructor, públicos, protegidos, privados | Todas las clases |
| E-07 / D-01 | Visibilidad y tipo de retorno en cada método | Todas las clases |
| EN-04 | La validación vive en los modificadores | `setNombre()`, `setEmail()`, `setTelefono()`, `setGenero()`, `setFechaNacimiento()`, … |
| B-01 / B-02 | Conexión única y credenciales fuera del código | `App\Infraestructura\BaseDeDatos`, `config/config.php` |
| B-06 | Consultas parametrizadas, sin concatenar valores | Todos los SQL de `Paciente` |
| B-07 | Sin `SELECT *`: las columnas se enumeran | Sentencias de `Paciente` |
| B-08 | Las escrituras van dentro de una transacción | `registrar()`, `actualizar()`, `eliminar()` |
| B-09 | El SQL se construye en métodos privados dedicados | `construirSqlInsercion()`, `construirSqlActualizacion()`, … |
| S-01 | La entrada se valida en el controlador | `App\Controladores\PacienteControlador` |
| S-04 | La regla de validación se expresa una sola vez | En los `setX()` del modelo |
| S-05 / S-07 | Los errores se registran con `error_log()` y no se muestran al usuario | `registrarError()` del controlador |
| SE-03 | Toda salida se escapa con `htmlspecialchars()` | Todas las vistas |
| DOC-01 / DOC-02 | PHPDoc con `@param`, `@return` y `@throws` | Clases y métodos públicos |
| Q-01 | Todos los archivos pasan `php -l` | Ver sección 8 |

### 7.1 Decisiones de diseño que conviene conocer

1. **Hoja de estilos fuera de `public/`.** La regla A-02 obliga a que `public/`
   sea la única carpeta expuesta, pero la hoja de estilos vive en
   `assets/css/estilos.css`. `public/estilos.php` la entrega con el tipo de
   contenido correcto, de modo que hay **una sola copia** del archivo y la
   carpeta `assets/` nunca queda expuesta por URL.
2. **El borrado se confirma antes de ejecutarse.** Un enlace `GET` nunca
   modifica datos: el enlace *Eliminar* del listado abre la vista
   `confirmacion.php` y el borrado real ocurre únicamente con un `POST`.
   Así, el módulo resiste las 24 no conformidades de la matriz del informe,
   entre ellas el acceso a `$_POST` desde la capa de modelo.
3. **Aviso de obsolescencia de `FILTER_SANITIZE_STRING`.** PHP 8.1 declaró
   obsoleto ese filtro, que el estándar del proyecto exige en la lectura de la
   entrada. El controlador lo mantiene y silencia **únicamente** el aviso
   `E_DEPRECATED` durante esa lectura (`aplicarFiltro()`), para que la
   respuesta al usuario no se mezcle con avisos del lenguaje.
4. **Sin token CSRF.** La protección CSRF tiene sentido cuando existe una
   sesión autenticada que un atacante pueda suplantar. Este módulo todavía no
   tiene autenticación (no hay usuarios ni sesión iniciada), de modo que un
   token no aportaría protección real; se incorporará en el módulo de usuarios
   cuando exista inicio de sesión, junto con la regla SE-04.

---

## 8. Verificación estática

Antes de integrar el módulo, todos los archivos pasan la validación de sintaxis:

```bash
C:\xampp\php\php.exe -l config/config.php
C:\xampp\php\php.exe -l infraestructura/BaseDeDatos.php
C:\xampp\php\php.exe -l modelos/Paciente.php
C:\xampp\php\php.exe -l controladores/PacienteControlador.php
C:\xampp\php\php.exe -l vistas/pacientes/registro.php
C:\xampp\php\php.exe -l vistas/pacientes/edicion.php
C:\xampp\php\php.exe -l vistas/pacientes/listado.php
C:\xampp\php\php.exe -l vistas/pacientes/exito.php
C:\xampp\php\php.exe -l vistas/pacientes/confirmacion.php
C:\xampp\php\php.exe -l public/index.php
C:\xampp\php\php.exe -l public/estilos.php
```

El detalle de la prueba funcional (CRUD completo, casos de error y estado final
de la tabla) queda registrado en `../VERIFICACION.txt`. Junto a la entrega se
incluyen los tres scripts que hacen reproducible la evidencia:

| Script | Función |
|---|---|
| `../prueba_funcional.py` | Ejecuta las 29 comprobaciones funcionales contra el servidor y la base de datos |
| `../generar_verificacion.py` | Levanta el servidor, corre la prueba y regenera `VERIFICACION.txt` |
| `../auditoria_estandar.py` | Comprueba mecánicamente el cumplimiento del estándar (líneas, PHPDoc, tipos, marcadores SQL) |

```bash
cd centro_medico
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public
# en otra terminal
python ..\prueba_funcional.py
```
