@if ($showLanguage ?? false)
  @include('users.parts.language-option-row', ['value' => old('language') ?? user()->getLocale()->appLocale(), 'showDescription' => $showDescription ?? true])
@endif

<div class="tw-grid tw-grid-cols-1">
    <label class="setting-list-label">{{ trans('segmentation.join_as') }}</label>
    <div class="stretch-inputs">
        <select name="join_as" id="user-join_as">
            <option value="">{{ trans('common.select') }}</option>
            @foreach(trans('segmentation.join_as_values') as $ac => $label)
                <option @if((setting()->getForCurrentUser('join_as', '')) === $ac) selected @endif value="{{ $ac }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="flex-container-column gap-xxs items-start wrap organisation-name-field mt-m" display="none">
    <h2 class="list-heading">{{trans('preferences.organisation_information')}}</h2>
    <label class="setting-list-label" for="organisation_name">{{ trans('preferences.organisation_name') }}</label>
    <div class="flex stretch-inputs">
        @include('form.text', ['name' => 'organisation_name', 'required' => true])
    </div>

    <label class="setting-list-label" for="website">{{ trans('preferences.website') }}</label>
    <div class="flex stretch-inputs">
        @include('form.text', ['name' => 'website'])
    </div>

    <label class="setting-list-label" for="vat">{{ trans('preferences.vat') }}</label>
    <div class="flex stretch-inputs">
        @include('form.text', ['name' => 'vat'])
    </div>

    <label class="setting-list-label" for="vat">{{ trans('settings.job_role') }}</label>
    <div class="flex stretch-inputs">
        @include('form.text', ['name' => 'job_role', 'required' => true])
    </div>
</div>

<div class="tw-grid tw-grid-cols-1">
    <label class="setting-list-label" for="email">{{ trans('auth.email') }}</label>
    <div class="stretch-inputs">
        @include('form.text', ['name' => 'email', 'required' => true, 'type' => 'email'])
    </div>
</div>

<div class="tw-grid tw-grid-cols-1">
    <label class="setting-list-label">{{ trans('segmentation.country') }}</label>
    <div class="stretch-inputs">
        <select name="country" id="user-country">
            @foreach(trans('segmentation.country_values') as $ac => $label)
                <option @if((setting()->getForCurrentUser('join_as', 'be')) === $ac) selected @endif value="{{ $ac }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="tw-grid tw-grid-cols-1">
    <label class="setting-list-label" for="city">{{ trans('segmentation.city') }}</label>
    <div class="stretch-inputs">
        @include('form.text', ['name' => 'city', 'required' => true])
    </div>
</div>

<div class="tw-grid tw-grid-cols-1">
    <label class="setting-list-label" for="user-region">{{trans('settings.region_name')}}</label>
    <div class="stretch-inputs">
      <select name="region" id="user-region">
          <option value="">{{ trans('common.select') }}</option>
          @foreach(trans('settings.regions') as $ac => $label)
              <option @if((setting()->getForCurrentUser('region', '')) === $ac) selected @endif value="{{ $ac }}">{{ $label }}</option>
          @endforeach
      </select>
    </div>
</div>

<script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function () {
        const accountTypeSelect = document.querySelectorAll('select[name="account_type"]');
        const organisationNameField = document.querySelector('.organisation-name-field');

        function toggleOrganisationNameField() {
            console.log(document.querySelector('select[name="account_type"]')?.value);
            if (document.querySelector('select[name="account_type"]')?.value !== 'citizen' && document.querySelector('select[name="account_type"]')?.value !== '') {
                organisationNameField.style.display = 'flex';
            } else {
                organisationNameField.style.display = 'none';
            }
        }

        accountTypeSelect.forEach(select => select.addEventListener('change', toggleOrganisationNameField));

        toggleOrganisationNameField();
    });
</script>