export class VisitaApi {
    async getCantidadVisitas(idCuota) {
        const response = await fetch(`http://localhost/Proyecto/BACKEND/controllers/VisitaController.php?idCuota=${idCuota}`);

        const cantidadVisitas = await response.json();

        if (!response.ok) {
            return false;
        }

        return cantidadVisitas;
    }

    async createVisita(_idCuota) {
        const response = await fetch('http://localhost/Proyecto//BACKEND/controllers/VisitaController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                idCuota: _idCuota
            })
        });

        const visitaCreada = await response.json();

        if (!response.ok) {
            alert(visitaCreada.message);
            return;
        }

        return visitaCreada;
    }
}