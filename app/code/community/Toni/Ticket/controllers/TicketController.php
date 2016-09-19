<?php
class Toni_Ticket_TicketController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
    public function ticketAction() {
        $this->_initLayout();
    }
    public function viewAction() {
        $this->_initLayout();
    }
    public function newticketAction() {
        $this->_initLayout();
    }
    public function postnewAction() {
        //Save
        $data = array(
            'user_id'=>Mage::getSingleton('customer/session')->getCustomer()->getId(),
            'subject'=>$this->_request->getParam('subject'),
            'message'=>$this->_request->getParam('message'),
            'active'=>1
        );


        $ticket = Mage::getModel('ticket/ticket');
        $ticket->setData($data);
        $ticket->save();


        //Redirect
        Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
        $this->_redirect('*/*/ticket');
    }
    public function closeAction() {
        //Close
        /**
         * @var Toni_Ticket_Model_Resource_Ticket_Collection $tickets
         * @var Toni_Ticket_Model_Ticket $ticket
         */
        $Id = $this->_request->getParam('entity_id');
        $tickets = Mage::getModel('ticket/ticket')->getCollection();
        $ticket = $tickets->getItemById($Id);
        $ticket->setData(array(
            'active'=>0
        ));
        try {
            $ticket->setId($Id)->save();
        } catch (Exception $e){
            echo "Failed to close ticket";
            return;
        }

        $this->_redirect('*/*/ticket');

    }
    public function newresponseAction() {
        //save
        $response = Mage::getModel("ticket/response");
        $response->setData(array(
            'creator'=>'user',
            'ticket_id'=>$this->_request->getParam('entity_id'),
            'response'=>$this->_request->getParam('response')
        ));
        $response->save();
        $this->_redirect("*/*/view/",array('entity_id'=>$this->_request->getParam('entity_id')));
    }
    public function _initLayout() {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Tickets'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }
    public function isItMine($ticket_id) {
        $ticket = Mage::getModel('ticket/ticket');
        $ticket->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('user_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->load();
    }
}