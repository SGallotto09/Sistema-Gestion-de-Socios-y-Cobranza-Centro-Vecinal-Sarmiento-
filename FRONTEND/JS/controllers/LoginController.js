import { LoginApi } from "../api/AuthApi.js";
import {mostrarModalExitoConAccion, mostrarModalError} from "./ModalExitoYErrorController.js";

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

        try {
            const usuarioEncontrado = await loginApi.iniciarSesion(txtUsuario.value, txtContrasenia.value);

            sessionStorage.setItem('usuarioLogeado', JSON.stringify(usuarioEncontrado));

            mostrarModalExitoConAccion(
                'Inicio de sesión exitoso',
                `${usuarioEncontrado.message} ${usuarioEncontrado.usuarioEncontrado.nombre}, ¡bienvenido al sistema!`,
                () => {
                    window.location.href = 'dashboard.php';
                }
            );
        } catch (error) {
            mostrarModalError('Error al iniciar sesión', error.message);

            txtUsuario.value = '';
            txtContrasenia.value = '';
        }
    });
}