<?php

/*
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Controller\Adminhtml\Profiles;

class Updater extends \Wyomind\MassStockUpdate\Controller\Adminhtml\Profiles
{

    public function execute()
    {
        $json = array();


        $data = $this->getRequest()->getPost('data');
        foreach ($data as $f) {
            $row = new \Magento\Framework\DataObject;
            $row->setId($f[0]);
            $row->setName($f[1]);
            $row->setCronSettings($f[2]);
            $status = $this->_objectManager->create("Wyomind\MassStockUpdate\Block\Adminhtml\Profiles\Renderer\Status");
            $json[] = array("id" => $f[0], "content" => ($status->render($row)));
        }
        $this->getResponse()->representJson($this->_objectManager->create('Magento\Framework\Json\Helper\Data')->jsonEncode($json));
    }

}
