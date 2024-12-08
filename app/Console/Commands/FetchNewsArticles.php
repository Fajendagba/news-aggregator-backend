<?php

namespace App\Console\Commands;

use App\Services\NewsAggregatorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchNewsArticles extends Command
{
    protected $signature = 'news:fetch {--category=} {--source=}';
    protected $description = 'Fetch news articles from all sources';

    public function handle(NewsAggregatorService $aggregator)
    {
        $this->info('Starting news fetch...');

        try {
            $params = [
                'category' => $this->option('category'),
                'source' => $this->option('source'),
            ];

            $aggregator->aggregateNews($params);
            $this->info('News articles fetched successfully!');
        } catch (\Exception $e) {
            Log::error('Error in news fetch command', ['error' => $e->getMessage()]);
            $this->error('Error fetching news articles: ' . $e->getMessage());
        }
    }
}
