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
 * Interface ISummitPackagePurchaseOrderManager
 */
interface ISummitPackagePurchaseOrderManager {

    /**
     * @param array $data
     * @param IMessageSenderService $new_purchase_order_message_sender
     * @throws EntityValidationException
     * @return void
     */
    public function registerPurchaseOrder(array $data, IMessageSenderService $new_purchase_order_message_sender);

    /**
     * @param $purchase_order_id
     * @param IMessageSenderService $approved_purchase_order_message_sender
     * @throws EntityValidationException
     * @throws NotFoundEntityException
     * @return void
     */
    public function approvePurchaseOrder($purchase_order_id, IMessageSenderService $approved_purchase_order_message_sender);

    /**
     * @param $purchase_order_id
     * @param IMessageSenderService $rejected_purchase_order_message_sender
     * @throws EntityValidationException
     * @throws NotFoundEntityException
     * @return void
     */
    public function rejectPurchaseOrder($purchase_order_id, IMessageSenderService $rejected_purchase_order_message_sender);
}