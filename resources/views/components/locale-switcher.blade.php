@php
    $current = app()->getLocale();
@endphp
<div class="locale-switcher d-flex align-items-center gap-1">
    @foreach(config('locales.supported', ['en']) as $locale)
        <form action="{{ route('locale.switch', $locale) }}" method="POST" class="d-inline m-0">
            @csrf
            <button type="submit"
                    class="btn btn-sm {{ $current === $locale ? 'btn-admin-primary' : 'btn-admin-outline' }} locale-switcher-btn py-1 px-2"
                    aria-label="{{ config('locales.labels.'.$locale, $locale) }}">
                {{ $locale === 'ar' ? 'عربي' : 'EN' }}
            </button>
        </form>
    @endforeach
</div>
