<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Cont seller aprobat</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f6f7fb; margin:0; padding:32px;">
    <div style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:18px; padding:32px; border:1px solid #e5e7eb;">
        <h1 style="margin:0 0 16px; font-size:24px; color:#111827;">Contul tau seller a fost aprobat</h1>

        <p style="margin:0 0 16px; color:#4b5563; font-size:15px;">
            Salut {{ $user->name }},
        </p>

        <p style="margin:0 0 16px; color:#4b5563; font-size:15px;">
            Cererea ta pentru magazinul <strong>{{ $shopName }}</strong> a fost aprobata. Mai jos ai datele de acces setate de admin.
        </p>

        <div style="margin:24px 0; padding:18px; border-radius:14px; background:#f9fafb; border:1px solid #e5e7eb;">
            <div style="margin-bottom:8px; color:#6b7280; font-size:13px; text-transform:uppercase; letter-spacing:0.08em;">Email</div>
            <div style="margin-bottom:16px; color:#111827; font-size:16px; font-weight:700;">{{ $user->email }}</div>

            <div style="margin-bottom:8px; color:#6b7280; font-size:13px; text-transform:uppercase; letter-spacing:0.08em;">Parola temporara</div>
            <div style="color:#111827; font-size:18px; font-weight:700;">{{ $plainPassword }}</div>
        </div>

        <p style="margin:0 0 18px; color:#4b5563; font-size:15px;">
            Dupa autentificare, iti recomandam sa schimbi parola din profil pentru siguranta.
        </p>

        <a href="{{ url('/login') }}"
           style="display:inline-block; background:#111827; color:#ffffff; text-decoration:none; padding:14px 22px; border-radius:12px; font-weight:700;">
            Intra in cont
        </a>
    </div>
</body>
</html>
