import { SocioApi } from "../api/SociosApi.js";
import { PagosApi } from "../api/PagosApi.js";
import { LinkAccesoApi } from "../api/LinkAccesoApi.js";
import { UserApi } from "../api/UsuariosApi.js";
import { VisitaApi } from "../api/VisitaApi.js";

document.addEventListener('DOMContentLoaded', iniciarCobranza);

function iniciarCobranza() {
    lucide.createIcons();

    // VARIABLES

    // INSTANCIAS DE CLASES
    const socioApi = new SocioApi();
    const pagosApi = new PagosApi();
    const linkAccesoApi = new LinkAccesoApi();
    const userApi = new UserApi();
    const visitaApi = new VisitaApi()

    //                      PANTALLA MAIN

    // FILTRO
    let txtBuscarSocio = document.getElementById('txtBuscarSocio');
    const btnBuscarSocio = document.getElementById('btnBuscarSocio');
    const txtSelectFiltro = document.getElementById('selectFiltro');

    // TABLA
    const tbodySocios = document.getElementById('tbodySocios');
    let socios = [];
    let paginaActual = 1;
    const sociosPorPagina = 10;
    let cobradores = null;
    const contenedorPaginas = document.getElementById('contenedorPaginas');
    const btnAnterior = document.getElementById("btnAnterior");
    const btnSiguiente = document.getElementById("btnSiguiente");
    const tituloCantiadSocios = document.getElementById('tituloCantiadSocios');

    //                        MODALES

    // MODAL ACCIONES SOCIO
    const modalAccionesSocio = document.getElementById('modalAccionesSocio');
    let txtDniTelefonoSocio = document.getElementsByClassName('dataSocioDniTelefono');
    const btnAbrirModalEstadoPago = document.getElementById('btnModalEstadoPago');
    const btnAbrirModalVisita = document.getElementById('btnModalVisita');

    // MODAL REGISTRAR VISITA
    const modalRegistrarVisita = document.getElementById('modalRegistrarVisita');
    let txtContadorVisitas = document.getElementById('txtContadorVisitas');
    const btnRegistrarVisita = document.getElementById('btnRegistrarVisita');

    // MODAL EDITAR ESTADO PAGO SOCIO
    const modalEditarEstadoPagoSocio = document.getElementById('modalEditarEstadoPagoSocio');
    let txtNumeroCuotaSocio = document.getElementById('txtNumeroCuota');
    let txtInfoSocio = document.getElementsByClassName('dataSocio');
    let idCuota = null;
    let estadoOriginalPago = null;
    const btnGuardarCambios = document.getElementById('btnGuardarCambios');

    // (1) MODAL GENERAR LINK SOCIO
    const btnAbrirModalLinkAcceso = document.getElementById('btnAbrirModalLinkAcceso');
    const modalLinkAcceso = document.getElementById('modalLinkAcceso');
    const selectCobradores = document.getElementById('selectCobradores');
    let datosLinkGenerado = null;
    const btnGenerarLinkAcceso = document.getElementById('btnGenerarLink');

    // (2) MODAL CARGA LINK
    const modalCargaLink = document.getElementById('modalCargaLink');  
    
    // (3) MODAL LINK GENERADO
    const modalLinkGenerado = document.getElementById('modalLinkGenerado');
    const txtCobradorAsignado = document.getElementById('txtCobradorAsignado');
    const txtDNICobrador = document.getElementById('txtDNICobrador');
    const txtToken = document.getElementById('txtToken');
    const txtFechaVencimiento = document.getElementById('txtFechaVencimiento');
    const txtDuracion = document.getElementById('txtDuracion');

    // MODAL PANTALLA DE IMPRESION
    const btnAbrirModalPLantillaImpresion = document.getElementById('btnGenerarPlantillaImpresion');
    const modalPlantillaImpresion = document.getElementById('modalPlantillaImpresion');
    const btnImprimirPlantilla = document.getElementById('btnImprimirPlantilla');    

    // COMPORTAMIENTOS

    btnAbrirModalLinkAcceso.addEventListener('click', async () => {
        openModal(modalLinkAcceso);

        if (cobradores === null) {
            cargarCobradores();
        }
    });

    btnAbrirModalPLantillaImpresion.addEventListener('click', () => {
        openModal(modalPlantillaImpresion);
    });

    tbodySocios.addEventListener('click', async (e) => {
        const botonEditar = e.target.closest('.lapiz');
        const fila = e.target.closest('tr');

        if (!fila) return;

        const idSocio = parseInt(fila.dataset.id);
        idCuota = parseInt(fila.dataset.idCuota);
        txtNumeroCuotaSocio.textContent = `Numero cuota: ${idCuota}`;

        for (let i = 0; i < socios.length; i++) {
            if (idSocio === socios[i].id) {
                for (let j = 0; j < txtInfoSocio.length; j++) {
                    txtInfoSocio[j].textContent = `${socios[i].id} - ${socios[i].apellido} ${socios[i].nombre}`;
                }

                for (let x = 0; x < txtDniTelefonoSocio.length; x++) {
                    txtDniTelefonoSocio[x].textContent = `DNI: ${formatearDNI(socios[i].dni)}  |  Telefono: ${socios[i].telefono}`;
                }

                estadoOriginalPago = parseInt(socios[i].estadoCuota);
                if (estadoOriginalPago === 0) {
                    estadoOriginalPago = 'noPagado'
                }
                else {
                    estadoOriginalPago = 'pagado'
                }
                break;
            }
        }

        txtContadorVisitas.textContent = await obtenerCantidadVisitasPorCuota(idCuota);

        if (botonEditar) {
            openModal(modalAccionesSocio);
        }
    });

    btnAbrirModalEstadoPago.addEventListener('click', () => {
        closeModal(modalAccionesSocio);
        const radioPago = document.querySelector(`input[name="estadoPago"][value="${estadoOriginalPago}"]`);

        if (radioPago) {
            radioPago.checked = true;
        }
        openModal(modalEditarEstadoPagoSocio);
    });

    btnGuardarCambios.addEventListener('click', async () => {
        const radioPagado = document.querySelector('input[name="estadoPago"]:checked');

        if (!radioPagado) {
            alert('Se requiere marcar una opcion de estado');
            return;
        }

        if (radioPagado.value !== estadoOriginalPago) {
            let pago = await pagosApi.registerPago(idCuota);
            if (pago !== null) {
                alert(pago.message);
            }

            cargarSocios();
        }

        closeModal(modalEditarEstadoPagoSocio);
    });

    btnAbrirModalVisita.addEventListener('click', () => {
        closeModal(modalAccionesSocio);
        openModal(modalRegistrarVisita);
    });

    btnRegistrarVisita.addEventListener('click', async () => {
        const visitaCreada = await visitaApi.createVisita(idCuota);

        alert(visitaCreada.message);
        closeModal(modalRegistrarVisita);

        mostrarPagina();
        crearPaginacion();
    });

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

    btnGenerarLinkAcceso.addEventListener('click', async () => {
        const idCobrador = Number(document.getElementById('selectCobradores').value);
        const duracionToken = Number(document.getElementById('txtTempo').value);

        if (!idCobrador || idCobrador <= 0) {
            alert('Se debe asignar a un cobrador.');
            return;
        }

        if (!duracionToken || duracionToken <= 0) {
            alert('El link de acceso requiere de un tiempo de duracion.');
            return;
        }

        openModal(modalCargaLink);

        const promesaLink = generarLinkAcceso(idCobrador, duracionToken);
        const promesaCarga = iniciarCarga();

        const [linkAcceso] = await Promise.all([
            promesaLink,
            promesaCarga
        ]);

        datosLinkGenerado = linkAcceso;

        openModal(modalLinkGenerado);

        completarDatosLinkGenerado();
    });

    // FUNCIONES

    function openModal(modal) {
        modal.classList.add('modal--show');
    }

    function closeModal(modal) {
        modal.classList.remove('modal--show');
    }

    async function cargarSocios() {
        socios = await socioApi.obtenerSociosCobranza();

        mostrarPagina();
        crearPaginacion();
    }

    async function cargarCobradores() {
        cobradores = await userApi.getUsuarios('Cobrador');

        cobradores.forEach(cobrador => {
            const option = document.createElement('option');

            option.value = cobrador.id;
            option.textContent = cobrador.nombre + ' ' + cobrador.apellido;

            selectCobradores.appendChild(option);
        });
    }

    async function mostrarPagina() {
        const inicio = (paginaActual - 1) * sociosPorPagina;
        const fin = inicio + sociosPorPagina;

        const sociosPagina = socios.slice(inicio, fin);

        let filas = '';

        for (let i = 0; i < sociosPagina.length; i++) {
            let estadoCuota = 'no-pagado';
            let estadoVisita = 'no-visitado';
            const visitasCuota = await obtenerCantidadVisitasPorCuota(sociosPagina[i].idCuota);

            if (sociosPagina[i].estadoCuota == 1) {
                estadoCuota = 'pagado';
            }

            if (visitasCuota >= 1) {
                estadoVisita = 'visitado';
            }

            filas += 
                `<tr data-id="${sociosPagina[i].id}" data-id-cuota="${sociosPagina[i].idCuota}">
                    <td>${sociosPagina[i].id}</td>
                    <td>${sociosPagina[i].apellido}</td>
                    <td>${sociosPagina[i].nombre}</td>
                    <td>${sociosPagina[i].dni}</td>
                    <td>${sociosPagina[i].telefono}</td>
                    <td>
                        <div class="estado">
                            <span class="punto ${estadoCuota}"></span>
                            <span>Pagado</span>
                        </div>
                    </td>    
                    <td>
                        <div class="estado">
                            <span class="punto ${estadoVisita}"></span>
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

    async function generarLinkAcceso(_idCobrador, _duracionToken) {
        const linkAcceso = await linkAccesoApi.registerLinkAcceso(_idCobrador, _duracionToken);

        if (linkAcceso === null) {
            alert(linkAcceso.message);
            return;
        }

        closeModal(modalLinkAcceso);
        return linkAcceso;
    }

    function iniciarCarga() {
        const barra = document.querySelector('.progreso_carga');
        const porcentajeTexto = document.getElementById('porcentajeCarga');

        barra.style.transition = 'none';
        barra.style.width = '0%';
        porcentajeTexto.textContent = '0%';

        barra.offsetWidth;

        barra.style.transition = 'width 0.05s linear';

        return new Promise(resolve => {
            let progreso = 0;
            const intervalo = setInterval(() => {
                progreso++;

                barra.style.width = `${progreso}%`;
                porcentajeTexto.textContent = `${progreso}%`;

                if (progreso >= 100) {
                    clearInterval(intervalo);
                    closeModal(modalCargaLink);

                    resolve();
                }
            }, 20);
        });
    }

    async function completarDatosLinkGenerado() {
        const cobrador = await userApi.getUsuarioById(datosLinkGenerado.linkAcceso.destinado_a, 'Cobrador');
        txtCobradorAsignado.textContent = `${cobrador.nombre} ${cobrador.apellido}`;
        txtDNICobrador.textContent = `DNI: ${formatearDNI(cobrador.dni)}`;
        txtToken.value = `${datosLinkGenerado.linkAcceso.token}`;
        txtFechaVencimiento.textContent = `${datosLinkGenerado.linkAcceso.fecha_vencimiento}`;
        txtDuracion.textContent = `${datosLinkGenerado.linkAcceso.duracionToken} horas`;
    }

    function formatearDNI(dni) {
        return new Intl.NumberFormat('es-AR').format(dni);
    }

    async function obtenerCantidadVisitasPorCuota(_idCuota) {
        let visitas = await visitaApi.getCantidadVisitas(_idCuota);
        return visitas;
    }

    cargarSocios();
    obtenerCantidadSocios();
}