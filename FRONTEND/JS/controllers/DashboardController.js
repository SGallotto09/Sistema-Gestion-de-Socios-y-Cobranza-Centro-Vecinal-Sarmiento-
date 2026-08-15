document.addEventListener('DOMContentLoaded', iniciarDashboard);

function iniciarDashboard() {
    lucide.createIcons();  

    // VARIABLES

    // ELEMETNOS DEL DOM
    const btnIrASocios = document.getElementById('btnNuevoSocioDashboard')
    const btnIrACobranza = document.getElementById('btnCobranzaDashboard');

    const h2TotalSocios = document.getElementById('h2TotalSocios');

    // COMPORTAMIENTOS
    btnIrASocios.addEventListener('click', () => {
        window.location.href = '../PAGES/socios.html';
    });
    btnIrACobranza.addEventListener('click', () => {
        window.location.href = '../PAGES/cobranza.html';
    });

    async function obtenerCantidadSocios() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php?accion=cantidad');

        const data = await response.json()
        h2TotalSocios.textContent = data.cantidad;
    }

    obtenerCantidadSocios();
}