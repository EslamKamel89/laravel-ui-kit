<?php

use App\Models\Meeting;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Inline Editing') ] class extends Component
{
    public Meeting $meeting;

    public string $title;

    public $date;

    public ?string $notes;

    public function mount()
    {
        $this->meeting = Meeting::first();
        $this->title = $this->meeting->title;
        $this->notes = $this->meeting->notes;
        $this->date = $this->meeting->date;
    }

    public function updated($property): void
    {
        if (in_array($property, ['title', 'notes'])) {
            $this->meeting->update([
                'title' => $this->title,
                'notes' => $this->notes,
            ]);
            $this->dispatch('notes-saved');
        }
    }
};
?>

<div class="mx-auto max-w-2xl p-8">
    <div
        class="relative rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm transition-colors dark:border-zinc-800 dark:bg-zinc-900">
        <div class="space-y-8">

            <div x-data="{ showSaved: false , timeout:null}" x-on:notes-saved.window="
                if(this.timeout) clearTimeout(this.timeout);
                showSaved = true;
                this.timeout = setTimeout(() => showSaved = false, 2000)
                " x-show="showSaved" x-transition
                class="absolute top-6 right-6 flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Saved
            </div>

            <section>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    Title
                </p>

                <textarea x-data="autoResizeTextArea()" wire:ignore.self wire:model.live.debounce.1000ms="title"
                    placeholder="Click here to add title..." rows="1"
                    class="mt-2 w-full resize-none overflow-hidden border-0 bg-transparent p-0 text-3xl font-bold tracking-tight text-zinc-900 placeholder:text-zinc-400 focus:border-0 focus:outline-none focus:ring-0 focus-visible:outline-none dark:text-zinc-100 dark:placeholder:text-zinc-500 "></textarea>
            </section>

            <section>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    Date
                </p>

                <p class="mt-2 text-lg text-zinc-700 dark:text-zinc-300">
                    {{ $date?->format('F j, Y') }}
                </p>
            </section>

            <section>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    Notes
                </p>

                <textarea x-data="autoResizeTextArea()" wire:ignore.self wire:model.live.debounce.1000ms="notes"
                    placeholder="Click here to add notes..." rows="3"
                    class="mt-2 w-full resize-none overflow-hidden border-0 bg-transparent p-0 leading-7 text-zinc-700 placeholder:text-zinc-400 focus:border-0 focus:outline-none focus:ring-0 focus-visible:outline-none dark:text-zinc-300 dark:placeholder:text-zinc-500 "></textarea>
            </section>
        </div>
    </div>
</div>
