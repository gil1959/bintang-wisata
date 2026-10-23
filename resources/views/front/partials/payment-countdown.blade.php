@php
    $isEn = app()->getLocale() === 'en';
    $remainingSeconds = $order->payment_remaining_seconds ?? 0;
    $deadline = $order->payment_deadline;
    $isExpired = $order->is_payment_expired || ($order->order_status === 'rejected' && $order->payment_status === 'failed');
@endphp

@if($order->payment_status === 'waiting_payment' || $order->payment_status === 'unpaid')
<div x-data="{
        seconds: {{ $remainingSeconds }},
        isExpired: {{ $isExpired ? 'true' : 'false' }},
        timer: null,
        formattedHours: '00',
        formattedMinutes: '00',
        formattedSeconds: '00',
        updateTime() {
            if (this.seconds <= 0) {
                this.isExpired = true;
                this.formattedHours = '00';
                this.formattedMinutes = '00';
                this.formattedSeconds = '00';
                if (this.timer) clearInterval(this.timer);
                return;
            }
            const h = Math.floor(this.seconds / 3600);
            const m = Math.floor((this.seconds % 3600) / 60);
            const s = this.seconds % 60;
            this.formattedHours = String(h).padStart(2, '0');
            this.formattedMinutes = String(m).padStart(2, '0');
            this.formattedSeconds = String(s).padStart(2, '0');
        },
        init() {
            this.updateTime();
            if (!this.isExpired && this.seconds > 0) {
                this.timer = setInterval(() => {
                    this.seconds--;
                    this.updateTime();
                    if (this.seconds <= 0) {
                        this.isExpired = true;
                        clearInterval(this.timer);
                        // Optional reload after expiry to sync backend cancellation state
                        setTimeout(() => { window.location.reload(); }, 2500);
                    }
                }, 1000);
            }
        }
    }"
    class="w-full">

    {{-- State: Masih ada waktu pembayaran --}}
    <div x-show="!isExpired" class="rounded-2xl border border-amber-300 bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 p-4 sm:p-5 shadow-sm text-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 shrink-0 rounded-xl bg-amber-500/10 border border-amber-400/30 flex items-center justify-center text-amber-600 mt-0.5">
                    <i data-lucide="clock" class="w-5 h-5 animate-pulse"></i>
                </div>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-amber-800">
                        {{ $isEn ? 'Payment Time Limit' : 'Batas Waktu Pembayaran' }}
                    </div>
                    <div class="text-xs text-amber-900/80 mt-0.5">
                        {{ $isEn ? 'Please complete your payment before the timer expires.' : 'Selesaikan pembayaran sebelum batas waktu berakhir.' }}
                        <span class="block sm:inline font-semibold">
                            (Maks. 6 jam sejak pesanan dibuat)
                        </span>
                    </div>
                    @if($deadline)
                    <div class="text-[11px] text-slate-600 mt-1">
                        {{ $isEn ? 'Expires on:' : 'Batas akhir:' }}
                        <span class="font-bold text-slate-800">{{ $deadline->format('d M Y, H:i') }} WIB</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Live Countdown Boxes --}}
            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0 bg-white/80 backdrop-blur border border-amber-200/80 px-3 py-2 rounded-xl shadow-xs">
                <div class="text-center min-w-[38px]">
                    <span class="block text-lg font-black text-amber-700 tracking-tight" x-text="formattedHours">00</span>
                    <span class="block text-[9px] font-bold text-slate-500 uppercase">{{ $isEn ? 'Hrs' : 'Jam' }}</span>
                </div>
                <span class="text-base font-black text-amber-400 -mt-3">:</span>
                <div class="text-center min-w-[38px]">
                    <span class="block text-lg font-black text-amber-700 tracking-tight" x-text="formattedMinutes">00</span>
                    <span class="block text-[9px] font-bold text-slate-500 uppercase">{{ $isEn ? 'Min' : 'Mnt' }}</span>
                </div>
                <span class="text-base font-black text-amber-400 -mt-3">:</span>
                <div class="text-center min-w-[38px]">
                    <span class="block text-lg font-black text-amber-700 tracking-tight text-rose-600" x-text="formattedSeconds">00</span>
                    <span class="block text-[9px] font-bold text-slate-500 uppercase">{{ $isEn ? 'Sec' : 'Dtk' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- State: Waktu habis / Expired --}}
    <div x-show="isExpired" x-cloak class="rounded-2xl border border-rose-300 bg-rose-50 p-4 sm:p-5 shadow-sm text-rose-900">
        <div class="flex items-start gap-3">
            <div class="h-10 w-10 shrink-0 rounded-xl bg-rose-500/10 border border-rose-300 flex items-center justify-center text-rose-600">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-sm font-extrabold text-rose-900">
                    {{ $isEn ? 'Payment Time Expired (Order Cancelled)' : 'Waktu Pembayaran Habis (Pesanan Dibatalkan)' }}
                </div>
                <div class="text-xs text-rose-700 mt-1 leading-relaxed">
                    {{ $isEn 
                        ? 'This order has been automatically cancelled because payment was not completed within 6 hours. Please make a new booking.' 
                        : 'Pesanan ini otomatis dibatalkan karena tidak dibayar dalam batas waktu 6 jam. Silakan lakukan pemesanan baru jika ingin melanjutkan.' }}
                </div>
            </div>
        </div>
    </div>
</div>
@elseif($order->payment_status === 'failed' || $order->order_status === 'rejected')
<div class="rounded-2xl border border-rose-300 bg-rose-50 p-4 sm:p-5 shadow-sm text-rose-900 w-full">
    <div class="flex items-start gap-3">
        <div class="h-10 w-10 shrink-0 rounded-xl bg-rose-500/10 border border-rose-300 flex items-center justify-center text-rose-600">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
        </div>
        <div>
            <div class="text-sm font-extrabold text-rose-900">
                {{ $isEn ? 'Order Cancelled' : 'Pesanan Dibatalkan' }}
            </div>
            <div class="text-xs text-rose-700 mt-1 leading-relaxed">
                {{ $isEn 
                    ? 'This order has been cancelled (expired/unpaid). You cannot complete payment for this invoice.' 
                    : 'Pesanan ini telah dibatalkan (kedaluwarsa atau belum dibayar dalam batas waktu 6 jam). Anda tidak dapat melakukan pembayaran untuk invoice ini.' }}
            </div>
        </div>
    </div>
</div>
@endif
