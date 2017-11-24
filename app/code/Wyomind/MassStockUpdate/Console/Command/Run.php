<?php

/* *
 * Copyright Â© 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * $ bin/magento help wyomind:massstockupdate:run
 * Usage:
 * wyomind:massstockupdate:run [profile_id1] ... [profile_idN]
 *
 * Arguments:
 * profile_id            Space-separated list of import profiles (run all profiles if empty)
 *
 * Options:
 * --help (-h)           Display this help message
 * --quiet (-q)          Do not output any message
 * --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 * --version (-V)        Display this application version
 * --ansi                Force ANSI output
 * --no-ansi             Disable ANSI output
 * --no-interaction (-n) Do not ask any interactive question
 */
class Run extends Command
{

    const PROFILE_ID_ARG = "profile_id";

    protected $_profilesCollectionFactory = null;
    protected $_logger = null;
    protected $_state = null;

    public function __construct(
        \Wyomind\MassStockUpdate\Model\ResourceModel\Profiles\CollectionFactory $profilesCollectionFactory,
        \Wyomind\MassStockUpdate\Logger\Logger $logger,
        \Magento\Framework\App\State $state
    ) {
        $this->_state = $state;
        $this->_profilesCollectionFactory = $profilesCollectionFactory;
        $this->_logger = $logger;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('wyomind:massstockupdate:run')
                ->setDescription(__('Run Mass Stock Update profiles'))
                ->setDefinition([
                    new InputArgument(
                        self::PROFILE_ID_ARG,
                        InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                        __('Space-separated list of import profiles (run all profiles if empty)')
                    )
                ]);
        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;

        try {
            $this->_state->setAreaCode('adminhtml');
            $profilesIds = $input->getArgument(self::PROFILE_ID_ARG);
            $collection = $this->_profilesCollectionFactory->create()->getList($profilesIds);
            foreach ($collection as $profile) {
                $this->_logger->notice("");
                $this->_logger->notice(__("~~~ Run profile #%1 : %2 ~~~", $profile->getId(), $profile->getName()));
                $this->_logger->notice("");
                die();
                $profile->import();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $output->writeln($e->getMessage());
            $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }


        return $returnValue;
    }
}
