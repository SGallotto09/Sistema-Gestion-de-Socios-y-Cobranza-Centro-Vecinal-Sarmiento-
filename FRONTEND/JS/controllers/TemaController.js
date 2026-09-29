document.addEventListener('DOMContentLoaded', cargarTema);

function cargarTema() {
    const temaGuardado = localStorage.getItem('tema') || 'light';

    document.documentElement.setAttribute('data-theme', temaGuardado);
}