<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Helper;

class Storage extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_mageRootDir = null;
    protected $_driverFile = null;
    protected $_directoryList = null;
    protected $_driverFileFactory = null;
    protected $_driverHttpFactory = null;
    protected $_driverHttpsFactory = null;
    protected $_ioFtpFactory = null;
    protected $_ioSftpFactory = null;
    protected $_logger = null;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
            \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
            \Magento\Framework\Filesystem\Driver\FileFactory $driverFileFactory,
            \Wyomind\MassStockUpdate\Filesystem\Driver\HttpFactory $driverHttpFactory,
            \Wyomind\MassStockUpdate\Filesystem\Driver\HttpsFactory $driverHttpsFactory,
            \Magento\Framework\Filesystem\Io\FtpFactory $ioFtpFactory,
            \Magento\Framework\Filesystem\Io\SftpFactory $ioSftpFactory,
            \Wyomind\MassStockUpdate\Logger\Logger $logger
    )
    {
        parent::__construct($context);
        $this->_directoryList = $directoryList;
        $this->_driverFileFactory = $driverFileFactory;
        $this->_driverHttpFactory = $driverHttpFactory;
        $this->_driverHttpsFactory = $driverHttpsFactory;
        $this->_ioFtpFactory = $ioFtpFactory;
        $this->_ioSftpFactory = $ioSftpFactory;
        $this->_logger = $logger;
    }

    public function newTempFileName()
    {
        $tmpFolder = $this->getMageRootDir() . \Wyomind\MassStockUpdate\Helper\Data::TMP_FOLDER;
        $this->mkdir($tmpFolder);
        $tempFileName = tempnam($tmpFolder, \Wyomind\MassStockUpdate\Helper\Data::TMP_FILE_PREFIX) . '.' . \Wyomind\MassStockUpdate\Helper\Data::TMP_FILE_EXT;
        if (strpos($tempFileName, ".orig") !== false) {
            $this->deleteFile(dirname($tempFileName), basename($tempFileName));
            $tempFileName = str_replace(".orig", "", $tempFileName);
        }
        return $tempFileName;
    }

    /**
     * Retrieve the content of the import file and put it in a tmp file
     * @param type $locationType FTP/Filesystem/URL ?
     * @param type $data location type options
     * @return string the temp file
     * @throws \Exception
     */
    public function getImportFile(
    $locationType, $data
    )
    {
        $this->_logger->notice(">> " . __METHOD__ . "()");
        $this->_logger->notice(__("Retrieving file containing data to update"));

        $tmpFileName = $this->newTempFileName();

        $driverFile = $this->getDriverFile();

        switch ($locationType) {
            case \Wyomind\MassStockUpdate\Helper\Data::LOCATION_MAGENTO:
                $this->_logger->notice(__("Retrieving file from Magento file system"));
                $newFileName = rtrim($this->getMageRootDir(), "/") . "/" . ltrim($data['file_path'], "/");
                $this->_logger->notice(__("Retrieving file : " . $newFileName));
                if (!$driverFile->isExists($newFileName)) {
                    $this->_logger->notice(__("Magento File System : File %1 not found !", $newFileName));
                    throw new \Exception(__("Magento File System : File %1 not found !", $newFileName));
                } else {
                    $driverFile->copy($newFileName, $tmpFileName);
                }
                break;

            case \Wyomind\MassStockUpdate\Helper\Data::LOCATION_URL:
                $this->_logger->notice(__("Retrieving file from url"));
                $content = "";
                if (strpos($data["file_path"], "http://") !== false) { // HTTP
                    $url = str_replace("http://", "", $data["file_path"]);
                    $this->_logger->notice("Url : " . $url);
                    $driverHttp = $this->_driverHttpFactory->create();
                    if (!$driverHttp->isExists($url)) {
                        $this->_logger->notice(__("HTTP : File %1 not found ! (%2)", $data["file_path"], $driverHttp->getStatus()));
                        throw new \Exception(__("HTTP : File %1 not found ! (%2)", $data["file_path"], $driverHttp->getStatus()));
                    }
                    $content = $driverHttp->fileGetContents($url);
                } elseif (strpos($data["file_path"], "https://") !== false) { // HTTPS
                    $url = str_replace("https://", "", $data["file_path"]);
                    $this->_logger->notice("Url : " . $url);
                    $driverHttps = $this->_driverHttpsFactory->create();
                    if (!$driverHttps->isExists($url)) {
                        $this->_logger->notice(__("HTTPS : File %1 not found ! (%2)", $data["file_path"], $driverHttp->getStatus()));
                        throw new \Exception(__("HTTPS : File %1 not found ! (%2)", $data["file_path"], $driverHttp->getStatus()));
                    }
                    $content = $driverHttps->fileGetContents($url);
                }

                $driverFile->filePutContents($tmpFileName, $content);
                break;

            case \Wyomind\MassStockUpdate\Helper\Data::LOCATION_FTP:
                $this->_logger->notice(__("Retrieving file from FTP server"));
                if ($data["use_sftp"]) {
                    $ftp = $this->_ioSftpFactory->create();
                } else {
                    $ftp = $this->_ioFtpFactory->create();
                }

                $host = str_replace(["ftp://", "ftps://"], "", $data["ftp_host"]);
                if ($data['ftp_port'] != "" && $data["use_sftp"]) {
                    $host .= ":" . $data['ftp_port'];
                }

                $fullFilePath = rtrim($data['ftp_dir'], "/") . "/" . ltrim($data['file_path'], "/");
                $fullPath = dirname($fullFilePath);
                $fileName = basename($fullFilePath);

                $ftp->open(
                        [
                            'host' => $host,
                            'user' => $data["ftp_login"],
                            // sftp only
                            'username' => $data["ftp_login"],
                            'port' => $data['ftp_port'],
                            'password' => $data["ftp_password"],
                            'timeout' => '120',
                            'path' => $fullPath,
                            'passive' => !$data["ftp_active"]
                        ]
                );
                $ftp->cd($fullPath);
                $allFiles = $ftp->ls();
                $found = false;
                foreach ($allFiles as $file) {
                    if ($file['id'] == $fullFilePath) {
                        $found = true;
                    }
                }
                if ($found) {
                    $ftp->read($fileName, $tmpFileName);
                    $ftp->close();
                } else {
                    $ftp->close();
                    $this->_logger->notice(__("FTP : File %1 not found !", $fullFilePath));
                    throw new \Exception(__("FTP : File %1 not found !", $fullFilePath));
                }

                break;
            case \Wyomind\MassStockUpdate\Helper\Data::LOCATION_WEBSERVICE:

                $this->_logger->notice(__("Retrieving file from webservice"));

                $url = $data['file_path'];
                $params = str_replace("{{DATE}}", date('Y-m-d'), $data['webservice_params']);
                $login = $data['webservice_login'];
                $password = $data['webservice_password'];

                $content = "";

                try {



                    $fields = array(
                        'login' => urlencode($login),
                        'password' => urlencode($password),
                        'data' => base64_encode(gzencode($params))
                    );

                    $fields_string = "";
                    foreach ($fields as $key => $value) {
                        $fields_string .= $key . '=' . $value . '&';
                    }
                    rtrim($fields_string, '&');

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, count($fields));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $content = curl_exec($ch);
                    curl_close($ch);
                } catch (\Exception $er) {
                    $this->_logger->notice(__("WEB SERVICE : %1", $er->getMessage()));
                    throw new \Exception(__("WEB SERVICE : %1", $er->getMessage()));
                }

                $driverFile->filePutContents($tmpFileName, $content);

                break;
        }

        $this->_logger->notice(__("Temporary file created : %1", $tmpFileName));
        return $tmpFileName;
    }

    /**
     * Create a folder
     * @param type $folder
     */
    public function mkdir($folder)
    {
        $driverFile = $this->getDriverFile();
        $driverFile->createDirectory($folder, 0755);
    }

    /**
     * Delete a file
     * @param type $folder
     * @param type $file
     */
    public function deleteFile(
    $folder, $file
    )
    {
        $driverFile = $this->getDriverFile();
        if ($driverFile->isExists($folder . "/" . $file)) {
            $driverFile->deleteFile($folder . '/' . $file);
        }
    }

    /**
     * Open file
     * @param type $folder
     * @param type $file
     * @return type
     */
    public function fileOpen(
    $folder, $file, $mode = 'w'
    )
    {
        $driverFile = $this->getDriverFile();
        $resource = $driverFile->fileOpen($folder . "/" . $file, $mode);
        return $resource;
    }

    public function fileClose($resource)
    {
        $driverFile = $this->getDriverFile();
        $result = $driverFile->fileClose($resource);
        return $result;
    }

    public function fileWrite(
    $resource, $data
    )
    {
        $driverFile = $this->getDriverFile();
        $result = $driverFile->fileWrite($resource, $data);
        return $result;
    }

    public function fileReadLine($resource)
    {
        $driverFile = $this->getDriverFile();
        $result = $driverFile->fileReadLine($resource, 0, "\n");
        return $result;
    }

    /**
     * Put data in a csv file
     * @param type $resource
     * @param type $data
     */
    public function filePutCsv(
    $resource, $data, $delimiter = ",", $enclosure = ""
    )
    {
        if ($enclosure == "none") {
            $enclosure = "";
        }
        $driverFile = $this->getDriverFile();
        if ($enclosure == "") {
            $driverFile->filePutCsv($resource, $data, $delimiter);
        } else {
            $driverFile->filePutCsv($resource, $data, $delimiter, $enclosure);
        }
    }

    /**
     * Get Mage root dir
     * @return type
     */
    public function getMageRootDir()
    {
        if ($this->_mageRootDir == null) {
            $this->_mageRootDir = $this->_directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        }
        return $this->_mageRootDir;
    }

    /**
     * Get file driver instance
     * @return type
     */
    public function getDriverFile()
    {

        if ($this->_driverFile == null) {
            $this->_driverFile = $this->_driverFileFactory->create();
        }
        return $this->_driverFile;
    }

}
