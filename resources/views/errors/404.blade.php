<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Halaman Tidak Ditemukan</title>
    <style>
        :root {
            color-scheme: light;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: var(--bg);
            color: var(--text);
            font-family: "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .notice {
            width: min(100%, 520px);
            padding: 34px 28px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
            box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08);
            text-align: center;
        }

        .notice-code {
            margin-bottom: 14px;
            color: var(--primary);
            font-size: 0.92rem;
            font-weight: 750;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .notice-icon {
            width: 76px;
            height: 76px;
            display: inline-grid;
            place-items: center;
            margin-bottom: 20px;
            border-radius: 50%;
            background: color-mix(in srgb, var(--primary) 14%, white);
            color: var(--primary-dark);
        }

        h1 {
            margin: 0 0 10px;
            font-size: 1.45rem;
            line-height: 1.35;
            font-weight: 750;
        }

        p {
            margin: 0;
            color: var(--muted);
            font-size: 0.98rem;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        a,
        button {
            appearance: none;
            border: 0;
            border-radius: 10px;
            padding: 10px 16px;
            font: inherit;
            font-weight: 650;
            text-decoration: none;
            cursor: pointer;
        }

        a {
            background: var(--primary);
            color: #ffffff;
        }

        button {
            background: #eef2f7;
            color: var(--text);
        }
    </style>
</head>
<body>
    <main class="notice" role="main" aria-labelledby="notice-title">
        <div class="notice-code">404</div>
        <div class="notice-icon" aria-hidden="true">
            <svg width="38" height="38" viewBox="0 0 24 24" fill="none" role="img" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.8 5.2a7 7 0 1 1-2.5 2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M4 4l5.8 5.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M11 11h4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M11 14h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </div>
        <h1 id="notice-title">Halaman tidak ditemukan</h1>
        <p>Alamat yang anda buka tidak tersedia atau sudah dipindahkan.</p>
        <div class="actions">
            <a href="{{ url('/') }}">Ke Halaman Utama</a>
            <button type="button" onclick="history.back()">Kembali</button>
        </div>
    </main>
</body>
</html>
