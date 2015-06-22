/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
jQuery(function($) {
    var nextNotAllowed = false;
    var prevNotAllowed = true;
    var $newsArchivedPagesContainer = $('.news-archived-articles');
    var totalPages = $newsArchivedPagesContainer.data('total-pages');
    var pageChangeInProgress = false;
    var searchTerm = getParameterByName('searchTerm');
    var options = {
        currentPage: 1,
        totalPages: totalPages,
        bootstrapMajorVersion: 3,
        numberOfPages: 10,
        onPageChanged: function(event, oldPage, newPage) {
            getArchivedNews(newPage, searchTerm);
        },
        onPageClicked: function(event, originalEvent, type, page) {
            if (!pageChangeInProgress &&
                !(prevNotAllowed && type == 'prev') &&
                !(nextNotAllowed && type == 'next')) {
                $('.news-container').css('opacity', '0.4');
                pageChangeInProgress = false;
            }
            else {prevNotAllowed
                event.stopImmediatePropagation();
            }

            if (type == 'prev' || (type == 'page' && page == 1)) {
                nextNotAllowed = false;
                if (page == 1) {
                    prevNotAllowed = true;
                }
                else {
                    prevNotAllowed = false;
                }
            }
            else {
                if (type == 'next' || (type == 'page' && page == totalPages)) {
                    prevNotAllowed = false;
                    if (page == totalPages) {
                        nextNotAllowed = true;
                    }
                    else {
                        nextNotAllowed = false;
                    }
                }
            }
        },
        itemContainerClass: function (type, page, current) {
            return (page === current) ? "active pointer-cursor" : "pointer-cursor";
        },
        shouldShowPage:function(type, page, current){
            switch(type)
            {
                case "first":
                case "last":
                    return false;
                default:
                    return true;
            }
        }
    };

    if (totalPages > 1) {
        $('.news-pager').bootstrapPaginator(options);
    }

    $('.news-archived-search button').click(function(e) {
        e.preventDefault();
        search();
    });

    $('.news-archived-search input').keyup(function(e){
        if(e.keyCode == 13)
        {
            search();
        }
    });

    function search(searchTerm) {
        var searchTerm = $('.news-archived-search input').val();
        if (searchTerm.length > 0) {
            window.location = 'news/archived?searchTerm=' + encodeURIComponent(searchTerm);
        }
        else {
            window.location = 'news/archived';

        }
    }

    function getArchivedNews(pageNumber, searchTerm) {
        $.ajax({
            url: 'news/archived/page',
            data: { number: pageNumber, searchTerm: searchTerm }
        }).done(function(result) {
            $('.news-archived-articles').empty().html(result);
        }).always(function() {
            $('.news-container').css('opacity', '1');
        });
    }

    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    $('.news-archived-search input').val(searchTerm);
});
