<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status Pembayaran - {{ $order->invoice_number }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f8fafc; padding:24px;">
  <div style="max-width: 680px; margin: 0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">
    <div style="padding:18px 20px; background:#0f172a; color:#ffffff;">
      <div style="font-size:18px; font-weight:bold;">Bintang Wisata</div>
      <div style="font-size:12px; opacity:0.9;">Invoice: {{ $order->invoice_number }}</div>
    </div>

    <div style="padding:20px; color:#0f172a;">
      @if($isAdminCopy)
        <p style="margin:0 0 12px; padding:10px 12px; background:#fff7ed; border:1px solid #fed7aa; border-radius:12px;">
          Ini salinan notifikasi untuk admin.
        </p>
      @endif

      <p style="margin:0 0 12px;">Halo <b>{{ $isAdminCopy ? 'Admin' : $order->customer_name }}</b>,</p>

      @if($result === 'approved')
        <p style="margin:0 0 16px;">
          Pembayaran untuk invoice <b>{{ $order->invoice_number }}</b> sudah <b>terverifikasi</b>.
          Status pesanan kini <b>Lunas</b>.
        </p>
      @else
        <p style="margin:0 0 16px;">
          Pembayaran untuk invoice <b>{{ $order->invoice_number }}</b> <b>ditolak</b>.
          Silakan cek kembali bukti transfer / nominal transfer, lalu hubungi admin.
        </p>
      @endif

      <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
        <tr>
          <td style="padding:10px 0; border-bottom:1px solid #e2e8f0; width:40%; color:#475569;">Produk</td>
          <td style="padding:10px 0; border-bottom:1px solid #e2e8f0;"><b>{{ $order->product_name }}</b></td>
        </tr>
        <tr>
          <td style="padding:10px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Total</td>
          <td style="padding:10px 0; border-bottom:1px solid #e2e8f0;">
            <b>Rp {{ number_format($order->final_price, 0, ',', '.') }}</b>
          </td>
        </tr>
        <tr>
          <td style="padding:10px 0; color:#475569;">Status</td>
          <td style="padding:10px 0;">
            <b>
              {{ $result === 'approved' ? 'Lunas / Approved' : 'Gagal / Rejected' }}
            </b>
          </td>
        </tr>
      </table>

      <div style="margin-top:18px; padding:12px 14px; background:#f1f5f9; border-radius:12px;">
        <div style="font-size:12px; color:#475569;">Langkah Selanjutnya</div>
        <div style="font-size:14px;">
          @if($result === 'approved')
            @if($isAdminCopy)
              Order sudah approved. Tidak ada tindakan lanjutan.
            @else
              Terima kasih. Tim kami akan memproses pesanan Anda.
            @endif
          @else
            @if($isAdminCopy)
              Pastikan alasan penolakan sudah jelas dan komunikasikan ke customer.
            @else
              Silakan hubungi admin untuk bantuan dan konfirmasi ulang pembayaran.
            @endif
          @endif
        </div>
      </div>

      <p style="margin:18px 0 0; font-size:12px; color:#64748b;">
        Email ini dikirim otomatis. Jika ada pertanyaan, balas email ini atau hubungi admin.
      </p>
    </div>
  </div>
</body>
</html>
