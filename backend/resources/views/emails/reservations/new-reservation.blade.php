<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nouvelle réservation</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; color: #333; }
    .wrapper { max-width: 600px; margin: 32px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
    .header { background: #0f0e0c; padding: 32px; text-align: center; }
    .header h1 { color: #f5f0e8; font-size: 22px; font-weight: 600; }
    .header p  { color: #8a8278; font-size: 13px; margin-top: 6px; }
    .badge { display: inline-block; background: #c84b2f; color: white; padding: 4px 14px; border-radius: 20px; font-size: 12px; margin-top: 12px; }
    .body { padding: 32px; }
    .body h2 { font-size: 18px; margin-bottom: 20px; color: #0f0e0c; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .info-table td { padding: 12px 16px; border-bottom: 1px solid #f0ece4; font-size: 14px; }
    .info-table td:first-child { color: #8a8278; font-weight: 500; width: 40%; }
    .info-table tr:last-child td { border-bottom: none; }
    .info-table tr { background: #faf8f4; }
    .info-table tr:nth-child(even) { background: white; }
    .cta { text-align: center; margin: 24px 0; }
    .cta a { display: inline-block; background: #0f0e0c; color: white; padding: 13px 32px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; }
    .cta a:hover { background: #c84b2f; }
    .footer { background: #f5f0e8; padding: 20px 32px; text-align: center; }
    .footer p { font-size: 12px; color: #8a8278; line-height: 1.6; }
    .divider { height: 1px; background: #f0ece4; margin: 20px 0; }
    .highlight { background: #fef9f0; border-left: 3px solid #b8922a; padding: 12px 16px; border-radius: 0 8px 8px 0; margin-bottom: 20px; font-size: 13px; color: #6b5a2a; }
  </style>
</head>
<body>
  <div class="wrapper">

    <div class="header">
      <h1>🎓 GestMat</h1>
      <p>Système de gestion des réservations</p>
      <span class="badge">Nouvelle demande</span>
    </div>

    <div class="body">
      <h2>Une nouvelle demande nécessite votre attention</h2>

      <div class="highlight">
         Cette demande est en attente de validation. Veuillez vérifier la disponibilité des matériels et traiter cette demande dès que possible.
      </div>

      <table class="info-table">
        <tr>
          <td> Enseignant</td>
          <td><strong>{{ $reservation->user->name }}</strong></td>
        </tr>
        <tr>
          <td>Email</td>
          <td>{{ $reservation->user->email }}</td>
        </tr>
        @if($reservation->user->phone)
        <tr>
          <td> Téléphone</td>
          <td>{{ $reservation->user->phone }}</td>
        </tr>
        @endif
        <tr>
          <td> Date de début</td>
          <td><strong>{{ $reservation->start_date->format('d/m/Y') }}</strong></td>
        </tr>
        <tr>
          <td> Date de fin</td>
          <td><strong>{{ $reservation->end_date->format('d/m/Y') }}</strong></td>
        </tr>
        <tr>
          <td> Durée</td>
          <td>{{ $reservation->start_date->diffInDays($reservation->end_date) }} jour(s)</td>
        </tr>
        <tr>
          <td> Référence</td>
          <td style="font-family:monospace;font-size:12px;">{{ $reservation->id }}</td>
        </tr>
        <tr>
          <td>Soumise le</td>
          <td>{{ $reservation->created_at->format('d/m/Y à H:i') }}</td>
        </tr>
      </table>

      <div class="cta">
        <a href="{{ config('app.url') }}/admin/reservations/{{ $reservation->id }}">
          Traiter cette demande →
        </a>
      </div>

      <div class="divider"></div>

      <p style="font-size:13px;color:#8a8278;text-align:center;">
        Connectez-vous au tableau de bord pour valider ou rejeter cette demande.
      </p>
    </div>

    <div class="footer">
      <p>Cet email a été envoyé automatiquement par GestMat.<br/>
      Ne pas répondre directement à cet email.</p>
    </div>

  </div>
</body>
</html>
