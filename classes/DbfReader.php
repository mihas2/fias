<?php

namespace Fias;

class DbfReader
{
    /**
     * Resource associated to the dbf file
     * @var resource
     */
    protected $_filePointer = null;
    /**
     * Path to the dbf file
     * @var string
     */
    protected $_fileName = null;
    /**
     * Headers of the dbf file
     * @var array
     */
    protected $_headers = null;
    /**
     * Fields headers
     * @var array
     */
    protected $_infos = null;
    /**
     * Unpack string build with fields headers
     * @var string
     */
    protected $_unpackString = '';
    /**
     * All data of the dbf file
     * @var array
     */
    protected $_data = [];

    /**
     * DbaseReader constructor: open the file and retrieve the headers
     * @param string $fileName Dbf filename
     * @throws \Exception
     */
    public function __construct($fileName)
    {
        $this->_fileName = $fileName;
        if (!file_exists($fileName) || !\is_readable($fileName)) {
            throw new \Exception('Dbf file does not exist or is not readable');
        }
        $this->_openFile();
        $buffer = fread($this->_filePointer, 32);
        $this->_headers = unpack("VrecordCount/vfirstRecord/vrecordLength",
                                 substr($buffer, 4, 8));
        $this->_closeFile();
    }

    /**
     * Open associated dbf file
     */
    private function _openFile()
    {
        if (!$this->_filePointer) {
            $this->_filePointer = fopen($this->_fileName, 'r');
        }
    }

    /**
     * Close associated dbf file
     */
    private function _closeFile()
    {
        if ($this->_filePointer) {
            fclose($this->_filePointer);
            $this->_filePointer = null;
        }
    }

    /**
     * Close associated dbf file when destructing object
     */
    public function __destruct()
    {
        $this->_closeFile();
    }

    /**
     * Retrieve file metadata
     * @return array
     * @throws \Exception
     */
    public function getInfos()
    {
        if (!$this->_infos) {
            $this->_openFile();
            $continue = true;
            $this->_unpackString = '';
            $fields = [];
            fseek($this->_filePointer, 32);
            // Read fields headers
            while ($continue && !feof($this->_filePointer)) {
                $buffer = fread($this->_filePointer, 32);
                if (substr($buffer, 0, 1) == chr(13)) {
                    $continue = false;
                } else {
                    $field = unpack("a11fieldName/A1fieldType/Voffset/CfieldLen/CfieldDec",
                                    substr($buffer, 0, 18));
                    // Check fields headers

                    if (!in_array($field['fieldType'], ['M', 'D', 'N', 'C', 'L', 'F'])) {
                        throw new \Exception("Field type of field '{$field['fieldName']}' is not correct");
                    }
                    $this->_unpackString .= 'A' . $field['fieldLen'] . $field['fieldName'] . '/';
                    array_push($fields, $field);
                }
            }
            $this->_infos = $fields;
            $this->_closeFile();
        }
        return $this->_infos;
    }

    /**
     * @param $rowNum
     * @param bool $saveData
     * @return array
     * @throws \Exception
     */
    public function fetch($rowNum, $saveData = true)
    {
        $recordSeek = $this->_headers['recordLength'] * $rowNum + $this->getHeaders()['firstRecord'];
        $recordMaxSeek = $this->getHeaders()['recordCount']
                         * $this->getHeaders()['recordLength']
                         + $this->getHeaders()['firstRecord']
                         - $this->getHeaders()['recordLength'];
        if ($recordSeek > $recordMaxSeek) {
            throw new \Exception("out of range {$recordSeek} of {$recordMaxSeek}");
        }
        if (!$this->_data[$rowNum] || !$saveData) {
            $this->getInfos();

            $this->_openFile();
            fseek($this->_filePointer, $recordSeek);
            $records = [];
            //First byte shows if the record is deleted
            $deleted = fread($this->_filePointer, 1);
            $buffer = fread($this->_filePointer, ($this->_headers['recordLength'] - 1));
            $record = unpack($this->_unpackString, $buffer);

            //Deleted records marked with *
            if ($deleted != '*') {
                array_push($records, $record);
            }

            if ($saveData) {
                $this->_data[$rowNum] = $record;
            }
            $this->_closeFile();
        }

        if ($saveData) {
            $record = $this->_data[$rowNum];
        }

        return $record;
    }

    /**
     * Return all records as an array
     * @return array
     * @throws \Exception
     */
    public function fetchAll()
    {
        if (!$this->_data) {
            $this->getInfos();
            $this->_openFile();
            for ($i = 0; $i < $this->getRecordCount(); $i++) {
                $this->fetch($i);
            }
            $this->_closeFile();
        }

        return $this->_data;
    }

    /**
     * Number of record
     * @return integer
     */
    public function getRecordCount()
    {
        return (int)$this->_headers['recordCount'];
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }
}
