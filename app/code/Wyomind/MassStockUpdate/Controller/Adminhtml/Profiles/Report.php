<?php

/* *
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Controller\Adminhtml\Profiles;

class Report extends \Wyomind\MassStockUpdate\Controller\Adminhtml\Profiles
{

    public function execute()
    {

        $data = $this->getRequest()->getPost();
        if ($data) {
            $model = $this->_objectManager->create('Wyomind\MassStockUpdate\Model\Profiles');

            $id = $this->getRequest()->getParam('id');

            if ($id) {
                $model->load($id);
            }
            
            return $this->getResponse()->representJson($model->getLastImportReport());
        }
    }
}
