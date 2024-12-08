<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Source;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{
    protected $sources;

    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    public function aggregateNews(array $params = []): void
    {
        foreach ($this->sources as $source) {
            try {
                $articles = $source->fetchArticles($params);
                $this->saveArticles($articles);
            } catch (\Exception $e) {
                Log::error('Error aggregating news', [
                    'source' => get_class($source),
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
    }

    protected function saveArticles(array $articles): void
    {
        foreach ($articles as $articleData) {
            try {
                $source = Source::firstOrCreate(
                    ['name' => $articleData['source_name']],
                    ['slug' => Str::slug($articleData['source_name'])]
                );

                Article::updateOrCreate(
                    ['url' => $articleData['url']],
                    [
                        'title' => $articleData['title'],
                        'description' => $articleData['description'],
                        'content' => $articleData['content'],
                        'author' => $articleData['author'],
                        'image_url' => $articleData['image_url'],
                        'published_at' => $articleData['published_at'],
                        'source_id' => $source->id,
                        'category' => $articleData['category'],
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Error saving article', [
                    'article' => $articleData['url'],
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
    }
}
