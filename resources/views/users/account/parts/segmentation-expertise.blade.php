<div class="tw-relative">
    <label class="setting-list-label" for="expertise_domain">{{trans('segmentation.expertise_domain')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
    <select multiple="multiple" name="expertise_domain[]" id="expertise_domain" style="width: 100%;" required>
        @foreach(trans('segmentation.expertise_domain_values') as $exKey => $exVal)
            <option value="{{$exKey}}" @if (in_array($exKey, getValFromUserSettingOrOld('expertise_domain', true) ?? [])) selected @endif >{{$exVal}}</option>
        @endforeach
    </select>
    @include('users.account.parts.error', ['name' => 'expertise_domain'])
     <!-- {{ print_r(setting()->getForCurrentUser('expertise_domain'), true) }} -->
</div>

<div class="tw-relative">
    <label class="setting-list-label" for="non_technical_expertise_domains">{{trans('segmentation.non_technical_expertise_domains')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
    <select multiple="multiple" name="non_technical_expertise_domains[]" id="non_technical_expertise_domains" style="width: 100%; height: 40px;" required>
        @foreach(trans('segmentation.non_technical_expertise_domains_values') as $exKey => $exVal)
        <option value="{{$exKey}}" @if (in_array($exKey, getValFromUserSettingOrOld('non_technical_expertise_domains', true) ?? [])) selected @endif >{{$exVal}}</option>
        @endforeach
    </select>
    @include('users.account.parts.error', ['name' => 'non_technical_expertise_domains'])
    <!-- {{print_r(old('non_technical_expertise_domains'), true)}} -->
</div>

<div class="tw-relative">
    <label class="setting-list-label" for="expertise_status">{{trans('segmentation.expertise_status')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
    <select name="expertise_status" id="expertise_status" class="tw-px-1 tw-w-full" required>
        <option value="">{{ trans('common.select') }}</option>
        @foreach(trans('segmentation.expertise_status_values') as $ac => $label)
            <option value="{{ $ac }}" @if (getValFromUserSettingOrOld('expertise_status') === $ac) selected @endif >{{ $label }}</option>
        @endforeach
    </select>
    @include('users.account.parts.error', ['name' => 'expertise_status'])
</div>

<div class="tw-relative">
    <label class="setting-list-label" for="application_sectors">{{trans('segmentation.application_sectors')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
    <select multiple="multiple" name="application_sectors[]" id="application_sectors" style="width: 100%; height: 40px;" required>
        @foreach(trans('segmentation.application_sectors_values') as $exKey => $exVal)
            <option value="{{$exKey}}" @if (in_array($exKey, getValFromUserSettingOrOld('application_sectors', true) ?? [])) selected @endif >{{$exVal}}</option>
        @endforeach
    </select>
    @include('users.account.parts.error', ['name' => 'application_sectors'])
</div>

<div class="tw-relative">
    <label class="setting-list-label" for="application_fields">{{trans('segmentation.application_fields')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
    <select multiple="multiple" name="application_fields[]" id="application_fields" style="width: 100%; height: 40px;" required>
        @foreach(trans('segmentation.application_fields_values') as $exKey => $exVal)
            <option value="{{$exKey}}" @if (in_array($exKey, getValFromUserSettingOrOld('application_fields', true) ?? [])) selected @endif >{{$exVal}}</option>
        @endforeach
    </select>
    @include('users.account.parts.error', ['name' => 'application_fields'])
</div>