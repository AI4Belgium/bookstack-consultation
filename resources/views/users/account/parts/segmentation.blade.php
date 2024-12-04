
<script nonce="{{ $cspNonce }}" type="text/javascript" >
    window.MultiselectDropdownOptions = {
        style: {
            width: '100%',
        }
    }
</script>
<script nonce="{{ $cspNonce }}" type="text/javascript" src="/libs/segmentation/multiselect-dropdown.js"></script>

<div class="tw-grid tw-grid-cols-1">
    <label class="setting-list-label tw-text-lg tw-font-bold">{{ trans('segmentation.join_as') }}:</label>
    <div class="stretch-inputs">
        <select name="join_as" id="join_as" class="tw-px-1">
            <option value="">{{ trans('common.select') }}</option>
            @foreach(trans('segmentation.join_as_values') as $ac => $label)
                <option @if(getValFromUserSettingOrOld('join_as') === $ac) selected @endif value="{{ $ac }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="tw-flex tw-flex-col tw-gap-1 items-start wrap organisation-name-field" display="none" data-parent="organisation">
    <input type="hidden" name="is_org" value="1" />

    <span class="tw-text-lg tw-font-bold tw-mt-2">{{trans('segmentation.organisation.info')}}</span>
    <div>
        <label class="setting-list-label" for="org_name">{{ trans('segmentation.organisation.name') }}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
        <div class="flex stretch-inputs">
            @include('form.text', ['name' => 'org_name', 'required' => true, 'model' => (object)['org_name' => getValFromUserSettingOrOld('org_name')]])
        </div>
        @include('users.account.parts.error', ['name' => 'org_name'])
    </div>

    <div>
        <label class="setting-list-label" for="founded">{{ trans('segmentation.organisation.founded') }}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
        <div class="flex stretch-inputs">
            <input type="number" name="founded" required min="300" max="{{date("Y")}}" class="!tw-w-full" value="{{getValFromUserSettingOrOld('founded')}}">
        </div>
        @include('users.account.parts.error', ['name' => 'founded'])
    </div>

    <div>
        <label class="setting-list-label" for="vat">{{ trans('segmentation.organisation.vat') }}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
        <div class="flex stretch-inputs">
            <input type="text" name="vat" pattern="^BE0[1-9][0-9]{7}$|^0[1-9][0-9]{7}$" value="{{getValFromUserSettingOrOld('vat')}}" required />
        </div>
        @include('users.account.parts.error', ['name' => 'vat'])
    </div>

    @include('users.account.parts.segmentation-origin')

    <div>
        <label class="setting-list-label tw-text-lg tw-font-bold tw-mt-2" for="org_profile">{{trans('segmentation.organisation.profile')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
        <select name="org_profile" id="org_profile" class="tw-px-1 tw-w-full" required>
            <option value="">{{ trans('common.select') }}</option>
            @foreach(trans('segmentation.organisation.profile_values') as $ac => $label)
                <option value="{{ $ac }}" @if ($ac === getValFromUserSettingOrOld('org_profile')) selected @endif >{{ $label }}</option>
            @endforeach
        </select>
        @include('users.account.parts.error', ['name' => 'org_profile'])
    </div>

    <div>
        <label class="setting-list-label tw-text-lg tw-font-bold tw-mt-2" for="nb_employees">{{trans('segmentation.organisation.number_of_employees')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
        <select name="nb_employees" id="nb_employees" class="tw-px-1 tw-w-full" required>
            <option value="">{{ trans('common.select') }}</option>
            @foreach(trans('segmentation.organisation.number_of_employees_values') as $ac => $label)
                <option value="{{ $ac }}" @if ($ac === getValFromUserSettingOrOld('nb_employees')) selected @endif >{{ $label }}</option>
            @endforeach
        </select>
        @include('users.account.parts.error', ['name' => 'nb_employees'])
    </div>

    <div>
        <label class="setting-list-label tw-text-lg tw-font-bold tw-mt-2" for="my_org">{{trans('segmentation.my_organisation')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
        <select name="my_org" id="my_org" class="tw-px-1 tw-w-full" required>
            <option value="">{{ trans('common.select') }}</option>
            @foreach(trans('segmentation.my_organisation_values') as $ac => $label)
                <option value="{{ $ac }}" @if ($ac === getValFromUserSettingOrOld('my_org')) selected @endif >{{ $label }}</option>
            @endforeach
        </select>
        @include('users.account.parts.error', ['name' => 'my_org'])
    </div>

    <span class="tw-text-lg tw-font-bold  tw-mt-2">{{trans('segmentation.expertise_info')}}</span>
    @include('users.account.parts.segmentation-expertise')

    @include('users.account.parts.segmentation-personal-info', ['showJobRole' => true])

    @include('users.account.parts.segmentation-lang')

    @include('users.account.parts.segmentation-be-member')
</div>


<div class="tw-flex tw-flex-col tw-gap-1" data-parent="citizen">
    <input type="hidden" name="is_org" value="0" />

    @include('users.account.parts.segmentation-personal-info')

    @include('users.account.parts.segmentation-origin')

    <div>
        <label class="setting-list-label tw-text-lg tw-font-bold tw-mt-2" for="user_profile">{{trans('segmentation.profile')}}<span class="tw-text-red-500 tw-font-bold">*</span>:</label>
        <select name="user_profile" id="user_profile" class="tw-px-1 tw-w-full" required>
            <option value="">{{ trans('common.select') }}</option>
            @foreach(trans('segmentation.profile_values') as $ac => $label)
                <option value="{{ $ac }}" @if ($ac === getValFromUserSettingOrOld('user_profile')) selected @endif >{{ $label }}</option>
            @endforeach
        </select>
        @include('users.account.parts.error', ['name' => 'user_profile'])
    </div>

    @include('users.account.parts.segmentation-lang')

    <div>
        <label component="custom-checkbox" class="toggle-switch @if($errors->has('is_expert')) text-neg @endif">
            <input type="checkbox" name="is_expert" value="1" @if(getValFromUserSettingOrOld('is_expert') === '1' ) checked @endif  @if($disabled ?? false) disabled="disabled" @endif>
            <span tabindex="0" role="checkbox" aria-checked="{{ getValFromUserSettingOrOld('is_expert') === '1' ? 'true' : 'false' }}" class="custom-checkbox text-primary">@icon('check')</span>
            <span class="label">{{trans('segmentation.is_expert')}} <br/> <span class="tw-text-xs">{{ trans('segmentation.is_expert_notice') }} </span></span>
        </label>
    </div>

    <div data-parent="expertise">
        <span class="tw-text-lg tw-font-bold tw-mt-2">{{trans('segmentation.expertise_info')}}</span>
        @include('users.account.parts.segmentation-expertise')
    </div>

    @include('users.account.parts.segmentation-be-member')
</div>

<div class="form-group text-right">
    <button class="button" id="segmentation_submint_btn">{{ trans('common.save') }}</button>
</div>



<script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function () {
        const accountTypeSelect = document.querySelectorAll('select[name="join_as"]');
        const organisationNameFields = document.querySelector('[data-parent="organisation"]');
        const citizenFields = document.querySelector('[data-parent="citizen"]');
        const btn = document.getElementById('segmentation_submint_btn');

        const isExpertCheckbox = document.querySelector('input[name="is_expert"]');
        const expertiseFields = document.querySelector('[data-parent="expertise"]');
        function toggleExpertiseFields() {
            if (isExpertCheckbox.checked) {
                expertiseFields.style.display = 'block';
                expertiseFields.querySelectorAll('input, select').forEach(input => input.disabled = false);
            } else {
                expertiseFields.style.display = 'none';
                expertiseFields.querySelectorAll('input, select').forEach(input => input.disabled = true);
            }
        }

        isExpertCheckbox.addEventListener('change', toggleExpertiseFields);
        toggleExpertiseFields();

        function toggleOrganisationNameField() {
            const value = document.querySelector('select[name="join_as"]')?.value;
            console.log(value);
            if (value === 'organisation') {
                organisationNameFields.style.display = 'flex';
                citizenFields.style.display = 'none';
                organisationNameFields.querySelectorAll('input, select').forEach(input => input.disabled = false);
                citizenFields.querySelectorAll('input, select').forEach(input => input.disabled = true);
                btn.disabled = false;
            } else if (value === 'citizen') {
                citizenFields.style.display = 'flex';
                organisationNameFields.style.display = 'none';
                organisationNameFields.querySelectorAll('input, select').forEach(input => input.disabled = true);
                citizenFields.querySelectorAll('input, select').forEach(input => input.disabled = false);
                toggleExpertiseFields();
                btn.disabled = false;
            } else {
                organisationNameFields.style.display = 'none';
                citizenFields.style.display = 'none';
                btn.disabled = true;
            }
        }

        accountTypeSelect.forEach(select => select.addEventListener('change', toggleOrganisationNameField));
        toggleOrganisationNameField();
        setTimeout(toggleOrganisationNameField, 300);
    });
</script>