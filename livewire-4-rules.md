# Livewire 4 — Rules & Conventions

> Source: https://livewire.laravel.com/docs/4.x  
> Version: Livewire 4.x  
> "Build dynamic, reactive interfaces using only PHP—no JavaScript required."

---

## Component Formats

Livewire 4 supports three component formats:

### 1. Single-File Component (Default — Preferred)

```php
<?php use Livewire\Component;

new class extends Component {
    public $title = '';

    public function save() {
        $this->validate(['title' => 'required|min:3']);
        Post::create(['title' => $this->title]);
        $this->redirect('/posts');
    }
};
?>
<div>
    <input wire:model="title" type="text">
    @error('title') <span>{{ $message }}</span> @enderror
    <button wire:click="save">Save</button>
</div>
```

Generate: `php artisan make:livewire post.create`  
File location: `resources/views/components/post/⚡create.blade.php`

### 2. Multi-File Component (MFC)

Generate: `php artisan make:livewire post.create --mfc`  
Creates: `resources/views/components/post/⚡create/` directory  
Contains: `index.blade.php`, `index.php`, `index.js`, `index.css`

### 3. Class-Based Component (Legacy / Complex Logic)

Generate: `php artisan make:livewire post.create --class`  
PHP class: `app/Livewire/Post/Create.php`  
View: `resources/views/livewire/post/create.blade.php`

---

## Template Rules

```blade
{{-- CORRECT: self-closing tag --}}
<livewire:post.create />

{{-- WRONG: unclosed tag treats next content as slot --}}
<livewire:post.create>  ← DO NOT DO THIS unless using slots
```

**Rule:** Always self-close component tags unless explicitly passing slot content.

**Rule:** Blade templates MUST have exactly **one root HTML element**. No sibling elements at root level.

```blade
{{-- WRONG --}}
<div>First</div>
<div>Second</div>

{{-- CORRECT --}}
<div>
    <div>First</div>
    <div>Second</div>
</div>
```

---

## Routing (v4 Change)

```php
// Livewire 4 — use Route::livewire()
Route::livewire('/posts/{post}', 'pages::post.show');
Route::livewire('/dashboard', 'pages::dashboard');

// Livewire 3 style — AVOID in v4
Route::get('/posts', PostList::class);
```

Page components live in: `resources/views/pages/`  
Referenced with `pages::` namespace prefix.

---

## wire:model Rules (v4 Behavior Change)

### Default Behavior (CHANGED from v3)

In Livewire 4, `wire:model` **does NOT update on every keystroke by default**.  
Updates only happen when an action fires (e.g., button click).

```blade
{{-- Only syncs when form is submitted --}}
<input wire:model="title" type="text">

{{-- Syncs live as user types (150ms debounce) --}}
<input wire:model.live="title" type="text">

{{-- Syncs when user leaves the field --}}
<input wire:model.blur="title" type="text">

{{-- Syncs on change event (good for selects) --}}
<select wire:model.change="status">...</select>

{{-- Enter key triggers sync --}}
<input wire:model.enter="search" type="text">
```

### Modifiers Reference

| Modifier | Behavior |
|----------|----------|
| `.live` | Sync on every keystroke (debounced 150ms) |
| `.live.debounce.500ms` | Sync with custom debounce |
| `.blur` | Sync when input loses focus |
| `.change` | Sync on change event |
| `.enter` | Sync on Enter key press |
| `.lazy` | v3 compatible change behavior |
| `.number` | Cast to integer on server |
| `.boolean` | Cast to boolean on server |
| `.deep` | Listen to child component events (replaces v3 default) |
| `.preserve-scroll` | Keep scroll position after update |
| `.fill` | Use initial HTML `value` attribute |

### Migration Note from v3

```blade
{{-- v3: .blur only controlled network timing --}}
wire:model.blur="title"

{{-- v4: .blur controls CLIENT-SIDE sync timing; add .live for old behavior --}}
wire:model.live.blur="title"

{{-- v3 behavior of child events: --}}
wire:model.deep="parent.value"
```

---

## Properties

### Public Properties

```php
public $title = '';        // Available in Blade as {{ $title }}
public $count = 0;         // Sent to/from client, persisted between requests
public array $items = [];  // Arrays work natively
```

### Protected Properties

```php
protected $apiKey = '';    // NOT sent to client, NOT persisted between requests
                           // Access in Blade as {{ $this->apiKey }}
```

### Computed Properties

```php
use Livewire\Attributes\Computed;

#[Computed]
public function posts(): Collection
{
    return Post::with('author')->latest()->get();
}
// Access in Blade as {{ $this->posts }} or @foreach($this->posts as $post)
// Cached per request — won't re-query on same request
```

