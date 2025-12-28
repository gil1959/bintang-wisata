@php
  $paymentLabel = match($order->payment_status) {
    'waiting_payment' => 'Menunggu Pembayaran',
    'waiting_verification' => 'Menunggu Verifikasi',
    'paid' => 'Lunas',
    'failed' => 'Gagal',
    default => $order->payment_status
  };

  $orderLabel = match($order->order_status) {
    'pending' => 'Pending',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    default => $order->order_status
  };

  $typeLabel = $order->type === 'tour'
  ? 'Tour'
  : ($order->type === 'rent_car' ? 'Rent Car' : 'Sewa Kapal');

  $departure = $order->departure_date ? $order->departure_date->translatedFormat('d F Y') : '-';
  $pickup    = $order->pickup_date ? $order->pickup_date->translatedFormat('d F Y') : '-';
  $return    = $order->return_date ? $order->return_date->translatedFormat('d F Y') : '-';

  $latestPayment = $order->payments?->sortByDesc('id')->first();
@endphp

{{-- INFO PESANAN --}}
<div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
  <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
    Info Pesanan
  </div>

  <div style="padding:14px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">Produk</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;"><b>{{ $order->product_name }}</b></td>
      </tr>

      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Tipe</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $typeLabel }}</td>
      </tr>

      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Customer</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">
          <b>{{ $order->customer_name }}</b><br>
          <span style="font-size:12px; color:#64748b;">{{ $order->customer_email }}</span>
        </td>
      </tr>

      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Telepon</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $order->customer_phone }}</td>
      </tr>

      <tr>
        <td style="padding:8px 0; color:#475569;">Status</td>
        <td style="padding:8px 0;">
          <span style="display:inline-block; padding:6px 10px; border:1px solid #e2e8f0; border-radius:999px; font-size:12px; font-weight:bold;">
            Payment: {{ $paymentLabel }}
          </span>
          <span style="display:inline-block; padding:6px 10px; border:1px solid #e2e8f0; border-radius:999px; font-size:12px; font-weight:bold; margin-left:6px;">
            Order: {{ $orderLabel }}
          </span>
        </td>
      </tr>
    </table>
  </div>
</div>

{{-- JADWAL / TANGGAL --}}
<div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
  <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
    Jadwal / Tanggal
  </div>

  <div style="padding:14px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
      @if($order->type === 'tour' || $order->type === 'umrah')
  <tr>
    <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">
      {{ $order->type === 'umrah' ? 'Tanggal Booking' : 'Keberangkatan' }}
    </td>
    <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $departure }}</td>
  </tr>
  <tr>
    <td style="padding:8px 0; color:#475569;">Partisipan</td>
    <td style="padding:8px 0;">
      {{ $order->participants ? number_format($order->participants,0,',','.') . ' orang' : '-' }}
    </td>
  </tr>
@elseif($order->type === 'rent_car')
  <tr>
    <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">Pickup</td>
    <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $pickup }}</td>
  </tr>
  <tr>
    <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Return</td>
    <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $return }}</td>
  </tr>
  <tr>
    <td style="padding:8px 0; color:#475569;">Durasi</td>
    <td style="padding:8px 0;">{{ $order->total_days ? $order->total_days . ' hari' : '-' }}</td>
  </tr>
@endif

@if($order->type === 'ship')
  <tr>
    <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">Tanggal Sewa</td>
    <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">
      {{ $order->departure_date ? $order->departure_date->format('d M Y') : '-' }}
    </td>
  </tr>
  <tr>
    <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">Qty</td>
    <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">
      {{ $order->participants ?? 1 }}
    </td>
  </tr>
@endif


    </table>
  </div>
</div>

{{-- RINGKASAN PEMBAYARAN --}}
<div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
  <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
    Ringkasan Pembayaran
  </div>

  <div style="padding:14px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">Subtotal</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
      </tr>
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Diskon</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
      </tr>
      <tr>
        <td style="padding:8px 0; color:#475569;">Total</td>
        <td style="padding:8px 0; font-size:16px;"><b>Rp {{ number_format($order->final_price, 0, ',', '.') }}</b></td>
      </tr>

      <tr>
        <td style="padding:8px 0; color:#475569;">Metode Pembayaran</td>
        <td style="padding:8px 0;">{{ $order->payment_method ?: '-' }}</td>
      </tr>

      @if($latestPayment && $latestPayment->payment_url)
        <tr>
          <td style="padding:8px 0; color:#475569;">Link Pembayaran</td>
          <td style="padding:8px 0;">
            <a href="{{ $latestPayment->payment_url }}" target="_blank" style="font-weight:bold; color:#0f172a;">
              Buka Link Pembayaran
            </a>
          </td>
        </tr>
      @endif
    </table>
  </div>
</div>

{{-- RIWAYAT PAYMENT --}}
<div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
  <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
    Riwayat Payment
  </div>

  <div style="padding:14px;">
    @if($order->payments && $order->payments->count())
      @foreach($order->payments->sortByDesc('id') as $pay)
        <div style="border:1px solid #e2e8f0; border-radius:12px; padding:12px; margin-bottom:10px;">
          <div style="font-size:13px; color:#0f172a;">
            <div><b>Metode:</b> {{ $pay->method }}</div>
            <div><b>Amount:</b> Rp {{ number_format($pay->amount,0,',','.') }}</div>
            <div><b>Status:</b> {{ $pay->status }}</div>

            @if($pay->gateway_name)
              <div style="font-size:12px; color:#475569; margin-top:4px;">
                <b>Gateway:</b> {{ $pay->gateway_name }}
              </div>
            @endif

            @if($pay->gateway_reference)
              <div style="font-size:12px; color:#475569; margin-top:4px;">
                <b>Gateway Ref:</b> {{ $pay->gateway_reference }}
              </div>
            @endif

            <div style="font-size:12px; color:#64748b; margin-top:6px;">
              {{ optional($pay->created_at)->format('d/m/Y H:i') }}
            </div>

            @if($pay->proof_image)
              <div style="margin-top:8px;">
                <a href="{{ url('storage/'.$pay->proof_image) }}"
                   target="_blank"
                   style="display:inline-block; padding:8px 10px; border:1px solid #e2e8f0; border-radius:10px; text-decoration:none; font-weight:bold; font-size:12px; color:#0f172a;">
                  Lihat Bukti
                </a>
              </div>
            @endif
          </div>
        </div>
      @endforeach
    @else
      <div style="padding:12px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:12px; color:#475569; font-size:13px;">
        Belum ada data payment.
      </div>
    @endif
  </div>
</div>
