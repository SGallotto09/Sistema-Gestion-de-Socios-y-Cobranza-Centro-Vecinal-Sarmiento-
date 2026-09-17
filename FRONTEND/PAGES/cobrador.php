<?php 

require_once '../../BACKEND/tasks/validarSesionIniciada.php'

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobranza - Cobrador</title>
    <link rel="stylesheet" href="../CSS/menu.css">
    <link rel="stylesheet" href="../CSS/cobrador.css">
    <link rel="stylesheet" href="../CSS/cobranza.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="../JS/controllers/CobradorController.js" type="module"></script>
</head>

<body>
    <main class="contenedor-cobrador">
        <header class="header-cobrador">
            <div class="titulo-header">
                <span class="texto-secundario">Cobranza</span>
                <h1 id="h2BimestreActual"></h1>
            </div>

            <div class="icono-usuario">
                <i data-lucide="circle-user"></i>
            </div>
        </header>

        <section class="buscador">
            <div class="contenedor-input">
                <i data-lucide="search"></i>

                <input type="text" id="txtBuscarSocio" placeholder="Buscar socio...">
            </div>
        </section>

        <div class="informacion-listado">
            <span id="txtCantidadSocios">10 socios</span>
        </div>

        <section class="lista-socios" id="listaSocios"></section>

        <section class="modal modal_acciones_socio" id="modalAccionesSocio">
            <div class="modal_content_acciones">
                <div class="content_titulo_acciones">
                    <div class="cerrar_modal">
                        <h2 class="modal_close">X</h2>
                    </div>
                    <div class="titulo_acciones">
                        <h4>¿Qué deseas hacer?</h4>
                        <p class="p_texto">Seleccione una acción.</p>
                    </div>
                </div>

                <div class="datos_socio_acciones">
                    <div class="icono_socio_accion">
                        <i data-lucide="user"></i>
                    </div>
                    
                    <div class="data_socio_accion">
                        <p class="p_texto_cobrador">Socio:</p>
                        <h4 class="dataSocio modal_title"></h4>
                        <p class="dataSocioDniTelefono p_texto"></p>
                    </div>

                    <div class="contador_visitas">
                        <i data-lucide="chart-no-axes-column-increasing"></i>
                        <p class="p_texto">Visitas registradas</p>
                        <h4 id="txtContadorVisitas"></h4>
                    </div>
                </div>

                <div class="acciones">
                    <button class="content_registrar_pago" id="btnModalEstadoPago">
                        <div class="titulo_accion">
                            <h4 class="p_texto_cobrador">Modificar estado de pago</h4>
                            <p class="p_texto">Cambia el estado de pago de la cuota.</p>
                        </div>
                        <div class="footer_content_accion">
                            <i data-lucide="arrow-right" class="flecha_pago"></i>
                        </div>
                    </button>
                    
                    <button class="content_registrar_visita" id="btnModalVisita">
                        <div class="titulo_accion">
                            <h4 class="p_texto_cobrador">Registrar visita</h4>
                            <p class="p_texto">Registra una nueva visita a este socio.</p>
                        </div>
                        <div class="footer_content_accion">
                            <i data-lucide="arrow-right" class="flecha_visita"></i>
                        </div>
                    </button>
                </div>

                <div class="footer_acciones">
                    <button class="modal_boton_cancelar">Cancelar</button>
                </div>
            </div>
        </section>

        <section class="modal modal_registro_visita" id="modalRegistrarVisita">
            <div class="modal_content_registro_visita">
                <div class="header_registro_visita">
                    <div class="cerrar_modal">
                        <h2 class="modal_close">X</h2>
                    </div>
                    <div class="titulo_registro_visitas">
                        <h4>Confirmar visita</h4>
                        <p class="p_texto">Se registrara una nueva visita al siguiente socio.</p>
                    </div>
                </div>

                <div class="body_registro_visita">
                    <div class="icono_socio_accion">
                        <i data-lucide="user"></i>
                    </div>
                    
                    <div class="data_socio_accion_modal2">
                        <p class="p_texto_cobrador">Socio:</p>
                        <h4 class="dataSocio modal_title"></h4>
                        <p class="dataSocioDniTelefono p_texto"></p>
                    </div>
                </div>

                <div class="footer_registro_visita">
                    <button class="modal_boton_cancelar">Cancelar</button>
                    <button class="modal_boton_accion" id="btnRegistrarVisita">Registrar visita</button>
                </div>
            </div>
        </section>

        <section class="modal modal_editar_socio" id="modalEditarEstadoPagoSocio">
            <div class="modal_content_editar_socio">
                <div class="modal_header_editar_socio">
                    <h2 class="modal_title_header">Editar estado de pago</h2>
                    <h2 class="modal_close">X</h2>
                </div>

                <div class="modal_datos_editar_socio">
                    <h3 class="modal_title">Socio:</h3>
                    <h2 class="dataSocio modal_title"></h2>
                </div>

                <div class="modal_body_editar_socio">
                    <div class="titulo_cuota">
                        <h4 id="txtNumeroCuota"></h4>
                    </div>
                    <div class="modal_estado_pago_editar_socio">
                        <h4>Estado de pago:</h4>
                        <div>
                            <input type="radio" name="estadoPago" class="modal_radio_editar_socio" value="pagado">
                            <span>Pagado</span>
                        </div>
                        
                        <div>
                            <input type="radio" name="estadoPago" class="modal_radio_editar_socio" value="noPagado">
                            <span>No pagado</span>
                        </div>
                    </div>
                </div>

                <div class="modal_footer_editar_socio">
                    <button class="modal_boton_cancelar">Cancelar</button>
                    <button class="modal_boton_accion" id="btnGuardarCambios">Guardar</button>
                </div>
            </div>
        </section>    
    </main>
</body>
</html>