---

## Actions

### Event Directives

```blade
<button wire:click="save">Save</button>
<form wire:submit="store">...</form>
<input wire:keydown.enter="search" type="text">
<input wire:keydown.shift.enter="newLine" type="text">
```

### Event Modifiers

```blade
<form wire:submit.prevent="store">    {{-- prevents default --}}
<button wire:click.stop="delete">    {{-- stops propagation --}}
<button wire:click.debounce.300ms="search">
<button wire:click.throttle.500ms="track">
<div wire:mouseenter.once="load">    {{-- fires only once --}}
<button wire:click.outside="close"> {{-- click outside element --}}
<button wire:click.window="handle"> {{-- listen on window --}}
```

### Passing Parameters

```blade
<button wire:click="delete({{ $post->id }})">Delete</button>
```

```php
public function delete($id)
{
    $post = Post::find($id);
    $this->authorize('delete', $post); // Always authorize!
    $post->delete();
}

// Auto model binding via type-hint
public function delete(Post $post)
{
    $this->authorize('delete', $post);
    $post->delete();
}
```

### Magic Actions

```blade
<button wire:click="$set('count', 0)">Reset</button>
<button wire:click="$toggle('showModal')">Toggle</button>
<button wire:click="$refresh">Refresh</button>
<button wire:click="$dispatch('post-saved')">Dispatch</button>
<button wire:click="$parent.close()">Close Parent</button>
```

### Async Actions (v4 New Feature)

```php
// Via modifier
<button wire:click.async="logActivity">Track</button>

// Via attribute
#[Async]
public function logActivity() { ... }
```

**Critical Rule:** NEVER use async actions if they modify component state shown in the UI.  
Safe uses: analytics, logging, fire-and-forget operations only.

### Skip Re-render

```php
#[Renderless]
public function incrementViewCount() { ... }

// Or in method
public function incrementViewCount() {
    $this->post->increment('views');
    $this->skipRender();
}

// Or via modifier
<button wire:click.renderless="incrementViewCount">View</button>
```

### Confirmation Dialog

```blade
<button wire:click="delete" wire:confirm="Are you sure?">Delete</button>
```

---

## Validation

### Inline Validation

```php
public function save()
{
    $validated = $this->validate([
        'title' => 'required|min:3',
        'body' => 'required',
    ]);
    Post::create($validated);
}
```

### Attribute-Based Validation (Preferred)

```php
#[Validate('required|min:3')]
public $title = '';

#[Validate('required|email')]
public $email = '';

// Real-time validation — combine with wire:model.live
// Validates automatically when property updates
```

### Custom Messages & Labels

```php
#[Validate('required|min:3', message: 'Title must be at least 3 characters.')]
public $title = '';

#[Validate('required', as: 'post title')]
public $title = '';
```

### Disable Auto-Validate on Update

```php
#[Validate('required|min:3', onUpdate: false)]
public $title = '';
```

### Complex Rules via rules() Method

```php
protected function rules(): array
{
    return [
        'title' => Rule::unique('posts')->ignore($this->post->id),
        'slug' => ['required', 'string', Rule::unique('posts')],
    ];
}

protected function messages(): array
{
    return ['title.unique' => 'This title is already taken.'];
}
```

### Error Display in Blade

```blade
@error('title') <span class="text-red-500">{{ $message }}</span> @enderror

{{-- All errors --}}
@if($errors->any())
    <ul>@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
@endif
```

### Form Objects (for complex forms)

```php
class PostForm extends Form
{
    #[Validate('required|min:3')]
    public $title = '';

    #[Validate('required')]
    public $body = '';
}

// In component
public PostForm $form;

public function save()
{
    $this->form->validate();
    Post::create($this->form->all());
}
```

```blade
<input wire:model="form.title" type="text">
@error('form.title') {{ $message }} @enderror
```

---

## Lifecycle Hooks

```php
public function mount($id = null)
{
    // Runs ONCE on initialization, not on re-renders
    // Good for loading initial data
    $this->post = Post::find($id);
}

public function updated($property)
{
    // Runs after any property is updated
}

public function updatedTitle($value)
{
    // Runs after $title is updated specifically
    $this->slug = Str::slug($value);
}

public function rendering()  { } // Before render
public function rendered()   { } // After render
public function hydrate()    { } // On every request
public function dehydrate()  { } // Before response
```

---

## Islands (v4 New Feature)

Isolated regions within a component that update independently:

```blade
<div>
    <livewire:island name="stats-panel" />
</div>
```

