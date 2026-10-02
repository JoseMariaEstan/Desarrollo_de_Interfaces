# Cuaderno de apuntes: gestión de alumnos

Apuntes del código de `D.Interfaces`. El proyecto permite insertar, consultar, actualizar y borrar alumnos mediante un formulario web, un backend PHP y una base de datos MySQL.

## Estructura y recorrido

```text
frontend/index.html  -> formulario y botones
frontend/index.js    -> eventos, lectura del formulario y peticiones HTTP
frontend/index.css   -> hoja de estilos enlazada (actualmente vacía)
backend/model/sw.alumnos.php -> endpoint: recibe la petición y elige la operación
backend/model/Alumno.php     -> operaciones de base de datos de alumno
backend/model/conection.php  -> conexión PDO con MySQL
```

El recorrido habitual es:

1. El usuario pulsa uno de los botones.
2. JavaScript recoge los campos, construye un objeto y lo convierte a JSON.
3. `fetch()` envía ese JSON al endpoint PHP mediante una petición `POST`.
4. PHP lee el cuerpo, interpreta `action` y llama al método correspondiente de `Alumno`.
5. La clase ejecuta SQL mediante PDO.
6. PHP devuelve un JSON con los datos, el resultado y un mensaje.
7. JavaScript procesa la respuesta y, al consultar o borrar, actualiza los campos del formulario.

## HTML: formulario y controles

El HTML define la estructura visible de la página. En `index.html` se usan:

- `<!DOCTYPE html>` para indicar que el documento usa HTML moderno.
- `<meta charset="UTF-8">` para permitir caracteres acentuados.
- `<meta name="viewport" ...>` para ajustar el ancho a dispositivos móviles.
- `<link rel="stylesheet" href="index.css">` para cargar estilos CSS.
- `<script src="index.js" defer>` para cargar JavaScript y ejecutarlo después de analizar el HTML.
- `<form>`, `<label>` e `<input>` para presentar los campos del alumno.
- Los atributos `id` permiten que JavaScript encuentre cada elemento con `getElementById()`.
- Los botones tienen `type="button"`; así no envían el formulario ni recargan la página por defecto.

Los campos usados por el programa son `dni`, `nombre`, `apellido_1` y `localidad`. El `for` de cada etiqueta coincide con el `id` del campo para asociarlos.

## JavaScript: DOM y eventos

El DOM es la representación de la página que JavaScript puede consultar y modificar.

```js
const campoDni = document.getElementById("dni");
const dni = campoDni.value;
```

`document.getElementById("...")` busca un elemento por su `id`; `.value` lee o cambia el contenido de un campo.

`addEventListener("click", ...)` registra una función que se ejecuta al pulsar un botón. En este proyecto, los botones `obtener`, `insertar`, `actualizar` y `borrar` llaman respectivamente a `getAlumno()`, `insertAlumno()`, `updateAlumno()` y `deleteAlumno()`.

También se registra un evento `mouseover` en el campo `nombre`. Al pasar el puntero por encima, cambia su `backgroundColor` a amarillo. Es una modificación directa de un estilo desde JavaScript.

`const` declara una referencia que no se reasigna y `let` una variable que puede recibir otro valor. Los objetos agrupan propiedades relacionadas:

```js
const alumno = {
    dni: document.getElementById("dni").value,
    nombre: document.getElementById("nombre").value,
    apellido_1: document.getElementById("apellido_1").value,
    localidad: document.getElementById("localidad").value
};
```

## JavaScript: petición HTTP y JSON

La constante `url` indica la dirección del endpoint PHP. Debe coincidir con la ruta en la que XAMPP sirve el proyecto.

`fetch(url, opciones)` envía una petición HTTP. En este proyecto todas las operaciones usan `POST`, porque el backend espera recibir en el cuerpo un JSON con la operación y sus datos.

```js
const request = {
    action: "insert",
    data: [alumno]
};

fetch(url, {
    method: "POST",
    body: JSON.stringify(request),
    headers: { "Content-Type": "application/json" }
});
```

