document.addEventListener('DOMContentLoaded', iniciarLogin);

function iniciarLogin() {
    lucide.createIcons();

    const txtUsuario = document.getElementById('txtUsuario');
    const txtContrasenia = document.getElementById('txtContrasenia');
    const btnIniciarSesion = document.getElementById('btnIniciarSesion');

    btnIniciarSesion.addEventListener('click', () => {
        iniciarSesion(txtUsuario.value, txtContrasenia.value);
    });

    async function iniciarSesion(usuario, contrasenia) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify ({
                usuario: usuario,
                contrasenia: contrasenia
            })
        });

        const data = await response.json();

        if (data.message === 'Acceso exitoso' && data.rol === 'administrador') {
            window.location.href = 'dashboard.html';
        }
        else {
            alert(data.message);
        }
    }
}