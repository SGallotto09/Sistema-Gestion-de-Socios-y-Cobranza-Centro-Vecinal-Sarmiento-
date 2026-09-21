async function cargarSocios() {
    try {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/SocioController.php');
        
        if (!response.ok) {
            throw new Error('Error al obtener los socios');
        }

        const socios = await response.json();

        const tbody = document.getElementById('tablaSocios');

        tbody.innerHTML = "";

        socios.forEach(socio => {
            const fila = document.createElement('tr');

            fila.innerHTML = `
                <td>${socio.id}</td>
                <td>${socio.apellido}</td>
                <td>${socio.nombre}</td>
                <td>${socio.dni}</td>
                <td>${socio.telefono}</td>
                <td>${socio.barrio}</td>
                <td>${socio.calle}</td>
                <td>${socio.altura}</td>
            `;

            tbody.appendChild(fila);
        });

    } catch (error) {
        console.error('Error:', error);
    }
}

document.getElementById('btnVolver').addEventListener('click', function (e) {
    e.preventDefault();

    window.location.href = 'cobranza.php';
});

function mostrarFecha() {
    const fecha = new Date();
    const fechaFormateada = fecha.toLocaleDateString('es-AR');

    document.getElementById('fecha').textContent = `Fecha de impresión: ${fechaFormateada}`;
}

document.getElementById('btnImprimir').addEventListener('click', () => {
    window.print();
});

cargarSocios();
mostrarFecha();