<div class="container">
    <h1> Call for Presentations </h1>

    <div class="row presentation-header-subtitle">
        <div class="col-sm-12">
            <p>
                Step <strong>{$CurrentStep} of 4</strong>
                for <strong> <% if $Presentation.Title %> $Presentation.Title <% else %> New Presentation <% end_if %> </strong></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" style="padding-left: 0 !important;padding-right: 0 !important;">
            <ul class="presentation-nav-steps">
                <li class="{$Top.getStepClass($CurrentStep,1)}">
                    <a data-step='1' id="step-1" href="$Presentation.EditLink" class="presentation-step {$Top.getStepClass($CurrentStep,1)}">
                        Presentation&nbsp;Summary&nbsp;&nbsp;
                        <i class="navigation-icon fa {$Top.getStepClassIcon($CurrentStep,1)}" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="{$Top.getStepClass($CurrentStep,2)}">
                    <% if $Presentation %>
                        <a data-step='2' id="step-2" href="$Presentation.EditTagsLink" class="presentation-step {$Top.getStepClass($CurrentStep,2)}">
                            Presentation&nbsp;Tags&nbsp;&nbsp;
                            <i class="navigation-icon fa {$Top.getStepClassIcon($CurrentStep,2)}" aria-hidden="true"></i>
                        </a>
                    <% else %>
                        <span class="disabled_step">
                            Presentation&nbsp;Tags&nbsp;&nbsp;
                            <i class="navigation-icon fa fa-plus-circle navigation-icon-incompleted" aria-hidden="true"></i>
                        </span>
                    <% end_if %>
                </li>
                <li class="{$Top.getStepClass($CurrentStep,3)}">
                    <% if $Presentation %>
                        <a data-step='3' id="step-3" href="$Presentation.EditSpeakersLink" class="presentation-step {$Top.getStepClass($CurrentStep,3)}">
                            Speakers&nbsp;&nbsp;
                            <i class="navigation-icon fa {$Top.getStepClassIcon($CurrentStep,3)}" aria-hidden="true"></i>
                        </a>
                    <% else %>
                        <span class="disabled_step">
                            Speakers&nbsp;&nbsp;
                            <i class="navigation-icon fa fa-plus-circle navigation-icon-incompleted" aria-hidden="true"></i>
                        </span>
                    <% end_if %>
                </li>
                <li class="{$Top.getStepClass($CurrentStep,4)}">
                    <% if $Presentation %>
                        <a data-step='4' id="step-4" href="$Presentation.EditConfirmLink" class="presentation-step {$Top.getStepClass($CurrentStep,4)}">
                            Review&nbsp;&&nbsp;Submit&nbsp;&nbsp;
                            <i class="navigation-icon fa {$Top.getStepClassIcon($CurrentStep,4)}" aria-hidden="true"></i>
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