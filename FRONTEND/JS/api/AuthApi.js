export class LoginApi {
    async iniciarSesion(usuario, contrasenia) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/AuthController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify ({
                usuario: usuario,
                contrasenia: contrasenia
            })
        });

        const usuarioEncontrado = await response.json();

        if (!response.ok) {
            alert(usuarioEncontrado.message);
        }
        else {
            alert(usuarioEncontrado.message);
            window.location.href = 'dashboard.php';
        }
    }
}

