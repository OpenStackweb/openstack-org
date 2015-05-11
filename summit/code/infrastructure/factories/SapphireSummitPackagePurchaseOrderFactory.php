<?php
/**
 * Copyright 2015 OpenStack Foundation
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
 * Class SapphireSummitPackagePurchaseOrderFactory
 */
final class SapphireSummitPackagePurchaseOrderFactory
    implements ISummitPackagePurchaseOrderFactory{

    /**
     * @param array $data
     * @return ISummitPackagePurchaseOrder
     */
    public function build(array $data)
    {
        $purchase_order = new SummitPackagePurchaseOrder();
        $purchase_order->FirstName = (string)$data['first_name'];
        $purchase_order->Surname = (string)$data['last_name'];
        $purchase_order->Email = (string)$data['email'];
        $purchase_order->Organization = (string)$data['organization'];
        $purchase_order->RegisteredOrganizationID = (int)$data['organization_id'];
        $purchase_order->PackageID = (int)$data['package_id'];
        $purchase_order->Rejected  = false;
        $purchase_order->Approved  = false;
        return $purchase_order;
    }
}