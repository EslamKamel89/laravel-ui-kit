<?php

use App\Helpers\Search;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dynamic Search')] class extends Component
{
    public string $search = '';

    #[Session]
    public array $recentSearches = [];

    #[Computed]
    public function results(): Collection
    {
        if (strlen($this->search) < 2) {
            return new Collection;
        }

        return Post::query()->where(function (Builder $query) {
            $query->where('title', 'like', "%{$this->search}%")
                ->orWhere('excerpt', 'like', "%{$this->search}%")
                ->orWhere('content', 'like', "%{$this->search}%")
                ->orWhere('author_name', 'like', "%{$this->search}%");
        })->latest('published_at')
            ->limit(10)
            ->get();
    }

    public function highlightMatch(string $text)
    {
        return Search::highlightMatch($text, $this->search);

    }

    public function getSnippet(Post $post)
    {
        if (stripos($post->excerpt, $this->search) !== false) {
            return $post->excerpt;
        }
        $excerpt = Str::excerpt($post->content, $this->search, ['radius' => 50]);
        if (! $excerpt) {
            return $post->excerpt;
        }

        return '...'.$excerpt.'...';
    }

    public function addToRecentSearches()
    {
        if (strlen($this->search) < 2) {
            return;
        }
        $this->recentSearches = collect([$this->search, ...$this->recentSearches])
            ->unique(fn ($v) => strtolower($v))
            ->take(5)
            ->values()
            ->toArray();
    }

    public function clearRecentSearches()
    {
        $this->recentSearches = [];
    }
};
?>

<div class="min-h-screen bg-gray-50 py-12" x-data="app()">
    <script>
    function app() {
        return {
            open: false,
            highlightedIndex: -1,
            moveUp() {
                if (this.highlightedIndex > 0) {
                    this.highlightedIndex--;
                    this.scrollToHighlightedIndex();
                }
            },
            moveDown() {
                const resultsList = document.getElementById('resultsList');
                if (this.highlightedIndex < resultsList?.children.length - 1) {
                    this.highlightedIndex++;
                    this.scrollToHighlightedIndex();
                }
            },
            scrollToHighlightedIndex() {
                this.$nextTick(() => {
                    const resultsList = document.getElementById('resultsList');
                    resultsList?.children[this.highlightedIndex].scrollIntoView({
                        block: 'nearest'
                    })
                });
            },
            selectHighlighted() {
                const resultsList = document.getElementById('resultsList');
                if (this.highlightedIndex >= 0) {
                    selected = resultsList?.children[this.highlightedIndex];
                    this.$wire.addToRecentSearches(this.$wire.search);
                    this.open = false;
                }
            }
        };
    }
    </script>
    <div class="max-w-xl mx-auto px-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h1 class="text-xl font-semibold text-gray-900 mb-1">
                Dynamic Search
            </h1>

            <p class="text-sm text-gray-500 mb-6">
                Search posts with real-time results
            </p>

            <!-- Search Input -->
            <div class="relative" @click.outside="open=false">
                <div class="relative">
                    <!-- Search icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.04 6.04a7.5 7.5 0 0 0 10.61 10.61Z" />
                    </svg>

                    <input wire:model.live.debounce.900ms="search" @focus="open=true" @click="open=true"
                        @keydown.escape="open=false" @keydown.arrow-down.prevent="moveDown"
                        @keydown.arrow-up.prevent="moveUp" @keydown.enter.prevent="selectHighlighted"
                        @input="highlightedIndex = -1" type="text" placeholder="Search posts..." value="laravel"
                        class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">

                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                        <div wire:loading wire:target="search">
                            <flux:icon.loading />
                        </div>
                        <button x-cloak x-show="$wire.search.length > 0" wire:click="$set('search' , '')"
                            @click="open=false" type="button"
                            class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Dropdown -->
                <div x-cloak x-show="open" x-transition
                    class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                    @if(strlen($search) < 2) <div class="px-4 py-6 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5" class="w-10 h-10 mx-auto text-gray-300 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.04 6.04a7.5 7.5 0 0 0 10.61 10.61Z" />
                        </svg>

                        <p class="text-sm text-gray-500">
                            Type at least two characters to search
                        </p>
                </div>
                @elseif($this->results->count() > 0)
                <!-- Results header -->
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                    <span class="text-xs font-medium text-gray-500">
                        {{ $this->results->count() }} {{ str('result')->plural( $this->results->count()) }}
                    </span>
                </div>

                <!-- Results -->
                <ul id="resultsList" class="max-h-80 overflow-y-auto py-1">
                    @foreach ($this->results as $post)
                    <li wire:key="post-{{ $post->id }}" class="px-4 py-3 border-b border-gray-100 last:border-0"
                        :class="{'bg-blue-50!': highlightedIndex === {{ $loop->index }} }"
                        @mouseenter="highlightedIndex = {{ $loop->index }}" @click="selectHighlighted">
                        <h3 class="font-medium text-gray-900 truncate">
                            {!! $this->highlightMatch($post->title) !!}
                        </h3>

                        <p class="text-sm text-gray-500 mt-0.5 ">
                            {!! $this->highlightMatch($this->getSnippet($post)) !!}
                        </p>

                        <div class="flex items-center gap-2 mt-1.5">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                {!! $this->highlightMatch($post->category) !!}
                            </span>

                            <span class="text-xs text-gray-400">
                                by
                                {!! $this->highlightMatch($post->author_name) !!}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="px-4 py-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" class="w-10 h-10 mx-auto text-gray-300 mb-3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m0 3.75h.008M10.34 3.94 2.92 17.5a1.5 1.5 0 0 0 1.3 2.25h15.56a1.5 1.5 0 0 0 1.3-2.25L13.66 3.94a1.5 1.5 0 0 0-2.62 0Z" />
                    </svg>

                    <p class="text-gray-500">
                        No results found for "{{ $search }}"
                    </p>

                    <p class="text-sm text-gray-400 mt-1">
                        Try searching for something else
                    </p>
                </div>
                @endif
                <!-- Recent searches section-->
                <div>
                    <!-- Recent searches -->
                    @if (count($recentSearches) > 0)
                    <div class="border-t border-gray-100">
                        <!-- Header -->
                        <div class="flex items-center justify-between px-4 py-2 bg-gray-50">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Recent Searches
                            </span>

                            <button wire:click="clearRecentSearches" type="button"
                                class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                                Clear
                            </button>
                        </div>

                        <!-- Search history -->
                        <ul class="py-1">
                            @foreach ($recentSearches as $recent)
                            <li>
                                <button wire:click="$set('search', '{{ $recent }}')" type="button"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-gray-50 transition-colors">
                                    <!-- Clock icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.5" class="w-5 h-5 shrink-0 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" />
                                    </svg>

                                    <span class="text-sm text-gray-700 truncate">
                                        {{ $recent }}
                                    </span>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                <!-- Footer with keyboard hints -->
                <div
                    class="flex items-center justify-between px-4 py-2 bg-gray-50 border-t border-gray-100 text-xs text-gray-500">
                    <div class="flex items-center gap-3">

                        <span class="flex items-center gap-1">
                            <kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded font-mono">
                                ↑
                            </kbd>

                            <kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded font-mono">
                                ↓
                            </kbd>

                            navigate
                        </span>

                        <span class="flex items-center gap-1">
                            <kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded font-mono">
                                ↵
                            </kbd>

                            select
                        </span>
                    </div>

                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded font-mono">
                            esc
                        </kbd>

                        close
                    </span>
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-400 mt-3">
            Search across titles, excerpts, content, and authors
        </p>
    </div>
</div>
</div>
