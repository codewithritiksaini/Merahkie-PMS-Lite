<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    use WithPagination;

    public string $search = '', $priorityFilter = '', $statusFilter = '';
    public bool $showDrawer = false, $isEditMode = false;
    public ?int $ticketId = null;
    public string $room_id = '', $issue = '', $priority = 'Medium',
                  $assigned_to = '', $status = 'Open', $notes = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetFields();
        $this->showDrawer = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $ticket = DB::table('maintenance_tickets')->find($id);
        if (!$ticket) return;
        $this->ticketId   = $ticket->id;
        $this->room_id    = (string)$ticket->room_id;
        $this->issue      = $ticket->issue;
        $this->priority   = $ticket->priority;
        $this->assigned_to = (string)($ticket->assigned_to ?? '');
        $this->status     = $ticket->status;
        $this->notes      = $ticket->notes ?? '';
        $this->isEditMode = true;
        $this->showDrawer = true;
    }

    public function store(): void
    {
        $this->validate([
            'room_id'  => 'required|exists:rooms,id',
            'issue'    => 'required|string|max:500',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'status'   => 'required|in:Open,In Progress,Completed,Cancelled',
        ]);

        $data = [
            'room_id'     => $this->room_id,
            'issue'       => $this->issue,
            'priority'    => $this->priority,
            'assigned_to' => $this->assigned_to ?: null,
            'status'      => $this->status,
            'notes'       => $this->notes ?: null,
            'updated_at'  => now(),
        ];

        if ($this->isEditMode) {
            DB::table('maintenance_tickets')->where('id', $this->ticketId)->update($data);
        } else {
            DB::table('maintenance_tickets')->insert(array_merge($data, [
                'reported_by' => Auth::id(),
                'created_at'  => now(),
            ]));
        }

        $this->resetFields();
        $this->showDrawer = false;
        $this->dispatch('toast', message: 'Ticket saved.', type: 'success');
    }

    public function delete(int $id): void
    {
        DB::table('maintenance_tickets')->where('id', $id)->delete();
        $this->dispatch('toast', message: 'Ticket deleted.', type: 'success');
    }

    private function resetFields(): void
    {
        $this->ticketId   = null;
        $this->room_id    = '';
        $this->issue      = '';
        $this->priority   = 'Medium';
        $this->assigned_to = '';
        $this->status     = 'Open';
        $this->notes      = '';
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function render(): mixed
    {
        $query = DB::table('maintenance_tickets')
            ->join('rooms', 'rooms.id', '=', 'maintenance_tickets.room_id')
            ->leftJoin('users', 'users.id', '=', 'maintenance_tickets.assigned_to')
            ->select('maintenance_tickets.*', 'rooms.room_number', 'users.name as assignee_name')
            ->when($this->search, fn ($q) => $q->where(function ($qq) {
                $qq->where('rooms.room_number', 'like', "%{$this->search}%")
                   ->orWhere('maintenance_tickets.issue', 'like', "%{$this->search}%");
            }))
            ->when($this->priorityFilter, fn ($q) => $q->where('maintenance_tickets.priority', $this->priorityFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('maintenance_tickets.status', $this->statusFilter))
            ->orderByRaw("CASE priority WHEN 'Critical' THEN 1 WHEN 'High' THEN 2 WHEN 'Medium' THEN 3 ELSE 4 END")
            ->orderBy('maintenance_tickets.created_at', 'desc');

        $tickets = $query->paginate(15);

        $counts = [
            'open'       => DB::table('maintenance_tickets')->where('status', 'Open')->count(),
            'inprogress' => DB::table('maintenance_tickets')->where('status', 'In Progress')->count(),
            'completed'  => DB::table('maintenance_tickets')->where('status', 'Completed')->count(),
            'critical'   => DB::table('maintenance_tickets')->where('priority', 'Critical')->where('status', '!=', 'Completed')->count(),
        ];

        return $this->view([
            'tickets' => $tickets,
            'counts'  => $counts,
            'rooms'   => Room::orderBy('room_number')->get(),
            'users'   => User::orderBy('name')->get(),
        ]);
    }
};
