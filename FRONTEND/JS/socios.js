document.addEventListener('DOMContentLoaded', iniciarSocios);

function iniciarSocios() {
    lucide.createIcons();

    // VARIABLES
    const modalAltaSocio = document.getElementById('modalAltaSocio');
    const modalEditarSocio = document.getElementById('modalEditarSocio');
    const modalEliminarSocio = document.getElementById('modalEliminarSocio');

    const btnNuevoSocio = document.getElementById('btnNuevoSocio');
    const btnDarDeAltaSocio = document.getElementById('btnDarDeAltaSocio');
    const btnEditarSocio = document.getElementById('btnEditarSocio');

    const tbodySocios = document.getElementById('tbodySocios');

    // VARIABLES DE ALTA SOCIO
    const txtApellidoAlta = document.getElementById('txtApellidoSocioAlta');
    const txtNombreAlta = document.getElementById('txtNombreSocioAlta');
    const txtDniAlta = document.getElementById('txtDniSocioAlta');
    const txtTelefonoAlta = document.getElementById('txtTelefonoSocioAlta');
    const txtBarrioAlta = document.getElementById('txtBarrioSocioAlta');
    const txtCalleAlta = document.getElementById('txtCalleSocioAlta');
    const txtAlturaAlta = document.getElementById('txtAlturaSocioAlta');

    // VARIABLES DE EDITAR SOCIO
    const txtIdSocioEditar = document.getElementById('txtIdSocioEditar');
    const txtApellidoEditar = document.getElementById('txtApellidoSocioEditar');
    const txtNombreEditar = document.getElementById('txtNombreSocioEditar');
    const txtDniEditar = document.getElementById('txtDniSocioEditar');
    const txtTelefonoEditar = document.getElementById('txtTelefonoSocioEditar');
    const txtBarrioEditar = document.getElementById('txtBarrioSocioEditar');
    const txtCalleEditar = document.getElementById('txtCalleSocioEditar');
    const txtAlturaEditar = document.getElementById('txtAlturaSocioEditar');

    // COMPORTAMIENTOS
    btnNuevoSocio.addEventListener('click', () => {
        openModal(modalAltaSocio);
    })

    btnDarDeAltaSocio.addEventListener('click', async () => {
        const creado = await darDeAltaSocio();

        if (creado) {
            closeModal(modalAltaSocio);
        } 
    })

    tbodySocios.addEventListener('click', (e) => {
        const botonEditar = e.target.closest('.lapiz');
        const botonEliminar = e.target.closest('.tacho');

        if (botonEditar) {
            openModal(modalEditarSocio);
            completarCamposConDatosFila(botonEditar.closest('tr'));
        }

        if (botonEliminar) {
            openModal(modalEliminarSocio);
        }
    });

    // FUNCIONES
    function openModal(modal) {
        modal.classList.add('modal--show');
    }

    function closeModal(modal) {
        modal.classList.remove('modal--show');
    }

    async function obtenerSocios() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php');

        const socios = await response.json();

        let filas = "";

        for (let i = 0; i < socios.length; i++) {
            filas += 
                `<tr>
                    <td data-id="${socios[i].id}">${socios[i].id}</td>
                    <td>${socios[i].apellido}</td>
                    <td>${socios[i].nombre}</td>
                    <td>${socios[i].dni}</td>
                    <td>${socios[i].telefono}</td>
                    <td>${socios[i].barrio}</td>
                    <td>${socios[i].calle}</td>
                    <td>${socios[i].altura}</td>
                    <td>
                        <i data-lucide="pencil" class="iconoTabla lapiz"></i>
                        <i data-lucide="trash-2" class="iconoTabla tacho"></i>
                    </td>
                </tr>`;

            }
        tbodySocios.innerHTML = filas;
        lucide.createIcons();
    }

    async function darDeAltaSocio() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                nombre: txtNombreAlta.value,
                apellido: txtApellidoAlta.value,
                dni: txtDniAlta.value,
                telefono: txtTelefonoAlta.value,
                barrio: txtBarrioAlta.value,
                calle: txtCalleAlta.value,
                altura: txtAlturaAlta.value
            }),
        }) 

        const data = await response.json();

        if (!response.ok) {
            alert(data.message);
            return;
        }

        alert(data.message);
        return true;
    }

    function completarCamposConDatosFila(fila) {
        txtIdSocioEditar.value =  fila.cells[0].textContent;
        txtApellidoEditar.value = fila.cells[1].textContent;
        txtNombreEditar.value = fila.cells[2].textContent;
        txtDniEditar.value = fila.cells[3].textContent;
        txtTelefonoEditar.value = fila.cells[4].textContent;
        txtBarrioEditar.value = fila.cells[5].textContent;
        txtCalleEditar.value = fila.cells[6].textContent;
        txtAlturaEditar.value = fila.cells[7].textContent;
    }

    obtenerSocios();
}