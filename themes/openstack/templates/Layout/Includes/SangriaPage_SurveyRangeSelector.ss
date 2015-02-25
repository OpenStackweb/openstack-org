<form id="range_form" action="{$FormAction}" method="POST">
    <label for="range">$Label</label>
    <select id="range">
        <option selected value="OLD">ARCHIVE</option>
        <option value="MARCH_2015">MARCH 2015</option>
    </select>
    <input type="hidden" id="survey_range" name="survey_range" value="{$Top.getSurveyRange($FromPage)}" />
</form>