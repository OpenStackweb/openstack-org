
<div id="openstack-search-bar"
     style="width: 100%; margin: 9px 0 0 10px;"
     data-baseUrl="search.openstack.org"
     data-context="www-openstack">

</div>


<script>
    (function (window, document) {
        var loader = function () {
            var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
            script.src = "https://search.openstack.org/widget/embed.min.js";
            tag.parentNode.insertBefore(script, tag);
        };
        window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
    })(window, document);
</script>