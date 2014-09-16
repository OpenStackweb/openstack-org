<li>
    <div class="featuredBox">
        <input type="hidden" class="article_id" value="$ID" />
        <input type="hidden" class="article_rank" value="$Rank" />
        <input type="hidden" class="article_type" value="featured" />
        <div class="featuredImage">
            <a href="$Link">$Image.CroppedImage(200,100)</a>
        </div>
        <div class="featuredText">
            <p class="headline">&ldquo;$Headline&rdquo;</p>
            <p class="summary">&mdash; $Summary</p>
        </div>
        <div class="newsEdit"><a href="news-add?article=$ID"> Edit </a></div>
        <div class="featuredRemove"><a href=""> Remove </a></div>
    </div>
</li>