<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <x-filament::section>
            <x-slot name="heading">
                Guardar Cambios
            </x-slot>
            
            <x-filament::button 
                type="submit" 
                size="lg"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>
                    Guardar Perfil
                </span>
                <span wire:loading>
                    <x-filament::loading-indicator class="h-5 w-5 inline-block" />
                    Guardando...
                </span>
            </x-filament::button>
        </x-filament::section>
    </form>
</x-filament-panels::page>
