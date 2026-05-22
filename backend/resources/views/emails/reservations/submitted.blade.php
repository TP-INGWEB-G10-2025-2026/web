<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Réservation soumise</title></head>
<body style="font-family:sans-serif;padding:24px;color:#333;">
    <h2>Demande de réservation reçue</h2>
    <p>Bonjour <strong>{{ $reservation->user->name }}</strong>,</p>
    <p>Votre demande de réservation a bien été soumise et est en attente de validation.</p>
    <table style="border-collapse:collapse;margin:16px 0;width:100%;">
        <tr>
            <td style="padding:8px;border:1px solid #ddd;"><strong>Date de début</strong></td>
            <td style="padding:8px;border:1px solid #ddd;">{{ $reservation->start_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td style="padding:8px;border:1px solid #ddd;"><strong>Date de fin</strong></td>
            <td style="padding:8px;border:1px solid #ddd;">{{ $reservation->end_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td style="padding:8px;border:1px solid #ddd;"><strong>Statut</strong></td>
            <td style="padding:8px;border:1px solid #ddd;">En attente</td>
        </tr>
    </table>
    <p>Vous serez notifié(e) dès que votre demande sera traitée par un administrateur.</p>
</body>
</html>
