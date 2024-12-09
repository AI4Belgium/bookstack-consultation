<span class="tw-text-lg tw-font-bold tw-mt-2">{{trans('segmentation.personal_info')}}</span>
<div>
    <label class="setting-list-label" for="last_name">{{ trans('segmentation.last_name') }}*</span>:</label>
    <div class="stretch-inputs">
        @include('form.text', ['name' => 'last_name', 'required' => true, 'model' => (object)['last_name' => getValFromUserSettingOrOld('last_name')]])
    </div>
</div>

<div>
    <label class="setting-list-label" for="first_name">{{ trans('segmentation.first_name') }}*</span>:</label>
    <div class="stretch-inputs">
        @include('form.text', ['name' => 'first_name', 'required' => true, 'model' => (object)['first_name' => getValFromUserSettingOrOld('first_name')]])
    </div>
</div>

@if (isset($showJobRole) && $showJobRole)
    <div>
        <label class="setting-list-label" for="job_role">{{ trans('segmentation.job_role') }}*</span>:</label>
        <div class="flex stretch-inputs">
            @include('form.text', ['name' => 'job_role', 'required' => true, 'model' => (object)['job_role' => getValFromUserSettingOrOld('job_role')]])
        </div>
    </div>
@endif

<div>
    <label class="setting-list-label" for="email">{{ trans('auth.email') }}*</span>:</label>
    <div class="stretch-inputs">
        @include('form.text', ['name' => 'email', 'required' => true, 'type' => 'email'])
    </div>
</div>