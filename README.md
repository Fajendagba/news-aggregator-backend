# News Aggregator Backend

## Overview
This News Aggregator API is built using Laravel, providing a robust backend that pulls articles from various sources and serves them to the frontend application. It implements data aggregation from multiple trusted news sources including NewsAPI, The Guardian, and New York Times.

*Note: User authentication is not included in this implementation.*


## Key Features
- Automated article fetching from multiple news sources
- Efficient local data storage with optimized querying
- RESTful API endpoints with comprehensive filtering options
- Scheduled updates to maintain fresh content
- Full-text search capabilities
- Caching implementation for improved performance

## Installation

1. **Clone the repository:**
    ```sh
    git clone https://github.com/fajendagba/news-aggregator-backend.git
    cd news-aggregator-backend
    ```

2. **Install dependencies:**
    ```sh
    composer install
    ```

3. **Environment Setup:**
    ```sh
    cp .env.example .env
    php artisan key:generate
    ```

4. **Configure Environment:**
    Update `.env` with your database and API credentials:
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password

    # API Keys (Required)
    NEWS_API_KEY=your_newsapi_key_here          # Get from https://newsapi.org
    GUARDIAN_API_KEY=your_guardian_key_here     # Get from https://open-platform.theguardian.com
    NYTIMES_API_KEY=your_nytimes_key_here      # Get from https://developer.nytimes.com
    ```

5. **Run database migrations:**
    ```sh
    php artisan migrate
    ```

6. **Configure Scheduler:**
    Add to your crontab:
    ```sh
    * * * * * cd /path-to-your-project && php artisan schedule:work >> /dev/null 2>&1
    ```

7. **Start the development server:**
    ```sh
    php artisan serve
    ```

## Usage

### Article Fetching
The system supports various fetch commands:
```sh
# Fetch all news
php artisan news:fetch

# Fetch by category
php artisan news:fetch --category=technology

# Fetch by source
php artisan news:fetch --source=guardian

# Fetch specific category from source
php artisan news:fetch --category=technology --source=guardian
```

## API Endpoint Documentation:
**Get Articles**
```sh
    GET /api/v1/articles
```

**Query Parameters:**
```sh
- search: Search term for articles (string)
- date_from: Start date for articles (YYYY-MM-DD)
- date_to: End date for articles (YYYY-MM-DD)
- category: Filter by category (string)
- sources: Comma-separated source IDs (string)
- author: Filter by author name (string)
- per_page: Number of results per page (integer, default: 15)
```

```sh
    ## Example Response:
    {
        "data": {
            "current_page": 1,
            "data": [
                {
                    "id": 1,
                    "title": "Article Title",
                    "description": "Article Description",
                    "content": "Article Content",
                    "author": "John Doe",
                    "url": "https://example.com/article",
                    "image_url": "https://example.com/image.jpg",
                    "published_at": "2024-12-07T12:00:00Z",
                    "category": "technology",
                    "source": {
                        "id": 1,
                        "name": "The Guardian",
                        "slug": "the-guardian"
                    }
                }
                // ... more articles
            ],
            "meta": {
                "available_categories": ["technology", "politics", "sports"],
                "available_sources": [
                    {"id": 1, "name": "The Guardian"},
                    {"id": 2, "name": "New York Times"},
                    {"id": 3, "name": "NewsAPI"}
                ]
            }
        }
    }
```
