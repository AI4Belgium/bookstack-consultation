<div>
    <label class="setting-list-label tw-text-lg tw-font-bold tw-mt-2" for="language">{{trans('segmentation.language')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
    <select name="language" id="language" class="tw-px-1 tw-w-full" required>
        <option value="">{{ trans('common.select') }}</option>
        @foreach(trans('segmentation.language_values') as $ac => $label)
            <option value="{{ $ac }}" @if ( $ac === (old('language') ?? user()->getLocale()->appLocale())) selected @endif >{{ $label }}</option>
        @endforeach
    </select>
    @include('users.account.parts.error', ['name' => 'language'])
</div>