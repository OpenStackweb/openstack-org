<% if IncludeFormTag %>
    <form $FormAttributes role="form">
<% end_if %>
<% if Message %>
        <p id="{$FormName}_error" class="message $MessageType">$Message</p>
<% else %>
        <p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
<% end_if %>

    <fieldset>
        <% if Legend %>
            <legend>$Legend</legend>
        <% end_if %>

        <div class="section_container">
            <div>
                <div class="field col-md-4">
                    <label for="$FormName_Title" class="left">$Fields.dataFieldByName(Title).Title</label>
                    $Fields.dataFieldByName(Title)
                </div>
                <div class="field col-md-4">
                    <label for="$FormName_FirstName" class="left">$Fields.dataFieldByName(FirstName).Title</label>
                    $Fields.dataFieldByName(FirstName)
                </div>
                <div class="field col-md-4">
                    <label for="$FormName_LastName" class="left">$Fields.dataFieldByName(LastName).Title</label>
                    $Fields.dataFieldByName(LastName)
                </div>
            </div>
            <div class="clear"></div>
            <div class="field">
                <label for="$FormName_Country" class="left">$Fields.dataFieldByName(Country).Title</label>
                <div class="">
                    $Fields.dataFieldByName(Country)
                </div>
            </div>
            <div class="field">
                <label for="$FormName_Bio" class="left">$Fields.dataFieldByName(Bio).Title</label>
                <div class="">
                    $Fields.dataFieldByName(Bio)
                </div>
            </div>
            <div>
                <div class="field col-md-6">
                    <label for="$FormName_IRCHandle" class="left">$Fields.dataFieldByName(IRCHandle).Title</label>
                    $Fields.dataFieldByName(IRCHandle)
                </div>
                <div class="field col-md-6">
                    <label for="$FormName_TwitterName" class="left">$Fields.dataFieldByName(TwitterName).Title</label>
                    $Fields.dataFieldByName(TwitterName)
                </div>
            </div>
            <div class="clear"></div>

            <div>
                $Fields.dataFieldByName(Photo).FieldHolder
            </div>
            <div class="clear"></div>

            <hr>
            <div class="checkbox field">
                $Fields.dataFieldByName(AvailableForBureau)
                <label for="$FormName_AvailableForBureau" class="left">
                    $Fields.dataFieldByName(AvailableForBureau).Title
                </label>
            </div>
            <div class="checkbox field">
                $Fields.dataFieldByName(WillingToPresentVideo)
                <label for="$FormName_WillingToPresentVideo" class="left">
                    $Fields.dataFieldByName(WillingToPresentVideo).Title
                </label>
            </div>
            <div class="checkbox field">
                $Fields.dataFieldByName(FundedTravel)
                <label for="$FormName_FundedTravel" class="left">
                    $Fields.dataFieldByName(FundedTravel).Title
                </label>
            </div>
            <div class="checkbox field">
                $Fields.dataFieldByName(WillingToTravel)
                <label for="$FormName_WillingToTravel" class="left">
                    $Fields.dataFieldByName(WillingToTravel).Title
                </label>
            </div>
            <div class="field">
                <label for="$FormName_CountriesToTravel" class="left">$Fields.dataFieldByName(CountriesToTravel).Title</label>
                <div class="">
                    $Fields.dataFieldByName(CountriesToTravel)
                </div>
            </div>

            <hr>

            <div class="field text ">
                <label for="$FormName_Languages" class="left">Spoken Languages (up to 5)</label>
                <div class="language">
                    $Fields.dataFieldByName(Languages)
                </div>
            </div>

            <div class="field text ">
                <label for="$FormName_Expertise" class="left">Area Of Expertise (up to 5)</label>
                <div class=" expertise">
                    $Fields.dataFieldByName(Expertise)
                </div>
            </div>

            <div class="field text ">
                <label for="$FormName_PresentationLink" class="left">Links To Previous Presentations</label>
                <div class="presentation">
                    <div> $Fields.dataFieldByName(PresentationLink[1]).Title </div>
                    <span>Link:</span> $Fields.dataFieldByName(PresentationLink[1])
                    <span>Title:</span> $Fields.dataFieldByName(PresentationTitle[1])
                </div>
                <div class="presentation">
                    <div> $Fields.dataFieldByName(PresentationLink[2]).Title </div>
                    <span>Link:</span> $Fields.dataFieldByName(PresentationLink[2])
                    <span>Title:</span> $Fields.dataFieldByName(PresentationTitle[2])
                </div>
                <div class="presentation">
                    <div> $Fields.dataFieldByName(PresentationLink[3]).Title </div>
                    <span>Link:</span> $Fields.dataFieldByName(PresentationLink[3])
                    <span>Title:</span> $Fields.dataFieldByName(PresentationTitle[3])
                </div>
                <div class="presentation">
                    <div> $Fields.dataFieldByName(PresentationLink[4]).Title </div>
                    <span>Link:</span> $Fields.dataFieldByName(PresentationLink[4])
                    <span>Title:</span> $Fields.dataFieldByName(PresentationTitle[4])
                </div>
                <div class="presentation">
                    <div> $Fields.dataFieldByName(PresentationLink[5]).Title </div>
                    <span>Link:</span> $Fields.dataFieldByName(PresentationLink[5])
                    <span>Title:</span> $Fields.dataFieldByName(PresentationTitle[5])
                </div>
            </div>

            <div class="field">
                <label for="$FormName_Notes" class="left">$Fields.dataFieldByName(Notes).Title</label>
                <div class="">
                    $Fields.dataFieldByName(Notes)
                </div>
            </div>

            <div class="field">
                $Fields.dataFieldByName(SpeakerID)
                $Fields.dataFieldByName(MemberID)
                $Fields.dataFieldByName(ReplaceBio)
                $Fields.dataFieldByName(ReplaceName)
                $Fields.dataFieldByName(ReplaceSurname)
            </div>


        </div>


        <div style="position: absolute; left: -9999px;">
            <label for="$FormName_username">Don't enter anything here</label>
            $Fields.dataFieldByName(user_name)
        </div>
        $Fields.dataFieldByName(SecurityID)
        <div class="clear"><!-- --></div>
    </fieldset>

    Speakers agree that Open Infrastructure Foundation may record and publish any talks presented during the OpenStack Summit. If you submit a proposal on behalf of a speaker, you represent to Open Infrastructure Foundation that you have the authority to submit the proposal
    on the speaker’s behalf and agree to the recording and publication of their presentation.
    <br><br>

<% if Actions %>
        <div class="Actions">
            <% loop Actions %>
                $Field
            <% end_loop %>
        </div>
<% end_if %>
<% if IncludeFormTag %>
    </form>
<% end_if %>