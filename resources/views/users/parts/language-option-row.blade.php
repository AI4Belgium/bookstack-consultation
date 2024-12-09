{{--
$value - Currently selected lanuage value
--}}
<div class="grid half v-center">
    <div>
        <label for="user-language" class="setting-list-label">{{ trans('settings.users_preferred_language') }}</label>
        @if ($showDescription ?? true)
        <p class="small">
            {{ trans('settings.users_preferred_language_desc') }}
        </p>
        @endif
    </div>
    <div class="flex stretch-inputs">
        <select name="language" id="user-language">
            @if (isset($forceSelect) && $forceSelect === true)
                <option value="" @if($value === $lang) selected @endif >{{ trans('common.select') }}</option>
            @endif
            @foreach(trans('settings.language_select') as $lang => $label)
                <option @if($value === $lang) selected @endif value="{{ $lang }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>