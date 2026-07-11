<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Halaman Dalam Perbaikan</title>
    <style>
        :root {
            color-scheme: light;
            --primary: #198754;
            --primary-dark: #166534;
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
        <div class="notice-icon" aria-hidden="true">
            <svg width="38" height="38" viewBox="0 0 24 24" fill="none" role="img" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.7 6.3a4.5 4.5 0 0 0-5.1 5.8L4.9 16.8a2 2 0 0 0 2.8 2.8l4.7-4.7a4.5 4.5 0 0 0 5.8-5.1l-2.8 2.8-2-2 2.8-2.8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7.4 17.4h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
            </svg>
        </div>
        <h1 id="notice-title">Halaman yang anda tuju sedang dalam perbaikan/pengembangan</h1>
        <p>Silakan kembali ke halaman utama atau coba lagi beberapa saat lagi.</p>
        <div class="actions">
            <a href="{{ url('/') }}">Ke Halaman Utama</a>
            <button type="button" onclick="history.back()">Kembali</button>
        </div>
    </main>
</body>
</html>
