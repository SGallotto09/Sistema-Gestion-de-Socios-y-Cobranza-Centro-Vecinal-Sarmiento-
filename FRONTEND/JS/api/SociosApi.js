export class SocioApi {
    async obtenerSocios() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php');

        const socios = await response.json();

        return socios;
    }

    async obtenerSociosPorNombre(_busqueda) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php?accion=nombreSocio&buscar=' + _busqueda)

        const socios = await response.json();

        return socios;
    }

    async obtenerSociosPorFiltro(_filtro) {
        let filtro = _filtro.toLowerCase();

        if (filtro === 'numero socio') {
            filtro = 'id';
        }

        const response = await fetch(`http://localhost/Proyecto/BACKEND/api/socios.php?parametro=${filtro}`)
        
        const socios = await response.json();

        return socios;
    }

    async obtenerCantidadSocios() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php?accion=cantidad');

        const cantidadSocios = await response.json()
        
        return cantidadSocios;
    }

    async darDeAltaSocio(_nombre, _apellido, _dni, _telefono, _barrio, _calle, _altura) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php', {
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
                altura: _altura,
                activo: 1,
                id_periodo: 1,
                created_by: id_administrador,
                created_at: new Date().toISOString().split('T')[0],
            }),
        }) 

        const data = await response.json();

        if (!response.ok) {
            alert(data.message);
            return;
        }

        return data;
    }

    async editarSocio(_id, _nombre, _apellido, _dni, _telefono, _barrio, _calle, _altura) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php', {
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
                updated_by: id_usuario,
                updated_at: new Date().toISOString().split('T')[0],
            }),
        })

        const data = await response.json();

        if (!response.ok) {
            alert(data.message);
            return;
        }

        return data;
    }

    async eliminarSocio(idEliminar) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: idEliminar,
                eliminado: 1,
                delete_by: id_administrador,
                delete_at: new Date().toISOString().split('T')[0],
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