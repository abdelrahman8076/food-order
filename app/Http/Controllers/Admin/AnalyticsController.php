<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analytics)
    {
    }

    public function index(Request $request)
    {
        $range = $this->analytics->resolveDateRange(
            $request->input('preset'),
            $request->input('from'),
            $request->input('to'),
        );

        $from = $range['from'];
        $to = $range['to'];
        $preset = $range['preset'];

        return view('admin.analytics.index', [
            'preset' => $preset,
            'from' => $from,
            'to' => $to,
            'fromInput' => $from->toDateString(),
            'toInput' => $to->toDateString(),
            'summary' => $this->analytics->summary($from, $to),
            'revenueByDay' => $this->analytics->revenueByDay($from, $to),
            'topItemsByQuantity' => $this->analytics->topItemsByQuantity($from, $to),
            'topItemsByRevenue' => $this->analytics->topItemsByRevenue($from, $to),
            'ordersByDeliveryArea' => $this->analytics->ordersByDeliveryArea($from, $to),
            'revenueByCategory' => $this->analytics->revenueByCategory($from, $to),
            'topCustomers' => $this->analytics->topCustomers($from, $to),
            'couponStats' => $this->analytics->couponStats($from, $to),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $range = $this->analytics->resolveDateRange(
            $request->input('preset'),
            $request->input('from'),
            $request->input('to'),
        );

        $from = $range['from'];
        $to = $range['to'];
        $revenueByDay = $this->analytics->revenueByDay($from, $to);
        $topItems = $this->analytics->topItemsByQuantity($from, $to);

        $filename = 'analytics-' . $from->toDateString() . '-to-' . $to->toDateString() . '.csv';

        return response()->streamDownload(function () use ($revenueByDay, $topItems, $from, $to) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Analytics Export', $from->toDateString(), 'to', $to->toDateString()]);
            fputcsv($handle, []);
            fputcsv($handle, ['Daily Revenue']);
            fputcsv($handle, ['Date', 'Revenue', 'Orders']);

            foreach ($revenueByDay as $row) {
                fputcsv($handle, [$row['date'], number_format($row['revenue'], 2, '.', ''), $row['orders']]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Top Items by Quantity']);
            fputcsv($handle, ['Item', 'Quantity Sold']);

            foreach ($topItems as $row) {
                fputcsv($handle, [$row['name'], $row['quantity']]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
