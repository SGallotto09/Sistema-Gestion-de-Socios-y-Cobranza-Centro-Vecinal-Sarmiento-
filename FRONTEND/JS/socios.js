document.addEventListener('DOMContentLoaded', iniciarSocios);

function iniciarSocios() {
    lucide.createIcons();

    // VARIABLES
    const modalAltaSocio = document.getElementById('modalAltaSocio');
    const modalEliminarSocio = document.getElementById('modalEliminarSocio');

    const btnNuevoSocio = document.getElementById('btnNuevoSocio');

    const botonesTacho = document.querySelectorAll('.tacho');

    // COMPORTAMIENTOS

    btnNuevoSocio.addEventListener('click', () => {
        openModal(modalAltaSocio);
    })

    botonesTacho.forEach(boton => {
        boton.addEventListener('click', () => {
            openModal(modalEliminarSocio);
        })
    })

    // FUNCIONES
    function openModal(modal) {
        modal.classList.add('modal--show');
    }
}