
            <div class="flex items-center gap-3 px-6 py-4 mb-4 border-b border-gray-100 dark:border-white/5">
                <x-filament-panels::avatar.user size="lg" :user="auth()->user()" />
                <div class="flex flex-col">
                    <span class="font-bold text-sm text-gray-900 dark:text-white">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="text-xs text-gray-500">
                        Farmacéutico
                    </span>
                </div>
            </div>
        