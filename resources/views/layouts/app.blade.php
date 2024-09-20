<!DOCTYPE html>
<html lang="en">

<head>
    <title>Resarvation Application</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title')</title>
    {{-- <link rel="icon" type="image/x-icon" href="https://app.idoraa.com/images/favicon.ico"> --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @livewireStyles
    @wireUiScripts
    {{-- @livewireChartsScripts --}}
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>

<body>
    <div>
        <div class="flex-1 h-full overflow-y-auto">
            <x-notifications z-index="z-50" />
            <x-dialog z-index="z-50" blur="md" align="center" />

            <div x-data="{ offcanvas: false }">
                @if (Auth::user())
                    @include('admin-components.sidebar')
                @endif

                <div class="md:pl-80 lg:w-full flex flex-col flex-1 overflow-y-full">
                    <div class="md:hidden">
                        <button x-on:click="offcanvas = true" type="button"
                            class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    <main class="">
                        <div class="py-6">
                            <div class="mx-auto px-4 sm:px-6 md:px-8">
                                {{ $slot }}
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    {{-- Livewire Scripts --}}
    @livewireScripts
    @livewireChartsScripts
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}

    <script>
        function initToggle(element, {
            value
        }) {
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
