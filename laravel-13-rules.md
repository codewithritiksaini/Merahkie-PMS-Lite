# Laravel 13 — Rules & Conventions

> Source: https://laravel.com/docs/13.x  
> Version: Laravel 13.x | PHP minimum: 8.3  
> Released: March 17, 2026

---

## PHP Version

- Laravel 13 requires **PHP 8.3 minimum** (supports up to 8.5)
- Use PHP 8.3+ features freely: typed class constants, readonly classes, `#[\Override]` attribute

---

## New Features in Laravel 13

### 1. Laravel AI SDK (First-Party)

```php
use App\Ai\Agents\SalesCoach;

// Text generation
$response = SalesCoach::make()->prompt('Analyze this...');

// Image generation
use Laravel\Ai\Image;
$image = Image::of('A donut on the counter')->generate();

// Audio generation
use Laravel\Ai\Audio;
$audio = Audio::of('Hello world')->generate();

// Embeddings via Str helper
$embeddings = Str::of('Some text')->toEmbeddings();
```

**Rule:** AI features are provider-agnostic — don't hardcode provider names. Use the Laravel AI SDK abstraction.

---

### 2. JSON:API Resources

Laravel 13 ships first-party JSON:API compliant resources.

- Handles resource serialization, relationships, sparse fieldsets, links
- Returns proper `application/vnd.api+json` headers automatically
- Use `JsonApiResource` instead of `JsonResource` for API-spec-compliant responses

---

### 3. PHP Attributes — Expanded Support

Use PHP attributes instead of method-based config wherever possible:

```php
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
class CommentController
{
    #[Middleware('subscribed')]
    #[Authorize('create', [Comment::class, 'post'])]
    public function store(Post $post) { ... }
}
```

**Queue job attributes:**

```php
#[Tries(5)]
#[Backoff(60)]
#[Timeout(120)]
#[FailOnTimeout]
class ProcessPodcast implements ShouldQueue { ... }
```

**Rule:** Prefer PHP attributes over defining `$tries`, `$timeout`, `$backoff` as class properties. Attributes are colocated and more readable.

---

### 4. Queue Routing by Class

```php
// Define in AppServiceProvider or a dedicated RouteServiceProvider
Queue::route(ProcessPodcast::class, connection: 'redis', queue: 'podcasts');
Queue::route(SendEmail::class, queue: 'emails');
```

**Rule:** Don't set `$queue` and `$connection` on individual job classes. Centralize routing via `Queue::route()`.

---

### 5. Semantic / Vector Search

```php
// Query builder
$results = DB::table('documents')
    ->whereVectorSimilarTo('embedding', 'Best wineries in Napa Valley')
    ->limit(10)
    ->get();

// With Eloquent Scout
$posts = Post::search('machine learning')->get();
```

Requires PostgreSQL + `pgvector`. Embeddings generated via `Str::of(...)->toEmbeddings()`.

---

### 6. Cache TTL Extension

```php
// Extend TTL without retrieving/re-storing
Cache::touch('key', ttl: 3600);
```

**Rule:** Use `Cache::touch()` instead of `Cache::get()` + `Cache::put()` pattern when you only need to refresh expiry.

---

### 7. Request Forgery Protection (Enhanced CSRF)

- Middleware renamed to `PreventRequestForgery`
- Now includes **origin-aware** request verification
- Backward compatible with token-based CSRF

**Rule:** Don't exclude routes from CSRF unless absolutely necessary. The new middleware handles SPA/API scenarios better.

---

## Laravel 13 Conventions

### Routing

```php
// Full-page Livewire components
Route::livewire('/dashboard', 'pages::dashboard');

// Resource routes still the same
Route::resource('posts', PostController::class);

// API routes
Route::apiResource('posts', PostController::class);
```

### Controllers

```php
// Prefer attribute-based middleware over constructor middleware
#[Middleware('auth')]
class PostController extends Controller
{
    public function index() { ... }
}
```

### Eloquent

- Use `#[Computed]` for expensive accessors
- Prefer `with()` eager loading — no lazy loading in production
- Cast arrays/enums using `protected $casts` or PHP 8 `Casts::*`

### Environment & Config

- `.env` must never be committed to git
- Use `config()` helper in application code, never `env()` directly outside config files
- Default DB is SQLite for local; use MySQL/PostgreSQL in production

### Directory Structure

```
app/
  Http/Controllers/    ← controllers
  Livewire/            ← class-based Livewire components
  Models/              ← Eloquent models
  Ai/Agents/           ← AI agents
resources/
  views/
    livewire/          ← Livewire blade views
    components/        ← single-file Livewire (⚡prefix)
    pages/             ← full-page Livewire components
```

### Artisan Commands

```bash
php artisan make:model Post -mfsc    # model + migration + factory + seeder + controller
php artisan make:livewire post.create
php artisan migrate
php artisan queue:work
composer run dev                      # starts server + queue + Vite together
```

### Testing

- Use **Pest** (preferred) or PHPUnit
- Test with real database (SQLite in-memory for speed)
- Use `RefreshDatabase` or `LazilyRefreshDatabase` trait

### AI-Assisted Development (Laravel Boost)

```bash
composer require laravel/boost --dev
php artisan boost:install
```

- Gives AI agents 17,000+ vectorized docs for your exact package versions
- Custom AI guidelines: add `.blade.php`/`.md` files to `.ai/guidelines/`

---

## Support Policy

| Version | Bug Fixes Until | Security Fixes Until |
|---------|----------------|---------------------|
| 13.x    | Q3 2027        | March 2028          |
| 12.x    | Aug 2026       | Feb 2027            |

---

## Upgrade Notes (from Laravel 12)

- Minimum PHP bumped to **8.3** (was 8.2)
- Named arguments on Laravel methods are **not** backward-compatible — avoid them in calls to framework methods
- `PreventRequestForgery` replaces old CSRF middleware name — update any custom exception handlers referencing the old class name
