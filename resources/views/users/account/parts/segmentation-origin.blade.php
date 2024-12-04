<div>
    <label class="setting-list-label">{{ trans('segmentation.country') }}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
    <select name="country" id="country" class="tw-px-1 tw-w-full">
        @foreach(trans('segmentation.country_values') as $ac => $label)
            <option @if(getValFromUserSettingOrOld('country', false, 'be')  === $ac) selected @endif value="{{ $ac }}">{{ $label }}</option>
        @endforeach
    </select>
    @include('users.account.parts.error', ['name' => 'country'])
</div>

<div>
    <label class="setting-list-label" for="region">{{trans('segmentation.region')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
    <select name="region" id="region" class="tw-px-1 tw-w-full" required>
        <option value="">{{ trans('common.select') }}</option>
        @foreach(trans('segmentation.region_values') as $ac => $label)
            <option @if(getValFromUserSettingOrOld('region') === $ac) selected @endif value="{{ $ac }}">{{ $label }}</option>
        @endforeach
    </select>
    @include('users.account.parts.error', ['name' => 'region'])
</div>

<div>
    <label class="setting-list-label" for="city">{{ trans('segmentation.city') }}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
    <div class="stretch-inputs">
        @include('form.text', ['name' => 'city', 'required' => true, 'model' => (object)['city' => getValFromUserSettingOrOld('city')]])
    </div>
</div>
