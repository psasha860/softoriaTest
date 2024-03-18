<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Competitor;
use Illuminate\Support\Facades\Log;

class CompetitorController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'targets' => 'required|array',
            'exclude_targets' => 'required|array',
        ]);

        $login = env('DATAFORSEO_LOGIN');
        $password = env('DATAFORSEO_PASSWORD');
        $credentials = base64_encode("{$login}:{$password}");

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type' => 'application/json',
        ])->post('https://api.dataforseo.com/v3/backlinks/domain_intersection/live', [
            [
                'targets' => $validated['targets'],
                'include_subdomains' => false,
                'exclude_targets' => $validated['exclude_targets'],
                'limit' => 5,
                'order_by' => ["1.backlinks,desc"],
                'exclude_internal_backlinks' => true,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $competitorsData = [];
            Log::info('Received request data:', $data); 
        if (!empty($data['tasks']) && isset($data['tasks'][0]['result'])) {
            $competitorsData = [];
            foreach ($data['tasks'] as $task) {
                if (!empty($task['result'])) {
                    foreach ($task['result'] as $result) {
                        if (!empty($result['items'])) {
                            foreach ($result['items'] as $item) {
                                foreach ($item['domain_intersection'] as $intersection) {
                                    foreach ($validated['exclude_targets'] as $excludedTarget) {
                                        // Підготовка даних для масового вставлення
                                        $competitorsData[] = [
                                            'target_domain' => $intersection['target'],
                                            'referring_domain' => $intersection['referring_domains'],
                                            'excluded_target' => $excludedTarget,
                                            'rank' => $intersection['rank'],
                                            'backlinks' => $intersection['backlinks'],
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // Масове вставлення даних одним запитом
            Competitor::insert($competitorsData);
            
            
            return response()->json(['message' => 'Success', 'type'=>'success', 'data' => $competitorsData]);
            } else {
                Log::error('Unexpected response structure from Domain Intersection API');
                return response()->json(['message' => 'Unexpected response structure from Domain Intersection API', 'type'=>'error'], 500);
            }
        } else {
            Log::error('Failed to fetch data from Domain Intersection API: ' . $response->body());
            return response()->json(['message' => 'Failed to fetch data from Domain Intersection API', 'type'=>'error'], 500);
        }
        
    }
}
