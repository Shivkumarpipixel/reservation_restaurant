<?php

namespace App\Http\Livewire;

use App\Models\Availability;
use App\Models\ClosedDate;
use App\Models\Restaurant as ModelsRestaurant;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\Actions;

class Restaurant extends Component
{
    use Actions, WithFileUploads;

    public $openModal = false;
    public $openImage = false;
    public $imagePath = null;
    public $OpenDays = [];
    public $restaurants;
    public $availability = [
        'breakfast' => ['time_slot' => '', 'available_seats' => '', 'opening_time' => '', 'closing_time' => '', 'open' => false],
        'lunch' => ['time_slot' => '', 'available_seats' => '', 'opening_time' => '', 'closing_time' => '', 'open' => false],
        'dinner' => ['time_slot' => '', 'available_seats' => '', 'opening_time' => '', 'closing_time' => '', 'open' => false],
    ];
    public $breakfastOptions = [];
    public $lunchOptions = [];
    public $dinnerOptions = [];
    public $weekDays = [];
    public $dayOptions = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    protected $listeners = ['onImageClick', 'onDeleteRestaurant', 'onEditRestaurant'];

    public function openRestaurant()
    {
        $this->openModal = true;
    }

    public function closeModal()
    {
        $this->openModal = false;
    }

    public function mount()
    {
        $this->dayOptions = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    }

    public function render()
    {
        return view('livewire.restaurant');
    }