- `method` establece el método HTTP.
- `JSON.stringify()` convierte un objeto JavaScript a texto JSON para enviarlo.
- `Content-Type: application/json` informa al servidor del formato del cuerpo.
- `response.json()` convierte la respuesta JSON del servidor a un objeto JavaScript.
- `.then()` encadena pasos asíncronos: primero se recibe la respuesta y luego se procesa.
- `.catch()` captura errores de red o errores durante la cadena de promesas.
- `console.log()` y `console.error()` muestran información en la consola del navegador, útil para depurar.

Las peticiones de consulta, inserción, actualización y borrado comparten el mismo formato general:

```json
{
  "action": "get",
  "data": [{ "dni": "12345678A" }]
}
```

Para insertar y actualizar, el elemento de `data` también incluye `nombre`, `apellido_1` y `localidad`. Para borrar solo hace falta el DNI.

## PHP: endpoint y despacho de acciones

`sw.alumnos.php` funciona como controlador o endpoint: recibe la petición, selecciona la acción, llama a la lógica de `Alumno` y forma la respuesta.

- `include "Alumno.php"` carga la clase utilizada por el endpoint.
- `header("Content-Type: application/json; charset=UTF-8")` anuncia que la respuesta es JSON codificado en UTF-8.
- `file_get_contents('php://input')` lee el cuerpo sin procesar de la petición HTTP.
- `json_decode($contenidoJSON, true)` convierte el JSON en un array asociativo de PHP. El segundo argumento `true` es lo que hace que se obtengan arrays en lugar de objetos.
- `switch` y `case` seleccionan el bloque que corresponde a `action`; `break` evita continuar en el siguiente caso.
- `isset(...)` comprueba si existe un valor antes de utilizarlo.
- El operador ternario `condicion ? valor_si_verdadero : valor_si_falso` elige un mensaje según el resultado.
- `echo json_encode($arrSalida)` serializa la respuesta PHP como JSON y la escribe en la respuesta HTTP.

Acciones implementadas:

| `action` | Método de `Alumno` | Uso |
|---|---|---|
| `insert` | `insertAlm()` | Crea un alumno con los cuatro campos. |
| `get` | `obtenerAlm()` | Busca los datos asociados al DNI. |
| `update` | `updateAlm()` | Cambia nombre, primer apellido y localidad del DNI indicado. |
| `delete` | `deleteAlm()` | Borra el alumno cuyo DNI se indica. |

Todas las respuestas siguen esta forma:

```json
{
  "data": [],
  "success": true,
  "msg": "Operación realizada"
}
```

`data` contiene los datos devueltos o utilizados, `success` indica si la operación tuvo éxito y `msg` contiene un mensaje informativo. Si llega una acción desconocida, el bloque `default` marca el resultado como fallido.

## PHP: clase `Alumno` y programación orientada a objetos

`Alumno.php` define la clase `Alumno`, que representa un alumno y reúne las operaciones que trabajan con sus datos.

- Las propiedades `$dni`, `$nombre`, `$apellido_1` y `$localidad` son `private`: solo se accede a ellas desde la clase.
- `__construct(...)` es el constructor. Guarda los valores recibidos en el objeto. Los valores predeterminados `null` permiten crear un objeto pasando solo el DNI, por ejemplo para consultar o borrar.
- `$this->propiedad` hace referencia a una propiedad del objeto actual.
- Los métodos `insertAlm()`, `obtenerAlm()`, `updateAlm()` y `deleteAlm()` implementan las cuatro operaciones.
- `try/catch (PDOException $e)` captura errores de base de datos; en estos métodos se devuelve `false` si ocurre una excepción.
- `global $conexion` permite que los métodos utilicen la conexión creada en `conection.php` e incluida desde `Alumno.php`.

Ejemplos de construcción usados por el endpoint:

