<?php

namespace Wyomind\MassStockUpdate\Block\Adminhtml\Profiles\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{

    public function render(\Magento\Framework\DataObject $row)
    {
        $actions = array(
            [
                'url' => $this->getUrl('massstockupdate/profiles/edit', ['id' => $row->getId()]),
                'caption' => __('Edit'),
            ],
            [
                'url' => "javascript:void(require(['wyomind_MassStockUpdate_profile'], function (profile) { profile.delete('" . $this->getUrl('massstockupdate/profiles/delete', ['id' => $row->getId()]) . "')}))",
                'caption' => __('Delete'),
            ],
            [
                'url' => "javascript:void(require(['wyomind_MassStockUpdate_profile'], function (profile) {profile.run('" . $this->getUrl('massstockupdate/profiles/run', ['id' => $row->getId()]) . "')}))",
                'caption' => __('Run profile'),
            ]
        );
        if ($row->getImportedAt() != "" &&  $row->getImportedAt() != "0000-00-00 00:00:00") {
            $actions[] = [
                'url' => "javascript:void(require(['wyomind_MassStockUpdate_profile'], function (profile) {profile.report('" . $this->getUrl('massstockupdate/profiles/report', ['id' => $row->getId()]) . "', '" . $row->getId() . "', '" . $row->getName() . "')}))",
                "caption" => __("Show report")
            ];
        }
        $this->getColumn()->setActions($actions);
        return parent::render($row);
    }

}
