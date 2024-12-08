<?php

namespace App\Services\NewsServices;

use App\Contracts\NewsSourceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsAPIService implements NewsSourceInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
        $this->baseUrl = config('services.newsapi.base_url');
    }

    public function fetchArticles(array $params = []): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/top-headlines", [
                'apiKey' => $this->apiKey,
                'country' => $params['country'] ?? 'us',
                'category' => $params['category'] ?? null,
                'q' => $params['query'] ?? null,
                'pageSize' => 100,
            ]);

            if ($response->failed()) {
                Log::error('NewsAPI request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            return $this->formatArticles($response->json()['articles']);
        } catch (\Exception $e) {
            Log::error('NewsAPI error', ['error' => $e->getMessage()]);
            return [];
        }
    }

    protected function formatArticles(array $articles): array
    {
        return array_map(function ($article) {
            return [
                'title' => $article['title'] ?? '',
                'description' => $article['description'] ?? '',
                'content' => $article['content'] ?? '',
                'author' => $article['author'] ?? 'Unknown',
                'url' => $article['url'],
                'image_url' => $article['urlToImage'] ?? null,
                'published_at' => $article['publishedAt'],
                'source_name' => $article['source']['name'],
                'category' => $article['category'] ?? null,
            ];
        }, $articles);
    }
}
