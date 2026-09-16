export class PagosApi {
    async getCantidadDePagos() {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/PagoController.php');

        const cantidadPagos = await response.json();

        return cantidadPagos;
    }

    async registerPago(idCuota) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/PagoController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_cuota: idCuota
            })
        });

        const data = await response.json();

        if (!response.ok) {
            alert(data.message);
            return null;
        }

        return data;
    }
}