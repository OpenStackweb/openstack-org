            <div class="container faq-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1>$Title</h1>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-3 col-sm-3">
            <div class="faq-sidebar">
                <div class="sidebar-wrapper">
                    <div class="sub-h3">Search</div>
                    <!-- Seach page as you type field -->
                    <input type="text" id="filter-field" name="filter" class="text form-control" placeholder="Type to search questions">
                </div>
                <div class="sidebar-wrapper">
                    <div class="sub-h3">Topics</div>
                    <ul>
                        <% loop $GroupedQuestions.GroupedBy(CategoryName) %>
                        <li><a href="#Category-{$Pos}">$CategoryName</a></li>
                        <% end_loop %>                        
                    </ul>
                </div>
                <div class="sidebar-wrapper">
                    <div class="sub-h3">Need More?</div>
                    <ul>
                        <li><a href="#" data-toggle="modal" data-target="#faqModal">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-9">
            <div class="page_content">
                $Content
            </div>

            <!-- Results header shows up if there's a search match -->
            <div id="results">
                <h5>Results (<span id="count"></span>)</h5>
                <div class="answer" id="none">No results were found.</div>
            </div>

            <% loop $GroupedQuestions.GroupedBy(CategoryName) %>
            <div class="section">
            <h5 class="section-title" id="Category-$Pos">$CategoryName</h5>
                <% loop $Children %>
                    <div class="section-item">
                            <p class="question">
                                $Question
                            </p>
                            <p class="answer">
                                $Answer
                            </p>
                        </div>
                <% end_loop %>
                </div>
            <% end_loop %>
            
        </div>
    </div>
</div>

    <!-- Contact Modal -->
    <div class="modal fade" id="faqModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Contact</h4>
          </div>
          <div class="modal-body">
            <p>
                <i class="fa fa-envelope-o fa-4x"></i>
                Before you email us, we have a <strong>TON of info</strong> on the FAQ page. You can even use the simple search bar on the left of the page to find the exact question you're looking for.
            </p>
            <p>
                <a href="#" class="close-modal-btn" data-dismiss="modal">View Frequently Asked Questions <i class="fa fa-chevron-right"></i></a>
            </p>
            <hr>
            <p>
                Still can't find what you're looking for? Send it our way and we’ll work hard to get you an answer as soon as possible.
            </p>
            <p>
                <a href="mailto:summit@openstack.org" class="modal-contact-btn"><i class="fa fa-envelope"></i> Email Us</a>
            </p>
          </div>
          <div class="modal-footer">
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- End Download Modal -->

