<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reservation Application</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @livewireStyles
    @wireUiScripts
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>

<body>
    <div x-data="{ sidebarOpen: false }" class="flex h-screen ">
        <!-- Sidebar -->
      @include('components.sidebar')

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="bg-white shadow-sm lg:hidden">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none focus:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto">
                <div class="py-6">
                    <div class="mx-auto px-4 sm:px-6 md:px-8">
                        <x-notifications z-index="z-50" />
                        <x-dialog z-index="z-50" blur="md" align="center" />
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    @livewireScripts

    <script>
        function initToggle(element, { value }) {
            element.addEventListener('click', () => {
                element.checked = !element.checked;
                const event = new Event('change');
                element.dispatchEvent(event);
            });
        }

        Alpine.directive('toggle', initToggle);

        Livewire.on('refreshPage', () => {
            window.location.reload();
        });
    </script>
</body>

</html>