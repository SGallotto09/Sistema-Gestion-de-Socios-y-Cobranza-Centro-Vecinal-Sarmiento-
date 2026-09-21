export class CuotasApi {
    async getCantidadCuotasSinPagar() {
        const response = await fetch('/Proyecto/BACKEND/controllers/CuotaController.php')

        const cantidadCuotasSinPagar = await response.json();

        return cantidadCuotasSinPagar;
    }
}