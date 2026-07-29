document.addEventListener('DOMContentLoaded', iniciarSocios);

function iniciarSocios() {
    lucide.createIcons();

    // VARIABLES

    // MODALES
    const modalAltaSocio = document.getElementById('modalAltaSocio');
    const modalEditarSocio = document.getElementById('modalEditarSocio');
    const modalEliminarSocio = document.getElementById('modalEliminarSocio');

    // BOTONES
    const btnNuevoSocio = document.getElementById('btnNuevoSocio');
    const btnDarDeAltaSocio = document.getElementById('btnDarDeAltaSocio');
    const btnEditarSocio = document.getElementById('btnEditarSocio');
    const btnEliminarSocio = document.getElementById('btnEliminarSocio');

    // TABLA
    const tbodySocios = document.getElementById('tbodySocios');
    let socios = [];
    let paginaActual = 1;
    const sociosPorPagina = 10;

    const contenedorPaginas = document.getElementById('contenedorPaginas');
    const btnAnterior = document.getElementById("btnAnterior");
    const btnSiguiente = document.getElementById("btnSiguiente");
    const tituloCantiadSocios = document.getElementById('tituloCantiadSocios');

    // FILTRO
    let txtBuscarSocio = document.getElementById('txtBuscarSocio');
    const btnBuscarSocio = document.getElementById('btnBuscarSocio');
    let txtSelectFiltro = document.getElementById('selectFiltro');

    // VARIABLES DE ALTA SOCIO
    const txtApellidoAlta = document.getElementById('txtApellidoSocioAlta');
    const txtNombreAlta = document.getElementById('txtNombreSocioAlta');
    const txtDniAlta = document.getElementById('txtDniSocioAlta');
    const txtTelefonoAlta = document.getElementById('txtTelefonoSocioAlta');
    const txtBarrioAlta = document.getElementById('txtBarrioSocioAlta');
    const txtCalleAlta = document.getElementById('txtCalleSocioAlta');
    const txtAlturaAlta = document.getElementById('txtAlturaSocioAlta');

    // VARIABLES DE EDITAR SOCIO
    const txtIdSocioEditar = document.getElementById('txtIdSocioEditar');
    const txtApellidoEditar = document.getElementById('txtApellidoSocioEditar');
    const txtNombreEditar = document.getElementById('txtNombreSocioEditar');
    const txtDniEditar = document.getElementById('txtDniSocioEditar');
    const txtTelefonoEditar = document.getElementById('txtTelefonoSocioEditar');
    const txtBarrioEditar = document.getElementById('txtBarrioSocioEditar');
    const txtCalleEditar = document.getElementById('txtCalleSocioEditar');
    const txtAlturaEditar = document.getElementById('txtAlturaSocioEditar');

    // ID PARA ELIMINAR SOCIO
    let idEliminar = '';

    // COMPORTAMIENTOS
    btnNuevoSocio.addEventListener('click', () => {
        openModal(modalAltaSocio);
    })

    btnDarDeAltaSocio.addEventListener('click', async () => {
        const creado = await darDeAltaSocio();

        if (creado) {
            closeModal(modalAltaSocio);
            obtenerSocios();
        } 
    });

    btnEditarSocio.addEventListener('click', async () => {
        const editado = await editarSocio();

        if (editado) {
            closeModal(modalEditarSocio);
            obtenerSocios();
        }
    });

    btnEliminarSocio.addEventListener('click', async () => {
        const eliminado = await eliminarSocio();

        if (eliminado) {
            closeModal(modalEliminarSocio);
            obtenerSocios();
            obtenerCantidadSocios();
        }
    });

    txtBuscarSocio.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            if (txtBuscarSocio.value.trim() === '') {
                obtenerSocios();
            } else {
                obtenerSociosPorNombre();
            }
        }

        txtSelectFiltro.value = 'Filtros';
    });

    btnBuscarSocio.addEventListener('click', () => {
        if (txtBuscarSocio.value.trim() === '') {
            obtenerSocios();
        } else {
            obtenerSociosPorNombre();
        }

        txtSelectFiltro.value = 'Filtros';
    });

    txtSelectFiltro.addEventListener('change', () => {
        if (!txtSelectFiltro.value.trim() === 'Todos') {
            obtenerSocios();
        } else {
            obtenerSociosPorFiltro();
        }
    });

    tbodySocios.addEventListener('click', (e) => {
        const botonEditar = e.target.closest('.lapiz');
        const botonEliminar = e.target.closest('.tacho');

        if (botonEditar) {
            openModal(modalEditarSocio);
            completarCamposConDatosFila(botonEditar.closest('tr'));
        }

        if (botonEliminar) {
            idEliminar = botonEliminar.closest('tr').dataset.id;
            openModal(modalEliminarSocio);
        }
    }); 

    btnAnterior.addEventListener("click", () => {
        if (paginaActual > 1) {
            paginaActual--;

            mostrarPagina();
            crearPaginacion();
        }
    });

    btnSiguiente.addEventListener("click", () => {
        const totalPaginas = Math.ceil(socios.length / sociosPorPagina);

        if (paginaActual < totalPaginas) {

            paginaActual++;

            mostrarPagina();
            crearPaginacion();
        }
    });

    // FUNCIONES
    function openModal(modal) {
        modal.classList.add('modal--show');
    }

    function closeModal(modal) {
        modal.classList.remove('modal--show');
    }

    async function obtenerSocios() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php');

        socios = await response.json();

        mostrarPagina();
        crearPaginacion();
    }

    function mostrarPagina() {
        const inicio = (paginaActual - 1) * sociosPorPagina;
        const fin = inicio + sociosPorPagina;

        const sociosPagina = socios.slice(inicio, fin);

        let filas = "";

        for (let i = 0; i < sociosPagina.length; i++) {
            filas += 
                `<tr data-id="${sociosPagina[i].id}">
                    <td>${sociosPagina[i].id}</td>
                    <td>${sociosPagina[i].apellido}</td>
                    <td>${sociosPagina[i].nombre}</td>
                    <td>${sociosPagina[i].dni}</td>
                    <td>${sociosPagina[i].telefono}</td>
                    <td>${sociosPagina[i].barrio}</td>
                    <td>${sociosPagina[i].calle}</td>
                    <td>${sociosPagina[i].altura}</td>
                    <td>
                        <i data-lucide="pencil" class="iconoTabla lapiz"></i>
                        <i data-lucide="trash-2" class="iconoTabla tacho"></i>
                    </td>
                </tr>`;
        }
        tbodySocios.innerHTML = filas;
        lucide.createIcons();
    }

    function crearPaginacion() {
        contenedorPaginas.innerHTML = "";

        const totalPaginas = Math.ceil(socios.length / sociosPorPagina);

        for (let i = 1; i <= totalPaginas; i++) {
            const boton = document.createElement('button');
            boton.classList.add('btnPagina');

            boton.textContent = i;

            if (i === paginaActual) {
                boton.classList.add('paginaActiva');
            }

            boton.addEventListener('click', () => {
                paginaActual = i;

                mostrarPagina();
                crearPaginacion();
            });

            contenedorPaginas.appendChild(boton);
        }
    }

    async function obtenerSociosPorNombre() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php?accion=nombreSocio&buscar=' + txtBuscarSocio.value)

        socios = await response.json();

        mostrarPagina();
        crearPaginacion();
    }

    async function obtenerSociosPorFiltro() {
        let filtro = txtSelectFiltro.value.toLowerCase();

        if (filtro === 'numero socio') {
            filtro = 'id';
        }

        const response = await fetch(`http://localhost/Proyecto/BACKEND/api/socios.php?parametro=${filtro}`)
        
        socios = await response.json();

        mostrarPagina();
        crearPaginacion();
    }

    async function obtenerCantidadSocios() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php?accion=cantidad');

        const data = await response.json()
        tituloCantiadSocios.textContent = `Mostrando 1 a 10 de ${data.cantidad} socios`;
    }

    async function darDeAltaSocio() {
        if (!validarCampos(txtNombreAlta, txtApellidoAlta, txtDniAlta, txtTelefonoAlta, txtBarrioAlta, txtCalleAlta, txtAlturaAlta)) return;

        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                nombre: txtNombreAlta.value,
                apellido: txtApellidoAlta.value,
                dni: txtDniAlta.value,
                telefono: txtTelefonoAlta.value,
                barrio: txtBarrioAlta.value,
                calle: txtCalleAlta.value,
                altura: txtAlturaAlta.value
            }),
        }) 

        const data = await response.json();

        if (!response.ok) {
            alert(data.message);
            return;
        }

        alert(data.message);
        return true;
    }

    async function editarSocio() {
        if (!validarCampos(txtNombreEditar, txtApellidoEditar, txtDniEditar, txtTelefonoEditar, txtBarrioEditar, txtCalleEditar, txtAlturaEditar)) return;

        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify ({
                id: txtIdSocioEditar.value,
                nombre: txtNombreEditar.value,
                apellido: txtApellidoEditar.value,
                dni: txtDniEditar.value,
                telefono: txtTelefonoEditar.value,
                barrio: txtBarrioEditar.value,
                calle: txtCalleEditar.value,
                altura: txtAlturaEditar.value
            }),
        })

        const data = await response.json();

        if (!response.ok) {
            alert(data.message);
            return;
        }

        alert(data.message);
        return true;
    }

    async function eliminarSocio() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: idEliminar,
            }),
        })

        const data = await response.json();

        if (!response.ok) {
            alert(data.message);
            return;
        }

        alert(data.message);
        return true;
    }

    function completarCamposConDatosFila(fila) {
        txtIdSocioEditar.value =  fila.cells[0].textContent;
        txtApellidoEditar.value = fila.cells[1].textContent;
        txtNombreEditar.value = fila.cells[2].textContent;
        txtDniEditar.value = fila.cells[3].textContent;
        txtTelefonoEditar.value = fila.cells[4].textContent;
        txtBarrioEditar.value = fila.cells[5].textContent;
        txtCalleEditar.value = fila.cells[6].textContent;
        txtAlturaEditar.value = fila.cells[7].textContent;
    }

    function validarCampos(nombre, apellido, dni, telefono, barrio, calle, altura) {

        if (nombre.value.trim() === '') {
            alert('El nombre del socio es obligatorio.');
            nombre.focus();
            return false;
        }

        if (apellido.value.trim() === '') {
            alert('El apellido del socio es obligatorio.');
            apellido.focus();
            return false;
        }

        const dniLimpio = dni.value.trim();

        if (dniLimpio === '') {
            alert('El DNI del socio es obligatorio.');
            dni.focus();
            return false;
        }

        if (!/^\d+$/.test(dniLimpio)) {
            alert('El DNI solo puede contener números.');
            dni.focus();
            return false;
        }

        if (dniLimpio.length < 7 || dniLimpio.length > 8) {
            alert('El DNI debe tener entre 7 y 8 dígitos.');
            dni.focus();
            return false;
        }

        const telefonoLimpio = telefono.value.trim();

        if (telefonoLimpio === '') {
            alert('El teléfono es obligatorio.');
            telefono.focus();
            return false;
        }

        if (!/^\d+$/.test(telefonoLimpio)) {
            alert('El teléfono solo puede contener números.');
            telefono.focus();
            return false;
        }

        if (barrio.value.trim() === '') {
            alert('El barrio es obligatorio.');
            barrio.focus();
            return false;
        }

        if (calle.value.trim() === '') {
            alert('La calle es obligatoria.');
            calle.focus();
            return false;
        }

        const alturaLimpia = altura.value.trim();

        if (alturaLimpia === '') {
            alert('La altura es obligatoria.');
            altura.focus();
            return false;
        }

        if (!/^\d+$/.test(alturaLimpia)) {
            alert('La altura solo puede contener números.');
            altura.focus();
            return false;
        }

        return true;
    }

    obtenerSocios();
    obtenerCantidadSocios();
}