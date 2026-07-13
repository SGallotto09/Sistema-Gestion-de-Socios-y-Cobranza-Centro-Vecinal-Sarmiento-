document.addEventListener('DOMContentLoaded', iniciarDashboard);

function iniciarDashboard() {
    lucide.createIcons();

    // VARIABLES
    const btnIrASocios = document.getElementById('btnNuevoSocioDashboard')
    const btnIrACobranza = document.getElementById('btnCobranzaDashboard');

    // COMPORTAMIENTOS
    btnIrASocios.addEventListener('click', () => {
        window.location.href = '../PAGES/socios.html';
    });
    btnIrACobranza.addEventListener('click', () => {
        window.location.href = '../PAGES/cobranza.html';
    });
}