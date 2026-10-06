import { SocioApi } from "../api/SociosApi.js";
import {mostrarModalExito, mostrarModalError} from "./ModalExitoYErrorController.js";

document.addEventListener('DOMContentLoaded', iniciarSocios);

function iniciarSocios() {
    lucide.createIcons();

    // VARIABLES

    // CLASE SocioApo
    const socioApi = new SocioApi();

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
    let sociosFiltrados = [];
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
    let socioAEditar = {};
    const txtIdSocioEditar = document.getElementById('txtIdSocioEditar');
    const txtApellidoEditar = document.getElementById('txtApellidoSocioEditar');
    const txtNombreEditar = document.getElementById('txtNombreSocioEditar');
    const txtDniEditar = document.getElementById('txtDniSocioEditar');
    const txtTelefonoEditar = document.getElementById('txtTelefonoSocioEditar');
    const txtBarrioEditar = document.getElementById('txtBarrioSocioEditar');
    const txtCalleEditar = document.getElementById('txtCalleSocioEditar');
    const txtAlturaEditar = document.getElementById('txtAlturaSocioEditar');
    const txtPeriodoEditar = document.getElementById('txtPeriodoSocioEditar');

    // ID PARA ELIMINAR SOCIO
    let idEliminar = '';

    // COMPORTAMIENTOS

    txtBuscarSocio.addEventListener('input', (e) => {
        let texto = txtBuscarSocio.value.trim();

        filtrarSocios(texto);
    });

    btnNuevoSocio.addEventListener('click', () => {
        openModal(modalAltaSocio);
    })

    btnDarDeAltaSocio.addEventListener('click', async () => {
        const creado = await darDeAltaSocio();

        if (creado) {
            closeModal(modalAltaSocio);
            limpiarCamposAltaSocio();
            cargarSocios();
        } 
    });

    btnEditarSocio.addEventListener('click', async () => {
        const editado = await editarSocio();

        if (editado) {
            closeModal(modalEditarSocio);
            cargarSocios();
        }
    });

    btnEliminarSocio.addEventListener('click', async () => {
        const eliminado = await eliminarSocio();

        if (eliminado) {
            closeModal(modalEliminarSocio);
            cargarSocios();;
            obtenerCantidadSocios();
        }
    });

    txtSelectFiltro.addEventListener('change', () => {
        filtrarSociosPorFiltro(txtSelectFiltro.value);
    });

    tbodySocios.addEventListener('click', (e) => {
        const botonEditar = e.target.closest('.lapiz');
        const botonEliminar = e.target.closest('.tacho');

        if (botonEditar) {
            completarCamposConDatosFila(botonEditar.closest('tr'));
            socioAEditar = {
                id: txtIdSocioEditar.value,
                nombre: txtNombreEditar.value, 
                apellido: txtApellidoEditar.value,
                dni: txtDniEditar.value,
                telefono: txtTelefonoEditar.value,
                barrio: txtBarrioEditar.value,
                calle: txtCalleEditar.value,
                altura: txtAlturaEditar.value
            };
            openModal(modalEditarSocio);
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
        const totalPaginas = Math.ceil(sociosFiltrados.length / sociosPorPagina);

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

    async function cargarSocios() {
        socios = await socioApi.obtenerSocios();

        sociosFiltrados = socios;
        paginaActual = 1;

        mostrarPagina();
        crearPaginacion();
    }

    async function obtenerCantidadSocios() {
        let cantidadSocios = await socioApi.obtenerCantidadSocios();
        tituloCantiadSocios.textContent = `Mostrando 1 a 10 de ${cantidadSocios.cantidad} socios`
    }

    async function darDeAltaSocio() {
        if (!validarCampos(txtNombreAlta, txtApellidoAlta, txtDniAlta, txtTelefonoAlta, txtBarrioAlta, txtCalleAlta,
            txtAlturaAlta)) return;

        try {
            const nuevoSocio = await socioApi.darDeAltaSocio(
                txtNombreAlta.value,
                txtApellidoAlta.value,
                txtDniAlta.value,
                txtTelefonoAlta.value,
                txtBarrioAlta.value,
                txtCalleAlta.value,
                txtAlturaAlta.value
            );

            mostrarModalExito('Socio creado', nuevoSocio.message);
            return true;
        } catch (error) {
            mostrarModalError('Error al crear el socio', error.message);
            return false;
        }
    }

    async function editarSocio() {
        if (!validarCampos(txtNombreEditar, txtApellidoEditar, txtDniEditar, txtTelefonoEditar, txtBarrioEditar, txtCalleEditar, txtAlturaEditar)) return;

        if (
            socioAEditar.id         ===     txtIdSocioEditar.value  &&
            socioAEditar.nombre     ===     txtNombreEditar.value   &&    
            socioAEditar.apellido   ===     txtApellidoEditar.value &&
            socioAEditar.dni        ===     txtDniEditar.value      &&
            socioAEditar.telefono   ===     txtTelefonoEditar.value &&
            socioAEditar.barrio     ===     txtBarrioEditar.value   &&
            socioAEditar.calle      ===     txtCalleEditar.value    &&
            socioAEditar.altura     ===     txtAlturaEditar.value
        ) {
            alert('No se realizaron cambios');
            return;
        }

        try {
            const socioEditado = await socioApi.editarSocio(
                txtIdSocioEditar.value,
                txtNombreEditar.value,
                txtApellidoEditar.value,
                txtDniEditar.value,
                txtTelefonoEditar.value,
                txtBarrioEditar.value,
                txtCalleEditar.value,
                txtAlturaEditar.value
            );

            mostrarModalExito('Socio editado', socioEditado.message);
            return true;

        } catch (error) {
            mostrarModalError('Error al editar el socio', error.message);
            return false;
        }
    }

    async function eliminarSocio() {
        try {
            const data = await socioApi.eliminarSocio(idEliminar);
            mostrarModalExito('Socio eliminado', data.message);

            return true;
        } catch (error) {
            mostrarModalError('Error al eliminar el socio', error.message);
            return false;
        }
    }

    function mostrarPagina() {
        const inicio = (paginaActual - 1) * sociosPorPagina;
        const fin = inicio + sociosPorPagina;

        const sociosPagina = sociosFiltrados.slice(inicio, fin);

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
                    <td>${sociosPagina[i].titulo}</td>
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

        const totalPaginas = Math.ceil(
            sociosFiltrados.length / sociosPorPagina
        );

        function agregarBoton(numero) {
            const boton = document.createElement('button');

            boton.classList.add('btnPagina');
            boton.textContent = numero;

            if (numero === paginaActual) {
                boton.classList.add('paginaActiva');
            }

            boton.addEventListener('click', () => {
                paginaActual = numero;

                mostrarPagina();
                crearPaginacion();
            });

            contenedorPaginas.appendChild(boton);
        }

        function agregarPuntos() {
            const puntos = document.createElement('span');

            puntos.textContent = '...';
            puntos.classList.add('puntosPaginacion');

            contenedorPaginas.appendChild(puntos);
        }

        // Si hay pocas páginas, mostramos todas
        if (totalPaginas <= 7) {
            for (let i = 1; i <= totalPaginas; i++) {
                agregarBoton(i);
            }

            return;
        }

        // SI ESTAMOS CERCA DEL PRINCIPIO
        if (paginaActual <= 3) {

            agregarBoton(1);
            agregarBoton(2);
            agregarBoton(3);
            agregarBoton(4);

            agregarPuntos();

            agregarBoton(totalPaginas);
        }   

        // SI ESTAMOS EN EL MEDIO
        else if (paginaActual < totalPaginas - 2) {
            agregarBoton(1);

            agregarPuntos();

            agregarBoton(paginaActual - 1);
            agregarBoton(paginaActual);
            agregarBoton(paginaActual + 1);

            agregarPuntos();

            agregarBoton(totalPaginas);
        }

        // SI ESTAMOS CERCA DEL FINAL
        else {
            agregarBoton(1);

            agregarPuntos();

            agregarBoton(totalPaginas - 3);
            agregarBoton(totalPaginas - 2);
            agregarBoton(totalPaginas - 1);
            agregarBoton(totalPaginas);
        }
    }

    function filtrarSocios(texto) {
        texto = texto.toLowerCase().trim();

        if (texto === '') {
            sociosFiltrados = socios;
        }
        else {
            sociosFiltrados = socios.filter(socio => {
                const nombreCompleto = `${socio.apellido} ${socio.nombre}`.toLowerCase();
                const dni = socio.dni.toString();

                return nombreCompleto.includes(texto) || dni.includes(texto);
            });
        }

        paginaActual = 1;

        mostrarPagina();
        crearPaginacion();
    }

    function filtrarSociosPorFiltro(filtro) {
        paginaActual = 1;

        if (filtro === '' || filtro === 'Filtro') {
            sociosFiltrados = socios;

            mostrarPagina();
            crearPaginacion();
            return;
        }

        sociosFiltrados = [...socios];

        switch (filtro) {
            case 'Numero socio':
                sociosFiltrados.sort((a, b) => a.id - b.id);
                break;

            case 'DNI':
                sociosFiltrados.sort((a, b) => a.dni - b.dni);
                break;

            case 'Barrio':
                sociosFiltrados.sort((a, b) => {
                    const comparacionBarrio = a.barrio.toLowerCase().localeCompare(b.barrio.toLowerCase());

                    if (comparacionBarrio !== 0) {
                        return comparacionBarrio;
                    }

                    return a.calle.toLowerCase().localeCompare(b.calle.toLowerCase());
                });
                break;

            case 'Calle':
                sociosFiltrados.sort((a, b) => a.calle.toLowerCase().localeCompare(b.calle.toLowerCase()));
                break;

            default:
                sociosFiltrados = socios;
                break;
        }

        mostrarPagina();
        crearPaginacion();
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
        txtPeriodoEditar.value = fila.cells[8].textContent;
    }

    function validarCampos(nombre, apellido, dni, telefono, barrio, calle, altura) {

        const apellidoLimpio = apellido.value.trim(); 

        if (apellidoLimpio === '') { 
            alert('El apellido del socio es obligatorio.'); 
            apellido.focus(); 
            return false; 
        } 
        
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(apellidoLimpio)) { 
            alert('El apellido solo puede contener letras y espacios.'); 
            apellido.focus(); 
            return false; 
        }

        if (apellido.value.trim() === '') {
            alert('El apellido del socio es obligatorio.');
            apellido.focus();
            return false;
        }

        const nombreLimpio = nombre.value.trim(); 
        
        if (nombreLimpio === '') { 
            alert('El nombre del socio es obligatorio.'); 
            nombre.focus(); 
            return false; 
        } 
        
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(nombreLimpio)) { 
            alert('El nombre solo puede contener letras y espacios.'); 
            nombre.focus(); return false; 
        } 

        const dniLimpio = dni.value.trim();

        if (dniLimpio === '') {
            alert('El DNI del socio es obligatorio.');
            dni.focus();
            return false;
        }

        if (dniLimpio <= -1) {
            alert('El DNI no puede ser negativo.')
            dni.focus();
            return;
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

        if (telefonoLimpio.length < 10 || telefonoLimpio.length > 12) {
            alert('El numero de teléfono debe contener entre 10 y 12 digitos');
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

    function limpiarCamposAltaSocio() {
        txtApellidoAlta.value = '';
        txtNombreAlta.value = '';
        txtDniAlta.value = '';
        txtTelefonoAlta.value = '';
        txtBarrioAlta.value = '';
        txtCalleAlta.value = '';
        txtAlturaAlta.value = '';
    }

    cargarSocios();
    obtenerCantidadSocios();
}