<form class="form-inline" $FormAttributes>
    <div class="form-group">
        <div>
            <fieldset class="footer-feedback-fieldset">
                <input class="feedback-input" type="input" placeholder="Give Us Your Feedback On This Page" id="FeedbackForm_FeedbackForm_Content" name="Content">
                $Fields.dataFieldByName(SecurityID)
                <button type="submit" class="feedback-btn" id="FeedbackForm_FeedbackForm_action_submitFeedback" name="action_submitFeedback">Submit</button>
            </fieldset>
        </div>
    </div>
</form>