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

    const h2BimestreActual = document.getElementById('h2BimestreActual');
    const listaSocios = document.getElementById('listaSocios');
    const btnAccionSocio = document.getElementById('btnAccionSocio');

    listaSocios.addEventListener('click', async (e) => {
        const fila = e.target.closest('button');

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

    function compaginarSocios() {
        let filas = '';

        for (let i = 0; i < socios.length; i++) {
            filas += 
                `<button data-id="${socios[i].id}" data-id-cuota="${socios[i].idCuota}" class="card-socio" type="button" id="btnAccionSocio">
                    <div class="numero-socio">
                        <span>Nº</span>
                        <strong>${socios[i].id}</strong>
                    </div>

                    <div class="datos-socio">
                        <h2>${socios[i].apellido} ${socios[i].nombre}</h2>

                        <div class="dni">
                            <i data-lucide="id-card"></i>
                            <span>DNI: ${formatearDNI(socios[i].dni)}</span>
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

        compaginarSocios();
    }

    async function obtenerCantidadVisitasPorCuota(_idCuota) {
        let visitas = await visitaApi.getCantidadVisitas(_idCuota);
        return visitas;
    }

    obtenerBimestreActual();
    cargarSocios();
}