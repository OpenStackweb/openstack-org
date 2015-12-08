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
                <label for="$FormName_AvailableForBureau" class="left">
                    $Fields.dataFieldByName(AvailableForBureau)
                    $Fields.dataFieldByName(AvailableForBureau).Title
              </label>
            </div>
            <div class="checkbox field">
                <label for="$FormName_FundedTravel" class="left">
                    $Fields.dataFieldByName(FundedTravel)
                    $Fields.dataFieldByName(FundedTravel).Title
              </label>
            </div>
            <div class="field">
                <label for="$FormName_WillingToTravel" class="left">
                    $Fields.dataFieldByName(WillingToTravel).Title
              </label>
              $Fields.dataFieldByName(WillingToTravel)
            </div>
            <div class="field">
                <label for="$FormName_CountriesToTravel" class="left">$Fields.dataFieldByName(CountriesToTravel).Title</label>
                <div class="">
                    $Fields.dataFieldByName(CountriesToTravel)
                </div>
            </div>

            <hr>

            <div class="field text ">
                <label for="$FormName_Laguages" class="left">Spoken Languages</label>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(Language[1]).Title </span>
                    $Fields.dataFieldByName(Language[1])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(Language[2]).Title </span>
                    $Fields.dataFieldByName(Language[2])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(Language[3]).Title </span>
                    $Fields.dataFieldByName(Language[3])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(Language[4]).Title </span>
                    $Fields.dataFieldByName(Language[4])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(Language[5]).Title </span>
                    $Fields.dataFieldByName(Language[5])
                </div>
            </div>

            <div class="field text ">
                <label for="$FormName_Expertise" class="left">Area Of Expertise</label>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(Expertise[1]).Title </span>
                    $Fields.dataFieldByName(Expertise[1])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(Expertise[2]).Title </span>
                    $Fields.dataFieldByName(Expertise[2])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(Expertise[3]).Title </span>
                    $Fields.dataFieldByName(Expertise[3])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(Expertise[4]).Title </span>
                    $Fields.dataFieldByName(Expertise[4])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(Expertise[5]).Title </span>
                    $Fields.dataFieldByName(Expertise[5])
                </div>
            </div>

            <div class="field text ">
                <label for="$FormName_PresentationLink" class="left">Links To Previous Presentations</label>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(PresentationLink[1]).Title </span>
                    $Fields.dataFieldByName(PresentationLink[1])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(PresentationLink[2]).Title </span>
                    $Fields.dataFieldByName(PresentationLink[2])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(PresentationLink[3]).Title </span>
                    $Fields.dataFieldByName(PresentationLink[3])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(PresentationLink[4]).Title </span>
                    $Fields.dataFieldByName(PresentationLink[4])
                </div>
                <div class=" expertise">
                    <span> $Fields.dataFieldByName(PresentationLink[5]).Title </span>
                    $Fields.dataFieldByName(PresentationLink[5])
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

    Speakers agree that OpenStack Foundation may record and publish their talks presented during the October 2015 OpenStack Summit.
    If you submit a proposal on behalf of a speaker, you represent to OpenStack Foundation that you have the authority to submit the proposal
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