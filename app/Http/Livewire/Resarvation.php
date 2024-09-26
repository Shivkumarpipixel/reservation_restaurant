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

            foreach ($this->selectedRestaurant->availability as $availability) {
                if (isset($reservations[$availability->time_slot])) {
                    $reservedSeats = $reservations[$availability->time_slot]->sum('reserved_seats');
                    $availability->available_seats -= $reservedSeats;
                }
            }

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
        $rules = [
            'users.name' => 'required|string|max:255',
            'users.email' => 'required|email|max:255',
            'users.mobile_no' => 'required|string|max:255',
            'reservation.time_slot' => 'required|string',
            'reservation.reserved_seats' => 'required|integer|min:1',
        ];

        $messages = [
            'users.name.required' => 'The name is required.',
            'users.email.required' => 'The email address is required.',
            'users.email.email' => 'Please enter a valid email address.',
            'users.mobile_no.required' => 'The mobile number is required.',
            'reservation.time_slot.required' => 'The time slot is required.',
            'reservation.reserved_seats.required' => 'The number of reserved seats is required.',
            'reservation.reserved_seats.min' => 'The number of reserved seats must be at least 1.',
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
