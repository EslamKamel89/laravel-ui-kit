<?php

use App\Models\Tag;
use Livewire\Component;

new class extends Component
{
    public string $search = '';

    public array $suggestions = [];

    public array $selectedTags = [];

    public function updatedSearch()
    {
        $this->loadSuggestions();
    }

    public function loadSuggestions()
    {
        if (strlen($this->search) < 1) {
            $this->suggestions = [];

            return;
        }

        $this->suggestions = Tag::query()
            ->where('name', 'LIKE', "%{$this->search}%")
            ->whereNotIn(
                'id',
                collect($this->selectedTags)
                    ->pluck('id')
                    ->toArray()
            )
            ->limit(5)
            ->select('id', 'name')
            ->get()
            ->toArray();
    }

    public function addTag(int $id)
    {
        if (
            collect($this->selectedTags)
                ->contains(fn ($suggestion) => $suggestion['id'] === $id)
        ) {
            return;
        }

        $suggestion = collect($this->suggestions)
            ->filter(fn ($suggestion) => $suggestion['id'] === $id)
            ->first();

        if (! $suggestion) {
            return;
        }

        $this->selectedTags[] = $suggestion;

        $this->search = '';
        $this->suggestions = [];
    }

    public function removeTag(int $id)
    {
        $this->selectedTags = collect($this->selectedTags)
            ->reject(fn ($tag) => $tag['id'] === $id)
            ->values()
            ->toArray();
    }

    public function createTag(): void
    {
        $name = ucfirst(trim($this->search));

        if (empty($name)) {
            return;
        }

        $existing = Tag::query()
            ->whereRaw('LOWER(name) = LOWER(?)', [$name])
            ->first();

        if ($existing) {
            $this->addTag($existing->id);

            return;
        }

        $tag = Tag::create([
            'name' => $name,
        ]);

        $this->selectedTags[] = [
            'id' => $tag->id,
            'name' => $tag->name,
        ];

        $this->search = '';
        $this->suggestions = [];

        $this->dispatch(
            'toast',
            message: "Tag \"{$tag->name}\" created!",
            type: 'success'
        );
    }
};

?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-xl mx-auto px-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <h1 class="text-xl font-semibold text-gray-900 mb-1">
                Tag Input
            </h1>

            <p class="text-sm text-gray-500 mb-6">
                Add tags to categorize your content
            </p>

            <!-- Selected Tags -->

            <div class="flex flex-wrap gap-2 mb-4">

                @foreach ($selectedTags as $tag)
                <span wire:key="tag-{{ $tag['id'] }}"
                    class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-sm font-medium px-3 py-1.5 rounded-full">
                    {{ $tag['name'] }}

                    <button wire:click="removeTag({{ $tag['id'] }})" type="button"
                        class="text-blue-400 hover:text-blue-600 transition-colors cursor-pointer">
                        <flux:icon.x-mark class="size-4" />
                    </button>
                </span>
                @endforeach

            </div>

            <!-- Tag Input -->

            <div x-data="{
                    open: false,

                    highlightedIndex: -1,
                    suggestions: $wire.suggestions ,

                    moveUp() {
                        if (this.highlightedIndex > -1) {
                            this.highlightedIndex--;
                        }
                    },

                    moveDown() {
                        const maxIndex = $wire.suggestions.length;

                        if (this.highlightedIndex < maxIndex) {
                            this.highlightedIndex++;
                        }
                    },
                }" @click.outside="open = false">

                <div class="relative">

                    <input wire:model.live.debounce.900ms="search" type="text" @focus="open = true" @input="open = true"
                        @keydown.escape="open = false" @keydown.up.prevent="moveUp" @keydown.down.prevent="moveDown"
                        @keydown.enter.prevent="
                            if (highlightedIndex >= 0 && highlightedIndex < $wire.suggestions.length) {
                                $wire.addTag(
                                    $wire.suggestions[highlightedIndex].id
                                );

                            } else if (highlightedIndex === $wire.suggestions.length) {
                                $wire.createTag();
                            }

                            highlightedIndex = -1;
                        " placeholder="Type to search or create tags..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">

                    <!-- Dropdown -->

                    <div x-show="open"
                        class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">

                        <!-- Suggestions -->

                        <ul wire:ignore x-show="$wire.suggestions.length > 0" class="py-1">
                            <template x-for="(suggestion, index) in suggestions" :key="suggestion.id">
                                <li @click="$wire.addTag(suggestion.id)" @mouseenter="highlightedIndex = index"
                                    x-text="suggestion.name"
                                    class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 hover:text-blue-700 transition-colors text-gray-700"
                                    :class="{
                                        '!text-blue-700 !bg-blue-50':
                                            index === highlightedIndex
                                    }">
                                </li>
                            </template>

                        </ul>

                        <!-- Create New Tag -->
                        @if ($search &&
                        !collect($suggestions)->contains(fn($suggestion)=>strtolower($suggestion['name'])===strtolower($search)))
                        <div wire:click="createTag" @mouseenter="highlightedIndex = $wire.suggestions.length"
                            class="px-4 py-2.5 cursor-pointer border-t border-gray-100 hover:bg-blue-50 hover:text-blue-700 transition-colors flex items-center gap-2 text-gray-600"
                            :class="{
                                '!text-blue-700 !bg-blue-50':
                                    highlightedIndex === $wire.suggestions.length
                            }">

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>

                            Create

                            <span class="font-medium text-gray-900">
                                {{ $search }}
                            </span>

                        </div>
                        @endif

                    </div>
                </div>

            </div>

            <!-- Keyboard Hint -->

            <p class="text-xs text-gray-400 mt-3">
                Press

                <kbd class="px-1.5 py-0.5 bg-gray-100 rounded text-gray-500 font-mono">
                    Enter
                </kbd>

                to select or create a tag
            </p>

        </div>
    </div>

    <x-toast />

</div>