Useful for sections that need to update without triggering full component re-render.

---

## New Directives in v4

| Directive | Purpose |
|-----------|---------|
| `wire:sort` | Drag-and-drop sorting |
| `wire:intersect` | Trigger action when element enters viewport |
| `wire:ref` | Reference DOM elements |
| `wire:navigate:scroll` | Preserve scroll in `@persist` blocks |
| `wire:confirm` | Native confirmation dialog |
| `data-loading` | CSS attribute for loading states |

---

## Loading States

```blade
{{-- Show during any action --}}
<span wire:loading>Processing...</span>

{{-- Hide element while loading --}}
<button wire:loading.remove>Save</button>

{{-- Target specific action --}}
<span wire:loading wire:target="save">Saving...</span>

{{-- CSS attribute approach (v4) --}}
<div data-loading="opacity-50 cursor-not-allowed">...</div>
```

---

## Configuration (v4 Changes)

`config/livewire.php` key changes from v3:

```php
return [
    // v3: 'layout' => 'layouts.app'
    'component_layout' => 'layouts::app',  // v4 key + namespace syntax

    // v3: 'lazy_placeholder'
    'component_placeholder' => 'components.placeholder',  // v4 key

    // smart_wire_keys now defaults to TRUE (was false in v3)
    'smart_wire_keys' => true,
];
```

---

## URL Hash Change (Breaking)

All Livewire internal URLs now include a hash:

```
v3: /livewire/message
v4: /livewire-{hash}/message
```

Don't hardcode Livewire internal URLs anywhere. This is auto-handled.

---

## JavaScript / Alpine Integration

```blade
{{-- Call Livewire method from Alpine --}}
<button x-on:click="$wire.save()">Save</button>
<button x-on:click="$wire.delete({{ $post->id }})">Delete</button>

{{-- Get return value --}}
<span x-init="$el.textContent = await $wire.getCount()"></span>

{{-- Access errors in JS --}}
<span x-show="$errors.has('title')" x-text="$errors.first('title')"></span>
```

### JavaScript Actions (v4 — New Syntax)

```html
<script>
  this.$js.bookmark = () => {
      $wire.bookmarked = !$wire.bookmarked;
      $wire.bookmarkPost();
  }
</script>

<button wire:click="$js.bookmark">Bookmark</button>
```

**v3 deprecated:** `$wire.$js()` method → use `$wire.$js.methodName = () => {}`

---

## Deprecated in v4 (Remove from Codebase)

| v3 | v4 Replacement |
|----|----------------|
| `Route::get('/path', MyComponent::class)` | `Route::livewire('/path', 'pages::my-component')` |
| `wire:scroll` inside `@persist` | `wire:navigate:scroll` |
| `wire:model.blur` (network timing) | `wire:model.live.blur` |
| `$wire.$js()` | `$wire.$js.methodName = () => {}` |
| `$js()` without prefix | `$wire.$js` or `this.$js` |
| `commit` hook | `interceptMessage()` |
| `request` hook | `interceptRequest()` |
| `wire:transition` modifiers (`.opacity`, `.scale`) | Browser View Transitions API |
| Volt (`Livewire\Volt\Component`) | Use `Livewire\Component` directly |

---

## Security Rules

1. **All public methods are callable from browser DevTools** — always treat action parameters as untrusted user input
2. **Always authorize in actions** — UI restrictions are not security
3. **Mark internal helpers as `protected` or `private`** to prevent client invocation
4. **Never async** if the action modifies UI state

```php
// WRONG — no authorization
public function delete($id)
{
    Post::find($id)->delete();
}

// CORRECT
public function delete($id)
{
    $post = Post::findOrFail($id);
    $this->authorize('delete', $post);
    $post->delete();
}
```

---

## Testing

```php
use Livewire\Livewire;

Livewire::test(CreatePost::class)
    ->set('title', 'My Post')
    ->set('body', 'Content here')
    ->call('save')
    ->assertRedirect('/posts')
    ->assertHasNoErrors();

Livewire::test(CreatePost::class)
    ->call('save')
    ->assertHasErrors(['title' => 'required']);
```

---

## Quick Reference

```bash
# Create components
php artisan make:livewire post.create           # single-file
php artisan make:livewire post.create --mfc     # multi-file
php artisan make:livewire post.create --class   # class-based
```

```blade
{{-- Include in layout (required) --}}
@livewireStyles
@livewireScripts

{{-- Embed component --}}
<livewire:post.create />
<livewire:post.create title="Hello" />
<livewire:post.create :title="$variable" />
```
