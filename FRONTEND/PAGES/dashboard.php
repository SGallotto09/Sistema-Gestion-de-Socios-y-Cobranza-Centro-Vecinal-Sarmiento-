<?php

require_once '../../BACKEND/tasks/validarSesionIniciada.php'

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <script>
        const tema = localStorage.getItem('tema') || 'light';
        document.documentElement.setAttribute('data-theme', tema);
    </script>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/menu.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
    <link rel="stylesheet" href="../CSS/modalExitoYError.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script type="module" src="../JS/controllers/MenuController.js"></script>
    <script type="module" src="../JS/controllers/DashboardController.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="contenedor-general">
        <div class="menu">
            <div class="titulo-menu">
                <div class="imagen">
                    <img src="../Image/Logo Vecinal Barrio Sarmiento.png" alt="Logo Centro vecinal Sarmiento">
                </div>
                
                <span>Centro Vecinal Sarmiento</span>
            </div>

            <div class="contenedor-botones-menu">
                <div class="botones">
                    <button class="boton activo" id="btnIrAHome">
                        <div class="icono">
                            <i data-lucide="home"></i>
                        </div>
                        
                        <span>Inicio</span>
                    </button>

                    <button class="boton" id="btnIrASocios">
                        <div class="icono">
                            <i data-lucide="users"></i>
                        </div>
                        
                        <span>Socios</span>
                    </button>

                    <button class="boton" id="btnIrACobranza">
                        <div class="icono">
                            <i data-lucide="credit-card"></i>
                        </div>
                        
                        <span>Cobranza</span>
                    </button>

                    <button class="boton" id="btnIrAConfiguracion">
                        <div class="icono">
                            <i data-lucide="settings"></i>
                        </div>
                        
                        <span>Configuración</span>
                    </button>
                </div>
                
                <div class="boton-cerrar-sesion">
                    <button class="botonSesion" id="btnCerrarSesion">
                        <div class="icono">
                            <i data-lucide="log-out"></i>
                        </div>
                        
                        <span>Cerrar sesión</span>
                    </button>
                </div>
            </div>
        </div>

        <section class="modal modal_cerrar_sesion" id="modalCerrarSesion">
            <div class="modal_content_cerrar_sesion">
                <div class="modal_header_cerrar_sesion">
                    <h2 class="modal_title_header">Cerrar sesión</h2>
                    <h2 class="modal_close">X</h2>
                </div>

                <div class="modal_body_cerrar_sesion">
                    <div><i data-lucide="log-out"></i></div>
                    <h3>¿Estás seguro que deseas cerrar sesión de tu cuenta?</h3>
                </div>

                <div class="modal_footer_cerrar_sesion">
                    <button class="modal_boton_cancelar">Cancelar</button>
                    <button class="modal_boton_accion" id="btnCerrarSesionModal">Cerrar sesión</button>
                </div>
            </div>
        </section>

        <div class="contenedor-dashboard">
            <div class="header">
                <div class="titulo-pagina">
                    <h1>Dashboard</h1>
                </div>

                <div class="rol-usuario">
                    <span><b id="spanNombreUsuario"></b></span>
                    <i data-lucide="circle-user"></i>
                </div>
            </div>

            <div class="bimestre-actual">
                <div class="dato-bimestre">
                    <p>Bimestre actual</p>
                    <div>
                        <span><b id="h2BimestreActual"></b></span>
                        <i data-lucide="calendar-days"></i>
                    </div>
                </div>
            </div>

            <div class="datos-socios">
                <div class="total-socios">
                    <i data-lucide="users" class="iconoDato"></i>
                    <h2 id="h2TotalSocios"></h2>
                    <div class="contenedor-parrafos">
                        <p><b>Total de socios</b></p>
                        <p><b>Activos</b></p>
                    </div>
                </div>

                <div class="pagaron-bimestre">
                    <i data-lucide="circle-dollar-sign" class="iconoDato cobrados"></i>
                    <h2 id="h2PagaronBimestre"></h2>
                    <div class="contenedor-parrafos">
                        <p><b>Pagaron el bimestre</b></p>
                        <p><b id="pagaronBimestre"></b></p>
                    </div>
                </div>

                <div class="socios-visitados">
                    <i data-lucide="user" class="iconoDato"></i>
                    <h2 id="h2SociosVisitados"></h2>
                    <div class="contenedor-parrafos">
                        <p><b>Socios visitados</b></p>
                        <p><b id="sociosVisitados"></b></p>
                    </div>
                </div>

                <div class="pendientes-pago">
                    <i data-lucide="circle-x" class="iconoDato pendiente"></i>
                    <h2 id="h2PendientesPago"></h2>
                    <div class="contenedor-parrafos">
                        <p><b>Pendientes de pago</b></p>
                        <p><b id="pendientesDePago"></b></p>
                    </div>
                </div>
            </div>

            <div class="progreso-acceso">
                <div class="progreso">
                    <div class="titulo">
                        <b>Progreso de cobranza</b>
                    </div>

                    <div class="barra">
                        <div class="barra-completa" id="barraCompleta"></div>
                    </div>

                    <span id="txtPorcentajeBarra"><b></b></span>

                    <p><b id="parrafoBarra"></b></p>
                </div>

                <div class="acceso">
                    <div class="titulo">
                        <b>Accesos rapidos</b>
                    </div>

                    <div class="boton-nuevoSocio">
                        <button id="btnNuevoSocioDashboard">
                            <i data-lucide="plus"></i>
                            <span><b>Nuevo socio</b></span>
                        </button>
                    </div>

                    <div class="boton-cobranza">
                        <button id="btnCobranzaDashboard">
                            <i data-lucide="credit-card"></i>
                            <span><b>Ir a cobranza</b></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <section id="modalExito" class="modal-exito">
            <div class="modal-exito-contenido">
                <div class="icono-exito">
                    <i data-lucide="check"></i>
                </div>

                <h2 id="modalExitoTitulo">
                    Operación exitosa
                </h2>

                <p id="modalExitoMensaje">
                    La operación se realizó correctamente.
                </p>

                <button id="btnAceptarExito" class="btn-aceptar-exito">
                    Aceptar
                </button>
            </div>
        </section>

        <section id="modalError" class="modal-error">
            <div class="modal-error-contenido">
                <div class="icono-error">
                    <i data-lucide="x"></i>
                </div>

                <h2 id="modalErrorTitulo">
                    Ocurrió un error
                </h2>

                <p id="modalErrorMensaje">
                    No se pudo realizar la operación.
                </p>

                <button id="btnAceptarError" class="btn-aceptar-error">
                    Aceptar
                </button>
            </div>
        </section>
    </div>

    <script src="../JS/verificarPestania.js"></script>
</body>
</html>