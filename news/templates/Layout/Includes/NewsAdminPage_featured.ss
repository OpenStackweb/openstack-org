<li>
    <div class="featuredBox">
        <input type="hidden" class="article_id" value="$ID" />
        <input type="hidden" class="article_rank" value="$Rank" />
        <input type="hidden" class="article_type" value="featured" />
        <div class="newsImage">
            <a href="news/view/$ID/$HeadlineForUrl">$Image.CroppedImage(200,100)</a>
        </div>
        <div class="newsText">
            <p class="headline">&ldquo;$Headline&rdquo;</p>
            <!-- <div class="summary">&mdash; $RAW_val(Summary)</div> -->
        </div>
        <div class="newsEdit"><a href="news-add?articleID=$ID"> Edit </a></div>
        <div class="newsRemove">Remove</div>
    </div>
</li>