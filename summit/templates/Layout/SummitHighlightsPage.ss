<div class="top-bar">
    <p>
        <i class="fa fa-exclamation-circle"></i>
        We had an amazing time in {$Summit.Title}, you don't want to miss us in {$Top.NextSummit.Name}! <a
            href="{$Summit.Next.RegistrationLink}">More on the Summit in {$Top.NextSummit.Name}.</a>
    </p>
</div>
<div id="wrap">
    <div class="summit-hero-wrapper condensed">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <a href="/">
                        <img alt="OpenStack Summit"
                             onerror="this.onerror=null; this.src=http://netlify.scdn4.secure.raxcdn.com/images/a488a245786385e04dfa980e2b3dbf3c43b36dc1/summit-logo-small.png"
                             src="http://netlify.scdn4.secure.raxcdn.com/49dfa6ca01c2c8511f673ccd0e2f58bf4ac36816/12931/images/summit-logo-small.svg"
                             class="summit-hero-logo">
                    </a>

                    <h2>
                        $Summit.DateLabel
                    </h2>

                    <h1>
                        $Summit.Title
                    </h1>
                </div>
            </div>
            <a class="open-panel" href="#"><i class="fa fa-bars fa-2x collapse-nav"></i></a>
        </div>
        <div class="hero-tab-wrapper">
            <!-- Microsite Navigation -->

            <ul class="nav nav-tabs">


                <li class="current">
                    <a href="$Top.Link">Summit Highlights</a>
                </li>


                <li class="">
                    <a href="$Top.Link(videos)">Videos</a>
                </li>


                <li class="">
                    <a href="$Top.Link(sponsors)">Sponsors</a>
                </li>

            </ul>

            <!-- End Microsite Navigation -->
        </div>
        <a target="_blank" title="" data-placement="left" data-toggle="tooltip" class="photo-credit"
           href="https://www.flickr.com/photos/stuckincustoms/9097171697/" data-original-title="Photo by Trey Ratcliff"><i
                class="fa fa-info-circle"></i></a>
    </div>

    <!-- Begin Page Content -->
    <div class="white summit-highlights-intro">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <h1>Thank You {$Summit.Title}!</h1>

                    <p>
                        We had a blast! Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quam velit,
                        vulputate eu pharetra nec, mattis ac neque. Duis vulputate commodo lectus, ac blandit elit
                        tincidunt id. Sed rhoncus, tortor sed eleifend tristique, tortor mauris molestie elit, et
                        lacinia ipsum quam nec dui. Quisque nec mauris sit amet elit iaculis pretium sit amet quis
                        magna. Aenean velit odio, elementum in tempus ut, vehicula eu diam.
                    </p>

                    <p>
                        Duis vulputate commodo lectus, ac blandit elit tincidunt id. Sed rhoncus, tortor sed eleifend
                        tristique, tortor mauris molestie elit, et lacinia ipsum quam nec dui.
                    </p>
                </div>
                <div class="col-sm-6">
                    <div class="future-summit-promo tokyo">
                        <div class="future-summit-next">
                            Up Next
                        </div>
                        <div class="future-summit-promo-city">
                            $Summit.Next.Title
                        </div>
                        <p>
                            Quisque nec mauris sit amet elit iaculis pretium sit amet quis magna. Aenean velit odio,
                            elementum in tempus ut, vehicula eu diam.
                        </p>

                        <p>
                            <a class="future-summit-btn" href="#">Find Out More</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="huge-success-video">
        <h1>It Was A <strong>HUGE</strong> Success</h1>

        <div class="summit-success-stats">
            <div class="stat">
                <div class="number attendance">
                    6,700+
                </div>
                <div class="title">
                    In Attendance
                </div>
            </div>
            <div class="stat">
                <div class="number companies">
                    1,218
                </div>
                <div class="title">
                    Companies Represented
                </div>
            </div>
            <div class="stat">
                <div class="number countries">
                    65
                </div>
                <div class="title">
                    Countries Represented
                </div>
            </div>
        </div>
        <video id="bgvid" poster="/images/post-summit/post-vancouver-placeholder.png" loop="" autoplay="">
            <source type="video/mp4"
                    src="http://49050579f704f4511fdb-b24fd3731f764f642191a9d52ad616f8.r72.cf1.rackcdn.com/highlights.mp4"></source>
        </video>
    </div>

    <highlights collection="summit-highlights">
        <div class="keynote-highlights">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12"><h1>Highlights from the Keynotes</h1></div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="clicked-keynote-highlight"
                             style="background-image:url('//images.contentful.com/elg19wezyouu/1deFOJ81ZmuIo4OocOWiSw/d34e49d2c6409dcd85b9f7a90e32fd0c/couch.jpg')">
                            <div class="clicked-keynote-description"><h4>Comcast’s living room demo</h4>

                                <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quam velit, vulputate
                                    eu pharetra nec, mattis ac neque. Duis vulputate commodo lectus, ac blandit elit
                                    tincidunt id. Sed rhoncus, tortor sed eleifend tristique, tortor mauris molestie
                                    elit, et lacinia ipsum quam nec dui. Quisque nec mauris sit amet elit iaculis
                                    pretium sit amet quis magna. Aenean velit odio, elementum in tempus ut, vehicula eu
                                    diam. </p></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="keynote-highlight-row">
                        <div class="keynote-highlight-day"> Day 1</div>
                        <div class="col-sm-3"><a class="keynote-highlight-single" href="#">
                            <div class="keynote-highlight-thumb active"><img alt=""
                                                                             src="//images.contentful.com/elg19wezyouu/44ajfa4kuc0Uua86ym6Gqs/ee896dbf0801b7044f4cd948a5846a42/3.jpg">
                            </div>
                            <div class="keynote-highlight-title"> Comcast’s living room demo</div>
                        </a></div>
                        <div class="col-sm-3"><a class="keynote-highlight-single" href="#">
                            <div class="keynote-highlight-thumb "><img alt=""
                                                                       src="//images.contentful.com/elg19wezyouu/5PiBwAmaIgqscw6UkWoKIe/c02fa89a0cd157f50d9fbc0520b9131d/1.jpg">
                            </div>
                            <div class="keynote-highlight-title"> Introducing the OpenStack Powered Cloud</div>
                        </a></div>
                        <div class="col-sm-3"><a class="keynote-highlight-single" href="#">
                            <div class="keynote-highlight-thumb "><img alt=""
                                                                       src="//images.contentful.com/elg19wezyouu/5qZWXl2QTuMAcGakGS2Gs2/9883555cb5a74f1570e07c9ad733afe7/2.jpg">
                            </div>
                            <div class="keynote-highlight-title"> DigitalFilm Treem abducts audience</div>
                        </a></div>
                        <div class="col-sm-3"><a class="keynote-highlight-single" href="#">
                            <div class="keynote-highlight-thumb "><img alt=""
                                                                       src="//images.contentful.com/elg19wezyouu/nyr9WzudbwEYuM8wMam4A/59816ac497fee6410b489e684c4eedcc/4.jpg">
                            </div>
                            <div class="keynote-highlight-title"> Walmart adopts OpenStack in a big way</div>
                        </a></div><!--riot placeholder--> </div>
                </div>
                <div class="row">
                    <div class="col-sm-8 col-sm-push-2 keynote-highlights-action"><p> Now you can watch videos of these
                        keynotes and almost every other session! </p>
                        <p><a class="red-btn" href="{$Top.Link(videos)}">Watch Summit Videos Now</a></p></div>
                </div>
            </div>
        </div>
    </highlights>

    <div class="summit-highlight-release">
        <div class="container">
            <div class="row">
                <h1>Kilo Release Announced</h1>

                <div class="col-sm-5 center">
                    <img alt=""
                         src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/9eba0a41cf41a9faa60a0756f9673999aa06a8f9/kilo-logo.png">
                </div>
                <div class="col-sm-7">
                    <p>
                        OpenStack Kilo, the 11th release of the open source software for building public, private, and
                        hybrid clouds has nearly 400 new features to support software development, big data analysis and
                        application infrastructure at scale. The OpenStack community continues to attract the best
                        developers and experts in their disciplines with 1,492 individuals employed by more than 169
                        organizations contributing to the Kilo release.
                    </p>

                    <p>
                        Learn how organizations are using OpenStack and options for commercial support at the upcoming
                        Summit.
                    </p>

                    <p>
                        <a class="highlight-software-btn" href="http://www.openstack.org/software/kilo/">More About Kilo
                            <i class="fa fa-chevron-right"></i></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="summit-highlights-pics">
        <div class="container">
            <div class="row">
                <h1>Oh, The Fun We Had!</h1>

                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/1.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/87364162cb06cb75eb98af59c2dba7d7b60962be/1.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/2.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/d2f9672a61b3589e422e9f95ab0fa66f8116c1ee/2.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/3.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/6f8eff505fcd0422d77d3c5d8a693819a0322c92/3.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/4.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/bbc7f5f98afb517a08da2d85e5c038e7a5b374e8/4.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/5.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/cf2206e77d1600dc19088ede557482bff281a9bb/5.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/6.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/ccde3531a73313ccbd9662de1d762ed55365f650/6.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/7.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/c238d04c263d37dbdc9681113d99f7649e5e1794/7.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/8.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/5799ef1c0bce02b32424b26bea832a91f64cbecb/8.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/9.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/6da78b9f588923bd667d1b0b3badde4dc6a23575/9.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/10.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/ce322daa32c1818789831e4c418a89e0a2df0789/10.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/11.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/b6a88f0726c94b3871cc04ed526f5c8dcde57417/11.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/12.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/1a205fed64b42f061ecb88613f786632dadd5812/12.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/13.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/9a6a31a5200bdba7a019cbef15aec38392c10e4f/13.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/14.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/bee4d804f10514ea55f34d2e186595c8443b442a/14.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/15.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/6ec5c12c3dee7e81598fb99d87ad58d66202a586/15.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/16.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/ce4f380fccbb8e9b09956db2a39ca1e2469ce4bd/16.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/17.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/861b5558f7187a960add1803b78d9e60b2cb2689/17.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/18.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/7f492de3a5aeebaeff59c6b6325759bf29fb2b2c/18.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/19.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/d11a5df3d2e4eec8cd385b3a257974a8f7ebda09/19.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/20.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/4649a4a2a9a3a0ac2bd7ea1c5dc0492f93061921/20.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/21.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/3d23e91fc053704f1aee12eb8b365fc40c4723b1/21.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/22.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/b96fb0ed384f8ea4e7ecf3697943d5691e81e7b7/22.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/23.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/30a42c5467b41f1df24869e97ec238edda9edee6/23.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
                <div class="pic-container">
                    <div data-toggle="lightbox" class="thumbnails">
                        <a class="thumbnail" href="/images/post-summit/pics/24.jpg">
                            <img alt=""
                                 src="http://netlify.scdn4.secure.raxcdn.com/images/post-summit/pics/bfacd6e87be70008e1614d9f81456b66ae93f144/24.jpg"
                                 class="img-responsive">
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 center">
                    <a class="highlights-pic-link" href="#">See all of the pics from The Summit in Tokyo on Flickr</a>
                </div>
            </div>
        </div>
    </div>
    <div class="about-city-row tokyo">
        <p>
            The next Summit will be here before you know it...
        </p>

        <h1>See You In {$Summit.Next.Title}</h1>

        <p>
            <a class="btn red-btn" href="$Summit.Next.RegistrationLink">Join Us In {$Summit.Next.Title}</a>
        </p>
        <a target="_blank" title="" data-placement="left" data-toggle="tooltip" class="photo-credit"
           href="https://flic.kr/p/nfxxkd" data-original-title="Photo by Luke Ma"><i class="fa fa-info-circle"></i></a>
    </div>
    <!-- End Page Content -->
    <div id="push"></div>
</div>