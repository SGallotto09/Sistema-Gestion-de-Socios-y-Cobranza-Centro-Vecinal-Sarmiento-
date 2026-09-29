document.addEventListener('DOMContentLoaded', iniciarConfiguracion);

function iniciarConfiguracion() {
    lucide.createIcons();

    const radioClaro = document.getElementById('claro');
    const radioOscuro = document.getElementById('oscuro');
    const btnGuardarCambios = document.getElementById('btnGuardarCambios');

    const temaGuardado = localStorage.getItem('tema') || 'light';

    if (temaGuardado === 'dark') {
        radioOscuro.checked = true;
    } else {
        radioClaro.checked = true;
    }

    btnGuardarCambios.addEventListener('click', () => {

        const temaSeleccionado = radioOscuro.checked
            ? 'dark'
            : 'light';

        document.documentElement.setAttribute('data-theme', temaSeleccionado);

        localStorage.setItem('tema', temaSeleccionado);
    });
}