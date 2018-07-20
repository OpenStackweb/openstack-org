</div>

<div class="intro-header text-center" style="background: url($Top.Paper.BackgroundImageUrl) center; ">
    <h1>$_T($Top.Paper.I18nContext, $Top.Paper.Title)</h1>
    <h2>$_T($Top.Paper.I18nContext, $Top.Paper.Subtitle)</h2>
</div>
<section class="containers-page">
    <div id="paper-nav" class="navigation stick-top">
        <div class="container">
            <div class="group">
                <button id="btnPrv" class="btn" disabled>
                    <i class="fa fa-caret-left"></i>
                </button>
                <button id="btnNxt" class="btn">
                    <i class="fa fa-caret-right"></i>
                </button>
            </div>
            <div class="dropdown" role="tablist" id="chapters">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="true">&nbsp;$_T($Top.Paper.I18nContext,$Top.Paper.FirstSection.Title)
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <% loop $Top.Paper.OrderedSections %>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-target="{$Slug}">$_T($Top.Paper.I18nContext, $Title)</a>
                        </li>
                        <% if $SubSections %>
                            <ul class="sub-dropdown-menu">
                                <% loop $OrderedSubSections %>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-target="{$Slug}">$_T($Top.Paper.I18nContext, $Title)</a>
                                    </li>
                                <% end_loop %>
                            </ul>
                        <% end_if %>
                    <% end_loop %>
                </ul>
            </div>
        </div>
    </div>
    <div class="container content">
        $Top.renderSections
    </div>
    <div class="scroll-top">
        <button class="btn btn-default" id="btn-top" onclick="topFunction()">
            <i class="fa fa-angle-up"></i>
            <span> Top</span>
        </button>
    </div>
</section>

<div id="lightbox" class="modal">
    <span class="close cursor" onclick="closeModal()">&times;</span>
    <div class="modal-content">
        <div class="mySlides">
            <img src="" style="width:100%">
        </div>
    </div>
</div>