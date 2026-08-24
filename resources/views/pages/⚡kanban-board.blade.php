<?php

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Kanban Board')] class extends Component
{
    public string $newTaskTitle = '';

    #[Computed]
    public function columns(): Collection
    {
        return Task::orderBy('position')
            ->get()
            ->groupBy('status')
            ->union(['todo' => collect(), 'in-progress' => collect(), 'done' => collect()]);
    }

    public function handleSort(int $id, int $position, string $status)
    {
        $task = Task::findOrFail($id);
        $oldStatus = $task->status;
        $task->update(['status' => $status]);
        $siblings = Task::where('status', $status)
            ->where('id', '!=', $task->id)
            ->orderBy('position')
            ->pluck('id')
            ->all();
        array_splice($siblings, $position, 0, [$task->id]);
        foreach ($siblings as $position => $id) {
            Task::where('id', $id)->update(['position' => $position]);
        }

        if ($oldStatus !== $status) {
            $this->reorder($oldStatus);
        }
        unset($this->columns);
    }

    public function reorder(string $status)
    {
        Task::where('status', $status)
            ->orderBy('position')
            ->get()
            ->each(fn (Task $task, int $index) => $task->update(['position' => $index]));
    }

    public function addTask(string $status)
    {
        $position = Task::where('status', $status)->max('position') + 1;
        Task::create([
            'title' => $this->newTaskTitle,
            'status' => $status,
            'position' => $position,
        ]);
        $this->reset('newTaskTitle');
        unset($this->columns);
    }

    public function deleteTask(int $id)
    {
        $task = Task::findOrFail($id);
        $status = $task->status;
        $task->delete($id);
        $this->reorder($status);
        unset($this->columns);
    }
};
?>

<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Kanban Board</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 items-start gap-6">
            @php
            $columnsConfig = [
            'todo' => ['label' => 'To Do', 'color' => 'bg-blue-500'],
            'in-progress' => ['label' => 'In Progress', 'color' => 'bg-amber-500'],
            'done' => ['label' => 'Done', 'color' => 'bg-green-500'],
            ];
            @endphp
            @foreach ( $columnsConfig as $status => $config)
            <div class="bg-gray-200/60 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2.5 h-2.5 rounded-full {{ $config['color'] }}"></span>
                    <h2 class="font-semibold text-gray-700">{{ $config['label'] }}</h2>

                    <span class="ml-auto text-sm text-gray-500 bg-white/70 rounded-full px-2 py-0.5">
                        {{ $this->columns[$status]->count() }}
                    </span>
                </div>

                <ul wire:sort="handleSort" wire:sort:group="kanban" wire:sort:group-id="{{ $status }}"
                    class="space-y-2 min-h-15">
                    @foreach ($this->columns[$status] as $task )

                    <li wire:key="task-{{ $task->id }}" wire:sort:item="{{ $task->id }}"
                        class="group bg-white rounded-lg border border-gray-200 p-3 shadow-sm cursor-grab active:cursor-grabbing">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-sm text-gray-800">
                                {{ $task->title }}
                            </span>

                            <button type="button" wire:click="deleteTask({{$task->id}})"
                                wire:confirm="Are you sure you want to delete this task?"
                                class="text-gray-400 hover:text-red-500 transition-colors shrink-0 opacity-0 group-hover:opacity-100 p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </li>
                    @endforeach
                </ul>

                <div class="mt-3" x-data="{adding:false}">
                    <button x-cloak x-transition x-show="!adding" type="button"
                        @click="adding=true; $nextTick(()=>$refs.taskInput.focus());"
                        class="w-full text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 py-1.5 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>

                        Add task
                    </button>

                    <form wire:submit.prevent="addTask('{{$status}}')" class="space-y-2" x-cloak x-show="adding"
                        x-transition @keydown.escape.prevent="adding=false" @click.outside="adding=false">
                        <input wire:model="newTaskTitle" x-ref="taskInput" type="text" placeholder="Task title..."
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">

                        <div class="flex gap-2">
                            <button type="submit" @click="adding=false"
                                class="text-sm bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-colors">
                                Add
                            </button>

                            <button type="button" @click="adding=false"
                                class="text-sm text-gray-500 hover:text-gray-700 px-3 py-1.5 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
