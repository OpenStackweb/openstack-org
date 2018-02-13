<% if GA_Data %>
    <% with GA_Data %>
        <!-- Google Code for HK_TICKET_ADWORDS Conversion Page -->
        <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>

        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-{$GAConversionId}"></script>

        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments)};

            gtag('js', new Date());

            gtag('config', 'AW-{$GAConversionId}');

            gtag('event', 'conversion', {
                'send_to': 'AW-{$GAConversionId}/{$GAConversionLabel}',
                'transaction_id': ''
            });
        </script>

        <!-- End Google Code for HK_TICKET_ADWORDS Conversion Page -->
    <% end_with %>
<% end_if %>