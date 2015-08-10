<?php
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
/**
 * Defines the LogoDownloads page type
 */
class InteropPage extends Page
{
	static $db = array();
	static $has_one = array();



	function getCMSFields()
	{
		$fields = parent::getCMSFields();

		return $fields;
	}
}

class InteropPage_Controller extends Page_Controller
{

    /**
     * @var IEntityRepository
     */
    private $interop_program_repository;

	function init()
	{
		parent::init();

        Requirements::javascript('themes/openstack/javascript/branding.interop.js');
		Requirements::javascript("http://assets.zendesk.com/external/zenbox/v2.5/zenbox.js");
		Requirements::css("http://assets.zendesk.com/external/zenbox/v2.5/zenbox.css");
		Requirements::customScript('
					 if (typeof(Zenbox) !== "undefined") {
					    Zenbox.init({
					      dropboxID:   "20115046",
					      url:         "https://openstack.zendesk.com",
					      tabID:       "Ask Us",
					      tabColor:    "black",
					      tabPosition: "Right"
					    });
					  }

				');

        $this->interop_program_repository = new SapphireInteropProgramVersion();


	}

	function BrandingMenu()
	{
		return TRUE;
	}

    public function getInteropProgramVersions()
    {
        list($res, $size) = $this->interop_program_repository->getAllOrdered();
        return new ArrayList($res);
    }

    function getCapabilitiesTable() {
        $html = '';
        $versions = $this->getInteropProgramVersions();

        foreach ($versions as $key => $version) {
            $capabilities = $version->Capabilities()->sort('Order');
            $html .= '<div style="display:'.(($key == 0) ? 'block':'none').'" id="version_'.$version->ID.'" class="version_wrapper">
                        <table style="width: 80%;" border="0" cellspacing="10" cellpadding="10">
                            <tbody>
                                <tr>
                                    <td width="50%">
                                        <h3 style="margin-bottom: 5px;">Required Capabilities</h3>
                                    </td>
                                    <td colspan="3" width="30%">
                                        <h3 style="margin-bottom: 5px;">Licensing Program</h3>
                                    </td>
                                </tr>
                                <tr style="border: solid 1px #ccc;">
                                    <td width="50%"><strong>&nbsp;</strong></td>
                                    <td width="10%"><strong>Platform</strong></td>
                                    <td width="10%"><strong>Compute</strong></td>
                                    <td width="10%"><strong>Object Storage</strong></td>
                                </tr>';

            for ($i=0; $i<count($capabilities); $i++) {
                $prepend_title = ($i == 0 || $capabilities[$i-1]->TypeID != $capabilities[$i]->TypeID);
                $capability = $capabilities[$i];

                if ($prepend_title) {
                    $html .= '<tr style="border: solid 1px #ccc;">
                                <td><strong>'.$capability->Type()->Name.'</strong></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>';
                }

                $html .= '<tr style="border: solid 1px #ccc;">
                                <td>'.$capability->Name.'</td>';


                if ($capability->isPlatform()) {
                    if ($capability->Status == 'Required') {
                        $html .= '<td style="text-align: center;">✓</td>';
                    } else {
                        $html .= '<td style="text-align: center;">'.$capability->Status.'</td>';
                    }
                } else {
                    $html .= '<td>&nbsp;</td>';
                }

                if ($capability->isCompute()) {
                    if ($capability->Status == 'Required') {
                        $html .= '<td style="text-align: center;">✓</td>';
                    } else {
                        $html .= '<td style="text-align: center;">'.$capability->Status.'</td>';
                    }
                } else {
                    $html .= '<td>&nbsp;</td>';
                }

                if ($capability->isStorage()) {
                    if ($capability->Status == 'Required') {
                        $html .= '<td style="text-align: center;">✓</td>';
                    } else {
                        $html .= '<td style="text-align: center;">'.$capability->Status.'</td>';
                    }
                } else {
                    $html .= '<td>&nbsp;</td>';
                }

                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            // Designated Sections
            $designated_sections = $version->DesignatedSections()->sort('Order');
            $html .= '  <p>&nbsp;</p>
                        <table style="width: 80%;" border="0" cellspacing="10" cellpadding="10">
                            <tbody>
                                <tr>
                                    <td width="50%">
                                        <h3 style="margin-bottom: 5px;">Designated Sections</h3>
                                    </td>
                                    <td width="10%">&nbsp;</td>
                                    <td width="10%">&nbsp;</td>
                                    <td width="10%">&nbsp;</td>
                                </tr>
                                <tr style="border: solid 1px #ccc;">
                                    <td>&nbsp;</td>
                                    <td><strong>Platform</strong></td>
                                    <td><strong>Compute</strong></td>
                                    <td><strong>Object Storage</strong></td>
                                </tr>';

            for ($i=0; $i<count($designated_sections); $i++) {
                $dsection = $designated_sections[$i];
                $html .= '<tr style="border: solid 1px #ccc;">
                                <td>'.$dsection->Guidance.'</td>';

                if ($dsection->isPlatform()) {
                    if ($dsection->Status == 'Required') {
                        $html .= '<td style="text-align: center;">✓</td>';
                    } else {
                        $html .= '<td style="text-align: center;">'.$dsection->Status.'</td>';
                    }
                } else {
                    $html .= '<td>&nbsp;</td>';
                }

                if ($dsection->isCompute()) {
                    if ($dsection->Status == 'Required') {
                        $html .= '<td style="text-align: center;">✓</td>';
                    } else {
                        $html .= '<td style="text-align: center;">'.$dsection->Status.'</td>';
                    }
                } else {
                    $html .= '<td>&nbsp;</td>';
                }

                if ($dsection->isStorage()) {
                    if ($dsection->Status == 'Required') {
                        $html .= '<td style="text-align: center;">✓</td>';
                    } else {
                        $html .= '<td style="text-align: center;">'.$dsection->Status.'</td>';
                    }
                } else {
                    $html .= '<td>&nbsp;</td>';
                }

                $html .= '</tr>';
            }

            $html .= '</tbody></table></div>';

        }

        return $html;
    }

}