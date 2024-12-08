<?php

namespace App\Contracts;

interface NewsSourceInterface
{
    public function fetchArticles(array $params = []): array;
}
