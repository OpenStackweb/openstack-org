<schedule-global-filter>
    <div class="row global-search-container">
        <form class="all-events-search-form">
            <div class="col-sm-10 col-xs-8 all-events-search-wrapper">
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