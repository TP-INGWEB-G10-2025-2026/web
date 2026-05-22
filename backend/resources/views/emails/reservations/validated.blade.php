
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:sans-serif;padding:24px;color:#333;">
    <h2 style="color:#2d9c6e;"> Réservation validée</h2>
    <p>Bonjour <strong>{{ $reservation->user->name }}</strong>,</p>
    <p>Votre demande de réservation a été <strong>validée</strong>.</p>
    <table style="border-collapse:collapse;margin:16px 0;width:100%;">
        <tr><td style="padding:8px;border:1px solid #ddd;"><strong>Matériel</strong></td><td style="padding:8px;border:1px solid #ddd;">{{ $reservation->material->name }}</td></tr>
        <tr><td style="padding:8px;border:1px solid #ddd;"><strong>Catégorie</strong></td><td style="padding:8px;border:1px solid #ddd;">{{ $reservation->material->category->name }}</td></tr>
        <tr><td style="padding:8px;border:1px solid #ddd;"><strong>Du</strong></td><td style="padding:8px;border:1px solid #ddd;">{{ $reservation->start_date->format('d/m/Y') }}</td></tr>
        <tr><td style="padding:8px;border:1px solid #ddd;"><strong>Au</strong></td><td style="padding:8px;border:1px solid #ddd;">{{ $reservation->end_date->format('d/m/Y') }}</td></tr>
    </table>
    <p>Vous pouvez récupérer votre matériel dès la date de début.</p>
</body>
</html>
