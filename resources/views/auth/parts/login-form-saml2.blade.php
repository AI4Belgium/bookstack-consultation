<script type="text/javascript" nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', function () {
        const samlLoginButton = document.getElementById('saml-login');
        const samlLoginButtonDownload = document.getElementById('saml-login-download');
        const isDownloadElement = document.getElementById('isDownload');
        const loginForm = document.getElementById('login-form');

        samlLoginButton.addEventListener('click', function () {
            isDownloadElement.disabled = true;
            loginForm.submit();
        });

        samlLoginButtonDownload.addEventListener('click', function () {
            isDownloadElement.disabled = false;
            loginForm.submit();
        });
    });
</script>


<form action="{{ url('/saml2/login') }}" method="POST" id="login-form" class="mt-l">
    {!! csrf_field() !!}

    <input type="hidden" id="isDownload" name="isDownload" value="1" disabled />

    <div>
        <button id="saml-login" class="button outline svg">
            @icon('saml2')
            <span>{{ trans('auth.log_in_with', ['socialDriver' => config('saml2.name')]) }}</span>
        </button>
    </div>
    <div>
        <button id="saml-login-download" class="button outline svg">
            @icon('saml2', ['class' => '!tw-m-0'])@icon('download')
            <span>{{ trans('auth.login_and_download', ['socialDriver' => config('saml2.name')]) }}</span>
        </button>
    </div>
</form>