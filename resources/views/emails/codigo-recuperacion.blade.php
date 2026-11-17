<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de recuperación | VOID</title>
</head>
<body style="margin:0;padding:32px 12px;background-color:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;margin:0 auto;background-color:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <tr>
            <td style="padding:28px 32px;background-color:#09090b;text-align:center;">
                <div style="font-size:30px;font-weight:800;letter-spacing:8px;color:#f8fafc;">V<span style="color:#3b82f6;">Ø</span>ID</div>
                <p style="margin:12px 0 0;color:#cbd5e1;font-size:12px;letter-spacing:2px;text-transform:uppercase;">Seguridad de tu cuenta</p>
            </td>
        </tr>
        <tr>
            <td style="padding:36px 32px 28px;">
                <h1 style="margin:0 0 16px;font-size:23px;line-height:1.3;color:#111827;">Recuperación de contraseña</h1>
                <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#475569;">Hola, <strong style="color:#111827;">{{ $nombreDestinatario }}</strong>.</p>
                <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#475569;">Recibimos una solicitud para restablecer la contraseña de tu cuenta en VOID. Utiliza el siguiente código para continuar:</p>
                <div style="margin:0 0 24px;padding:22px 16px;border:1px solid #bfdbfe;border-radius:12px;background-color:#eff6ff;text-align:center;">
                    <p style="margin:0 0 8px;font-size:11px;font-weight:700;letter-spacing:2px;color:#1d4ed8;text-transform:uppercase;">Tu código de verificación</p>
                    <div style="font-size:36px;font-weight:800;letter-spacing:10px;color:#111827;">{{ $codigo }}</div>
                </div>
                <p style="margin:0 0 12px;font-size:14px;line-height:1.7;color:#475569;">Este código vence en <strong>15 minutos</strong> y solo puede utilizarse una vez.</p>
                <p style="margin:0;font-size:14px;line-height:1.7;color:#475569;">Si no solicitaste recuperar tu contraseña, ignora este mensaje. Tu contraseña actual seguirá siendo la misma.</p>
            </td>
        </tr>
        <tr>
            <td style="padding:20px 32px;background-color:#f8fafc;border-top:1px solid #e2e8f0;text-align:center;">
                <p style="margin:0;font-size:12px;line-height:1.6;color:#64748b;">Mensaje automático de VOID. Por seguridad, no compartas este código con nadie.</p>
            </td>
        </tr>
    </table>
</body>
</html>
