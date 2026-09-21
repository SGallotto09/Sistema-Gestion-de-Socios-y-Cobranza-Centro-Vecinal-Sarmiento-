import { SocioApi } from "../api/SociosApi.js";
import { VisitaApi } from "../api/VisitaApi.js";
import { PagosApi } from "../api/PagosApi.js";

document.addEventListener('DOMContentLoaded', iniciarCobrador);

function iniciarCobrador() {
    lucide.createIcons();

    const socioApi = new SocioApi();
    const visitaApi = new VisitaApi();
    const pagosApi = new PagosApi();
    let socios = [];

    //                        PANTALLA MAIN

    const h2BimestreActual = document.getElementById('h2BimestreActual');
    const buscador = document.getElementById('txtBuscarSocio');
    const listaSocios = document.getElementById('listaSocios');
    const txtCantidadSocios = document.getElementById('txtCantidadSocios');
    const btnAccionSocio = document.getElementById('btnAccionSocio');

    //                        MODALES

    let txtInfoSocio = document.getElementsByClassName('dataSocio');
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
    let idCuota = null;
    let estadoOriginalPago = null;
    const btnGuardarCambios = document.getElementById('btnGuardarCambios');

    const botonesCancelar = document.querySelectorAll('.modal_boton_cancelar');
    const botonesCerrarModal = document.querySelectorAll('.modal_close');

    buscador.addEventListener('input', () => {
        const texto = buscador.value.trim();

        filtrarSocios(texto);
    });

    listaSocios.addEventListener('click', async (e) => {
        const fila = e.target.closest('.card-socio');

        const idSocio = parseInt(fila.dataset.id);
        idCuota = parseInt(fila.dataset.idCuota);
        txtNumeroCuotaSocio.textContent = `Numero cuota: ${idCuota}`;

        for (let i = 0; i < socios.length; i++) {
            if (idSocio === socios[i].id) {
                for (let j = 0; j < txtInfoSocio.length; j++) {
                    txtInfoSocio[j].textContent = `${socios[i].id} - ${socios[i].apellido} ${socios[i].nombre}`;
                }

                for (let x = 0; x < txtDniTelefonoSocio.length; x++) {
                    txtDniTelefonoSocio[x].innerHTML = `
                        <span>DNI: ${formatearDNI(socios[i].dni)}</span>
                        <span>| Teléfono: ${socios[i].telefono}</span>
                    `;
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

        try {
            txtContadorVisitas.textContent = await obtenerCantidadVisitasPorCuota(idCuota);
        } catch (error) {
            console.error('Error al obtener cantidad de visitas:', error);
            txtContadorVisitas.textContent = 'Error';
        }

        openModal(modalAccionesSocio);
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
    });

    botonesCancelar.forEach(boton => {
        boton.addEventListener('click', () => {
            closeModal(boton.closest('.modal'));
        })
    });

    botonesCerrarModal.forEach(boton => {
        boton.addEventListener('click', () => {
            closeModal(boton.closest('.modal'));
        })
    });

    function openModal(modal) {
        modal.classList.add('modal--show');
    }

    function closeModal(modal) {
        modal.classList.remove('modal--show');
    }

    function obtenerBimestreActual() {
        const fecha = new Date();

        const meses = [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ];

        const mesActual = fecha.getMonth();

        const mesProximo = (mesActual + 1) % 12;

        h2BimestreActual.textContent = `${meses[mesActual]} - ${meses[mesProximo]}`;
    }

    function compaginarSocios(sociosAMostrar) {
        let filas = '';

        for (let i = 0; i < sociosAMostrar.length; i++) {
            filas += 
                `<button data-id="${sociosAMostrar[i].id}" data-id-cuota="${sociosAMostrar[i].idCuota}" class="card-socio" type="button" id="btnAccionSocio">
                    <div class="numero-socio">
                        <span>Nº</span>
                        <strong>${sociosAMostrar[i].id}</strong>
                    </div>

                    <div class="datos-socio">
                        <h2>${sociosAMostrar[i].apellido} ${sociosAMostrar[i].nombre}</h2>

                        <div class="dni">
                            <i data-lucide="id-card"></i>
                            <span>DNI: ${formatearDNI(sociosAMostrar[i].dni)}</span>
                        </div>
                    </div>

                    <div class="flecha">
                        <i data-lucide="chevron-right"></i>
                    </div>
                </button>`;
        }
        
        listaSocios.innerHTML = filas;
        lucide.createIcons();
    }

    function formatearDNI(dni) {
        return new Intl.NumberFormat('es-AR').format(dni);
    }

    async function cargarSocios() {
        socios = await socioApi.obtenerSociosCobranza();

        compaginarSocios(socios);
    }

    async function obtenerCantidadSocios() {
        let cantidadSocios = await socioApi.obtenerCantidadSociosCobranza();
        txtCantidadSocios.textContent = cantidadSocios.cantidad + ' socios:';
    }

    async function obtenerCantidadVisitasPorCuota(_idCuota) {
        let visitas = await visitaApi.getCantidadVisitas(_idCuota);
        return visitas;
    }

    function filtrarSocios(texto) {
        const resultado = socios.filter(socio => {

            const nombreCompleto = `${socio.nombre} ${socio.apellido}`.toLowerCase();
            const dni = socio.dni.toString();

            return nombreCompleto.includes(texto.toLowerCase()) || dni.includes(texto);
        });

        compaginarSocios(resultado);
    }

    obtenerBimestreActual();
    cargarSocios();
    obtenerCantidadSocios();
}