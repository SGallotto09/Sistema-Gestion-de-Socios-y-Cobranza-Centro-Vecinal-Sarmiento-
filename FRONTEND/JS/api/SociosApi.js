export class SocioApi {
    async obtenerSocios() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/SocioController.php');

        const socios = await response.json();

        return socios;
    }

    async obtenerSociosPorNombre(_busqueda) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/SocioController.php&buscar=' + _busqueda)

        const socios = await response.json();

        return socios;
    }

    async obtenerSociosPorFiltro(_filtro) {
        let filtro = _filtro.toLowerCase();

        if (filtro === 'numero socio') {
            filtro = 'id';
        }

        const response = await fetch(`http://localhost/Proyecto/BACKEND/controllers/SocioController.php?parametro=${filtro}`)
        
        const socios = await response.json();

        return socios;
    }

    async obtenerCantidadSocios() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/SocioController.php?accion=cantidad');

        const cantidadSocios = await response.json()
        
        return cantidadSocios;
    }

    async darDeAltaSocio(_nombre, _apellido, _dni, _telefono, _barrio, _calle, _altura) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/SocioController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                nombre: _nombre,
                apellido: _apellido,
                dni: _dni,
                telefono: _telefono,
                barrio: _barrio,
                calle: _calle,
                altura: _altura
            }),
        }) 

        const nuevoSocio = await response.json();

        if (!response.ok) {
            alert(nuevoSocio.message);
            return;
        }

        return nuevoSocio;
    }

    async editarSocio(_id, _nombre, _apellido, _dni, _telefono, _barrio, _calle, _altura) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/SocioController.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify ({
                id: _id,
                nombre: _nombre,
                apellido: _apellido,
                dni: _dni,
                telefono: _telefono,
                barrio: _barrio,
                calle: _calle,
                altura: _altura,
            }),
        })

        const socioEditado = await response.json();

        if (!response.ok) {
            alert(data.message);
            return;
        }

        return socioEditado;
    }

    async eliminarSocio(idEliminar) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/SocioController.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: idEliminar,
            }),
        })

        const data = await response.json();

        if (!response.ok) {
            alert(data.message);
            return;
        }

        return data;
    }
}