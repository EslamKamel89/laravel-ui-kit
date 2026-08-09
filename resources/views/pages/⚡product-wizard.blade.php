<?php

use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Multi Step Wizard Modal')] class extends Component
{
    #[Session]
    public int $currentStep = 1;

    #[Session]
    public string $name = '';

    #[Session]
    public string $category = '';

    #[Session]
    public string $description = '';

    #[Session]
    public float $price = 0;

    #[Session]
    public string $url = '';

    public function next()
    {
        if ($this->currentStep < 3) {
            $this->validateStep();
            $this->currentStep++;
            $this->transition('forward');
        }
    }

    public function back()
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
            $this->transition('backward');
        }
    }

    public function goToStep(int $step)
    {
        $this->validateStep();
        $this->currentStep = $step;
        $this->transition('backward');
    }

    public function validateStep()
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string',
                'description' => 'string|required|min:5|max:1000',
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'price' => 'required|numeric|min:0',
                'url' => 'required|url|max:255',
            ]);
        }
    }

    public function submit()
    {
        $this->reset();
        $this->dispatch('toast', type: 'success', message: 'Product created successfully');
    }
};
?>

<div>
    <div class="m-8" x-data="{ open: false }">
        <button @click="open = true" class="px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-xs">
            Create Product
        </button>

        <!-- Modal -->
        <div class="fixed inset-0 z-50" x-show="open" @click.outside="open = false" x-cloak>
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/50"></div>

            <!-- Modal Content -->
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative bg-white rounded-lg max-w-lg w-full max-h-[85vh] flex flex-col">

                        <!-- Header -->
                        <div class="px-5 py-4 border-b border-gray-200 shrink-0">
                            <button @click="open = false"
                                class="absolute top-3.5 right-3.5 text-gray-400 hover:text-gray-600">
                                <flux:icon.x-mark />
                            </button>

                            <h2 class="text-sm font-semibold text-gray-900">
                                Create Product
                            </h2>

                            <p class="text-xs text-gray-500 mt-0.5">
                                Step {{ $currentStep }} of 3
                            </p>
                        </div>

                        <!-- Content Area - Scrollable -->
                        <div class="flex-1 overflow-y-auto px-5 py-5">
                            @if($currentStep === 1)
                            <!-- Step 1 Content -->
                            <div wire:transition="form" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Product Name
                                    </label>

                                    <input type="text" wire:model="name"
                                        class="w-full px-2.5 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="e.g., Tailwind UI Kit">
                                    @error('name')
                                    <p class="text-red-400 text-xs">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Category
                                    </label>

                                    <select wire:model="category"
                                        class="w-full px-2.5 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select a category...</option>
                                        <option value="templates">Templates</option>
                                        <option value="courses">Courses</option>
                                        <option value="ui-kits">UI Kits</option>
                                        <option value="plugins">Plugins</option>
                                    </select>
                                    @error('category')
                                    <p class="text-red-400 text-xs">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Description
                                    </label>

                                    <textarea wire:model="description" rows="3"
                                        class="w-full px-2.5 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Describe your product..."></textarea>
                                    @error('description')
                                    <p class="text-red-400 text-xs">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            @elseif($currentStep === 2)
                            <!-- Step 2 Content -->
                            <div wire:transition="form" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Price
                                    </label>

                                    <div class="relative">
                                        <span class="absolute left-2.5 top-1.5 text-sm text-gray-500">
                                            $
                                        </span>

                                        <input wire:model="price" type="number"
                                            class="w-full pl-7 pr-2.5 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="0.00" step="0.01">
                                        @error('price')
                                        <p class="text-red-400 text-xs">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Product URL
                                    </label>

                                    <input wire:model="url" type="url"
                                        class="w-full px-2.5 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="https://example.com/product">
                                    @error('url')
                                    <p class="text-red-400 text-xs">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            @elseif($currentStep === 3)
                            <!-- Step 3 Content - Preview -->
                            <div wire:transition="form">
                                <!-- Product Preview -->
                                <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">

                                    <div class="p-4 space-y-2.5">

                                        <!-- Category badge -->
                                        <div class="flex items-center gap-1.5">
                                            <span
                                                class="inline-block px-2 py-0.5 text-[11px] font-medium text-blue-700 bg-blue-50 rounded-full capitalize">
                                                {{ $category }}
                                            </span>

                                            <button type="button" wire:click="goToStep(1)"
                                                class="text-gray-400 hover:text-gray-600" title="Edit category">
                                                <flux:icon.pencil class="size-3.5" />
                                            </button>
                                        </div>

                                        <!-- Name & price -->
                                        <div class="flex items-start justify-between gap-3">

                                            <div class="flex items-center gap-1">
                                                <h3 class="text-sm font-semibold text-gray-900 leading-tight">
                                                    {{ $name }}
                                                </h3>

                                                <button type="button" wire:click="goToStep(1)"
                                                    class="text-gray-400 hover:text-gray-600" title="Edit name">
                                                    <flux:icon.pencil class="size-3.5" />
                                                </button>
                                            </div>

                                            <div class="flex items-center gap-1 shrink-0">
                                                <span class="text-sm font-bold text-gray-900">
                                                    ${{ $price }}
                                                </span>

                                                <button type="button" wire:click="goToStep(2)"
                                                    class="text-gray-400 hover:text-gray-600" title="Edit price">
                                                    <flux:icon.pencil class="size-3.5" />
                                                </button>
                                            </div>

                                        </div>

                                        <!-- URL -->
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs text-blue-600 truncate">
                                                {{ $url }}
                                            </span>

                                            <button type="button" wire:click="goToStep(2)"
                                                class="text-gray-400 hover:text-gray-600" title="Edit URL">
                                                <flux:icon.pencil class="size-3.5" />
                                            </button>
                                        </div>

                                    </div>

                                    <!-- Description -->
                                    <div class="border-t border-gray-200 px-4 py-3.5">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">
                                                Description
                                            </h4>

                                            <button type="button" wire:click="goToStep(1)"
                                                class="text-gray-400 hover:text-gray-600" title="Edit description">
                                                <flux:icon.pencil class="size-3.5" />
                                            </button>
                                        </div>

                                        <p class="text-xs text-gray-600 leading-relaxed line-clamp-3">
                                            {{ $description }}
                                        </p>
                                    </div>

                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="px-5 py-3.5 border-t border-gray-200 shrink-0 flex justify-between">

                            <button wire:click="back" x-show="$wire.currentStep > 1"
                                class="px-3 py-1.5 text-xs text-gray-700 hover:text-gray-900">
                                &larr; Back
                            </button>

                            <div class="flex gap-2 ml-auto">

                                <button @click="open = false"
                                    class="px-3 py-1.5 text-xs text-gray-700 hover:text-gray-900">
                                    Cancel
                                </button>

                                <!-- Next button -->
                                <button wire:click="next" x-show="$wire.currentStep < 3"
                                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <span>Next &rarr;</span>
                                </button>

                                <!-- Submit button -->
                                <button @click="open = false" wire:click="submit" x-show="$wire.currentStep == 3"
                                    class="px-3 py-1.5 text-xs bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Submit Product
                                </button>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-toast />

</div>