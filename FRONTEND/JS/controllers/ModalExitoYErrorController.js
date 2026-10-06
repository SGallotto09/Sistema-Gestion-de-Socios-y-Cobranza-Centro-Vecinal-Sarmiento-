const modalExito = document.getElementById('modalExito');
const tituloModalExito = document.getElementById('modalExitoTitulo');
const mensajeModalExito = document.getElementById('modalExitoMensaje');
const btnAceptarExito = document.getElementById('btnAceptarExito');

const modalError = document.getElementById('modalError');
const tituloModalError = document.getElementById('modalErrorTitulo');
const mensajeModalError = document.getElementById('modalErrorMensaje');
const btnAceptarError = document.getElementById('btnAceptarError');

export function mostrarModalExito(titulo, mensaje) {
    tituloModalExito.textContent = titulo;
    mensajeModalExito.textContent = mensaje;

    modalExito.classList.add('activo');
}

export function mostrarModalExitoConAccion(titulo, mensaje, accion) {
    mostrarModalExito(titulo, mensaje);

    btnAceptarExito.onclick = () => {
        cerrarModalExito();
        accion();
    };
}

export function cerrarModalExito() {
    modalExito.classList.remove('activo');
}

btnAceptarExito.addEventListener('click', cerrarModalExito);

export function mostrarModalError(titulo, mensaje) {
    tituloModalError.textContent = titulo;
    mensajeModalError.textContent = mensaje;

    modalError.classList.add('activo');
}

export function cerrarModalError() {
    modalError.classList.remove('activo');
}

btnAceptarError.addEventListener('click', cerrarModalError);