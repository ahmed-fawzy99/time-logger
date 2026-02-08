<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        @page {
            margin: 60px 50px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #2C2A25;
            font-size: 11px;
            font-weight: normal;
            line-height: 1.6;
            padding: 3rem;
        }

        .header {
            border-bottom: 3px solid{{ $primaryColor }};
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-title {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-weight: bold;
            font-size: 26px;
            color: {{ $primaryColor }};
        }

        .header-info {
            color: #6A665F;
            font-size: 10px;
            font-weight: normal;
            /*line-height: 1.8;*/
            /*margin-top: 4px;*/
        }

        .header-info .name {
            font-size: 13px;
            font-weight: normal;
            color: #2C2A25;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .meta-table td {
            vertical-align: top;
        }

        .meta-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            /*color: #6A665F;*/
            /*font-weight: normal;*/
            /*line-height: 1.8;*/
        }

        .meta-value {
            font-size: 12px;
            font-weight: bold;
            color: #2C2A25;
            /*margin-top: 2px;*/
            /*line-height: 1.8;*/
        }

        /* ── Shared table styles ── */
        .entries-table,
        .totals-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .entries-table thead th,
        .totals-table thead th {
            background-color: {{ $primaryColor }};
            color: #FFFFFF;
            padding: 10px 12px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: bold;
        }

        /* ── Entries table ── */
        .entries-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .entries-table thead th:first-child {
            border-radius: 6px 0 0 0;
        }

        .entries-table thead th:last-child {
            border-radius: 0 6px 0 0;
            text-align: right;
        }

        .entries-table thead th.text-right {
            text-align: right;
        }

        /*.entries-table tbody td {*/
        /*    padding: 9px 12px;*/
        /*    font-size: 10px;*/
        /*    border-top: 1px solid #D6D0BF;*/
        /*    border-left: 1px solid #D6D0BF;*/
        /*}*/

        .entries-table tbody td {
            padding: 9px 12px;
            font-size: 10px;
            /*border-top: 1px solid #D6D0BF;*/
            border-bottom: 1px solid #D6D0BF;
            border-left: 1px solid #D6D0BF;
        }

        .entries-table tbody td:first-child {
            border-left: 1px solid #D6D0BF;
        }

        .entries-table tbody td:last-child {
            border-right: 1px solid #D6D0BF;
        }

        /*.entries-table tbody tr:last-child td {*/
        /*    border-bottom: 1px solid #D6D0BF;*/
        /*}*/

        .entries-table tbody tr:last-child td:first-child {
            border-radius: 0 0 0 6px;
        }

        .entries-table tbody tr:last-child td:last-child {
            border-radius: 0 0 6px 0;
        }

        .entries-table tbody tr:nth-child(even) {
            background-color: #F6F3EC;
        }

        .entries-table tbody td.text-right {
            text-align: right;
        }

        .entries-table tbody td.description {
            color: #6A665F;
            font-size: 10px;
        }

        /* ── Totals table ── */
        .totals-wrapper {
            width: 100%;
            margin-top: 10px;
        }

        .totals-table {
            width: 320px;
            margin-left: auto;
        }

        .totals-table thead th:first-child {
            border-radius: 6px 0 0 0;
        }

        .totals-table thead th:last-child {
            border-radius: 0 6px 0 0;
            text-align: right;
        }

        .totals-table tbody td {
            padding: 9px 12px;
            font-size: 10px;
            font-weight: normal;
            border-top: 1px solid #D6D0BF;
            border-left: 1px solid #D6D0BF;
        }

        .totals-table tbody td:last-child {
            border-right: 1px solid #D6D0BF;
            text-align: right;
        }

        .totals-table tbody tr:last-child td {
            border-bottom: 1px solid #D6D0BF;
        }

        .totals-table tbody tr:last-child td:first-child {
            border-radius: 0 0 0 6px;
        }

        .totals-table tbody tr:last-child td:last-child {
            border-radius: 0 0 6px 0;
        }

        .totals-table tbody tr:nth-child(even) {
            background-color: #F6F3EC;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #6A665F;
            border-top: 1px solid #D6D0BF;
            padding-top: 10px;
        }
    </style>
</head>
<body>
{{-- Header --}}
<div class="header">
    <div class="header-title">{{ $invoiceTitle ?? 'INVOICE' }}</div>
    <div class="header-info">
        @if($invoiceName)
            <div class="name">{{ $invoiceName }}</div>
        @endif
        @if($invoiceAddress)
            <div>{{ $invoiceAddress }}</div>
        @endif
    </div>
</div>

{{-- Meta info --}}
<table class="meta-table">
    <tr>
        <td width="50%">
            <div class="meta-label">Period</div>
            <div class="meta-value">{{ $startDate }} &ndash; {{ $endDate }}</div>
        </td>
        <td width="50%" style="text-align: right;">
            <div class="meta-label">Hourly Rate</div>
            <div class="meta-value">{{ $currency }} {{ number_format($hourlyRate, 2) }}</div>
        </td>
    </tr>
</table>

{{-- Entries table --}}
<table class="entries-table">
    <thead>
    <tr>
        <th style="width: 18%;">Date</th>
        <th style="width: 44%;">Description</th>
        <th class="text-right" style="width: 18%;">Duration</th>
        <th style="text-align: right; width: 20%;">Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach($entries as $entry)
        <tr>
            <td style="font-weight: bold">{{ $entry['work_day'] }}</td>
            <td class="description">{{ $entry['description'] ?: '—' }}</td>
            <td class="text-right">{{ $entry['duration'] }}</td>
            <td class="text-right">{{ $currency }} {{ number_format($entry['amount'], 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- Totals --}}
<div class="totals-wrapper">
    <table class="totals-table">
        <thead>
        <tr>
            <th>Summary</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Total Duration</td>
            <td>{{ $totalDuration }}</td>
        </tr>
        <tr>
            <td>Total Payable ({{ $totalDuration }} &times; {{ $currency }} {{ number_format($hourlyRate, 2) }})</td>
            <td>{{ $currency }} {{ number_format($totalAmount, 2) }}</td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
