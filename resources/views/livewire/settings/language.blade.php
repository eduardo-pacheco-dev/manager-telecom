<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Language settings') }}</flux:heading>

    <x-settings.layout :heading="__('Language')" :subheading="__('Update the language for your account')">
        <div>
            <flux:select
                wire:model="locale"
                wire:change="updateLocale"
                :label="__('Language')"
            >
                @foreach ($this->locales() as $code => $name)
                    <flux:select.option value="{{ $code }}">{{ $name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="locale" />
        </div>
    </x-settings.layout>
</section>