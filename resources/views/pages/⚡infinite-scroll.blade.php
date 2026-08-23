<?php

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Infinite scroll with Filters')] class extends Component
{
    public int $page = 1;

    public int $perPage = 5;

    public string $search = '';

    public string $category = '';

    public array $categories = ['Technology', 'Design', 'Business', 'Lifestyle', 'Tutorial'];

    #[Computed]
    public function posts(): Collection
    {
        return Post::query()
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->category, fn ($q) => $q->inCategory($this->category))
            ->latest('published_at')
            ->forPage($this->page, $this->perPage)
            ->get();
    }

    public function loadMore()
    {
        $this->page++;
        if ($this->posts()->count() < $this->perPage) {
            $this->dispatch('end-of-feed');

        }
    }

    public function updated(string $property, mixed $value)
    {
        if (in_array($property, ['search', 'category'])) {
            $this->reset('page');
            $this->renderIsland('posts');
            $this->js("window.scrollTo({top:0 ,behavior:'smooth'})");
        }

    }
};
?>

<div x-data="{showScrollToTop:false}" @scroll.window="showScrollToTop = window.scrollY > 500"
    class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-3xl mx-auto px-6 py-4">
            <h1 class="text-xl font-semibold text-gray-900 mb-4">Posts</h1>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search -->
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <input type="text" placeholder="Search posts..." wire:model.live.debounce.900ms="search"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <!-- Category Dropdown -->
                <div class="relative" x-data="{show:false}" @click.outside="show=false">
                    <button type="button" @click="show=!show"
                        class="w-full sm:w-auto flex items-center justify-between gap-2 px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-50 transition">
                        <span>{{  $category ? $category : 'All Categories' }}</span>

                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="show" x-transition
                        class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-20">
                        <button wire:click="$set('category' , '')" @click="show=false" type="button"
                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                            :class="{'bg-blue-50! text-blue-700!': !$wire.category}">
                            All Categories
                        </button>
                        @foreach ($categories as $cat)
                        <button type="button" wire:click="$set('category', '{{$cat}}')" @click="show=false"
                            class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                            :class="{'bg-blue-50! text-blue-700!': $wire.category==='{{ $cat }}' }">
                            {{ $cat }}
                        </button>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Posts List -->
    <div class="max-w-3xl mx-auto px-6 py-6">
        <div class="space-y-4">
            @if ($this->posts->isEmpty())
            <p class="text-sm text-gray-500 w-full text-center">There are no posts available</p>
            @endif
            @island('posts')
            @foreach ($this->posts as $post )
            <article class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                    <span class="px-2 py-1 bg-gray-100 rounded-full text-xs font-medium">
                        {{ $post->category }}
                    </span>

                    <span>&middot;</span>

                    <span>{{ $post->published_at->format('M j, Y') }}</span>
                </div>

                <h2 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ $post->title }}
                </h2>

                <p class="text-gray-600 text-sm">
                    {{ $post->content }}
                </p>

                <div class="mt-4 text-sm text-gray-500">
                    {{ $post->author }}
                </div>
            </article>
            @endforeach
            @endisland
            <div x-data="{ended:false}" @end-of-feed.window="ended = true">
                <div wire:intersect="loadMore" wire:island.append="posts"></div>
                <p x-show="ended" class="text-center text-gray-400 py-8 text-sm">
                    You have reached the end
                </p>
            </div>
            <!-- Infinite Scroll Placeholder -->
            <div wire:loading wire:target="loadMore" class="py-8 flex items-center justify-center">
                <div class="flex items-center gap-2 text-gray-500">
                    <svg class="w-5 h-5 animate-spin inline-flex" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>

                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                    <span class="inline-flex">
                        Loading more posts...
                    </span>
                </div>
            </div>



        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button x-show="showScrollToTop" type="button" @click="window.scrollTo({top:0, behavior:'smooth'})"
        class="fixed bottom-6 right-6 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-colors cursor-pointer">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</div>