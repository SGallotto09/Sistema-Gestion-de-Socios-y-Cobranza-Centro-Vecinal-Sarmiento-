import { SocioApi } from "../api/SociosApi.js";
import { PagosApi } from "../api/PagosApi.js";
import { LinkAccesoApi } from "../api/LinkAccesoApi.js";
import { UserApi } from "../api/UsuariosApi.js";
document.addEventListener('DOMContentLoaded', iniciarCobranza);

function iniciarCobranza() {
    lucide.createIcons();

    // VARIABLES

    // INSTANCIAS DE CLASES
    const socioApi = new SocioApi();
    const pagosApi = new PagosApi();
    const linkAccesoApi = new LinkAccesoApi();
    const userApi = new UserApi();

    // MODALES
    const modalEditarSocio = document.getElementById('modalEditarSocio');
    const modalLinkAcceso = document.getElementById('modalLinkAcceso');
    const modalCargaLink = document.getElementById('modalCargaLink');
    const modalLinkGenerado = document.getElementById('modalLinkGenerado');
    const modalPlantillaImpresion = document.getElementById('modalPlantillaImpresion');

    // BOTONES
    const btnAbrirModalLinkAcceso = document.getElementById('btnAbrirModalLinkAcceso');
    const btnGenerarPLantillaImpresion = document.getElementById('btnGenerarPlantillaImpresion');
    const btnGenerarLinkAcceso = document.getElementById('btnGenerarLink');
    const btnGuardarCambios = document.getElementById('btnGuardarCambios');

    let txtInfoSocio = document.getElementById('txtInfoSocio');
    let idCuota = null;
    let estadoOriginalPago = null;
    let datosLinkGenerado = null;
    const txtCobradorAsignado = document.getElementById('txtCobradorAsignado');
    const txtDNICobrador = document.getElementById('txtDNICobrador');
    const txtToken = document.getElementById('txtToken');
    const txtFechaVencimiento = document.getElementById('txtFechaVencimiento');
    const txtDuracion = document.getElementById('txtDuracion');

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

    // FILTRO
    let txtBuscarSocio = document.getElementById('txtBuscarSocio');
    const btnBuscarSocio = document.getElementById('btnBuscarSocio');
    const txtSelectFiltro = document.getElementById('selectFiltro');

    // COMPORTAMIENTOS

    btnAbrirModalLinkAcceso.addEventListener('click', async () => {
        openModal(modalLinkAcceso);

        if (cobradores === null) {
            cargarCobradores();
        }
    });

    btnGenerarPLantillaImpresion.addEventListener('click', () => {
        openModal(modalPlantillaImpresion);
    });

    tbodySocios.addEventListener('click', (e) => {
        const botonEditar = e.target.closest('.lapiz');
        const fila = e.target.closest('tr');

        if (!fila) return;

        const idSocio = parseInt(fila.dataset.id);
        idCuota = parseInt(fila.dataset.idCuota);

        for (let i = 0; i < socios.length; i++) {
            if (idSocio === socios[i].id) {
                txtInfoSocio.textContent = `${socios[i].id} - ${socios[i].apellido} ${socios[i].nombre}`;
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

        if (botonEditar) {
            const radioPago = document.querySelector(`input[name="estadoPago"][value="${estadoOriginalPago}"]`);

            if (radioPago) {
                radioPago.checked = true;
            }
            openModal(modalEditarSocio);
        }
    });

    btnGuardarCambios.addEventListener('click', async () => {
        const radioPagado = document.querySelector('input[name="estadoPago"]:checked');
        const radioVisitado = document.querySelector('input[name="visita"]:checked');

        if (!radioPagado && !radioVisitado) {
            alert('No hay ninguna opcion marcada');
            return;
        }
        if (!radioPagado || !radioVisitado) {
            alert('te flata marcar una opcion');
            return;
        }

        if (radioPagado.value !== estadoOriginalPago) {
            let pago = await pagosApi.registerPago(idCuota);
            if (pago !== null) {
                alert(pago.message);
            }

            cargarSocios();
        }

        closeModal(modalEditarSocio);
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
        openModal(modalCargaLink);

        const promesaLink = generarLinkAcceso();
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

        const selectCobradores = document.getElementById('selectCobradores');

        cobradores.forEach(cobrador => {
            const option = document.createElement('option');

            option.value = cobrador.id;
            option.textContent = cobrador.nombre + ' ' + cobrador.apellido;

            selectCobradores.appendChild(option);
        });
    }

    function mostrarPagina() {
        const inicio = (paginaActual - 1) * sociosPorPagina;
        const fin = inicio + sociosPorPagina;

        const sociosPagina = socios.slice(inicio, fin);

        let filas = '';

        for (let i = 0; i < sociosPagina.length; i++) {
            let estadoCuota = 'no-pagado';
            let estadoVisita = 'no-visitado';

            if (sociosPagina[i].estadoCuota == 1) {
                estadoCuota = 'pagado';
            }

            if (sociosPagina[i].estadoVisita == 1) {
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

    async function generarLinkAcceso() {
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

        const linkAcceso = await linkAccesoApi.registerLinkAcceso(idCobrador, duracionToken);

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
        txtDNICobrador.textContent = `${cobrador.dni}`;
        txtToken.value = `${datosLinkGenerado.linkAcceso.token}`;
        txtFechaVencimiento.textContent = `${datosLinkGenerado.linkAcceso.fecha_vencimiento}`;
        txtDuracion.textContent = `${datosLinkGenerado.linkAcceso.duracionToken} horas`;
    }

    cargarSocios();
    obtenerCantidadSocios();
}