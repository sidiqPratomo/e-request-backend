<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditrailResource;
use Illuminate\Http\Request;

class AuditrailController extends Controller
{
    public function index(Request $request)
    {
        $logsDir = storage_path('logs');
        $listFiles = scandir($logsDir);
        $auditrailLogs = array_filter($listFiles, fn ($file) => strpos($file, 'auditrail-') === 0);

        $search = $request->input('!search');
        if ($search) {
            $auditrailLogs = array_filter($auditrailLogs, fn ($file) => strpos($file, $search) !== false);
        }

        $sort = $request->input('sort', 'desc');
        if ($sort === 'asc') {
            sort($auditrailLogs);
        } else {
            rsort($auditrailLogs);
        }

        $skip = $request->input('!skip', 0);
        $limit = $request->input('!limit', 10);
        $auditrailLogs = array_slice($auditrailLogs, $skip, $limit);

        $formattedLogs = AuditrailResource::collection($auditrailLogs);

        return response()->json([
            'data' => [
                'result' => $formattedLogs,
                'count' => count($auditrailLogs),
            ],
        ]);
    }

    public function download(Request $request)
    {
        $nameFile = $request->query('name_file');

        $path = storage_path("logs/{$nameFile}");

        if (! file_exists($path)) {
            return response()->json(['message' => 'Log file not found'], 404);
        }

        return response()->download($path);
    }
}
