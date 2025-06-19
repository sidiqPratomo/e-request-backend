<?php

namespace App\Services\Report;

interface ReportServiceInterface
{
    public function export(array $headers, array $body);
}
