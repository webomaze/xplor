<?php

namespace Wyomind\MassStockUpdate\Block\Adminhtml\Profiles;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $_collectionFactory;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper,
            \Wyomind\MassStockUpdate\Model\ResourceModel\Profiles\CollectionFactory $collectionFactory,
            array $data = []
    )
    {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('MassStockUpdateGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
                'id', [
            'header' => __('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
            'filter' => false,
                ]
        );

        $this->addColumn(
                'name', [
            'header' => __('Profile name'),
            'align' => 'left',
            'index' => 'name',
                ]
        );

        $this->addColumn('file_system_type', array(
            'header' => __('File location'),
            'align' => 'left',
            'index' => 'file_system_type',
            'type' => 'options',
            'options' => array(
                \Wyomind\MassStockUpdate\Helper\Data::LOCATION_MAGENTO => __('Magento file system'),
                \Wyomind\MassStockUpdate\Helper\Data::LOCATION_FTP => __('Ftp server'),
                \Wyomind\MassStockUpdate\Helper\Data::LOCATION_URL => __('Url'),
                \Wyomind\MassStockUpdate\Helper\Data::LOCATION_WEBSERVICE => __('Web service')
            ),
        ));


        $this->addColumn('file_type', array(
            'header' => __('File type'),
            'align' => 'left',
            'index' => 'file_type',
            'type' => 'options',
            'options' => array(
                \Wyomind\MassStockUpdate\Helper\Data::CSV => __('csv'),
                \Wyomind\MassStockUpdate\Helper\Data::XML => __('xml')
            ),
        ));

       
        $this->addColumn('status', array(
            'header' => __('Status'),
            'align' => 'left',
            'index' => 'status',
            'renderer' => 'Wyomind\MassStockUpdate\Block\Adminhtml\Profiles\Renderer\Status',
        ));

        $this->addColumn(
                'imported_at', [
            'header' => __('Last execution'),
            'align' => 'left',
            'index' => 'imported_at',
            'width' => '80px',
            'type' => "datetime",
            'renderer' => 'Wyomind\MassStockUpdate\Block\Adminhtml\Profiles\Renderer\Datetime'
                ]
        );

        $this->addColumn(
                'action', [
            'header' => __('Action'),
            'align' => 'left',
            'index' => 'action',
            'filter' => false,
            'sortable' => false,
            'width' => '120px',
            'renderer' => 'Wyomind\MassStockUpdate\Block\Adminhtml\Profiles\Renderer\Action',
                ]
        );
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return "";
    }

}
