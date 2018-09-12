<?php

class CVS
{
    private $_data;

    private $_name;

    private $_path = './data/';

    public function ins($path)
    {
        $this->_path = $path;
    }

    public function out($data, $filename)
    {
        $this->_data = $data;
        $this->_name = $filename;

        $head = array_keys($data[0]);
        $fp   = fopen($this->_getFile(), 'a');
        $r    = [];
        foreach ($data as $item) {
            $r[] = array_values($item);         
        }
        fputcsv($fp, $r);
    }

    private function _getFile()
    {
        return $this->_path.$this->_name;
    }
}