    public function onSaveRestaurant()
    {
        $mealDays = array_merge($this->breakfastOptions ?? [], $this->lunchOptions ?? [], $this->dinnerOptions ?? []);

        $overlapDays = array_intersect($mealDays, $this->weekDays ?? []);

        if (!empty($overlapDays)) {
            $this->notification()->error('The selected meal days cannot overlap with closed days: ' . implode(', ', $overlapDays));
            return;
        }

        $rules = [
            'restaurants.name' => 'required|string|max:255',
            'restaurants.email' => 'required|email|max:255',
            'restaurants.address' => 'required|string|max:255',
            'restaurants.phone_number' => 'required|string|max:15',
            'restaurants.image' => 'nullable|image|max:2048',
            // 'availability.*.time_slot' => 'required|string',
            'availability.*.available_seats' => 'required|integer|min:1',
            'availability.*.opening_time' => 'required|date_format:H:i',
            'availability.*.closing_time' => 'required|date_format:H:i|after:availability.*.opening_time',
            'availability.*.open' => 'boolean',
            'breakfastOptions' => 'required|array|min:1',
            'breakfastOptions.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'lunchOptions' => 'required|array|min:1',
            'lunchOptions.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'dinnerOptions' => 'required|array|min:1',
            'dinnerOptions.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'weekDays' => 'array',
            'weekDays.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ];

        $messages = [
            'restaurants.name.required' => 'The restaurant name is required.',
            'restaurants.email.required' => 'The email address is required.',
            'restaurants.email.email' => 'Please enter a valid email address.',
            'restaurants.address.required' => 'The restaurant address is required.',
            'restaurants.phone_number.required' => 'The phone number is required.',
            'restaurants.image.image' => 'The uploaded file must be an image.',
            'restaurants.image.max' => 'The image size should not exceed 2MB.',
            // 'availability.*.time_slot.required' => 'The time slot is required for all meal types.',
            'availability.*.available_seats.required' => 'The number of available seats is required for all meal types.',
            'availability.*.available_seats.min' => 'The number of available seats must be at least 1.',
            'availability.*.opening_time.required' => 'The opening time is required for all meal types.',
            'availability.*.closing_time.required' => 'The closing time is required for all meal types.',
            'availability.*.closing_time.after' => 'The closing time must be after the opening time.',
            'weekDays.*.in' => 'Invalid day selected for closed days.',
            'breakfastOptions.required' => 'Please select at least one day for breakfast.',
            'lunchOptions.required' => 'Please select at least one day for lunch.',
            'dinnerOptions.required' => 'Please select at least one day for dinner.',
            'breakfastOptions.*.in' => 'Invalid day selected for breakfast.',
            'lunchOptions.*.in' => 'Invalid day selected for lunch.',
            'dinnerOptions.*.in' => 'Invalid day selected for dinner.',
        ];

        $this->validate($rules, $messages);

        $restaurant = !empty($this->restaurants['id']) ? ModelsRestaurant::find($this->restaurants['id']) : new ModelsRestaurant();

        if (isset($this->restaurants['image'])) {
            $imageName = $this->restaurants['image']->store('restaurant_images', 'public');
            $restaurant->image = $imageName;
        }

        $restaurant->fill([
            'name' => $this->restaurants['name'],
            'email' => $this->restaurants['email'],
            'password' => Hash::make("Test@123"),
            'address' => $this->restaurants['address'],
            'phone_number' => $this->restaurants['phone_number'],
        ]);

        $restaurant->save();

        Availability::where('restaurant_id', $restaurant->id)->delete();

        $mealTypes = [
            'breakfast' => $this->breakfastOptions,
            'lunch' => $this->lunchOptions,
            'dinner' => $this->dinnerOptions
        ];

        foreach ($mealTypes as $mealType => $days) {
            foreach ($days as $day) {
                if (isset($this->availability[$mealType])) {
                    Availability::create([
                        'restaurant_id' => $restaurant->id,
                        'day' => $day,
                        'meal_type' => $mealType,
                        // 'time_slot' => $this->availability[$mealType]['time_slot'],
                        'available_seats' => $this->availability[$mealType]['available_seats'],
                        'open' => $this->availability[$mealType]['open'],
                        'opening_time' => $this->availability[$mealType]['opening_time'],
                        'closing_time' => $this->availability[$mealType]['closing_time'],
                    ]);
                }
            }
        }

        ClosedDate::where('restaurant_id', $restaurant->id)->delete();

        if (!empty($this->weekDays)) {
            foreach ($this->weekDays as $closedDay) {
                ClosedDate::create([
                    'restaurant_id' => $restaurant->id,
                    'weekday' => $closedDay,
                ]);
            }
        }

        $restaurantId = $restaurant->id;

        $this->notification()->success(
            $restaurantId ? 'Restaurant Updated Successfully!' : 'Restaurant Created Successfully!'
        );

        $this->reset(['restaurants', 'availability', 'breakfastOptions', 'lunchOptions', 'dinnerOptions', 'weekDays']);
        $this->emit('onRestaurantAddUpdate');
        $this->openModal = false;
    }


    public function onImageClick($imageId)
    {
        $image = ModelsRestaurant::find($imageId);
        $this->imagePath = $image->image;
        $this->openImage = true;
    }

    public function closeImage()
    {
        $this->openImage = false;
    }

    public function onEditRestaurant($id)
    {
        $restaurant = ModelsRestaurant::find($id);
        $this->restaurants = $restaurant->toArray();
        $this->openModal = true;
        $this->emit('onRestaurantAddUpdate');
    }

    public function onDeleteRestaurant($id)
    {
        $this->dialog()->confirm([
            'title' => 'Are you Sure?',
            'icon' => 'exclamation-circle',
            'iconColor' => 'text-red-500',
            'description' => 'Are you sure you want to delete this Restaurant, The action cannot be undone?',
            'accept' => [
                'label' => 'Yes, delete it',
                'method' => 'doDeleteRestaurant',
                'params' => $id,
                'color' => 'negative',
                'size' => 'md',
            ],
            'reject' => [
                'label' => 'No',
                'size' => 'md',
            ],
        ]);
    }

    public function doDeleteRestaurant($id)
    {
        $restaurant = ModelsRestaurant::find($id);
        if ($restaurant) {
            $restaurant->delete();
            $this->notification()->success('Restaurant Deleted Successfully!');
            $this->emit('onRestaurantAddUpdate');
        }
    }
}
