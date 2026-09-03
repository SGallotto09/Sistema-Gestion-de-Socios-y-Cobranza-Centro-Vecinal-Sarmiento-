export class LinkAccesoApi {
    async registerLinkAcceso(idCobrador, duracionToken) {
        const response = await fetch('http://localhost/Proyecto/BACKEND/controllers/LinkAccesoController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                idCobrador: idCobrador,
                duracionToken: duracionToken
            })
        });

        const linkAcceso = await response.json();

        if (!response.ok) {
            alert(linkAcceso.message);
            return;
        }

        return linkAcceso;
    }
}