import { SocioApi } from "../api/SociosApi.js";
import { PagosApi } from "../api/PagosApi.js";
import { CuotasApi } from "../api/CuotasApi.js";
import { VisitaApi } from "../api/VisitaApi.js";

document.addEventListener('DOMContentLoaded', iniciarDashboard);

function iniciarDashboard() {
    lucide.createIcons();  
    const sociosApi = new SocioApi();
    const pagosApi = new PagosApi();
    const cuotaApi = new CuotasApi();
    const visitaApi = new VisitaApi();

    // VARIABLES

    // ELEMETNOS DEL DOM
    const btnIrASocios = document.getElementById('btnNuevoSocioDashboard')
    const btnIrACobranza = document.getElementById('btnCobranzaDashboard');

    let cantidadSocios = null;
    let cantidadPagos = null;
    const spanNombreUsuario = document.getElementById('spanNombreUsuario');
    const h2BimestreActual = document.getElementById('h2BimestreActual');
    const h2TotalSocios = document.getElementById('h2TotalSocios');
    const h2PagaronBimestre = document.getElementById('h2PagaronBimestre');
    const pagaronBimestre = document.getElementById('pagaronBimestre');
    const h2PendientesPago = document.getElementById('h2PendientesPago');
    const pendientesDePago = document.getElementById('pendientesDePago');
    const h2SociosVisitados = document.getElementById('h2SociosVisitados');
    const sociosVisitados = document.getElementById('sociosVisitados');

    const barraCompleta = document.getElementById('barraCompleta');
    const txtPorcentajeBarra = document.getElementById('txtPorcentajeBarra');
    const parrafoBarra = document.getElementById('parrafoBarra');

    // COMPORTAMIENTOS
    btnIrASocios.addEventListener('click', () => {
        window.location.href = '../PAGES/socios.php';
    });
    btnIrACobranza.addEventListener('click', () => {
        window.location.href = '../PAGES/cobranza.php';
    });

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

    function asignarNombreAdministrador() {
        const usuarioLogeado = JSON.parse(sessionStorage.getItem("usuarioLogeado"));
        spanNombreUsuario.textContent = usuarioLogeado.usuarioEncontrado.nombre + ' '  
                                        + usuarioLogeado.usuarioEncontrado.apellido;
    }

    async function obtenerCantidadSocios() {
        cantidadSocios = await sociosApi.obtenerCantidadSociosCobranza();
        h2TotalSocios.textContent = cantidadSocios.cantidad;
    }

    async function obtenerCantidadTotalDePagos() {
        cantidadPagos = await pagosApi.getCantidadDePagos();
        h2PagaronBimestre.textContent = cantidadPagos.cantidadPagos;

        if (cantidadSocios.cantidad === 0) {
            pagaronBimestre.textContent = 'No hay socios registrados.';
            return;
        }

        pagaronBimestre.textContent = `${calcularPorcentaje(cantidadPagos.cantidadPagos, cantidadSocios.cantidad)}% del total`;
    }

    async function obtenerTotalSociosVisitados() {
        const totalVisitas = await visitaApi.getTotalVisitas();
        h2SociosVisitados.textContent = totalVisitas.totalVisitas;

        if (cantidadSocios.cantidad === 0) {
            sociosVisitados.textContent = 'No hay socios registrados.';
            return;
        }

        sociosVisitados.textContent = `${calcularPorcentaje(totalVisitas.totalVisitas, cantidadSocios.cantidad)}% del total`;
    }

    async function obtenerCantidadCuotasSinPagar() {
        const cantidadCuotasSinPagar = await cuotaApi.getCantidadCuotasSinPagar();
        h2PendientesPago.textContent = cantidadCuotasSinPagar.cuotasSinPagar;

        if (cantidadSocios.cantidad === 0) {
            pendientesDePago.textContent = 'No hay socios registrados.';
            return;
        }

        pendientesDePago.textContent = `${calcularPorcentaje(cantidadCuotasSinPagar.cuotasSinPagar, cantidadSocios.cantidad)}% del total`;
    }

    function calcularPorcentaje(cantidad, total) {
        if (total === 0) {
            return 0;
        }
        return Math.round((cantidad/ total) * 100);
    }

    function completarParrafoBarra() {
        const porcentaje = calcularPorcentaje(cantidadPagos.cantidadPagos, cantidadSocios.cantidad);

        barraCompleta.style.width = `${porcentaje}%`;

        txtPorcentajeBarra.innerHTML = `<b>${porcentaje}%</b>`;    

        parrafoBarra.textContent = `${cantidadPagos.cantidadPagos} de ${cantidadSocios.cantidad} socios pagaron el bimestre actual.`;
    }

    async function cargarDatos() {
        asignarNombreAdministrador();
        obtenerBimestreActual();

        await obtenerCantidadSocios();

        const resultados = await Promise.allSettled([
            obtenerCantidadTotalDePagos(),
            obtenerTotalSociosVisitados(),
            obtenerCantidadCuotasSinPagar()
        ]);

        resultados.forEach((resultado, index) => {
            if (resultado.status === 'rejected') {
                console.error(`Error en la función ${index}:`, resultado.reason);
            }
        });

        if (cantidadSocios && cantidadPagos) {
            completarParrafoBarra();
        }
    }

    cargarDatos();
}