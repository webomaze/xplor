<?php

namespace Wyomind\MassStockUpdate\Controller\Adminhtml\Profiles;

/**
 * Full import process
 */
class Run extends \Wyomind\MassStockUpdate\Controller\Adminhtml\Profiles
{

    public function execute()
    {
        $request = $this->getRequest();
        try {
            $this->_logger->notice("-------------------------------------------------");
            $this->_logger->notice(">> " . __METHOD__ . "()");

            $data = $this->getRequest()->getPost();

            if ($this->getRequest()->getParam("isAjax")) {
                session_write_close();
            }


            if ($data) {
                $model = $this->_objectManager->create('Wyomind\MassStockUpdate\Model\Profiles');

                $id = $this->getRequest()->getParam('id');

                if ($id) {
                    $model->load($id);
                }

                $this->_logger->notice(__("Running Profile #%1", $id));
                $rtn = $model->import();

                $this->messageManager->addSuccess(__('The profile %1 [ID:%2] has been processed.', $model->getName(), $model->getId()));
                if (count($rtn["success"]) > 0) {
                    $this->messageManager->addSuccess(__('%1', $rtn["success"]));
                }
                if (count($rtn["notice"]) > 0) {
                    $this->messageManager->addNotice(__('%1', $rtn["notice"]));
                }
                if (count($rtn["warning"]) > 0) {
                    $this->messageManager->addWarning(__('%1', $rtn["warning"]));
                }
                $this->_logger->addNotice(__("Profile #%1 has been processed", $id));



                if ($request->getParam('run_i')) {
                    return $this->_resultRedirectFactory->create()->setPath('massstockupdate/profiles/edit', ['id' => $model->getId(), "_current" => true]);
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            if ($request->getParam('run_i')) {
                return $this->_resultRedirectFactory->create()->setPath('massstockupdate/profiles/edit', ['id' => $model->getId(), "_current" => true]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            if ($request->getParam('run_i')) {
                return $this->_resultRedirectFactory->create()->setPath('massstockupdate/profiles/edit', ['id' => $model->getId(), "_current" => true]);
            }
        }
    }

}
