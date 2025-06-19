<?php

namespace App\Services\Report\Impl;

use App\Services\Report\ReportServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvReportServiceImpl implements ReportServiceInterface
{
    public function export(array $headers, array $body)
    {
        $filename = date('Ymd') . '.csv';
        $response = new StreamedResponse(function () use ($headers, $body) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($body as $row) {
                $bodyRow = array_map(function ($head) use ($row) {
                    return $row[$head];
                }, $headers);
                fputcsv($handle, $bodyRow);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
