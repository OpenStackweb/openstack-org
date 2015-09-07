<div class="course-box">
    <div class="course-description">
        <div>
            <div>
                <h3 class="course-name"
                    style="color: #{$CompanyColor}"
                    id="course_{$CourseID}">
                    $CourseName
                </h3>
            </div>
            <div>
                <span class="{$LwrLevel}">$Level Level</span>
            </div>
        </div>
        <p>$Description&nbsp;</p>

        <ul class="projects-covered">
           <% loop Projects %>
                <li>
                    $Name
                </li>
           <% end_loop %>
        </ul>


        <div class="training-course-table">
            <table classs="table">
                <tbody>
                <tr>
                    <th>Location</th>
                    <th>Starts</th>
                    <th>Ends</th>
                    <th>Duration</th>
                    <th>&nbsp;</th>
                </tr>
                <% if IsOnline %>
                    <tr>
                        <td>Online Only</td>
                        <td>Ongoing</td>
                        <td>Ongoing</td>
                        <td>&nbsp;</td>
                        <td><a style="background-color: #{$CompanyColor}" href="$Link" class="outbound-link">Register</a></td>
                    </tr>
                <% else %>
                    <% loop CurrentLocations %>
                    <tr>
                        <td>$City, $Country</td>
                        <td>$StartDateMonth $StartDateDay, $StartDateYear</td>
                        <td>$EndDateMonth $EndDateDay, $EndDateYear</td>
                        <td>$Days Days</td>
                        <td><a style="background-color: #{$CompanyColor}" href="$Link"  class="outbound-link">Register</a></td>
                    </tr>
                    <% end_loop %>
                <% end_if %>
                </tbody>
            </table>
        </div>
    </div>
</div>
