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
 * Class SummitPackagePurchaseOrderManager
 */
final class SummitPackagePurchaseOrderManager
    implements ISummitPackagePurchaseOrderManager {

    /**
     * @var ITransactionManager
     */
    private $tx_manager;
    /**
     * @var IEntityRepository
     */
    private $repository;
    /**
     * @var ISummitPackagePurchaseOrderFactory
     */
    private $factory;


    public function __construct(IEntityRepository $repository,
                                ISummitPackagePurchaseOrderFactory $factory,
                                ITransactionManager $tx_manager){

        $this->repository = $repository;
        $this->factory    = $factory;
        $this->tx_manager = $tx_manager;
    }


    /**
     * @param array $data
     * @param IMessageSenderService $new_purchase_order_message_sender
     * @throws EntityValidationException
     * @return mixed
     */
    public function registerPurchaseOrder(array $data, IMessageSenderService $new_purchase_order_message_sender)
    {
        $repository = $this->repository;
        $factory    = $this->factory;

        $this->tx_manager->transaction(function() use($data, $repository, $factory, $new_purchase_order_message_sender){

            $rules = array(
                'package_id'        => 'required|integer',
                'first_name'        => 'required|text|max:250',
                'last_name'         => 'required|text|max:250',
                'email'             => 'required|text|max:250|email',
                'organization'      => 'required|text',
                'organization_id'   => 'sometimes|integer',
            );

            $messages = array(
                'first_name.required'     => ':attribute is required',
                'first_name.text'         => ':attribute should be valid text.',
                'first_name.max'          => ':attribute should have less than 250 chars.',
                'last_name.required'      => ':attribute is required',
                'last_name.text'          => ':attribute should be valid text.',
                'last_name.max'           => ':attribute should have less than 250 chars.',
                'email.required'          => ':attribute is required',
                'email.text'              => ':attribute should be valid text.',
                'email.max'               => ':attribute should have less than 250 chars.',
                'email.email'             => ':attribute should be valid email.',
                'organization.required'   => ':attribute is required',
                'organization.text'       => ':attribute should be valid text.',
                'package_id.required'     => ':attribute is required',
                'package_id.integer'      => ':attribute should be valid integer.',
                'organization_id.integer' => ':attribute should be valid integer.',
            );

            $validator = ValidatorService::make($data, $rules, $messages);

            if ($validator->fails()) {
                throw new EntityValidationException($messages);
            }

            $purchase_order = $factory->build($data);

            $repository->add($purchase_order);

            $new_purchase_order_message_sender->send($purchase_order);
        });
    }

    /**
     * @param $purchase_order_id
     * @param IMessageSenderService $approved_purchase_order_message_sender
     * @throws EntityValidationException
     * @throws NotFoundEntityException
     * @return void
     */
    public function approvePurchaseOrder($purchase_order_id, IMessageSenderService $approved_purchase_order_message_sender)
    {
        $repository = $this->repository;
        $this->tx_manager->transaction(function() use($purchase_order_id, $repository, $approved_purchase_order_message_sender){

            $purchase_order = $repository->getById($purchase_order_id);

            if(is_null($purchase_order))
                throw new NotFoundEntityException('SummitPackagePurchaseOrder', sprintf('id %d', $purchase_order_id));

            if($purchase_order->isApproved())
                throw new EntityValidationException( EntityValidationException::buildMessage(sprintf('order id %s is already approved!', $purchase_order_id)));

            if($purchase_order->isRejected())
                throw new EntityValidationException( EntityValidationException::buildMessage(sprintf('order id %s is already rejected!', $purchase_order_id)));

            $package =  $purchase_order->package();

            if($package->SoldOut()){
                throw new EntityValidationException( EntityValidationException::buildMessage(sprintf('package id %s is Sold Out!', $package->getIdentifier())));
            }

            $purchase_order->approve($approved_purchase_order_message_sender);

            //decrement package availability
            $package->sell();
        });
    }

    /**
     * @param $purchase_order_id
     * @param IMessageSenderService $rejected_purchase_order_message_sender
     * @throws EntityValidationException
     * @throws NotFoundEntityException
     * @return void
     */
    public function rejectPurchaseOrder($purchase_order_id, IMessageSenderService $rejected_purchase_order_message_sender)
    {
        $repository = $this->repository;

        $this->tx_manager->transaction(function() use($purchase_order_id, $repository, $rejected_purchase_order_message_sender){

            $purchase_order = $repository->getById($purchase_order_id);

            if(is_null($purchase_order))
                throw new NotFoundEntityException('SummitPackagePurchaseOrder', sprintf('id %d', $purchase_order_id));

            if($purchase_order->isApproved())
                throw new EntityValidationException( EntityValidationException::buildMessage(sprintf('order id %s is already approved!', $purchase_order_id)));

            if($purchase_order->isRejected())
                throw new EntityValidationException( EntityValidationException::buildMessage(sprintf('order id %s is already rejected!', $purchase_order_id)));

            $purchase_order->reject($rejected_purchase_order_message_sender);
        });
    }
}