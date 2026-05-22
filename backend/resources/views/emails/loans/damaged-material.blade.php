<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <title>Matériel endommagé</title>
  <style>
    body { font-family: sans-serif; padding: 24px; color: #333; background: #f4f4f4; }
    .wrapper { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; }
    .header { background: #c84b2f; padding: 24px 32px; }
    .header h1 { color: white; font-size: 20px; }
    .body { padding: 32px; }
    table { width: 100%; border-collapse: collapse; margin: 16px 0; }
    td { padding: 10px 14px; border-bottom: 1px solid #f0ece4; font-size: 14px; }
    td:first-child { color: #8a8278; width: 40%; }
    .footer { background: #f5f0e8; padding: 16px 32px; font-size: 12px; color: #8a8278; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <h1> Signalement matériel {{ $loan->return_status->value === 'lost' ? 'perdu' : 'endommagé' }}</h1>
    </div>
    <div class="body">
      <p>Un matériel a été retourné avec un problème signalé.</p>
      <table>
        <tr><td>Matériel</td><td><strong>{{ $loan->material->name }}</strong></td></tr>
        <tr><td>État signalé</td><td><strong>{{ $loan->return_status->label() }}</strong></td></tr>
        <tr><td>Retourné par</td><td>{{ $loan->user->name }} ({{ $loan->user->email }})</td></tr>
        <tr><td>Date de retour</td><td>{{ $loan->actual_return_date->format('d/m/Y') }}</td></tr>
        @if($loan->notes)
        <tr><td>Notes</td><td>{{ $loan->notes }}</td></tr>
        @endif
      </table>
      <p>Veuillez vérifier l'état du matériel et prendre les mesures nécessaires.</p>
    </div>
    <div class="footer">EduPlatform — Notification automatique</div>
  </div>
</body>
</html>
