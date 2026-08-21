import { SocioApi } from "../api/SociosApi.js";

document.addEventListener('DOMContentLoaded', iniciarDashboard);

function iniciarDashboard() {
    lucide.createIcons();  
    const sociosApi = new SocioApi();

    // VARIABLES

    // ELEMETNOS DEL DOM
    const btnIrASocios = document.getElementById('btnNuevoSocioDashboard')
    const btnIrACobranza = document.getElementById('btnCobranzaDashboard');

    const h2TotalSocios = document.getElementById('h2TotalSocios');
    const spanNombreUsuario = document.getElementById('spanNombreUsuario');

    // COMPORTAMIENTOS
    btnIrASocios.addEventListener('click', () => {
        window.location.href = '../PAGES/socios.php';
    });
    btnIrACobranza.addEventListener('click', () => {
        window.location.href = '../PAGES/cobranza.php';
    });

    async function cargarCantidadSocios() {
        const cantidadSocios = await sociosApi.obtenerCantidadSocios();
        h2TotalSocios.textContent = cantidadSocios.cantidad;
    }

    cargarCantidadSocios();
}