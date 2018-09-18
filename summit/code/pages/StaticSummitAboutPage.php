<?php

class StaticSummitAboutPage extends Page {

}


class StaticSummitAboutPage_Controller extends Page_Controller {

    public function init()
    {
        parent::init();

        Requirements::block('summit/css/combined.css');
        Requirements::css('node_modules/@fortawesome/fontawesome-pro/css/all.css');
        Requirements::css('themes/openstack/static/css/combined.css');
        Requirements::css('summit/css/static-summit-about-page.css');
		Requirements::javascript('summit/javascript/in-view.min.js');
		Requirements::javascript('summit/javascript/static-summit-about-page.js');

    }

    public function getSummitAboutPageLink() {
        return $this->Link();
    }

    public function getAboutPageNavClass(){
        return 'current';
    }

    public function getOrder(){
        $order = $this->request->getVar('order');
        return isset($order) && $order == "complete";
    }

    /*
    * Return google tracking script if ?order=complete query string param is present
    *  using settings of current conference page
    */
    function GATrackingCode()
    {
        $request = $this->request;
        $order = $request->requestVar("order");
        $tracking_code = '';
        if (isset($order) && $order == "complete") {
            //add GA tracking script
            $page = SummitPage::get()->filter('SummitID', 25)->first();
            if ($page && !empty($page->GAConversionId)
                && !empty($page->GAConversionLanguage)
                && !empty($page->GAConversionFormat)
                && !empty($page->GAConversionColor)
                && !empty($page->GAConversionLabel)
            ) {
                $tracking_code = $this->renderWith("SummitPage_GA", array(
                    "GA_Data" => new ArrayData(array(
                        "GAConversionId" => $page->GAConversionId,
                        "GAConversionLanguage" => $page->GAConversionLanguage,
                        "GAConversionFormat" => $page->GAConversionFormat,
                        "GAConversionColor" => $page->GAConversionColor,
                        "GAConversionLabel" => $page->GAConversionLabel,
                        "GAConversionValue" => $page->GAConversionValue,
                        "GARemarketingOnly" => $page->GARemarketingOnly ? "true" : "false",
                    ))
                ));
            }
        }
        return $tracking_code;
    }

    function FBTrackingCode()
    {
        $request       = $this->request;
        $order         = $request->requestVar("order");
        $tracking_code = '';

        if (isset($order) && $order == "complete") {
            //add FB tracking script
            $page = SummitPage::get()->filter('SummitID', 25)->first();
            if ($page && !empty($page->FBPixelId)) {
                $tracking_code = $this->renderWith("SummitPage_FBPixelCode",[
                    "FB_Data" => new ArrayData([
                        "FBPixelId" => $page->FBPixelId,
                    ])
                ]);
            }
        }
        return $tracking_code;
    }

    function TwitterTrackingCode()
    {
        $request = $this->request;
        $order = $request->requestVar("order");
        $tracking_code = '';
        if (isset($order) && $order == "complete") {
            //add FB tracking script
            $page = SummitPage::get()->filter('SummitID', 25)->first();
            if ($page && !empty($page->TwitterPixelId)
            ) {
                $tracking_code = $this->renderWith("SummitPage_Twitter", array(
                    "Twitter_Data" => new ArrayData(array(
                        "TwitterPixelId" => $page->TwitterPixelId,
                    ))
                ));
            }
        }
        return $tracking_code;
    }

    public function MetaTags()
    {
        $tags = parent::MetaTags();
        return $tags;
    }
}