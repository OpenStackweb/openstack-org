<% if $IsSSOBootstrapEnabled %>
<script id="SSOBootstrap" type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
      const csrfToken = "$SecurityToken";
      try {
        const authInfo = localStorage.authInfo;
        if (!authInfo) return;

        const {
          accessTokenUpdatedAt,
          accessToken,
          idToken,
          expiresIn,
        } = (JSON.parse(authInfo) || {});

        const checked = localStorage.getItem('sso:bootstrapped') === "true";
        if (checked) return;

        const isValid = accessToken &&
          expiresIn &&
          accessTokenUpdatedAt &&
          ((expiresIn + accessTokenUpdatedAt) * 1000) > Date.now().valueOf();

        if (!isValid)
        {
          localStorage.removeItem('authInfo');
          return;
        }


        // Check token validity and bootstrap session
        jQuery.ajax({
          url: '/oidc/session/bootstrap',
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + accessToken,
            'X-CSRF-Token': csrfToken,
          },
          dataType: "json",
          success: function(response, textStatus, jqXHR) {
            if (jqXHR.status === 204) {
              localStorage.setItem('sso:bootstrapped', "true");
              window.location.reload();
            }
          },
          error: function({responseText, status, statusText}, statusType, error) {
            if (statusType !== "error") {
              console.warn('OIDC session bootstrap failed - Non-error status received:', statusType);
              return;
            }; // Ignore non-error statuses

            const response = JSON.parse(responseText || 'false');

            if (status === 404 || (response && ['invalid_token', 'invalid_grant'].includes(response.code))) {
              localStorage.setItem('sso:bootstrapped', "true");
            }

            console.error('OIDC session bootstrap failed:', {
              error,
              status,
              statusText,
              response: response || responseText,
            });
          }
        });

      } catch (e) {
        console.error('Error in Page_LoggedInVerification.ss:', e);
        console.log('authInfo (raw):', localStorage.getItem('authInfo'));
      }

    });
</script>
<% end_if %>