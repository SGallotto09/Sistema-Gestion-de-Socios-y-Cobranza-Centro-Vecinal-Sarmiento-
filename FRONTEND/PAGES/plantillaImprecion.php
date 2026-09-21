<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Listado de Socios</title>

    <link rel="stylesheet" href="../CSS/plantillaImprecion.css">
</head>

<body>
    <div class="contenedor">
        <div class="volver">
            <a href="cobranza.php" id="btnVolver">
                <span class="flecha">←</span>
                <span>Volver</span>
            </a>
        </div>

        <header class="encabezado">
            <h1>Centro Vecinal Sarmiento</h1>
            <h2>Listado de Socios</h2>

            <div class="informacion">
                <span id="fecha"></span>
            </div>
        </header>

        <main>
            <table>
                <thead>
                    <tr>
                        <th>Nº Socio</th>
                        <th>Apellido</th>
                        <th>Nombre</th>
                        <th>DNI</th>
                        <th>Teléfono</th>
                        <th>Barrio</th>
                        <th>Calle</th>
                        <th>Altura</th>
                    </tr>
                </thead>

                <tbody id="tablaSocios">
                    <!-- JavaScript agrega los socios -->
                </tbody>
            </table>
        </main>

        <div class="acciones">
            <button id="btnImprimir">Imprimir</button>
        </div>
    </div>

    <script src="../JS/controllers/PlantillaImprecionController.js" type="module"></script>
</body>

</html>