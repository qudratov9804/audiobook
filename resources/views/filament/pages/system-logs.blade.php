<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">laravel.log</x-slot>
        <x-slot name="description">Showing the last {{ $lines }} lines &middot; {{ $this->getLogSize() }}</x-slot>

        <pre class="max-h-[70vh] overflow-auto rounded-lg bg-gray-950 p-4 text-xs text-gray-100 whitespace-pre-wrap">{{ $this->getLogContent() }}</pre>
    </x-filament::section>
</x-filament-panels::page>
