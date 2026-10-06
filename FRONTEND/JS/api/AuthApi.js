export class LoginApi {

    async iniciarSesion(usuario, contrasenia) {

        const response = await fetch(
            '/Proyecto/BACKEND/controllers/AuthController.php',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    usuario: usuario,
                    contrasenia: contrasenia
                })
            }
        );

        const usuarioEncontrado = await response.json();

        if (!response.ok) {
            throw new Error(usuarioEncontrado.message);
        }

        sessionStorage.setItem('tokenPestania', usuarioEncontrado.token);


        return usuarioEncontrado;
    }
}