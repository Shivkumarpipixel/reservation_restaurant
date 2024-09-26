<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class BookingTable extends DataTableComponent
{
    protected $model = Reservation::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()->hideIf(1),
            Column::make("Restaurant Name", "restaurant.name")
                ->sortable()->searchable(),
            Column::make("Time slot", "time_slot")
                ->sortable()->searchable(),
            Column::make("Restaurant Image", "restaurant.image")
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    $imagePath = $value ? asset('storage/' . $value) : 'default-image-path';
                    return '<img src="' . $imagePath . '" class="rounded-full h-10 w-10 object-cover cursor-pointer" wire:click="$emit(\'onImageClick\', ' . $row->id . ')" alt="Restaurant Image">';
                })
                ->html(),
            Column::make("User Name", "user.name")
                ->sortable()->searchable(),
            Column::make("User Email", "user.email")
                ->sortable()->searchable(),
            Column::make("Time slot", "time_slot")
                ->sortable()->searchable(),
            Column::make("Reserved seats", "reserved_seats")
                ->sortable()->searchable(),
            Column::make("Reservation Date", "reservation_date")
                ->sortable()
                ->format(
                    fn($value, $row, Column $column) => Carbon::parse($value)->format('l d M, Y')
                )
                ->html()->searchable(),
            Column::make("Created at", "created_at")
                ->sortable()->hideIf(1),
            Column::make("Updated at", "updated_at")
                ->sortable()->hideIf(1),
        ];
    }

    public function builder(): Builder
    {
        return Reservation::query()
            ->with(['user', 'restaurant']);
    }
}
