import { LoginApi } from "../api/AuthApi.js";

document.addEventListener('DOMContentLoaded', iniciarLogin);

function iniciarLogin() {
    const loginApi = new LoginApi();
    lucide.createIcons();

    const txtUsuario = document.getElementById('txtUsuario');
    const txtContrasenia = document.getElementById('txtContrasenia');
    const btnIniciarSesion = document.getElementById('btnIniciarSesion');

    btnIniciarSesion.addEventListener('click', async () => {
        if (txtUsuario.value.trim() === '') {
            alert('El usuario es obligatorio');
            return;
        }

        if (txtContrasenia.value.trim() === '') {
            alert('La contrasenia es obligatoria');
            return;
        }

        loginApi.iniciarSesion(txtUsuario.value, txtContrasenia.value);
    });
}