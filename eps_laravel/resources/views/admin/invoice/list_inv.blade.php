<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        DataTable Example
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Invoice</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Pemeriksa</th>
                    <th>Status Pajak</th>
                    <th>Pelapor</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>No Invoice</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Pemeriksa</th>
                    <th>Status Pajak</th>
                    <th>Pelapor</th>
                    <th>Action</th>
                </tr>
            </tfoot>
            <tbody>
                @php $i = 1 @endphp
                @foreach ($data as $item)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $item->invoice }}</td>
                    <td>Rp. {{ number_format($item->total, 0, ',', '.') }}</td>

                    <td>
                        
                        <?php
                         if ($item->status == "complete_payment") {
                            echo "Selesai";
                         }
                         elseif ($item->status == "cencel") {
                            echo "Batal";
                         }
                         elseif ($item->status == "pending") {
                            echo "Belum Dibayar";
                         }
                         elseif ($item->status == "on_delivery") {
                            echo "Dalam Pengiriman";
                         }
                         elseif ($item->status == "waiting_approve_by_ppk") {
                            echo "Menunggu Persetujuan";
                         }
                         elseif ($item->status == "expired") {
                            echo "Expired";
                         }
                         elseif ($item->status == "cancel_part") {
                            echo "Batals";
                         }else {
                            echo $item->status;
                         }
                        ?>
                    </td>
                    <td>
                        @if ($item->user)
                        {{ $item->user->username }}
                        @else
                            {{-- Ini Kalau data kosong --}}
                        @endif
                    </td>
                    <td>
                    <?php

                        if ($item->status_pelaporan_pajak == '2') {
                            echo "Dilaporkan semua";
                        }
    
                        if ($item->status_pelaporan_pajak == '1') {
                            echo "Dilaporkan sebagian";
                        }
    
                        if ($item->status_pelaporan_pajak == '0') {
                            echo "Belum Dilaporkan";
                        } 
                    ?>
                    </td>
                    <td>{{ $item->pelapor_pajak }}</td>
                    <td><button></button></td>
                    
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>