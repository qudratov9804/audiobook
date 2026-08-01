<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-filament::section>
            <div class="text-sm text-gray-500">Pending Jobs</div>
            <div class="text-3xl font-bold">{{ $this->getPendingJobsCount() }}</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500">Failed Jobs</div>
            <div class="text-3xl font-bold text-danger-600">{{ $this->getFailedJobsCount() }}</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500">Batches In Progress</div>
            <div class="text-3xl font-bold">{{ $this->getBatchesInProgress() }}</div>
        </x-filament::section>
    </div>

    <x-filament::section class="mt-6">
        <x-slot name="heading">Failed Jobs</x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