```php
$alumnoA = new Alumno($dni); // Consulta o borrado
$alumnoA = new Alumno($dni, $nombre, $apellido, $localidad); // Alta o modificación
```

## MySQL y PDO

`conection.php` crea la conexión con MySQL. La configuración actual usa el servidor `localhost`, la base de datos `universidad`, el usuario `root` y una contraseña vacía, como suele configurarse en un entorno local de XAMPP. Estos valores deben coincidir con la configuración local de MySQL.

`new PDO("mysql:host=...;dbname=...;charset=utf8", ...)` crea la conexión. `PDO::ATTR_ERRMODE` configurado como `PDO::ERRMODE_EXCEPTION` hace que los errores de PDO se lancen como excepciones, que después pueden ser capturadas. Si falla la conexión, el código termina con `die()` y muestra un mensaje crítico.

La clase utiliza SQL preparado con `prepare()` y marcadores con nombre, por ejemplo `:dni`. Luego `execute([...])` enlaza los valores y ejecuta la consulta. Separar los valores del texto SQL ayuda a evitar inyección SQL.

Operaciones SQL presentes:

```sql
INSERT INTO alumno (DNI, NOMBRE, APELLIDO_1, LOCALIDAD)
VALUES (:dni, :nombre, :apellido_1, :localidad);

SELECT dni, nombre, apellido_1, localidad
FROM alumno
WHERE dni = :dni;

UPDATE alumno
SET nombre = :nombre, apellido_1 = :apellido_1, localidad = :localidad
WHERE dni = :dni;

DELETE FROM alumno WHERE dni = :dni;
```

`fetchAll(PDO::FETCH_ASSOC)` devuelve las filas encontradas como arrays asociativos (claves con los nombres de las columnas). La consulta de DNI único devuelve una lista: el primer alumno está en el índice `0`. `execute()` devuelve un booleano que el controlador usa para rellenar `success`.

La tabla `alumno` debe existir en la base de datos `universidad` y tener las columnas `DNI`, `NOMBRE`, `APELLIDO_1` y `LOCALIDAD`.

## Qué hace cada operación en el navegador

- **Obtener:** envía el DNI; si el servidor devuelve una fila, copia sus cuatro valores a los campos del formulario.
- **Insertar:** lee los cuatro campos y envía el objeto completo con `action: "insert"`.
- **Actualizar:** lee los cuatro campos y los envía con `action: "update"`; el DNI identifica la fila.
- **Borrar:** envía el DNI con `action: "delete"`; si `data.success` es verdadero, limpia los cuatro campos.

## Detalles a tener en cuenta al probarlo

- El archivo `index.css` está enlazado, pero está vacío; por ahora no aporta estilos.
- El endpoint está escrito en `url` como una ruta local de XAMPP. Si cambia la carpeta publicada o el puerto, hay que actualizar esa URL.
- En la consulta, `obtenerAlm()` puede devolver una lista vacía cuando no existe el DNI. El JavaScript accede directamente a `data.data[0]`; por tanto, una búsqueda sin resultados necesita comprobar que haya una fila antes de leer sus propiedades.
- Los métodos capturan la excepción PDO y devuelven `false`, pero no guardan ni muestran el detalle de la excepción. El mensaje del endpoint será el mensaje genérico de fallo.
- En el endpoint se asume que el JSON recibido tiene `action` y la estructura `data` esperada. Una petición incompleta o JSON inválido no tiene una validación explícita antes de acceder a sus claves.
- La conexión termina con `die()` y muestra el mensaje de PDO si no puede conectarse. Es práctico durante el desarrollo local; en una aplicación publicada conviene no exponer detalles internos del servidor.

## Patron Singleton:
Es una metodología de trabajo que instancia una clase fuera del modelo en el backend, que permite instanciar una clase conexión una vez, y que cuando quiera volver a crear la conexión (como se hacia el año pasado) esta instancia tiene un método que nos devuelve el objeto conexión en vez de crearlo de nuevo
