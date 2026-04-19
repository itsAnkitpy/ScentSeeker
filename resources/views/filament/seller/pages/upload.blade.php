<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Upload Form --}}
        <x-filament::section>
            <x-slot name="heading">Upload Price List</x-slot>
            <x-slot name="description">Upload your product prices using our Excel template format.</x-slot>

            <div class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Click <strong>Upload Price List</strong> to select an .xlsx or .xls file (max 20MB). Your file is queued for processing and appears in Upload History below when staging completes.
                </p>

                <div class="flex items-center gap-3">
                    {{ $this->uploadAction }}
                    {{ $this->downloadTemplateAction }}
                </div>
            </div>
        </x-filament::section>

        {{-- Instructions --}}
        <x-filament::section>
            <x-slot name="heading">Import Guide</x-slot>

            <div class="text-sm space-y-3 text-gray-600 dark:text-gray-400">
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100 mb-1">Required columns:</p>
                    <p>Perfume Name, Brand, Size (ml), Price, Currency</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100 mb-1">Optional columns:</p>
                    <p>Stock Status, Product URL, Item Type, Concentration, Gender, Description, Notes, Image URL, Launch Year, Offer Details</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100 mb-1">How it works:</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Download the template and fill in your data</li>
                        <li>Upload the file — it gets queued for processing</li>
                        <li>View your staged data to check for errors</li>
                        <li>Admin reviews and publishes to the live site</li>
                    </ol>
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- Upload History --}}
    <div class="mt-6">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
