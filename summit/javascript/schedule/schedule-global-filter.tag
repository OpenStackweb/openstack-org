<schedule-global-filter>
    <div class="row global-search-container">
        <form class="all-events-search-form">
            <div class="col-sm-12 col-xs-12 all-events-search-wrapper">
                <input type="search" placeholder="Search for Events/Speakers ..." id="all-events-search" onkeyup={ doFreeTextSearch }>
                <i class="fa fa-search"></i>
            </div>
        </form>
    </div>

    <script>

    doFreeTextSearch(e) {
        console.log('doFreeTextSearch');
    }

    </script>
</schedule-global-filter>