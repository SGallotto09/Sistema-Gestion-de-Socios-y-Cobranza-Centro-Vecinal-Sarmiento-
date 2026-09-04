<?php 

session_start();

if(!isset($_SESSION['id'])) {
    header('Location: login.html');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cobranza</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/menu.css">
    <link rel="stylesheet" href="../CSS/cobranza.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="../JS/controllers/MenuController.js"></script>
    <script type="module" src="../JS/controllers/CobranzaController.js"></script>
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
                    <button class="boton" id="btnIrAHome">
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

                    <button class="boton activo" id="btnIrACobranza">
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
                
                <div class="boton-cerrar-sesion" id="btnCerrarSesion">
                    <button class="botonSesion">
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
                    <h3 class="modal_title">¿Estás seguro que deseas cerrar sesión de tu cuenta?</h3>
                </div>

                <div class="modal_footer_cerrar_sesion">
                    <button class="modal_boton_cancelar">Cancelar</button>
                    <button class="modal_boton_accion" id="btnCerrarSesionModal">Cerrar sesión</button>
                </div>
            </div>
        </section>

        <div class="contenedor-cobranza">
            <div class="header">
                <h2>Cobranza: Bimestre Julio - Agosto 2026</h2>
                <div class="botonesHeader">
                    <button class="btnHeader" id="btnAbrirModalLinkAcceso">
                        <i data-lucide="link-2"></i>
                        <span><b>Generar link de acceso</b></span>
                    </button>
                    <button class="btnHeader" id="btnGenerarPlantillaImpresion">
                        <i data-lucide="printer"></i>
                        <span><b>Generar plantilla de impresion</b></span>
                    </button>
                </div>
            </div>

            <div class="filtro-socios">
                <div class="input-socio">
                    <input type="text" placeholder="Buscar socio..." id="txtBuscarSocio"> 
                    <div id="btnBuscarSocio"><i data-lucide="search"></i></div>
                </div>
                
                <div class="select-filtro">
                    <h2>Filtrar:</h2>
                    <select id="selectFiltro">
                        <option disabled selected>Filtros</option>
                        <option>Pagados</option>
                        <option>Visitados</option>
                    </select>
                </div>
            </div>

            <div class="tabla-socios">
                <table>
                    <thead>
                        <tr>
                            <th>Nº Socio</th>
                            <th>Apellido</th>
                            <th>Nombre</th>
                            <th>DNI</th>
                            <th>Telefono</th>
                            <th>Estado de pago</th>
                            <th>Visita</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    
                    <tbody id="tbodySocios" class="cuerpo_tabla">
                        
                    </tbody>
                </table>
            </div>

            <div class="footer">
                <div class="texto-footer">
                    <h3 id="tituloCantiadSocios"></h3>
                </div>

                <div class="siguiente-pagina">
                    <button id="btnAnterior" class="btnPagina">
                        ‹
                    </button>

                    <div id="contenedorPaginas"></div>

                    <button id="btnSiguiente" class="btnPagina">
                        ›
                    </button>
                </div>
            </div>

            <section class="modal modal_editar_socio" id="modalEditarSocio">
                <div class="modal_content_editar_socio">
                    <div class="modal_header_editar_socio">
                        <h2 class="modal_title_header">Editar estado de pago y visita</h2>
                        <h2 class="modal_close">X</h2>
                    </div>

                    <div class="modal_datos_editar_socio">
                        <h3 class="modal_title">Socio:</h3>
                        <h2 class="modal_title" id="txtInfoSocio"></h2>
                    </div>

                    <div class="modal_body_editar_socio">
                        <div class="modal_estado_pago_editar_socio">
                            <h4 class="modal_title">Estado de pago:</h4>
                            <div>
                                <input type="radio" name="estadoPago" class="modal_radio_editar_socio" value="pagado">
                                <span>Pagado</span>
                            </div>
                            
                            <div>
                                <input type="radio" name="estadoPago" class="modal_radio_editar_socio" value="noPagado">
                                <span>No pagado</span>
                            </div>
                        </div>

                        <div class="modal_visita_editar_socio">
                            <h4 class="modal_title">Visita:</h4>
                            <div>
                                <input type="radio" name="visita" class="modal_radio_editar_socio" value="visitado">
                                <span>Visitado</span>
                            </div>
                            
                            <div>
                                <input type="radio" name="visita" class="modal_radio_editar_socio" value="noVisitado">
                                <span>No visitado</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal_footer_editar_socio">
                        <button class="modal_boton_cancelar">Cancelar</button>
                        <button class="modal_boton_accion" id="btnGuardarCambios">Guardar</button>
                    </div>
                </div>
            </section>

            <section class="modal modal_generar_link_socio" id="modalLinkAcceso">
                <div class="modal_content_generar_link_socio">
                    <div class="modal_header_generar_link_socio">
                        <h2 class="modal_title_header">Generar link de acceso</h1>
                        <h2 class="modal_close">X</h2>
                    </div>

                    <div class="modal_data_generar_link_socio">
                        <p>El cobrador podrá acceder y actualizar el estado de pagos desde este enlace.</p>
                    </div>

                    <div class="modal_body_generar_link_socio">
                        <div class="modal_select_cobrador_generar_link_socio">
                            <h4 class="modal_title">Seleccionar cobrador:</h4>
                            <div class="modal_content_select_cobrador">
                                <div class="select_cobrador">
                                    <i data-lucide="user"></i>
                                    <select id="selectCobradores">
                                        <option value="">Seleccione un usuario</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal_tempo_generar_link_socio">
                            <h4 class="modal_title">El link caducara en:</h4>
                            <div class="modal_input_tempo_general_link_socio">
                                <select id="txtTempo">
                                    <option value="24">24 horas</option>
                                    <option value="12">12 horas</option>
                                    <option value="8">8 horas</option>
                                    <option value="4">4 horas</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal_aviso_generar_link_socio">
                            <div class="modal_icono_aviso_generar_link_socio">
                                <i data-lucide="info"></i>
                            </div>
                            
                            <div class="modal_info_aviso_generar_link_socio">
                                <h4 class="modal_title">Importante: Este enlace será válido hasta la fecha de vencimiento indicada. Una vez vencido, deberá generarse un nuevo enlace para acceder.</h4>
                            </div>
                        </div>
                    </div>

                    <div class="modal_footer_generar_link_socio">
                        <button class="modal_boton_cancelar">Cancelar</button>
                        <button class="modal_boton_accion" id="btnGenerarLink">Generar link</button>
                    </div>
                </div>
            </section>

            <section class="modal_carga_link" id="modalCargaLink">
                <div class="modal_content_carga">
                    <div class="modal_header_carga">
                        <div class="icon_content_carga">
                            <i data-lucide="link-2"></i>
                        </div>
                    </div>
                    <div class="modal_body_carga">
                        <div class="title_carga">
                            <h2>Generando link de acceso...</h2>
                        </div>

                        <div class="subtitle_carga">
                            <p class="p_texto_cobrador">Estamos preparando un enlace seguro para el cobrador.</p>
                            <p class="p_texto_cobrador">Esto puede tardar unos segundos.</p>
                        </div>
                    </div>
                    <div class="modal_footer_carga">
                        <div class="content_barra_carga">
                            <div class="barra_carga">
                                <div class="progreso_carga"></div>
                            </div>
                        </div>

                        <div class="porcentaje_carga">
                            <span id="porcentajeCarga">0%</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="modal modal_link_generado" id="modalLinkGenerado">
                <div class="modal_content_link_generado">
                    <div class="modal_header_link_generado">
                        <div class="modal_icono_check_header">
                            <i data-lucide="circle-check-big" id="iconoCheck"></i>
                        </div>
                        <h2 class="modal_title_header">¡Link generado correctamente!</h1>
                        <h2 class="modal_close">X</h2>
                    </div>

                    <div class="modal_data_link_generado">
                        <p>El link de acceso fue generado con exito y está listo para compartir con el cobrador.</p>
                    </div>

                    <div class="modal_body_link_generado">
                        <div class="modal_body_link_generado_cobrador_asignado">
                            <div class="icono_info_cobrador">
                                <i data-lucide="user" class="icono_cobrador"></i>
                            </div>

                            <div class="info_cobrador">
                                <h4 class="modal_title">Cobrador asignado:</h4>
                                <p class="p_texto_cobrador" id="txtCobradorAsignado"><b></b></p>
                                <p class="p_texto" id="txtDNICobrador"></p>
                            </div>
                        </div>

                        <div class="modal_body_link_generado_resumen">
                            <div class="link_generado">
                                <div class="icono_link_generado">
                                    <i data-lucide="link" class="icono_link"></i>
                                </div>
                                
                                <div class="content_link_generado">
                                    <h4 class="modal_title">Link de acceso (unico):</h4>
                                    <div class="modal_input_link_generado">
                                        <input type="text" id="txtToken" value="" readonly>

                                        <button id="btnCopiar">
                                            <i data-lucide="copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="valido_hasta">
                                <div class="icono_valido_hasta">
                                    <i data-lucide="clock" class="icono_valido"></i>
                                </div>
                                <div class="info_valido_hasta">
                                    <h4 class="modal_title">Valido hasta:</h4>
                                    <p class="p_texto" id="txtFechaVencimiento"></p>
                                </div>
                            </div>

                            <div class="duracion">
                                <div class="icono_duracion_link">
                                    <i data-lucide="shield" class="icono_duracion"></i>
                                </div>

                                <div class="info_duracion">
                                    <h4 class="modal_title">Duración:</h4>
                                    <p class="p_texto" id="txtDuracion"></p>
                                </div>
                            </div>
                        </div>

                        <div class="modal_body_link_generado_aviso">
                            <div class="icono_aviso_link_generado">
                                <i data-lucide="triangle-alert" class="icono_warning"></i>
                            </div>

                            <div class="aviso_link_generado">
                                <p class="p_texto">Compartí este enlace únicamente con el cobrador asignado.</p>
                                <p class="p_texto">Por seguridad, el link dejará de funcionar al vencer.</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal_footer_link_generado">
                        <button class="modal_boton_cancelar">Cancelar</button>
                        <button class="modal_boton_accion" id="btnCopiarLink">Copiar link</button>
                    </div>
                </div>
            </section>

            <section class="modal modal_plantilla_impresion" id="modalPlantillaImpresion">
                <div class="modal_content_plantilla_impresion">
                    <div class="modal_header_plantilla_impresion">
                        <h2 class="modal_title_header">Planilla de impresión</h2>
                        <h2 class="modal_close">X</h2>
                    </div>

                    <div class="modal_data_plantilla_impresion">
                        <h2>Planilla de cobranza del bimestre: Julio - Agosto 2026</h2>
                    </div>

                    <div class="modal_body_plantilla_impresion">
                        <div class="modal_table_content_planilla_impresion">
                            <table>
                                <tr>
                                    <th>Nº Socio</th>
                                    <th>Apellido</th>
                                    <th>Nombre</th>
                                    <th>DNI</th>
                                    <th>Telefono</th>
                                    <th>Barrio</th>
                                    <th>Calle</th>
                                    <th>Altura</th>
                                </tr>

                                <tr>
                                    <td>1001</td>
                                    <td>Gallotto</td>
                                    <td>Santiago</td>
                                    <td>47.823.673</td>
                                    <td>3562502652</td>
                                    <td>Gandolfo</td>
                                    <td>General la Valle</td>
                                    <td>153</td>
                                </tr>

                                <tr>
                                    <td>1001</td>
                                    <td>Gallotto</td>
                                    <td>Santiago</td>
                                    <td>47.823.673</td>
                                    <td>3562502652</td>
                                    <td>Gandolfo</td>
                                    <td>General la Valle</td>
                                    <td>153</td>
                                </tr>

                                <tr>
                                    <td>1001</td>
                                    <td>Gallotto</td>
                                    <td>Santiago</td>
                                    <td>47.823.673</td>
                                    <td>3562502652</td>
                                    <td>Gandolfo</td>
                                    <td>General la Valle</td>
                                    <td>153</td>
                                </tr>

                                <tr>
                                    <td>1001</td>
                                    <td>Gallotto</td>
                                    <td>Santiago</td>
                                    <td>47.823.673</td>
                                    <td>3562502652</td>
                                    <td>Gandolfo</td>
                                    <td>General la Valle</td>
                                    <td>153</td>
                                </tr>

                                <tr>
                                    <td>1001</td>
                                    <td>Gallotto</td>
                                    <td>Santiago</td>
                                    <td>47.823.673</td>
                                    <td>3562502652</td>
                                    <td>Gandolfo</td>
                                    <td>General la Valle</td>
                                    <td>153</td>
                                </tr>

                                <tr>
                                    <td>1001</td>
                                    <td>Gallotto</td>
                                    <td>Santiago</td>
                                    <td>47.823.673</td>
                                    <td>3562502652</td>
                                    <td>Gandolfo</td>
                                    <td>General la Valle</td>
                                    <td>153</td>
                                </tr>

                                <tr>
                                    <td>1001</td>
                                    <td>Gallotto</td>
                                    <td>Santiago</td>
                                    <td>47.823.673</td>
                                    <td>3562502652</td>
                                    <td>Gandolfo</td>
                                    <td>General la Valle</td>
                                    <td>153</td>
                                </tr>

                                <tr>
                                    <td>1001</td>
                                    <td>Gallotto</td>
                                    <td>Santiago</td>
                                    <td>47.823.673</td>
                                    <td>3562502652</td>
                                    <td>Gandolfo</td>
                                    <td>General la Valle</td>
                                    <td>153</td>
                                </tr>

                                <tr>
                                    <td>1001</td>
                                    <td>Gallotto</td>
                                    <td>Santiago</td>
                                    <td>47.823.673</td>
                                    <td>3562502652</td>
                                    <td>Gandolfo</td>
                                    <td>General la Valle</td>
                                    <td>153</td>
                                </tr>

                                <tr>
                                    <td>1001</td>
                                    <td>Gallotto</td>
                                    <td>Santiago</td>
                                    <td>47.823.673</td>
                                    <td>3562502652</td>
                                    <td>Gandolfo</td>
                                    <td>General la Valle</td>
                                    <td>153</td>
                                </tr>
                            </table>
                        </div>

                        <div class="modal_data_table_planilla_impresion">
                            <p>Ordenado por barrio (A - Z)</p>
                        </div>
                        
                    </div>

                    <div class="modal_footer_plantilla-impresion">
                        <button class="modal_boton_cancelar">Cancelar</button>
                        <button class="modal_boton_accion" id="btnImprimirPlantilla">Imprimir</button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>