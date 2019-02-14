<?php

namespace Signalize\Hardware;

abstract class Serial
{
    private $fp;
    private $size;


    /**
     * @param string $chuck
     * @param string $buffer
     * @return Package
     */
    abstract function process(string $chuck, string $buffer): \Signalize\Hardware\Package;
    
    /**
     * Serial constructor.
     * @param string $device
     * @param int $size
     */
    public function __construct(string $device, int $size)
    {
        $this->fp = fopen($device, "r+");
        $this->size = $size;
    }

    /**
     * @param callable $method
     */
    public function subscribe(callable $method)
    {
        $buffer = null;
        while ($chuck = fgets($this->fp, $this->size)) {
            if (trim($chuck)) {
                $buffer .= $chuck;
                if ($package = $this->process($chuck, trim($buffer))) {
                    if (is_a($package, Package::class)) {
                        $method($package);
                    }
                } else {
                    $buffer = null;
                }
            }
        }
    }


    public function __destruct()
    {
        fclose($this->fp);
    }
}