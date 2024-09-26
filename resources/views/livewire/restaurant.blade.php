<div>
    <div class="flex justify-end">
        <x-button wire:click="openRestaurant" sm outline slate label="Add Restaurant" />
    </div>

    @if ($openModal)
        <div class="fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-30 z-10 overflow-y-auto">
            <div class="w-2/4 h-auto relative max-h-screen overflow-y-auto">
                <x-card title="New/Update Restaurant">
                    <form wire:submit.prevent="onSaveRestaurant">
                        <!-- Restaurant Info -->
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input placeholder="Restaurant Name" wire:model.defer="restaurants.name"
                                label="Restaurant Name" />
                            <x-input placeholder="Phone Number" wire:model.defer="restaurants.phone_number"
                                label="Phone Number" />
                            <x-input placeholder="Email" wire:model.defer="restaurants.email" label="Email" />
                            <x-input placeholder="Image" type="file" wire:model.defer="restaurants.image"
                                label="Image" />
                            <x-input placeholder="Address" wire:model.defer="restaurants.address" label="Address" />
                        </div>

                        <!-- Availability Section: Breakfast, Lunch, Dinner -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-2">Availability</h3>

                            @foreach (['breakfast', 'lunch', 'dinner'] as $meal)
                                <div class="flex flex-col space-y-2 mb-4">
                                    <h4 class="text-md font-semibold">{{ ucfirst($meal) }}</h4>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-5">
                                        <div class="sm:col-span-2">
                                            <x-select label="Available Days" placeholder="Select days" multiselect
                                                :options="$dayOptions" wire:model.defer="{{ $meal }}Options" />
                                        </div>
                                        {{-- <x-input placeholder="Time Slot"
                                            wire:model.defer="availability.{{ $meal }}.time_slot"
                                            label="Time Slot" /> --}}
                                        <x-input placeholder="Available Seats" type="number"
                                            wire:model.defer="availability.{{ $meal }}.available_seats"
                                            label="Available Seats" />
                                        <x-input placeholder="Opening Time" type="time"
                                            wire:model.defer="availability.{{ $meal }}.opening_time"
                                            label="Opening Time" />
                                        <x-input placeholder="Closing Time" type="time"
                                            wire:model.defer="availability.{{ $meal }}.closing_time"
                                            label="Closing Time" />
                                    </div>
                                    <x-checkbox wire:model.defer="availability.{{ $meal }}.open"
                                        label="Open" />
                                </div>
                            @endforeach
                        </div>

                        <!-- Closed Dates -->
                        <div class="mt-6">
                            <x-select label="Closed Days" placeholder="Select days" multiselect :options="$dayOptions"
                                wire:model.defer="weekDays" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-between py-4">
                            <x-button type="submit" sm outline blue label="Submit" />
                            <x-button type="button" xs outline red label="Close" wire:click="closeModal" />
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    @endif

    @if ($openImage)
        <div class="fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-70 z-50 overflow-y-auto">
            <div class="w-2/4 h-3/4 relative">
                <x-card title="View Image">
                    <button type="button" wire:click="closeImage" class="absolute top-0 right-0 mt-4 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="flex justify-center items-center h-full">
                        @if ($imagePath)
                            <img src="{{ asset('storage/' . $imagePath) }}" alt="Restaurant Image"
                                class="max-w-full max-h-full rounded-lg object-cover">
                        @else
                            <p>No image available.</p>
                        @endif
                    </div>
                </x-card>
            </div>
        </div>
    @endif

    <div class="py-4">
        @livewire('restaurant-table')
    </div>
</div>
