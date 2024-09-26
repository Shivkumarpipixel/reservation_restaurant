<div :class="{'hidden': !sidebarOpen, 'block': sidebarOpen}" class="lg:block w-64 bg-white shadow-md">
    <div class="flex flex-col h-full">
        <!-- Sidebar header -->
        <div class="flex items-center justify-center h-16 bg-gray-100 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800">CMA</h1>
        </div>

        <!-- Navigation links -->
        <nav class="flex-1 px-4 py-6">
            <ul class="space-y-4">
                @php
                $routes = [
                    ['name' => 'Restaurant', 'path' => 'restaurant', 'icon' => 'home'],
                    ['name' => 'Reservations', 'path' => 'resarvations', 'icon' => 'calendar'],
                    ['name' => 'Bookings', 'path' => 'bookings', 'icon' => 'book-open'],
                ];
                $currentRoute = Route::currentRouteName();
                @endphp

                @foreach ($routes as $route)
                    <li>
                        <a href="{{ route($route['path']) }}"
                           class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                                  {{ $currentRoute === $route['path']
                                     ? 'bg-gray-200 text-gray-900'
                                     : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                @if($route['icon'] === 'home')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                @elseif($route['icon'] === 'calendar')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                @elseif($route['icon'] === 'book-open')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                @endif
                            </svg>
                            {{ $route['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</div>