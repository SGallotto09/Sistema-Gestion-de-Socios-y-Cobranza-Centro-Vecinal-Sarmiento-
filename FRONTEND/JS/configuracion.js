document.addEventListener('DOMContentLoaded', iniciarConfiguracion);

function iniciarConfiguracion() {
    lucide.createIcons();

    // VARIABLES
    const tarjetasTema = document.querySelectorAll(".tema-card");

    // COMPORTAMIENTOS

    tarjetasTema.forEach(tarjeta => {
        tarjeta.addEventListener("click", () => {
            tarjetasTema.forEach(t => t.classList.remove("activa"));
            tarjeta.classList.add("activa");
        });
    });
}