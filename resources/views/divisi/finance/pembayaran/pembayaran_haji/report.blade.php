<!DOCTYPE html>
<html>
<head>
	<title>Membuat Laporan PDF Dengan DOMPDF Laravel</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}

        @page {
            margin: 0.8cm 0.8cm;
        }

        body {
            margin: 0.9cm 0.9cm;
        }
	</style>
 
	<table class='table table-sm table-borderless' style="width: 100%;">
        <tr>
            <th colspan="7" class="text-center align-middle">
                <h2 class="font-weight-bold">Laporan Pembayaran Haji Keberangkatan 2025</h2>
            </th>
        </tr>
        @if (count($data) > 0)
            @for ($i = 0; $i < count($data); $i++)
                <tr>
                    <th colspan="7" class="border-top border-left border-right">Nama : {{ $data[$i]['jemaah_name'] }}</th>
                </tr>
                <tr>
                    <th colspan="7" class="border-left border-right">No. BPIH : {{  $data[$i]['no_bpih'] }}</th>
                </tr>
                <tr>
                    <th colspan="7" class="border-bottom border-left border-right">Room : {{ $data[$i]['hj_paket'] }}</th>
                </tr>
                <tr>
                    <th colspan="7">&nbsp;</th>
                </tr>
                <tr>
                    <th class="text-center align-middle" style="width: 15%;">&nbsp;</th>
                    <th class="text-center align-middle border-top border-left">Tgl. Bayar</th>
                    <th class="text-center align-middle border-top">Metode Bayar</th>
                    <th class="text-center align-middle border-top">Nama Bank</th>
                    <th class="text-center align-middle border-top">No. Rekening</th>
                    <th class="text-center align-middle border-top">Jumlah Bayar</th>
                    <th class="text-center align-middle border-top border-right">Kurensi</th>
                </tr>
                @php
                    $total_bayar    = 0;
                @endphp
                @for ($j = 0; $j < count($data[$i]['detail_bayar']); $j++)
                    @php 
                        $detail_bayar   = $data[$i]['detail_bayar'];
                    @endphp
                    <tr>
                        <td class="text-left align-middle border">Pembayaran Ke {{ $detail_bayar[$j]['seq'] }}</td>
                        <td class="text-center align-middle border">{{ date('d-m-Y', strtotime($detail_bayar[$j]['payment_date'])) }}</td>
                        <td class="text-center align-middle border">{{ $detail_bayar[$j]['payment_method'] }}</td>
                        <td class="text-left align-middle border">{{ $detail_bayar[$j]['payment_bank_account'] }}</td>
                        <td class="text-left align-middle border">{{ $detail_bayar[$j]['payment_bank_account_number'] }}</td>
                        <td class="text-right align-middle border">{{ number_format($detail_bayar[$j]['payment_total'], 2) }}</td>
                        <td class="text-center align-middle border">{{ $detail_bayar[$j]['payment_currency'] }}</td>
                    </tr>
                    @php
                        $total_bayar    += (int) $detail_bayar[$j]['payment_total'];
                    @endphp
                @endfor
                <tr>
                    <th colspan="5" class="text-right align-middle">Total : </th>
                    <th class="text-right align-middle">@php echo number_format($total_bayar, 2); @endphp</th>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <th colspan="7">&nbsp;</th>
                </tr>
            @endfor
        @endif
	</table>
 
</body>
</html>