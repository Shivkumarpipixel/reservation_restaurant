<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Restaurant;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class RestaurantTable extends DataTableComponent
{
    protected $model = Restaurant::class;

    protected $listeners = ['onRestaurantAddUpdate' => '$refresh'];

    public function configure(): void
    {
        $this->setEagerLoadAllRelationsEnabled();
        $this->setDefaultSort('created_at', 'desc');
        $this->setFilterLayoutSlideDown();
        $this->setQueryStringDisabled();
        $this->setPerPageAccepted([25, 50, 75, 100]);
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()->hideIf(1),
            Column::make("Restaurant Name", "name")
                ->sortable(),
            Column::make("Email", "email")
                ->sortable(),
            Column::make("Image", "image")
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    $imagePath = $value ? asset('storage/' . $value) : 'default-image-path'; 
                    return '<img src="' . $imagePath . '" class="rounded-full h-10 w-10 object-cover cursor-pointer" wire:click="$emit(\'onImageClick\', ' . $row->id . ')" alt="Restaurant Image">';
                })
                ->html(),

            Column::make("Password", "password")
                ->sortable()->hideIf(1),
            Column::make("Address", "address")
                ->sortable(),
            Column::make("Phone number", "phone_number")
                ->sortable(),
            Column::make("Date", "created_at")
                ->sortable()
                ->format(
                    fn($value, $row, Column $column) => Carbon::parse($value)->format('l d M, Y')
                )
                ->html()->searchable(),
            Column::make("Updated at", "updated_at")
                ->sortable()->hideIf(1),
            ButtonGroupColumn::make('Actions', 'id')
                ->attributes(function ($row) {
                    return [
                        'class' => 'flex gap-3',
                    ];
                })
                ->buttons([

                    LinkColumn::make('Actions')
                        ->title(fn($row) => 'Edit')
                        ->location(fn($row) => 'javascript:void(0)')
                        ->attributes(function ($row) {
                            return [
                                'class' => 'inline-flex items-center px-2.5 py-1.5 border border-blue-300 text-xs font-medium rounded text-blue-600 bg-blue-50 hover:bg-blue-100 hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
                                'wire:click' => "\$emit('onEditRestaurant', {$row->id})",
                            ];
                        }),

                    LinkColumn::make('Actions')
                        ->title(fn($row) => 'Delete')
                        ->location(fn($row) => 'javascript:void(0)')
                        ->attributes(function ($row) {
                            return [
                                'class' => 'inline-flex items-center px-2.5 py-1.5 border border-red-300 text-xs font-medium rounded text-red-600 bg-red-50 hover:bg-red-100 hover:border-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400', // Adjusted ring color
                                'wire:click' => "\$emit('onDeleteRestaurant', {$row->id})",
                            ];
                        }),
                ]),
        ];
    }
}
