<form $AttributesHTML>
    <% if $Message %>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            $Message
        </div>
    <% end_if %>
<div class="row">
    <fieldset class="fake">
        <p>Review your completed sections and Submit Your Survey when ready. Thank you for helping create a better OpenStack!</p>
        <table class="table table-striped">
        <% loop Survey.getAvailableSteps %>
            <% if $Template.Type != 'SurveyReviewStepTemplate' %>
            <tr class="review-row">
                <td width="95%">
                    <i class="navigation-icon fa {$Top.SurveyStepClassIcon($Template.Name)}" aria-hidden="true"></i>
                    <span class="step-name">$Template.FriendlyName</span>
                </td>
                <td width="5%">
                    <a href="$Top.Link($Template.Title)">
                    <% if isComplete %>
                        Edit
                    <% else %>
                        Start
                    <% end_if %>
                    </a>
                </td>
            </tr>
            <% end_if %>
        <% end_loop %>
        </table>
    </fieldset>
</div>
    <% if $Actions %>
        <div class="Actions">
            <% loop $Actions %>
                $Field
            <% end_loop %>
        </div>
    <% end_if %>
</form>