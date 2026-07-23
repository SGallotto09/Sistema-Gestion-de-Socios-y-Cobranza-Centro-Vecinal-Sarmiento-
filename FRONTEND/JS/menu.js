document.addEventListener('DOMContentLoaded', iniciarMenu);

function iniciarMenu() {
    //                  VARIABLES

    // MODAL
    const modalCerrarSesion = document.getElementById('modalCerrarSesion');
    const botonesCancelar = document.querySelectorAll('.modal_boton_cancelar');
    const botonesCerrarModal = document.querySelectorAll('.modal_close');

    // BTNS PARA NAVEGAR Y CERRAR SESION
    const btnIrAHome = document.getElementById('btnIrAHome');
    const btnIrASocios = document.getElementById('btnIrASocios');
    const btnIrACobranza = document.getElementById('btnIrACobranza');
    const btnIrAConfiguracion = document.getElementById('btnIrAConfiguracion');
    const btnCerrarSesion = document.getElementById('btnCerrarSesion');
    const btnCerrarSesionModal = document.getElementById('btnCerrarSesionModal');
    
    //                  COMPORTAMIENTOS 


    // NAVEGAR ENTRE PAGINAS
    btnIrAHome.addEventListener('click', () => {
        window.location.href = '../PAGES/dashboard.html';
    })
    btnIrASocios.addEventListener('click', () => {
        window.location.href = '../PAGES/socios.html';
    })
    btnIrACobranza.addEventListener('click', () => {
        window.location.href = '../PAGES/cobranza.html';
    })
    btnIrAConfiguracion.addEventListener('click', () => {
        window.location.href = '../PAGES/configuracion.html';
    })

    // CERRAR SESION
    btnCerrarSesion.addEventListener('click', () => {
        modalCerrarSesion.classList.add('modal--show');
    });

    btnCerrarSesionModal.addEventListener('click', () => {
        closeModal(modalCerrarSesion);
        cerrarSesion();
    })

    // TODOS LOS BOTONES PARA CANCELAR Y CERRAR MODALES
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


    //                  FUNCIONES

    // CERRAR MODALES
    function closeModal(modal) {
        modal.classList.remove('modal--show');
    }

    function cerrarSesion() {
        window.location.href = 'login.html';
    }
}