<?php

class Toni_Logger_Model_Resource_Logger_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract

{

    public function _construct()

    {

        $this->_init('toni_logger/logger');

    }

}