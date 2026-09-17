async function verificarPestania() {
    const tokenPestania = sessionStorage.getItem('tokenPestania');

    // No existe token en esta pestaña
    if (!tokenPestania) {
        await fetch('http://localhost/Proyecto/BACKEND/controllers/LogoutController.php', {
                method: 'POST'
            }
        );

        window.location.href = 'login.php';
        return;
    }

    // Enviamos el token al backend para comprobar que corresponde a la sesión actual
    const response = await fetch('http://localhost/Proyecto/BACKEND/tasks/verificarPestania.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                tokenPestania: tokenPestania
            })
        }
    );

    const data = await response.json();

    if (!data.valida) {
        sessionStorage.removeItem('tokenPestania');

        window.location.href = 'login.php';
    }
}

verificarPestania();