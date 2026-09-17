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

    const spanNombreUsuario = document.getElementById('spanNombreUsuario');
    const h2BimestreActual = document.getElementById('h2BimestreActual');
    const h2TotalSocios = document.getElementById('h2TotalSocios');
    const h2PagaronBimestre = document.getElementById('h2PagaronBimestre');
    const h2PendientesPago = document.getElementById('h2PendientesPago');
    const h2SociosVisitados = document.getElementById('h2SociosVisitados');

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
        const cantidadSocios = await sociosApi.obtenerCantidadSociosCobranza();
        h2TotalSocios.textContent = cantidadSocios.cantidad;
    }

    async function obtenerCantidadTotalDePagos() {
        const cantidadPagos = await pagosApi.getCantidadDePagos();
        h2PagaronBimestre.textContent = cantidadPagos.cantidadPagos;
    }

    async function obtenerTotalSociosVisitados() {
        const totalVisitas = await visitaApi.getTotalVisitas();
        h2SociosVisitados.textContent = totalVisitas.totalVisitas;
    }

    async function obtenerCantidadCuotasSinPagar() {
        const cantidadCuotasSinPagar = await cuotaApi.getCantidadCuotasSinPagar();
        h2PendientesPago.textContent = cantidadCuotasSinPagar.cuotasSinPagar;
    }

    asignarNombreAdministrador();
    obtenerBimestreActual();
    obtenerCantidadSocios();
    obtenerCantidadTotalDePagos();
    obtenerTotalSociosVisitados();
    obtenerCantidadCuotasSinPagar();
}