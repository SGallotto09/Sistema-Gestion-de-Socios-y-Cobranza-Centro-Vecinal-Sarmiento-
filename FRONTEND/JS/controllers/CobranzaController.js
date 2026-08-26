import { SocioApi } from "../api/SociosApi.js";
document.addEventListener('DOMContentLoaded', iniciarCobranza);

function iniciarCobranza() {
    lucide.createIcons();

    // VARIABLES

    // CLASE SOCIO
    const socioApi = new SocioApi();

    // MODALES
    const modalEditarSocio = document.getElementById('modalEditarSocio');
    const modalLinkAcceso = document.getElementById('modalLinkAcceso');
    const modalPlantillaImpresion = document.getElementById('modalPlantillaImpresion');

    const btnGenrarLinkAcceso = document.getElementById('btnGenerarLinkAcceso');
    const btnGenerarPLantillaImpresion = document.getElementById('btnGenerarPlantillaImpresion');
    const btnGuardarCambios = document.getElementById('btnGuardarCambios');

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

    // COMPORTAMIENTOS

    btnGenrarLinkAcceso.addEventListener('click', () => {
        openModal(modalLinkAcceso);
    });

    btnGenerarPLantillaImpresion.addEventListener('click', () => {
        openModal(modalPlantillaImpresion);
    });

    btnGuardarCambios.addEventListener('click', () => {
        // IMPLEMENTAR LA FUNCION DE EDITAR EL ESTADO DE PAGO Y VISITA
    })

    txtBuscarSocio.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            if (txtBuscarSocio.value.trim() === '') {
                cargarSociosSocios();
            } else {
                cargarSociosPorNombre();
            }
        }

        txtSelectFiltro.value = 'Filtros';
    });

    btnBuscarSocio.addEventListener('click', () => {
        if (txtBuscarSocio.value.trim() === '') {
            cargarSocios();
        } else {
            cargarSociosPorNombre();
        }

        txtSelectFiltro.value = 'Filtros';
    });

    tbodySocios.addEventListener('click', (e) => {
        const botonEditar = e.target.closest('.lapiz');

        if (botonEditar) {
            openModal(modalEditarSocio);
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

    async function cargarSocios() {
        socios = await socioApi.obtenerSociosCobranza();

        mostrarPagina();
        crearPaginacion();
    }

    function mostrarPagina() {
        const inicio = (paginaActual - 1) * sociosPorPagina;
        const fin = inicio + sociosPorPagina;

        const sociosPagina = socios.slice(inicio, fin);

        let filas = '';
        let divPagado = '';

        for (let i = 0; i < sociosPagina.length; i++) {
            let estadoPago = 'no-pagado';
            let estadoVisita = 'no-visitado';

            if (sociosPagina[i].estadoPago == 1) {
                estadoPago = 'pagado';
            }

            if (sociosPagina[i].estadoVisita == 1) {
                estadoVisita = 'visitado';
            }

            filas += 
                `<tr data-id="${sociosPagina[i].id}">
                    <td>${sociosPagina[i].id}</td>
                    <td>${sociosPagina[i].apellido}</td>
                    <td>${sociosPagina[i].nombre}</td>
                    <td>${sociosPagina[i].dni}</td>
                    <td>${sociosPagina[i].telefono}</td>
                    <td>
                        <div class="estado">
                            <span class="punto ${estadoPago}"></span>
                            <span>Pagado</span>
                        </div>
                    </td>    
                    <td>
                        <div class="estado">
                            <span class="punto no-visitado"></span>
                            <span>Visitado</span>
                        </div>
                    </td>
                    <td>
                        <i data-lucide="pencil" class="iconoTabla lapiz"></i>
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

    async function cargarSociosPorNombre() {
        socios = await socioApi.obtenerSociosPorNombre(txtBuscarSocio);

        mostrarPagina();
        crearPaginacion();
    }

    async function cargarSociosPorFiltro() {
        let filtro = txtSelectFiltro.value.toLowerCase();

        if (filtro === 'numero socio') {
            filtro = 'id';
        }

        socios = await socioApi.obtenerSociosPorFiltro(txtSelectFiltro)

        mostrarPagina();
        crearPaginacion();
    }

    async function obtenerCantidadSocios() {
        let cantidadSocios = await socioApi.obtenerCantidadSociosCobranza();
        tituloCantiadSocios.textContent = `Mostrando 1 a 10 de ${cantidadSocios.cantidad} socios`;
    }

    cargarSocios();
    obtenerCantidadSocios();
}