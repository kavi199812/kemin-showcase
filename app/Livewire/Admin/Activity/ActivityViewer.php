<?php

namespace App\Livewire\Admin\Activity;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.admin')]
#[Title('Audit Activity Log — Admin')]
class ActivityViewer extends Component
{
    use WithPagination;

    #[Url]
    public string $event = '';

    #[Url]
    public string $q = '';

    public function updatedEvent(): void
    {
        $this->resetPage();
    }

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->event = '';
        $this->q = '';
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Activity::with('causer')->latest();

        if (filled($this->event)) {
            $query->where('event', $this->event);
        }

        if (filled($this->q)) {
            $query->where(function ($q) {
                $q->where('description', 'LIKE', "%{$this->q}%")
                    ->orWhere('subject_type', 'LIKE', "%{$this->q}%");
            });
        }

        $activities = $query->paginate(20);

        return view('livewire.admin.activity.activity-viewer', [
            'activities' => $activities,
        ]);
    }
}
