<?php

namespace App\Http\Livewire;

use App\Models\Availability;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use WireUi\Traits\Actions;

class Resarvation extends Component
{
    use Actions;
    public $restaurantList = [];
    public $openrestaurantList = true;
    public $today;
    public $checkAvailability = false;
    public $selectedRestaurant = null;
    public $users = [
        'name' => '',
        'email' => '',
        'mobile_no' => ''
    ];
    public $reservation = [
        'time_slot' => '',
        'reserved_seats' => '',
        'reservation_date' => ''
    ];

    public function mount()
    {
        $this->today = Carbon::now()->format('l');
        $this->loadRestaurants();
    }

    public function loadRestaurants()
    {
        $this->restaurantList = Restaurant::with(['availability' => function ($query) {
            $query->where('day', $this->today)->where('open', true);
        }])->get();

        foreach ($this->restaurantList as $restaurant) {
            $availability = $restaurant->availability;
            if ($availability->count()) {
                $reservations = Reservation::where('restaurant_id', $restaurant->id)
                    ->whereDate('created_at', Carbon::today())
                    ->get()
                    ->groupBy('time_slot');

                $totalAvailableSeats = 0;
                $totalSeats = 0;
                $hadSeatsToday = false;
                foreach ($availability as $slot) {
                    if ($slot->open) {
                        $hadSeatsToday = true;
                        $totalSeats += $slot->available_seats;
                        if (isset($reservations[$slot->meal_type])) {
                            $reservedSeats = $reservations[$slot->meal_type]->sum('reserved_seats');
                            $slot->available_seats -= $reservedSeats;
                        }
                        $totalAvailableSeats += max(0, $slot->available_seats);
                    }
                }

                $restaurant->isAvailable = $totalAvailableSeats > 0;
                $restaurant->availableSeatsToday = $totalAvailableSeats;
                $restaurant->hadSeatsToday = $hadSeatsToday;
                $restaurant->allBooked = $hadSeatsToday && $totalAvailableSeats == 0;
            } else {
                $restaurant->isAvailable = false;
                $restaurant->availableSeatsToday = 0;
                $restaurant->hadSeatsToday = false;
                $restaurant->allBooked = false;
            }
        }
    }

    public function checkAvailability($restaurantId)
    {
        $this->selectedRestaurant = Restaurant::with(['availability' => function ($query) {
            $query->where('day', $this->today)->where('open', true);
        }])->find($restaurantId);

        if ($this->selectedRestaurant && $this->selectedRestaurant->availability->count()) {
            $reservations = Reservation::where('restaurant_id', $restaurantId)
                ->whereDate('created_at', Carbon::today())
                ->get()
                ->groupBy('time_slot');

            $totalAvailableSeats = 0;
            foreach ($this->selectedRestaurant->availability as $availability) {
                if (isset($reservations[$availability->meal_type])) {
                    $reservedSeats = $reservations[$availability->meal_type]->sum('reserved_seats');
                    $availability->available_seats -= $reservedSeats;
                }
                $totalAvailableSeats += max(0, $availability->available_seats);
            }

            $this->selectedRestaurant->availableSeatsToday = $totalAvailableSeats;

            $this->checkAvailability = true;
            $this->openrestaurantList = false;
        } else {
            $this->notification()->error('No availability for today at ' . $this->selectedRestaurant->name . '.');
            $this->openrestaurantList = true;
        }
    }

    public function goRestaurant()
    {
        $this->checkAvailability = false;
        $this->openrestaurantList = true;
    }

    public function makeReservation()
    {
        $reservations = Reservation::where('restaurant_id', $this->selectedRestaurant->id)
            ->whereDate('created_at', Carbon::today())
            ->get()
            ->groupBy('time_slot');

        $availableSeats = 0;
        foreach ($this->selectedRestaurant->availability as $availability) {
            if ($availability->meal_type == $this->reservation['time_slot'] && $availability->open) {
                $reservedSeats = isset($reservations[$availability->meal_type])
                    ? $reservations[$availability->meal_type]->sum('reserved_seats')
                    : 0;
                $availableSeats = max(0, $availability->available_seats - $reservedSeats);
                break;
            }
        }
    
        $rules = [
            'users.name' => 'required|string|max:255',
            'users.email' => 'required|email|max:255|unique:users,email,' . ($this->users['id'] ?? ''),
            'users.mobile_no' => 'required|string|max:255',
            'reservation.time_slot' => 'required|string',
            'reservation.reserved_seats' => [
                'required',
                'integer',
                'min:1',
                'max:' . $availableSeats
            ],
        ];

        $messages = [
            'users.name.required' => 'The name is required.',
            'users.email.required' => 'The email address is required.',
            'users.email.email' => 'Please enter a valid email address.',
            'users.email.unique' => 'This email address is already in use.',
            'users.mobile_no.required' => 'The mobile number is required.',
            'reservation.time_slot.required' => 'The time slot is required.',
            'reservation.reserved_seats.required' => 'The number of reserved seats is required.',
            'reservation.reserved_seats.min' => 'The number of reserved seats must be at least 1.',
            'reservation.reserved_seats.max' => 'The number of reserved seats cannot exceed the available seats (' . $availableSeats . ').',
        ];

        $this->validate($rules, $messages);

        $user = !empty($this->users['id']) ? User::find($this->users['id']) : new User();

        $user->fill([
            'name' => $this->users['name'],
            'email' => $this->users['email'],
            'password' => Hash::make("Test@123"),
            'mobile_no' => $this->users['mobile_no'],
            'user_type' => 'customer',
        ]);

        $user->save();
        if ($user) {
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'restaurant_id' => $this->selectedRestaurant->id,
                'time_slot' => $this->reservation['time_slot'],
                'reserved_seats' => $this->reservation['reserved_seats'],
                'reservation_date' => Carbon::now()
            ]);

            if ($reservation) {
                $this->notification()->success('Reservation created successfully!');
                $this->reset(['users', 'reservation', 'checkAvailability', 'selectedRestaurant']);
                $this->loadRestaurants();
                $this->emit('reservationCreated');
                return redirect()->intended('/bookings');
            } else {
                $this->notification()->error('Failed to create reservation. Please try again.');
            }
        } else {
            $this->notification()->error('Failed to create or find user. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.resarvation');
    }
}
