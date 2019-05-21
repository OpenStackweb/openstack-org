<div class="all-sessions-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>$HeaderTitle</h1>
            </div>
        </div>
        <% loop $Summit.CategoryGroups() %>
        <div class="row sessions-landing-intro">
            <div class="col-sm-12">
                <h3>$Name</h3>
                <p>$Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <% loop $Categories() %>
                    <% if $VotingVisible && $ChairVisible %>
                        <p>
                            <strong> $Title </strong><br>
                            $Description
                        </p>
                    <% end_if %>
                <% end_loop %>
            </div>
        </div>
        <% end_loop %>
    </div>
</div>


<%--<div class="sessions-landing-intro">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>$HeaderTitle</h1>
                <h3>The Summit</h3>
                <p>The Open Infrastructure Summit includes keynotes, presentations, panels, hands-on workshops, and collaborative working sessions covering over 30 open source projects. Expect to hear about the intersection of many open source infrastructure projects, including Ansible, Ceph, Kata Containers, Kubernetes, ONAP, OpenStack and more.</p>
            </div>
        </div>
    </div>
</div>
<div class="sessions-landing-intro">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                
                <h3>The Forum</h3>
                <p>OpenStack users and developers gather at the Forum to brainstorm the requirements for the next release, gather feedback on the past version and have strategic discussions that go beyond just one release cycle. Sessions are submitted outside of the Summit Call for Presentations and are more collaborative, discussion-oriented.</p>
            </div>
        </div>
    </div>
</div>--%>
