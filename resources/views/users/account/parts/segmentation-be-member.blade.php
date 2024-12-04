<div>
    <label component="custom-checkbox" class="toggle-switch @if($errors->has('become_member')) text-neg @endif">
        <input type="checkbox" name="become_member" value="1" @if(getValFromUserSettingOrOld('become_member') === '1') checked @endif  @if($disabled ?? false) disabled="disabled" @endif>
        <span tabindex="0" role="checkbox" aria-checked="{{ getValFromUserSettingOrOld('become_member') == '1' ? 'true' : 'false' }}" class="custom-checkbox text-primary">@icon('check')</span>
        <span class="label">{{trans('segmentation.become_member')}}</span>
    </label>
    @include('users.account.parts.error', ['name' => 'become_member'])
</div>
