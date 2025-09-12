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
        delete authInfo;

        const checked = localStorage.authInfoChecked === "true";
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

        localStorage.setItem('authInfoChecked', "true");

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
          data: JSON.stringify({
            accessTokenUpdatedAt,
            accessToken,
            idToken,
            expiresIn,
          }),
          success: function(response, textStatus, jqXHR) {
            if (jqXHR.status === 204) {
              window.location.reload();
            }
          },
          error: function({responseText, status, statusText}, statusType, error) {
            if (statusType !== "error") {
              console.warn('OIDC session bootstrap failed - Non-error status received:', statusType);
              return;
            }; // Ignore non-error statuses

            const response = JSON.parse(responseText || 'false');

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