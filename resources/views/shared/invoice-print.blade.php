<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $order->invoice_number }}</title>

    <style>
        :root { --ink:#0f172a; --muted:##0186DB; --line:#e2e8f0; --bg:#f8fafc; --brand:#0194F3; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: Arial, Helvetica, sans-serif; background: var(--bg); color: var(--ink); padding: 20px; }
        .wrap { max-width: 860px; margin: 0 auto; }
        .toolbar {
            display:flex; gap:10px; justify-content:flex-end; align-items:center;
            margin: 0 0 14px;
        }
        .btn {
            display:inline-flex; align-items:center; justify-content:center;
            padding:10px 14px; border-radius:12px; text-decoration:none; font-weight:700; font-size:13px;
            border:1px solid var(--line); background:#fff; color:var(--ink);
        }
        .btn-primary { background: var(--brand); border-color: var(--brand); color:#fff; }
        .card { background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; }
        .head { padding:16px 18px; background:#0186DB; color:#fff; }
        .head .title { font-weight:800; font-size:18px; }
        .head .meta { margin-top:6px; font-size:12px; opacity:.9; }

        .content { padding: 18px; }

        .badge {
            display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:800;
            border:1px solid rgba(255,255,255,.25); background: rgba(255,255,255,.12);
        }

        @media print {
            body { background:#fff; padding:0; }
            .toolbar { display:none !important; }
            .wrap { max-width: 100%; }
            .card { border: none; border-radius: 0; }
            .head { border-radius: 0; }
            a { color: inherit; text-decoration: none; }
        }
    </style>
</head>
<body>
<div class="wrap">

    <div class="toolbar">
        <a href="javascript:window.print()" class="btn btn-primary">Cetak Invoice</a>

        {{-- tombol kembali: otomatis balik ke halaman sebelumnya --}}
        <a href="javascript:history.back()" class="btn">Kembali</a>
    </div>

    <div class="card">
        <div class="head">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                <div>
                    <div class="title">Bintang Wisata</div>
                    <div class="meta">
                        Invoice: <b>{{ $order->invoice_number }}</b>
                        <span style="opacity:.75;">•</span>
                        Tanggal: {{ optional($order->created_at)->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="badge">
                    {{ strtoupper($order->type ?? '-') }}
                </div>
            </div>
        </div>

        <div class="content">
            {{-- reuse template detail yang udah ada (biar konsisten) --}}
            @include('emails.partials.order_full_detail', ['order' => $order])

            <div style="margin-top:14px; font-size:12px; color: var(--muted);">
                Dicetak dari sistem Bintang Wisata.
            </div>
        </div>
    </div>

</div>
</body>
</html>
