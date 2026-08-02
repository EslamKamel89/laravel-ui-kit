<?php

use App\Models\UserSetting;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Toast Notifications System')]
class extends Component
{
    public UserSetting $settings;

    public string $name;

    public string $theme;

    public string $language;

    public function mount()
    {
        $this->settings = UserSetting::first();
        $this->name = $this->settings->name;
        $this->theme = $this->settings->theme;
        $this->language = $this->settings->language;
    }

    public function updateName()
    {
        $this->settings->update([
            'name' => $this->name,
        ]);
        $this->dispatch('toast', message: 'Display name updated', type: 'success');
    }

    public function updatedTheme()
    {
        $this->settings->update([
            'theme' => $this->theme,
        ]);
        if ($this->theme === 'light') {
            $this->dispatch('toast',
                type: 'success',
                message: 'Theme changed to light mode'
            );
        } elseif ($this->theme === 'dark') {
            $this->dispatch('toast',
                type: 'warning',
                message: 'Dark mode is in beta'
            );
        } else {
            $this->dispatch('toast',
                type: 'info',
                message: 'Theme will match your system preferences'
            );
        }
    }

    public function updatedLanguage()
    {
        $this->settings->update([
            'language' => $this->language,
        ]);
        $this->dispatch('toast',
            type: 'info',
            message: 'Page will reload to apply language'
        );
    }
};
?>

<div class="min-h-screen bg-white text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
    <!-- Header -->
    <div class="border-b border-zinc-200 dark:border-zinc-800">
        <div class="mx-auto max-w-2xl px-6 py-6">
            <h1 class="text-xl font-semibold">
                Profile Settings
            </h1>

            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Manage your account preferences
            </p>
        </div>
    </div>

    <!-- Content -->
    <div class="mx-auto max-w-2xl px-6 py-8">
        <div class="space-y-8">

            <!-- Display Name -->
            <div>
                <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Display Name
                </label>

                <div class="flex gap-3">
                    <input type="text" wire:model="name"
                        class="flex-1 rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder:text-zinc-500">

                    <button wire:click="updateName"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </div>

            <!-- Theme -->
            <div>
                <label class="mb-3 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Theme
                </label>

                <div class="flex gap-6">
                    <label class="flex cursor-pointer items-center">
                        <input type="radio" value="light" wire:model.live="theme" class="h-4 w-4 text-blue-600">

                        <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">
                            Light
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-center">
                        <input type="radio" value="dark" wire:model.live="theme" class="h-4 w-4 text-blue-600">

                        <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">
                            Dark
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-center">
                        <input type="radio" value="auto" wire:model.live="theme" class="h-4 w-4 text-blue-600">

                        <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">
                            Auto
                        </span>
                    </label>
                </div>
            </div>

            <!-- Language -->
            <div>
                <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Language
                </label>

                <select wire:model.live="language"
                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="english">English</option>
                    <option value="spanish">Spanish</option>
                    <option value="french">French</option>
                    <option value="german">German</option>
                </select>
            </div>

        </div>
    </div>

    <x-toast />
</div>