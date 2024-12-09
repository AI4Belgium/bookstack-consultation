<div>
    <label component="custom-checkbox" for="{{ $id ?? 'become_member' }}" class="tw-mt-3 tw-flex tw-items-center tw-gap-3" @if($errors->has('become_member')) text-neg @endif">
        <input type="checkbox" id="{{ $id ?? 'become_member' }}" required name="become_member" value="1" @if(getValFromUserSettingOrOld('become_member') === '1') checked @endif  @if($disabled ?? false) disabled="disabled" @endif>
        <span class="label">{{trans('segmentation.become_member')}}</span>
    </label>
    @include('users.account.parts.error', ['name' => 'become_member'])
</div>
