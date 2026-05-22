
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:sans-serif;padding:24px;color:#333;">
    <h2 style="color:#c84b2f;"> Réservation rejetée</h2>
    <p>Bonjour <strong>{{ $reservation->user->name }}</strong>,</p>
    <p>Votre demande de réservation du <strong>{{ $reservation->start_date->format('d/m/Y') }}</strong> au <strong>{{ $reservation->end_date->format('d/m/Y') }}</strong> a été <strong>rejetée</strong>.</p>
    @if($reservation->rejection_reason)
    <p><strong>Raison :</strong> {{ $reservation->rejection_reason }}</p>
    @endif
    <p>Vous pouvez soumettre une nouvelle demande pour une autre période.</p>
</body>
</html>
