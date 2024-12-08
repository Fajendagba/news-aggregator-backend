<?php

namespace App\Services\NewsServices;

use App\Contracts\NewsSourceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NYTimesService implements NewsSourceInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.nytimes.key');
        $this->baseUrl = config('services.nytimes.base_url');
    }

    public function fetchArticles(array $params = []): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/search/v2/articlesearch.json", [
                'api-key' => $this->apiKey,
                'q' => $params['query'] ?? null,
                'fq' => $params['category'] ? "news_desk:(\"{$params['category']}\")" : null,
            ]);

            if ($response->failed()) {
                Log::error('NYTimes API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            return $this->formatArticles($response->json()['response']['docs']);
        } catch (\Exception $e) {
            Log::error('NYTimes API error', ['error' => $e->getMessage()]);
            return [];
        }
    }

    protected function formatArticles(array $articles): array
    {
        return array_map(function ($article) {
            return [
                'title' => $article['headline']['main'],
                'description' => $article['abstract'],
                'content' => $article['lead_paragraph'],
                'author' => $article['byline']['original'] ?? 'Unknown',
                'url' => $article['web_url'],
                'image_url' => isset($article['multimedia'][0]) ?
                    "https://www.nytimes.com/{$article['multimedia'][0]['url']}" : null,
                'published_at' => $article['pub_date'],
                'source_name' => 'The New York Times',
                'category' => $article['news_desk'],
            ];
        }, $articles);
    }
}
