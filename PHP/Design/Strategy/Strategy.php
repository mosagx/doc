<?php

interface OutputInterface
{
    public function load();
}

class SerializedArrayOutput implements OutputInterface
{
    public function load($arrayData = [])
    {
        return serialize($arrayData);
    }
}

class JsonStringOutput implements OutputInterface
{
    public function load($arrayData = [])
    {
        return json_encode($arrayData);
    }
}

class ArrayOutput implements OutputInterface
{
    public function load($arrayData = [])
    {
        return $arrayData;
    }
}

class SomeClient
{
    private $output;

    public function setOutput(OutputInterface $outputType)
    {
        $this->output = $outputType;
    }

    public function loadOutput($arrayData = [])
    {
        return $this->output->load($arrayData);
    }
}

$client = new SomeClient();

$client->setOutput(new SerializedArrayOutput());

$data = $client->loadOutput([
    'key1' => 'val1',
    'key2' => 'val2'
]);

var_dump($data);die;