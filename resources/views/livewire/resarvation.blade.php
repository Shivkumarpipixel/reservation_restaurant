<div class="container mx-auto px-4 py-8">
    @if ($openrestaurantList)
        <h2 class="text-2xl font-bold mb-6">Restaurant List (Today's Availability)</h2>
        @if ($restaurantList->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($restaurantList as $restaurant)
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}"
                            class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-xl font-semibold mb-2">{{ $restaurant->name }}</h3>
                            <p class="text-gray-600 mb-1"><i class="fas fa-envelope mr-2"></i>{{ $restaurant->email }}
                            </p>
                            <p class="text-gray-600 mb-1"><i
                                    class="fas fa-map-marker-alt mr-2"></i>{{ $restaurant->address }}</p>
                            <p class="text-gray-600 mb-3"><i
                                    class="fas fa-phone mr-2"></i>{{ $restaurant->phone_number }}</p>

                            @if ($restaurant->isAvailable)
                                <p class="text-green-500 font-bold text-lg mb-2">Available Seats:
                                    {{ $restaurant->availableSeatsToday }}</p>
                                <button wire:click="checkAvailability({{ $restaurant->id }})"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                                    Check Availability
                                </button>
                            @elseif ($restaurant->allBooked)
                                <p class="text-red-500 font-bold text-lg">All Booked</p>
                            @elseif ($restaurant->hadSeatsToday)
                                <p class="text-yellow-500 font-bold text-lg">No Availability</p>
                            @else
                                <p class="text-gray-500 font-bold text-lg">Not Available Today</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    @if ($checkAvailability)
        <div class="mt-8 bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gray-100 px-6 py-4">
                <h3 class="text-xl font-bold">{{ $selectedRestaurant->name }} - Available Seats for Today</h3>
            </div>
            <div class="p-6">
                @foreach ($selectedRestaurant->availability as $availability)
                    <div class="mb-4 pb-4 border-b last:border-b-0">
                        <p class="font-semibold">{{ ucfirst($availability->meal_type) }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- <p class="text-gray-600"><span class="font-medium">Time Slot:</span>
                                {{ $availability->time_slot }}</p> --}}
                            <p class="text-gray-600"><span class="font-medium">Available Seats:</span>
                                {{ $availability->available_seats }}</p>
                            <p class="text-gray-600"><span class="font-medium">Start Time:</span>
                                {{ \Carbon\Carbon::parse($availability->opening_time)->format('h:i A') }}</p>
                            <p class="text-gray-600"><span class="font-medium">End Time:</span>
                                {{ \Carbon\Carbon::parse($availability->closing_time)->format('h:i A') }}</p>
                        </div>
                    </div>
                @endforeach

                <h4 class="text-lg font-semibold mb-4 mt-6">Make a Reservation</h4>
                <form wire:submit.prevent="makeReservation">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input wire:model.defer="users.name" label="Name" placeholder="Enter your name" />
                        <x-input wire:model.defer="users.email" label="Email" placeholder="Enter your email" />
                        <x-input wire:model.defer="users.mobile_no" label="Mobile Number"
                            placeholder="Enter your mobile number" />
                        <x-select wire:model.defer="reservation.time_slot" label="Time Slot" :options="$selectedRestaurant->availability->pluck('meal_type', 'meal_type')"
                            placeholder="Select a time slot" />
                        <x-input wire:model.defer="reservation.reserved_seats" label="Number of Seats" type="number"
                            placeholder="Enter number of seats" />
                    </div>

                    <div class="flex mt-6 justify-between">
                        <x-button type="submit" primary label="Make Reservation" />
                        <x-button wire:click="goRestaurant" primary label="Back" />
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
