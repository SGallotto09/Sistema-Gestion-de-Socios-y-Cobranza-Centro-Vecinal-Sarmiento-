document.addEventListener('DOMContentLoaded', iniciarCobranza);

function iniciarCobranza() {
    lucide.createIcons();

    // VARIABLES
    const modalEditarSocio = document.getElementById('modalEditarSocio');
    const modalLinkAcceso = document.getElementById('modalLinkAcceso');
    const modalPlantillaImpresion = document.getElementById('modalPlantillaImpresion');

    const botonesEditarSocio = document.querySelectorAll('.btnEditarSocio');
    const btnGenrarLinkAcceso = document.getElementById('btnGenerarLinkAcceso');
    const btnGenerarPLantillaImpresion = document.getElementById('btnGenerarPlantillaImpresion');

    // COMPORTAMIENTOS

    botonesEditarSocio.forEach(boton => {
        boton.addEventListener('click', () => {
            openModal(modalEditarSocio);
        })
    })
    btnGenrarLinkAcceso.addEventListener('click', () => {
        openModal(modalLinkAcceso);
    })
    btnGenerarPLantillaImpresion.addEventListener('click', () => {
        openModal(modalPlantillaImpresion);
    });

    // FUNCIONES
    function openModal(modal) {
        modal.classList.add('modal--show');
    }
}