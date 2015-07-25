<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li><a href="$Top.Link/{$Summit.ID}/dashboard">$Summit.Name</a></li>
            <li class="active">Presentation List</li>
        </ol>
        <div class="panel panel-default">
            <div class="panel-heading">Presentation Lists</div>
            <div class="panel-body">
                <label for="filter">Trackchair Categories</label>
                <div class="btn-group">
                    <button id="filter" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        All<span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#">All</a></li>
                        <li><a href="#">Cloudfunding: Startups and Capital</a></li>
                        <li><a href="#">Enterprise IT Strategies</a></li>
                        <li><a href="#">Telco Strategies</a></li>
                        <li><a href="#">Products, Tools, & Services</a></li>
                    </ul>
                </div>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <td>Name</td>
                    <td>Track Category</td>
                    <td># Available Positions</td>
                    <td># Selected Presentations</td>
                    <td># Alternate Presentations</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><a href="$Top.Link/{$Summit.ID}/events/presentation-lists/1">List Name One #1</a></td>
                    <td>Cloudfunding: Startups and Capital</td>
                    <td><span class="badge">10</span></td>
                    <td><span class="badge">10</span></td>
                    <td><span class="badge">4</span></td>
                </tr>
                <tr>
                    <td><a href="$Top.Link/{$Summit.ID}/events/presentation-lists/2">List Name One #2</a></td>
                    <td>Products, Tools, & Services</td>
                    <td><span class="badge">8</span></td>
                    <td><span class="badge">8</span></td>
                    <td><span class="badge">2</span></td>
                </tr>
                </tbody>
            </table>
            <nav>
                <ul class="pagination">
                    <li class="disabled">
                        <a href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li  class="active"><a href="#">1</a></li>
                    <li><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
                    <li><a href="#">5</a></li>
                    <li>
                        <a href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>