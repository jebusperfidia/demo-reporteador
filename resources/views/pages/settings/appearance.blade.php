<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Appearance settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">

        <div x-data="{
            palettes: [
                { name: 'Violeta', value: '#7c3aed' },
                { name: 'Azul', value: '#1d4ed8' },
                { name: 'Esmeralda', value: '#047857' },
                { name: 'Naranja', value: '#c2410c' },
                { name: 'Rosa', value: '#be185d' },
                { name: 'Rojo', value: '#b91c1c' }
            ],
        
            selectedAppearance: $flux.appearance,
            selectedAccent: '#8b5cf6',
            savedAccent: '#8b5cf6',
            hasChanges: false,
        
            init() {
                try {
                    const stored = localStorage.getItem('reporter-accent');
        
                    if (this.isValidHex(stored)) {
                        this.selectedAccent = stored.toLowerCase();
                        this.savedAccent = stored.toLowerCase();
                    }
                } catch (error) {
                    // Se utilizará el color predeterminado.
                }
        
                this.applyAccent(this.savedAccent);
        
                this.$watch('selectedAppearance', () => this.updateChanges());
                this.$watch('selectedAccent', () => this.updateChanges());
            },
        
            isValidHex(value) {
                return typeof value === 'string' &&
                    /^#[0-9a-f]{6}$/i.test(value);
            },
        
            contrastColor(hex) {
                const [r, g, b] = hex.match(/[0-9a-f]{2}/gi)
                    .map(value => parseInt(value, 16) / 255);
        
                const linearize = value =>
                    value <= 0.04045 ?
                    value / 12.92 :
                    Math.pow((value + 0.055) / 1.055, 2.4);
        
                const luminance =
                    0.2126 * linearize(r) +
                    0.7152 * linearize(g) +
                    0.0722 * linearize(b);
        
                return (1.05 / (luminance + 0.05)) >=
                    ((luminance + 0.05) / 0.05) ?
                    '#ffffff' :
                    '#111827';
            },
        
            applyAccent(hex) {
                const root = document.documentElement;
                const contrast = this.contrastColor(hex);
        
                root.style.setProperty('--reporter-accent', hex);
                root.style.setProperty('--reporter-accent-contrast', contrast);
        
                // Actualizar también los colores de Flux UI
                root.style.setProperty('--color-accent', hex);
                root.style.setProperty('--color-accent-content', hex);
                root.style.setProperty('--color-accent-foreground', contrast);
            },
        
            updateChanges() {
                this.hasChanges =
                    this.selectedAppearance !== $flux.appearance ||
                    this.selectedAccent.toLowerCase() !== this.savedAccent;
            },
        
            save() {
                if (!this.hasChanges) return;
        
                try {
                    localStorage.setItem(
                        'reporter-accent',
                        this.selectedAccent.toLowerCase()
                    );
                } catch (error) {
                    Toaster.error('No se pudo guardar la preferencia de color.');
                    return;
                }
        
                this.applyAccent(this.selectedAccent);
                $flux.appearance = this.selectedAppearance;
        
                this.savedAccent = this.selectedAccent.toLowerCase();
                this.updateChanges();
        
                Toaster.success('Apariencia actualizada correctamente.');
            }
        }" class="space-y-8">
            {{-- Apariencia general --}}
            <section class="space-y-3">
                <div>
                    <h3 class="section-title">Tema de visualización</h3>
                    <p class="section-subtitle">
                        Selecciona cómo deseas visualizar el sistema.
                    </p>
                </div>

                <flux:radio.group variant="segmented" x-model="selectedAppearance">
                    <flux:radio value="light" icon="sun">Claro</flux:radio>
                    <flux:radio value="dark" icon="moon">Oscuro</flux:radio>
                    <flux:radio value="system" icon="computer-desktop">Sistema</flux:radio>
                </flux:radio.group>
            </section>

            {{-- Paleta de colores --}}
            <section class="space-y-4">
                <div>
                    <h3 class="section-title">Color principal</h3>
                    <p class="section-subtitle">
                        Personaliza el color de botones, controles y elementos destacados.
                    </p>
                </div>

                <div class="appearance-palette-grid">
                    <template x-for="palette in palettes" :key="palette.value">
                        <button type="button" class="appearance-palette-option"
                            :style="{
                                backgroundColor: palette.value,
                                borderColor: palette.value
                            }"
                            :aria-pressed="selectedAccent === palette.value"
                            :aria-label="'Seleccionar color ' + palette.name" @click="selectedAccent = palette.value">
                            <span x-text="palette.name"></span>

                            <flux:icon name="check" class="appearance-palette-check"
                                x-show="selectedAccent === palette.value" x-cloak />
                        </button>
                    </template>
                </div>

                {{-- Color personalizado --}}
                <div class="appearance-custom-color">
                    <div class="space-y-1">
                        <label for="reporter-custom-accent" class="section-title">
                            Color personalizado
                        </label>

                        <p class="section-subtitle">
                            Selecciona cualquier tonalidad mediante el selector de color.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input id="reporter-custom-accent" type="color" x-model="selectedAccent"
                            class="appearance-color-input" aria-label="Seleccionar color personalizado" />

                        <span class="text-sm font-semibold font-mono data-value"
                            x-text="selectedAccent.toUpperCase()"></span>
                    </div>
                </div>
            </section>

            {{-- Vista previa --}}
            <section class="appearance-color-preview">
                <div class="space-y-1">
                    <h3 class="section-title">Vista previa</h3>
                    <p class="section-subtitle">
                        Así se verá el color principal después de guardar.
                    </p>
                </div>

                <span class="appearance-preview-action"
                    :style="{
                        backgroundColor: selectedAccent,
                        color: contrastColor(selectedAccent),
                        borderColor: selectedAccent
                    }">
                    Acción principal
                </span>
            </section>

            {{-- Guardar cambios --}}
            <div class="flex justify-end pt-5">
                <flux:button variant="primary" icon="check" x-on:click="save()" x-bind:disabled="!hasChanges"
                    class="cursor-pointer">
                    Guardar cambios
                </flux:button>
            </div>
        </div>


    </x-pages::settings.layout>
</section>
