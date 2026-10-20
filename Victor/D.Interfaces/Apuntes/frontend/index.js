const url="http://localhost/VSC_Pruebas/2dam/Victor/D.Interfaces/backend/model/sw.alumnos.php"; //URL del backend donde se encuentra el archivo alumno.php

const button = document.getElementById("obtener");
//Botontes que llaman a las funciones concretas cuando se reproduce el evento "click"
button.addEventListener("click", (event) => {
    //button.textContent = `Recuento de clics: ${event.detail}`;
    getAlumno();
});
const buttonInsertar = document.getElementById("insertar");
buttonInsertar.addEventListener("click", (event) => {
    insertAlumno();
});
const buttonUpdate = document.getElementById("actualizar");
buttonUpdate.addEventListener("click", (event)=>{
    updateAlumno();
})
const buttonDelete = document.getElementById("borrar");
buttonDelete.addEventListener("click", (event)=>{
    deleteAlumno();
})
//Funciones de los botones
//Obtener ALumno
function getAlumno(){
    console.log("Pulsado botón obtener");
    //Se manda una solicitud POST a la URL del backend para obtener los datos del alumno
    //(se usa POST en vez de GET porque el backend siempre espera un body JSON con la accion)
    let request = {};
    request.action = "get";
    request.data = [{
        dni: document.getElementById("dni").value
    }];

    fetch(url, {
        method: "POST",
        body: JSON.stringify(request),
        headers: {
            "Content-Type": "application/json",
        },
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // Muestra el nombre del alumno en JSON en la consola del navegador -->
        const alumno = data.data[0];
        
        document.getElementById("dni").value = alumno.dni //Asigna a data.dni el valor de lo que pongamos en la casilla dni del form
        document.getElementById("nombre").value = alumno.nombre //Asigna a data.nombre el valor de lo que pongamos en la casilla nombre del form

        var apellido_1_i = document.getElementById("apellido_1");
        apellido_1_i.value = alumno.apellido_1 //Asigna a data.apellido_1 el valor de lo que pongamos en la casilla apellido_1 del form

        document.getElementById("localidad").value = alumno.localidad //Asigna a data.localidad el valor de lo que pongamos en la casilla localidad del form  
        //Mostrar los datos en la consola cuando se pulsa el boton get
        console.log("dni: " + alumno.dni);
        console.log("Nombre: " + alumno.nombre); 
        console.log("Apellido: " + alumno.apellido_1); 
        console.log("Localidad: " + alumno.localidad); 

        //Si necesitamos mas de una vez el valor del dato que queremos mejor hacerlo como APELLIDO
        // ya que de esta manera no tenemos que llamar a data.Xcosa cada vez que queramos usarlo, sino que
        //lo guardamos en una variable y lo usamos cuando queramos.
    })

    .catch(function (error) {
        console.error(error);
    });
}
//A cada una de las funciones del objeto se le asigna un valor 
// que es el que se obtiene de la casilla del form correspondiente
//Insertar Alumno
function insertAlumno(){
    let alumno = {};
    alumno.dni = document.getElementById("dni").value;
    alumno.nombre = document.getElementById("nombre").value;
    alumno.apellido_1 = document.getElementById("apellido_1").value;
    alumno.localidad = document.getElementById("localidad").value;

    //Este es el objeto peticion que usa el backend para saber que accion tiene que realizar y con que datos
    let request = {};
    request.action = "insert";
    request.data = [alumno];


    //el fetch lo hacemos para enviar al servidor los datos del alumno en formato JSON,
    //ya que el backend espera recibir los datos en este formato para poder procesarlos correctamente
    fetch(url,{
        method: "POST", // or 'PUT'
        body:JSON.stringify(request), // data can be `string` or {object}!
        headers: {
        "Content-Type": "application/json",
    },
})
    .then((res) => res.json())
    .then((response) => console.log("Success:", response))
    .catch((error) => console.error("Error:", error));
  
}

const formulario = document.getElementById("nombre");

formulario.addEventListener("mouseover", (event) => {
    formulario.style.backgroundColor="yellow";
});

//Update alumno
function updateAlumno(){
    console.log("Pulsado boton Update")
    const alumno = {
        dni: document.getElementById("dni").value,
        nombre: document.getElementById("nombre").value,
        apellido_1: document.getElementById("apellido_1").value,
        localidad: document.getElementById("localidad").value
    };
    let request = {};
    request.action = "update";
    request.data = [alumno];
    fetch(url, {

        method: "POST",
        body: JSON.stringify(request),
        headers: {
            "Content-Type": "application/json",
        },
    })
    .then(response=>response.json())
    .then(data => console.log(data))
    .catch (function (error) {
        console.error(error);
    });
}


//Borrar Alumno
function deleteAlumno(){
    console.log("Pulsado boton delete")
    let request = {};
    request.action= "delete";
    request.data = [{
        dni: document.getElementById("dni").value
    }];
    fetch(url, {

        method: "POST",
        body: JSON.stringify(request),
        headers: {
            "Content-Type": "application/json",
        },
    })
    .then(response=>response.json())
    .then(data => {
        console.log(data);
        if (data.success) {
            document.getElementById("dni").value = "";
            document.getElementById("nombre").value = "";
            document.getElementById("apellido_1").value = "";
            document.getElementById("localidad").value = "";
        }
    })
    .catch (function (error) {
        console.error(error);
    });
}
/* Respuesta
{
    data: [{}, {}],
    success: true/false,
    msg: "",
    error: [{msg: , errro:}, {}...]
}

// Envío
{
    action: insert/delete/update/get,
    data: [{}],
    filters: {},
    page: ,
    offset: ,
}

*/