<?php

namespace App\Livewire\Settings;

use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Language settings')]
class Language extends Component
{
    public string $locale = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->locale = app()->getLocale();
    }

    /**
     * Get the available locales.
     *
     * @return array<string, string>
     */
    public function locales(): array
    {
        return config('locales.supported');
    }

    /**
     * Update the session locale.
     */
    public function updateLocale(): void
    {
        $validated = $this->validate([
            'locale' => ['required', 'in:'.implode(',', array_keys($this->locales()))],
        ]);

        session()->put('locale', $validated['locale']);

        app()->setLocale($validated['locale']);

        Flux::toast(text: __('Language updated.'));

        $this->redirect(route('language.edit'), navigate: true);
    }
}
