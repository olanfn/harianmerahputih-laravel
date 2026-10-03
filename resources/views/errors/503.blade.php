<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sedang Dalam Pemeliharaan · Harian Merah Putih</title>
    <style>
        :root {
            color-scheme: light;
            --red: #b3132b;
            --red-dark: #850d20;
            --ink: #1b1d21;
            --muted: #686d75;
            --line: #e5e7eb;
            --paper: #ffffff;
            --canvas: #f4f5f7;
        }

        * { box-sizing: border-box; }

        html, body { min-height: 100%; }

        body {
            display: grid;
            min-height: 100vh;
            min-height: 100svh;
            margin: 0;
            place-items: center;
            background:
                radial-gradient(circle at 15% 15%, rgba(179, 19, 43, .08), transparent 30rem),
                var(--canvas);
            padding: 20px;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
        }

        .maintenance-card {
            width: min(100%, 680px);
            overflow: hidden;
            border: 1px solid var(--line);
            border-top: 5px solid var(--red);
            border-radius: 12px;
            background: var(--paper);
            box-shadow: 0 20px 55px rgba(27, 29, 33, .09);
        }

        .maintenance-brand {
            display: flex;
            min-height: 112px;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--line);
            padding: 22px 28px;
        }

        .maintenance-brand img {
            display: block;
            width: min(100%, 390px);
            height: auto;
            max-height: 82px;
            object-fit: contain;
        }

        .maintenance-content {
            padding: clamp(30px, 7vw, 58px);
            text-align: center;
        }

        .maintenance-kicker {
            display: block;
            margin-bottom: 13px;
            color: var(--red);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(30px, 7vw, 50px);
            letter-spacing: -.035em;
            line-height: 1.05;
        }

        .maintenance-message {
            max-width: 520px;
            margin: 22px auto 0;
            color: var(--muted);
            font-size: clamp(15px, 2.5vw, 17px);
            line-height: 1.75;
        }

        .maintenance-thanks {
            margin: 27px 0 0;
            color: var(--red-dark);
            font-size: 13px;
            font-weight: 700;
        }

        @media (max-width: 480px) {
            body { padding: 12px; }
            .maintenance-card { border-radius: 9px; }
            .maintenance-brand { min-height: 92px; padding: 17px 20px; }
            .maintenance-brand img { max-height: 64px; }
            .maintenance-content { padding: 32px 22px 36px; }
        }
    </style>
</head>
<body>
    <main class="maintenance-card" role="status" aria-labelledby="maintenance-title">
        <div class="maintenance-brand">
            <img src="/branding/logo-primary.png" alt="Harian Merah Putih" width="390" height="98">
        </div>
        <div class="maintenance-content">
            <span class="maintenance-kicker">Harian Merah Putih</span>
            <h1 id="maintenance-title">Sedang Dalam Pemeliharaan</h1>
            <p class="maintenance-message">Kami sedang melakukan peningkatan sistem untuk memberikan pengalaman yang lebih baik. Silakan kembali beberapa saat lagi.</p>
            <p class="maintenance-thanks">Terima kasih atas pengertiannya.</p>
        </div>
    </main>
</body>
</html>
