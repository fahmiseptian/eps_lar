<div class="transactions-container">
    <h3 class="transactions-title">Transaksi Saya</h3>

    <ul class="nav nav-tabs" id="transactionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">Semua</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">Butuh Persetujuan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">Disetujui</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="on_delivery-tab" data-bs-toggle="tab" data-bs-target="#on_delivery" type="button" role="tab" aria-controls="on_delivery" aria-selected="false">Dikirim</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">Ditolak</button>
        </li>
    </ul>

    <div class="tab-content" id="transactionTabsContent">
        @php
        $statuses = [
        'all' => ['Menunggu_Konfirmasi_PPK', 'Menunggu_Konfirmasi_Penjual', 'Dalam_Pengiriman', 'Pesanan_Dibatalkan', 'Packing', 'Selesai', 'Pesanan_Dikembalikan'],
        'pending' => ['Menunggu_Konfirmasi_PPK'],
        'approved' => ['Menunggu_Konfirmasi_Penjual'],
        'on_delivery' => ['Dalam_Pengiriman'],
        'rejected' => ['Pesanan_Dibatalkan']
        ];
        //$statuses = [
        //'all' => ['Menunggu Konfirmasi PPK', 'Menunggu Konfirmasi Penjual', 'Dalam Pengiriman', 'Pesanan Dibatalkan', 'Pesanan Selesai', 'Belum Di Bayar', 'Sudah Di Bayar', 'Menunggu Konfirmasi Pembayaran'],
        //'pending' => ['Menunggu Konfirmasi PPK'],
        //'approved' => ['Menunggu Konfirmasi Penjual'],
        //'on_delivery' => ['Dalam Pengiriman'],
        //'rejected' => ['Pesanan Dibatalkan']
        //];
        @endphp

        @foreach($statuses as $tabId => $statusList)
        <div class="tab-pane fade {{ $tabId === 'all' ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
            <div class="transaction-list">
                @php
                $filteredTransactions = $transactions->filter(function($group) use ($statusList) {
                return collect($group)->contains(function($transaction) use ($statusList) {
                return in_array($transaction->status, $statusList);
                });
                });
                @endphp

                @forelse($filteredTransactions as $invoice => $group)
                <div class="transaction-group">
                    <div class="transaction-group-header">
                        <div class="transaction-group-header-left">
                            <h4>{{ $invoice }}</h4>
                            <p>Tanggal: {{ \Carbon\Carbon::parse($group[0]->created_date)->format('d M Y') }}</p>
                        </div>
                        <p class="transaction-status status-{{ $group[0]->status_invoice }}">
                            {{ ucfirst(str_replace('_', ' ', $group[0]->status_invoice)) }}
                        </p>
                    </div>
                    @foreach($group as $transaction)
                    <div class="transaction-item">
                        <div class="transaction-header">
                            <span class="transaction-invoice">{{ $transaction->invoice }} - {{ $transaction->id_cart_shop }}</span>
                            <span class="transaction-status status-{{ $transaction->status }}">{{ ucfirst(str_replace('_', ' ', $transaction->status)) }}</span>
                        </div>
                        <div class="transaction-subheader">
                            <span class="transaction-shop"> {{ $transaction->nama_pt }}</span>
                        </div>
                        <div class="transaction-body">
                            @foreach($transaction->items as $item)
                            @php
                            $requiresBaseUrl = strpos($item->image, 'http') === false;
                            $pc_image = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $item->image : $item->image;
                            @endphp
                            <div class="transaction-product">
                                <img src="{{ $pc_image }}" alt="{{ $item->nama }}" class="product-image">
                                <div class="product-info-profile">
                                    <h5 class="product-name">{{ $item->nama }}</h5>
                                    <p class="product-details">
                                        <span class="product-quantity">{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                    </p>
                                </div>
                                <div class="product-total-profile">
                                    <p>Rp {{ number_format($item->qty * $item->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="transaction-footer">
                            <div class="transaction-info">
                                &nbsp;
                            </div>
                            <div class="transaction-actions">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="transaction-group-footer">
                        <p>Total Transaksi: Rp {{ number_format($transaction->total, 0, ',', '.')  }}</p>

                        <button class="btn btn-sm btn-primary detail-transaction"
                            data-id="{{ $group[0]->id }}">
                            Detail
                        </button>
                    </div>
                </div>
                @empty
                <p>Tidak ada transaksi.</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var triggerTabList = [].slice.call(document.querySelectorAll('#transactionTabs button'))
        triggerTabList.forEach(function(triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)

            triggerEl.addEventListener('click', function(event) {
                event.preventDefault()
                tabTrigger.show()
            })
        })
    });
</script>