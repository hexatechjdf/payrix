<script type="module">
    import {
        ssoData,
        ssoToken,
        setSsoData,
        getUserData,
        setRefreshToken,
    } from "{{ asset('assets/js/sso.js') }}";

    async function validateToken(token) {
        try {
            // Replace with actual token validation endpoint if available
            const response = await $.ajax({
                url: '{{ route('location.verify') }}',
                method: 'GET',
                data: {
                    page: 1,
                    perPage: 1
                },
                beforeSend: function(jqxhr) {
                    jqxhr.setRequestHeader('ghlAuthorization', token);
                }
            });

            return true; // Token is valid if API call succeeds
        } catch (error) {
            console.error('Token validation failed:', error);
            return false;
        }
    }


    function setSsoTokenInAjaxHeaders() {
        try {
            // console.log('Setting AJAX headers with ssoToken:', ssoToken);
            $.ajaxSetup({
                beforeSend: function(jqxhr) {
                    if (ssoToken) {
                        jqxhr.setRequestHeader('ghlAuthorization', ssoToken);
                    }
                }
            });
        } catch (error) {
            console.error('Failed to set AJAX headers:', error);
            setTimeout(setSsoTokenInAjaxHeaders, 1000);
        }
    }

    function handleSSOError(message) {
        $('#sso-loader').fadeOut(300, function() {
            $('#sso-error-message').text(message);
            $('#sso-error').removeClass('d-none').fadeIn(300);
            toastr.error(message);
            // setTimeout(() => {
            //     window.location.href = '/login';
            // }, 3000);
        });
    }

    async function initializeSSO() {

        // Check for cached SSO data
        const cachedToken = sessionStorage.getItem('ssoToken');
        const cachedSsoData = sessionStorage.getItem('ssoData');

        if (cachedToken && cachedSsoData) {
            try {
            alert(65656);

                // Validate cached token
                const isValid = await validateToken(cachedToken);
                if (isValid) {
                    // Reuse cached data
                    ssoToken = cachedToken; // Update global ssoToken
                    setSsoData(JSON.parse(cachedSsoData));
                    setSsoTokenInAjaxHeaders();
                    // if (ssoData.role.toLowerCase() === 'user') {
                    //     $('.nav-item .integration-link').remove();
                    // }
                    // Show content immediately without loader
                    $('#main-content').fadeIn(300);
                    initializeApp();
                    return;
                } else {
                    // Clear invalid cache
                    sessionStorage.removeItem('ssoToken');
                    sessionStorage.removeItem('ssoData');
                }
            } catch (error) {
                console.error('Error validating cached token:', error);
                sessionStorage.removeItem('ssoToken');
                sessionStorage.removeItem('ssoData');
            }
        }

        // No valid cached token, show loader and initialize SSO
        $('#sso-loader').fadeIn(300);
        try {
            alert(111);
            const decryptedData = await getUserData();

            console.log('sdfsfsd');
            console.log(decryptedData);

            if (decryptedData && decryptedData.validSSO) {
                setSsoData(decryptedData.validSSO);
                setSsoTokenInAjaxHeaders();

                // Cache SSO data
                sessionStorage.setItem('ssoToken', ssoToken);
                sessionStorage.setItem('ssoData', JSON.stringify(ssoData));

                if (ssoData.role.toLowerCase() === 'user') {
                    $('.nav-item .integration-link, .nav-item .media-link').remove();
                }

                // Hide loader, show content, and initialize app
                $('#sso-loader').fadeOut(300, function() {
                    $('#main-content').fadeIn(300);
                    initializeApp();
                });
            } else {
                handleSSOError('SSO validation failed.'); //SSO validation failed. Redirecting to login...
            }
        } catch (error) {
            alert('rerererer');
            console.error('SSO initialization failed:', error);
            handleSSOError(
                'Failed to initialize SSO.'); //old msg: Failed to initialize SSO. Redirecting to login...
        }
    }


    initializeSSO();

    //  $('#main-content').fadeIn(300);
    //  initializeApp();


    async function initializeApp() {
        try {
            const response = await $.ajax({
                url: '{{ route('location.fetch.integrations.list') }}',
                method: 'GET',
                beforeSend: function(jqxhr) {
                    jqxhr.setRequestHeader('ghlAuthorization', ssoToken);
                }
            });
            alert(123);

            $('.integrations-area').html(response);
            return true;
        } catch (error) {
            console.error("Token validation failed:", error);
            return false;
        }
        $('.integrations-area').html(inte);
    }



    export {
        initializeSSO
    };
</script>
