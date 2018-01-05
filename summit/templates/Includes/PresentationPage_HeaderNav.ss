<div class="container">
    <h1> Call for Presentations </h1>

    <div class="row presentation-header-subtitle">
        <div class="col-sm-12">
            <p>Step <strong>{$CurrentStep} of 4</strong></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 presentation-nav-wrapper">
            <ul class="presentation-nav-steps">
                <li class="{$Top.getStepClass($CurrentStep,1,$Presentation.getProgress())}">
                    <a data-step='1' id="step-1" href="$Presentation.EditLink" class="presentation-step {$Top.getStepClass($CurrentStep,1,$Presentation.getProgress())}">
                        Presentation&nbsp;Summary&nbsp;&nbsp;
                        <i class="navigation-icon fa {$Top.getStepClassIcon($CurrentStep,1,$Presentation.getProgress())}" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="{$Top.getStepClass($CurrentStep,2,$Presentation.getProgress())}">
                    <% if $Presentation %>
                        <a data-step='2' id="step-2" href="$Presentation.EditTagsLink" class="presentation-step {$Top.getStepClass($CurrentStep,2,$Presentation.getProgress())}">
                            Presentation&nbsp;Tags&nbsp;&nbsp;
                            <i class="navigation-icon fa {$Top.getStepClassIcon($CurrentStep,2,$Presentation.getProgress())}" aria-hidden="true"></i>
                        </a>
                    <% else %>
                        <span class="disabled_step">
                            Presentation&nbsp;Tags&nbsp;&nbsp;
                            <i class="navigation-icon fa fa-plus-circle navigation-icon-incompleted" aria-hidden="true"></i>
                        </span>
                    <% end_if %>
                </li>
                <li class="{$Top.getStepClass($CurrentStep,3,$Presentation.getProgress())}">
                    <% if $Presentation %>
                        <a data-step='3' id="step-3" href="$Presentation.EditSpeakersLink" class="presentation-step {$Top.getStepClass($CurrentStep,3,$Presentation.getProgress())}">
                            Speakers&nbsp;&nbsp;
                            <i class="navigation-icon fa {$Top.getStepClassIcon($CurrentStep,3,$Presentation.getProgress())}" aria-hidden="true"></i>
                        </a>
                    <% else %>
                        <span class="disabled_step">
                            Speakers&nbsp;&nbsp;
                            <i class="navigation-icon fa fa-plus-circle navigation-icon-incompleted" aria-hidden="true"></i>
                        </span>
                    <% end_if %>
                </li>
                <li class="{$Top.getStepClass($CurrentStep,4,$Presentation.getProgress())}">
                    <% if $Presentation && $Presentation.Speakers().Count() > 0 %>
                        <a data-step='4' id="step-4" href="$Presentation.EditConfirmLink" class="presentation-step {$Top.getStepClass($CurrentStep,4,$Presentation.getProgress())}">
                            Review&nbsp;&&nbsp;Submit&nbsp;&nbsp;
                            <i class="navigation-icon fa {$Top.getStepClassIcon($CurrentStep,4,$Presentation.getProgress())}" aria-hidden="true"></i>
                        </a>
                    <% else %>
                        <span class="disabled_step">
                            Review&nbsp;&&nbsp;Submit&nbsp;&nbsp;
                            <i class="navigation-icon fa fa-plus-circle navigation-icon-incompleted" aria-hidden="true"></i>
                        </span>
                    <% end_if %>
                </li>
            </ul>
        </div>
    </div>
</div>