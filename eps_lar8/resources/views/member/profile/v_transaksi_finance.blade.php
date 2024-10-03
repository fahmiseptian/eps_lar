<div class="transactions-container">
    <h3 class="transactions-title">Transaksi Saya</h3>

    <ul class="nav nav-tabs" id="transactionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">Semua</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">Belum Bayar</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">Sudah Bayar</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">Pesanan Dibatalkan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="jatuh_tempo-tab" data-bs-toggle="tab" data-bs-target="#jatuh_tempo" type="button" role="tab" aria-controls="jatuh_tempo" aria-selected="false">Sudah Jatuh Tempo</button>
        </li>
    </ul>

    <div class="tab-content" id="transactionTabsContent">
        @php
        $statuses = [
        'all' => ['sudah_bayar', 'belum_bayar', 'Menunggu_Konfirmasi_PPK', 'pesanan_dibatalkan', 'sudah_jatuh_tempo', 'menunggu_pengecekan_pembayaran'],
        'pending' => ['belum_bayar'],
        'approved' => ['sudah_bayar', 'menunggu_pengecekan_pembayaran'],
        'rejected' => ['pesanan_dibatalkan'],
        'jatuh_tempo' => ['sudah_jatuh_tempo'],
        ];
        @endphp

        @foreach($statuses as $tabId => $statusList)
        <div class="tab-pane fade {{ $tabId === 'all' ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
            <div class="transaction-list">
                @php
                $filteredTransactions = $transactions->filter(function($group) use ($statusList) {
                return collect($group)->contains(function($transaction) use ($statusList) {
                return in_array($transaction->status_pembayaran, $statusList);
                });
                });
                @endphp
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Performa Invoice</th>
                            <th scope="col">Pemohon</th>
                            <th scope="col">Total Transaksi</th>
                            <th scope="col">Status</th>
                            <th scope="col">Tanggal Tagihan</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    @forelse($filteredTransactions as $invoice => $group)
                    <tbody>
                        <tr>
                            <td>{{ $invoice }}</td>
                            <td>{{ $group[0]->nama }}</td>
                            <td>Rp {{ number_format($group[0]->total, 0, ',', '.')  }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $group[0]->status_pembayaran)) }}</td>
                            <td>{{ $group[0]->batas_pembayaran_top ? $group[0]->batas_pembayaran_top : '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary detail-transaction"
                                    data-id="{{ $group[0]->id }}">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <th scope="row" colspan="7">Tidak ada transaksi</th>
                        </tr>
                        @endforelse
                </table>
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