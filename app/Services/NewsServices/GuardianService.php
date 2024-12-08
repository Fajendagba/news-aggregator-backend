<?php

namespace App\Services\NewsServices;

use App\Contracts\NewsSourceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianService implements NewsSourceInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.guardian.key');
        $this->baseUrl = config('services.guardian.base_url');
    }

    public function fetchArticles(array $params = []): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/search", [
                'api-key' => $this->apiKey,
                'section' => $params['category'] ?? null,
                'q' => $params['query'] ?? null,
                'page-size' => 100,
                'show-fields' => 'all',
            ]);

            if ($response->failed()) {
                Log::error('Guardian API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            return $this->formatArticles($response->json()['response']['results']);
        } catch (\Exception $e) {
            Log::error('Guardian API error', ['error' => $e->getMessage()]);
            return [];
        }
    }

    protected function formatArticles(array $articles): array
    {
        return array_map(function ($article) {
            return [
                'title' => $article['webTitle'],
                'description' => $article['fields']['trailText'] ?? '',
                'content' => $article['fields']['bodyText'] ?? '',
                'author' => $article['fields']['byline'] ?? 'Unknown',
                'url' => $article['webUrl'],
                'image_url' => $article['fields']['thumbnail'] ?? null,
                'published_at' => $article['webPublicationDate'],
                'source_name' => 'The Guardian',
                'category' => $article['sectionName'],
            ];
        }, $articles);
    }
}
