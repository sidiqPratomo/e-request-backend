<?php

namespace App\Services\Report;

use App\Services\Report\Impl\CsvReportServiceImpl;
use InvalidArgumentException;

class ReportServiceFactory
{
    public function create(string $type): ReportServiceInterface
    {
        switch ($type) {
            case 'csv':
                return new CsvReportServiceImpl();
            default:
                throw new InvalidArgumentException("Unsupported report type: $type", 400);
        }
    }
}
