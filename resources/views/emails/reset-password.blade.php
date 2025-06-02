<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe - Le Mans Ultimate Stats</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .header {
            background: #0B5AB8;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="racing" patternUnits="userSpaceOnUse" width="20" height="20"><rect width="10" height="10" fill="rgba(255,255,255,0.1)"/><rect x="10" y="10" width="10" height="10" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23racing)"/></svg>') repeat;
            opacity: 0.1;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% {
                transform: translateX(-50px);
            }

            100% {
                transform: translateX(50px);
            }
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            position: relative;
            z-index: 1;
        }

        .content {
            padding: 40px 30px;
            background: #101223;
        }

        .greeting {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: white;
        }

        .message {
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.7;
            color: white;
        }

        .cta-container {
            text-align: center;
            margin: 40px 0;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(125deg, rgb(62, 25, 50) 0%, rgb(66, 21, 76) 50%, rgb(15, 34, 69) 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .cta-button:hover::before {
            left: 100%;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .info-box {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
            color: black;
        }

        .info-box .icon {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .warning {
            background: linear-gradient(135deg, #fff8f0 0%, #ffeaa7 20%);
            border-left: 4px solid #f39c12;
            padding: 20px;
            color: #8b4513;
        }

        .footer {
            background: #EE234D;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #eee;
        }

        .footer-text {
            color: white;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-links a {
            margin: 0 10px;
            color: #0B5AB8;
            text-decoration: none;
            font-weight: 500;
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #667eea, transparent);
            margin: 30px 0;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 12px;
            }

            .header,
            .content,
            .footer {
                padding: 20px;
            }

            .greeting {
                font-size: 20px;
            }

            .cta-button {
                padding: 14px 28px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">🏁 Le Mans Ultimate Stats App</div>
            <div class="header-subtitle">Votre plateforme de statistiques racing</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Bonjour {{ $user->first_name ?? 'Pilote' }} ! 👋
            </div>

            <div class="message">
                Vous avez demandé la réinitialisation de votre mot de passe pour votre compte Le Mans Ultimate Stats
                App.
                Pas de panique, cela arrive aux meilleurs d'entre nous !
            </div>

            <div class="info-box">
                <div class="icon">🔐</div>
                <strong>Sécurité avant tout :</strong><br>
                Ce lien est sécurisé et expire automatiquement dans <strong>{{ $expireTime }} minutes</strong>
                pour protéger votre compte.
            </div>

            <div class="cta-container">
                <a href="{{ $resetUrl }}" class="cta-button">
                    🔑 Réinitialiser mon mot de passe
                </a>
            </div>

            <div class="divider"></div>

            <div class="message">
                Si le bouton ne fonctionne pas, vous pouvez copier et coller ce lien dans votre navigateur :
            </div>

            <div
                style="background: #f8f9fa; padding: 15px; border-radius: 8px; word-break: break-all; font-family: monospace; font-size: 14px; color: #495057; margin: 20px 0;">
                {{ $resetUrl }}
            </div>
        </div>

        <!-- Warning -->
        <div class="warning">
            <strong>⚠️ Important :</strong> Si vous n'avez pas demandé cette réinitialisation,
            ignorez simplement cet email. Votre mot de passe actuel reste inchangé et sécurisé.
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">
                Cet email a été envoyé par <strong>Le Mans Ultimate Stats App</strong><br>
                Votre passion pour la course, nos statistiques de pointe ! 🏎️
            </div>

            <div class="social-links">
                <a href="mailto:lemansultimatestatsapp@gmail.com">📧 Support</a>
            </div>

            <div class="footer-text" style="margin-top: 20px; font-size: 12px;">{{ date('Y') }} Le Mans
                Ultimate Stats App
            </div>
        </div>
    </div>
</body>

</html>