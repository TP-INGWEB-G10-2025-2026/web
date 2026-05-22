<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Bienvenue</title></head>
<body style="font-family: sans-serif; padding: 24px; color: #333;">
    <h2>Bienvenue, {{ $teacher->name }} !</h2>
    <p>Votre compte enseignant a été créé avec succès.</p>
    <p>Voici vos identifiants de connexion :</p>
    <table style="border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="padding: 8px 16px 8px 0;"><strong>Email :</strong></td>
            <td style="padding: 8px 0;">{{ $teacher->email }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 16px 8px 0;"><strong>Mot de passe :</strong></td>
            <td style="padding: 8px 0;">{{ $plainPassword }}</td>
        </tr>
    </table>
    <p style="color: #e53e3e;">Veuillez changer votre mot de passe après votre première connexion.</p>
</body>
</html>
