<style>
    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    .table thead {
        background-color: #f2f2f2;
    }

    .table th,
    .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .table th {
        font-weight: bold;
        color: #333;
    }

    .table tr:hover {
        background-color: #f1f1f1;
    }

    .fa-map-marker {
        color: #007bff;
        /* Ubah warna ikon */
    }
</style>

<table class="table">
    <thead>
        @if ($id_courier == 4)
        @else
        <tr>
            <th>Track ID</th>
            <th>Deskripsi</th>
            <th>Date/Time</th>
        </tr>
        @endif
    </thead>
    <tbody>
        @if ($id_courier == 0)
        @if ($tracking->delivery_start != null)
        <tr>
            <td style="text-align: left;"><span class="fa fa-map-marker"> On Progress </span></td>
            <td style="text-align: left;">Pesanan Dalam Pengiriman</td>
            <td style="text-align: left; color: gray;">{{ $tracking->delivery_start }}</td>
        </tr>
        @endif
        @if ($tracking->delivery_end != null)
        <tr>
            <td style="text-align: left;"><span class="fa fa-map-marker"> Complete </span></td>
            <td style="text-align: left;">Pesanan Sudah Sampai</td>
            <td style="text-align: left; color: gray;">{{ $tracking->delivery_end }}</td>
        </tr>
        @endif
        @elseif ($id_courier == 6)
        @foreach ($tracking as $track)
        <tr>
            <td style="text-align: left; font-weight: bold;">{{ $track['tracking_id'] }}</td>
            <td style="text-align: left;">{{ $track['description'] }}</td>
            <td style="text-align: left; color: gray;">{{ \Carbon\Carbon::parse($track['create_date'])->format('d-m-Y H:i') }}</td>
        </tr>
        @endforeach
        @elseif ($id_courier == 4)
        @foreach ($tracking as $track)
        <tr>
            <td style="text-align: left; font-weight: bold;">{{ $track['TRACKING_ID'] }}</td>
            <td style="text-align: left;">{{ $track['TRACKING_DESC'] }}</td>
            <td style="text-align: left; color: gray;">{{ $track['TRACKING_DATE'] }} {{ $track['TRACKING_TIME'] }}</td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>