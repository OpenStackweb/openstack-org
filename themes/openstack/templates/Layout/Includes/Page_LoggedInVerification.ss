<script>
    document.addEventListener('DOMContentLoaded', () => {
      const csrfToken = "$SecurityToken";
      try {
        const {
          accessTokenUpdatedAt,
          accessToken,
          idToken,
          expiresIn,
        } = JSON.parse(localStorage.getItem('authInfo') || '{}');

        const isValid = accessToken &&
          expiresIn &&
          accessTokenUpdatedAt &&
          ((expiresIn + accessTokenUpdatedAt) * 1000) > Date.now().valueOf();

        // No access token found => user is not logged in
        if (!isValid)
        {
          return;
        }

        // Bootstrap OIDC session

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
          success: function(response) {
            console.log('OIDC session bootstrap successful:', response);
          },
          error: function(xhr, status, error) {
            console.error('OIDC session bootstrap failed:', {
              status: xhr.status,
              statusText: xhr.statusText,
              response: JSON.parse(xhr.responseText || 'false') || xhr.responseText,
              error: error
            });
          }
        });

      } catch (e) {
        console.error('Error in Page_LoggedInVerification.ss:', e);
        console.log('authInfo (raw):', localStorage.getItem('authInfo'));
      }

    });
</script>