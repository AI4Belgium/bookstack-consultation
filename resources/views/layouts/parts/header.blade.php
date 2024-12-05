<header id="header" component="header-mobile-toggle" class="primary-background px-xl grid print-hidden">
    <div class="flex-container-row justify-space-between gap-s items-center">
        <div class="tw-flex tw-items-center tw-gap-1">
            @include('layouts.parts.header-logo')
            <a href="/update-lang/en" class="{{app()->getLocale() === 'en' ? 'tw-underline' : ''}}" >En</a>
            <a href="/update-lang/fr" class="{{app()->getLocale() === 'fr' ? 'tw-underline' : ''}}" >Fr</a>
            <a href="/update-lang/nl"class="{{app()->getLocale() === 'nl' ? 'tw-underline' : ''}}" >nl</a>
        </div>
        <div class="hide-over-l py-s">
            <button type="button"
                    refs="header-mobile-toggle@toggle"
                    title="{{ trans('common.header_menu_expand') }}"
                    aria-expanded="false"
                    class="mobile-menu-toggle">@icon('more')</button>
        </div>
    </div>

    <div class="flex-container-column items-center justify-center hide-under-l">
    @if(user()->hasAppAccess())
        @include('layouts.parts.header-search')
    @endif
    </div>

    <nav refs="header-mobile-toggle@menu" class="header-links">
        <div class="links text-center">
            @include('layouts.parts.header-links')
        </div>
        @if(!user()->isGuest())
            @include('layouts.parts.header-user-menu', ['user' => user()])
        @endif
    </nav>
</header>
