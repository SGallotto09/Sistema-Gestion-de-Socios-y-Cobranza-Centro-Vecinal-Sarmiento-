import { SocioApi } from "../api/SociosApi.js";

async function cargarSocios() {
    const socioApi = new SocioApi();

    try {
        const socios = await socioApi.obtenerSociosCobranza();

        const tbody = document.getElementById('tablaSocios');

        tbody.innerHTML = "";

        const sociosImpresion = socios.filter(socio => Number(socio.estadoCuota) === 0).sort((a, b) => {
            const comparacionBarrio = a.barrio.toLowerCase().localeCompare(b.barrio.toLowerCase());

            if (comparacionBarrio !== 0) {
                return comparacionBarrio;
            }

            const comparacionCalle = a.calle.localeCompare(b.calle);

            if (comparacionCalle !== 0) {
                return comparacionCalle;
            }

            return Number(a.altura) - Number(b.altura);
        });

        sociosImpresion.forEach(socio => {
            const fila = document.createElement('tr');

            fila.innerHTML = `
                <td>${socio.id}</td>
                <td>${socio.apellido}</td>
                <td>${socio.nombre}</td>
                <td>${formatearDNI(socio.dni)}</td>
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

function formatearDNI(dni) {
    return new Intl.NumberFormat('es-AR').format(dni);
}

document.getElementById('btnImprimir').addEventListener('click', () => {
    window.print();
});

cargarSocios();
mostrarFecha();