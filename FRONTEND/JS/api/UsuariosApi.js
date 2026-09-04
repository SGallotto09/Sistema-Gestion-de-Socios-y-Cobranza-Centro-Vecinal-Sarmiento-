export class UserApi {
    async getUsuarios(rol) {
        const response = await fetch(`http://localhost/Proyecto/BACKEND/controllers/UserController.php?rol=${rol}`);

        const usuarios = await response.json();

        if (!response.ok) {
            throw new Error(usuarios.message);
        }

        return usuarios;
    }

    async getUsuarioById(id, rol) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/UserController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                rol: rol
            })
        });

        const usuario = await response.json();

        if (!response.ok) {
            alert(usuario.message);
        }

        return usuario;
    }
}