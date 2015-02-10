<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <% loop $Entries %>
        <url>
            <loc>$Url</loc>
            <% if $LastEdited %><lastmod>$LastEdited.Format(c)</lastmod><% end_if %>
            <% if $ChangeFreq %><changefreq>$ChangeFreq</changefreq><% end_if %>
            <% if $Priority %><priority>$Priority</priority><% end_if %>
        </url>
    <% end_loop %>
</urlset>