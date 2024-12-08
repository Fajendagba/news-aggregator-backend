<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // Validate request parameters
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'category' => 'nullable|string|max:50',
            'sources' => 'nullable|string', // comma-separated IDs
            'author' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate cache key based on request parameters
        $cacheKey = 'articles:' . md5(json_encode($request->all()));

        return Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Article::with('source');

            // Apply search filter
            if ($search = $request->input('search')) {
                $query->whereFullText(['title', 'description', 'content'], $search);
            }

            // Apply date filter
            if ($dateFrom = $request->input('date_from')) {
                $query->where('published_at', '>=', $dateFrom);
            }

            if ($dateTo = $request->input('date_to')) {
                $query->where('published_at', '<=', $dateTo);
            }

            // Apply category filter
            if ($category = $request->input('category')) {
                $query->where('category', $category);
            }

            // Apply source filter
            if ($sources = $request->input('sources')) {
                $query->whereIn('source_id', explode(',', $sources));
            }

            // Apply author filter
            if ($author = $request->input('author')) {
                $query->where('author', 'like', "%{$author}%");
            }

            // Order by published date
            $query->orderBy('published_at', 'desc');

            return response()->json([
                'data' => $query->paginate($request->input('per_page', 15)),
                'meta' => [
                    'available_categories' => Article::distinct('category')->pluck('category'),
                    'available_sources' => Source::all(['id', 'name']),
                ]
            ]);
        });
    }
}
