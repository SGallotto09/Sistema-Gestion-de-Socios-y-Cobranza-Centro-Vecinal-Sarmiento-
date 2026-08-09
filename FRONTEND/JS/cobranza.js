document.addEventListener('DOMContentLoaded', iniciarCobranza);

function iniciarCobranza() {
    lucide.createIcons();

    // VARIABLES

    // MODALES
    const modalEditarSocio = document.getElementById('modalEditarSocio');
    const modalLinkAcceso = document.getElementById('modalLinkAcceso');
    const modalPlantillaImpresion = document.getElementById('modalPlantillaImpresion');

    const btnGenrarLinkAcceso = document.getElementById('btnGenerarLinkAcceso');
    const btnGenerarPLantillaImpresion = document.getElementById('btnGenerarPlantillaImpresion');
    const btnGuardarCambios = document.getElementById('btnGuardarCambios');

    // TABLA
    const tbodySocios = document.getElementById('tbodySocios');
    let socios = [];
    let paginaActual = 1;
    const sociosPorPagina = 10;

    const contenedorPaginas = document.getElementById('contenedorPaginas');
    const btnAnterior = document.getElementById("btnAnterior");
    const btnSiguiente = document.getElementById("btnSiguiente");
    const tituloCantiadSocios = document.getElementById('tituloCantiadSocios');

    // FILTRO
    let txtBuscarSocio = document.getElementById('txtBuscarSocio');
    const btnBuscarSocio = document.getElementById('btnBuscarSocio');
    let txtSelectFiltro = document.getElementById('selectFiltro');

    // COMPORTAMIENTOS

    btnGenrarLinkAcceso.addEventListener('click', () => {
        openModal(modalLinkAcceso);
    });

    btnGenerarPLantillaImpresion.addEventListener('click', () => {
        openModal(modalPlantillaImpresion);
    });

    btnGuardarCambios.addEventListener('click', () => {
        // IMPLEMENTAR LA FUNCION DE EDITAR EL ESTADO DE PAGO Y VISITA
    })

    txtBuscarSocio.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            if (txtBuscarSocio.value.trim() === '') {
                obtenerSocios();
            } else {
                obtenerSociosPorNombre();
            }
        }

        txtSelectFiltro.value = 'Filtros';
    });

    btnBuscarSocio.addEventListener('click', () => {
        if (txtBuscarSocio.value.trim() === '') {
            obtenerSocios();
        } else {
            obtenerSociosPorNombre();
        }

        txtSelectFiltro.value = 'Filtros';
    });

    tbodySocios.addEventListener('click', (e) => {
        const botonEditar = e.target.closest('.lapiz');

        if (botonEditar) {
            openModal(modalEditarSocio);
        }
    });

    btnAnterior.addEventListener("click", () => {
        if (paginaActual > 1) {
            paginaActual--;

            mostrarPagina();
            crearPaginacion();
        }
    });

    btnSiguiente.addEventListener("click", () => {
        const totalPaginas = Math.ceil(socios.length / sociosPorPagina);

        if (paginaActual < totalPaginas) {

            paginaActual++;

            mostrarPagina();
            crearPaginacion();
        }
    });

    // FUNCIONES
    function openModal(modal) {
        modal.classList.add('modal--show');
    }

    async function obtenerSocios() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php');

        socios = await response.json();

        mostrarPagina();
        crearPaginacion();
    }

    function mostrarPagina() {
        const inicio = (paginaActual - 1) * sociosPorPagina;
        const fin = inicio + sociosPorPagina;

        const sociosPagina = socios.slice(inicio, fin);

        let filas = '';
        let divPagado = '';

        for (let i = 0; i < sociosPagina.length; i++) {

            filas += 
                `<tr data-id="${sociosPagina[i].id}">
                    <td>${sociosPagina[i].id}</td>
                    <td>${sociosPagina[i].apellido}</td>
                    <td>${sociosPagina[i].nombre}</td>
                    <td>${sociosPagina[i].dni}</td>
                    <td>${sociosPagina[i].telefono}</td>
                    <td>
                        <div class="estado">
                            <span class="punto pagado"></span>
                            <span>Pagado</span>
                        </div>
                    </td>    
                    <td>
                        <div class="estado">
                            <span class="punto visitado"></span>
                            <span>Visitado</span>
                        </div>
                    </td>
                    <td>
                        <i data-lucide="pencil" class="iconoTabla lapiz"></i>
                    </td>
                </tr>`;
        }
        tbodySocios.innerHTML = filas;
        lucide.createIcons();
    }

    function crearPaginacion() {
        contenedorPaginas.innerHTML = "";

        const totalPaginas = Math.ceil(socios.length / sociosPorPagina);

        for (let i = 1; i <= totalPaginas; i++) {

            const boton = document.createElement('button');
            boton.classList.add('btnPagina');

            boton.textContent = i;

            if (i === paginaActual) {
                boton.classList.add('paginaActiva');
            }

            boton.addEventListener('click', () => {
                paginaActual = i;

                mostrarPagina();
                crearPaginacion();
            });

            contenedorPaginas.appendChild(boton);
        }
    }

    async function obtenerSociosPorNombre() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php?accion=nombreSocio&buscar=' + txtBuscarSocio.value)

        socios = await response.json();

        mostrarPagina();
        crearPaginacion();
    }

    async function obtenerSociosPorFiltro() {
        let filtro = txtSelectFiltro.value.toLowerCase();

        if (filtro === 'numero socio') {
            filtro = 'id';
        }

        const response = await fetch(`http://localhost/Proyecto/BACKEND/api/socios.php?parametro=${filtro}`)
        
        socios = await response.json();

        mostrarPagina();
        crearPaginacion();
    }

    async function obtenerCantidadSocios() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/api/socios.php?accion=cantidad');

        const data = await response.json()
        tituloCantiadSocios.textContent = `Mostrando 1 a 10 de ${data.cantidad} socios`;
    }

    obtenerSocios();
    obtenerCantidadSocios();
}