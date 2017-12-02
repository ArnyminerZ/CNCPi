<?php

/**
 * =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 * From Arjan with ❤
 * Requires PHP Digital IO extension
 * =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 */

/**
 * Keys to request serial info using the getInfo($key) method
 * --------------------------------------------------
     * - "device" - device
     * - "inode" - inode
     * - "mode" - mode
     * - "nlink" - number of hard links
     * - "uid" - user id
     * - "gid" - group id
     * - "device_type" - device type (if inode device)
     * - "size" - total size in bytes
     * - "blocksize" - blocksize
     * - "blocks" - number of blocks allocated
     * - "atime" - time of last access
     * - "mtime" - time of last modification
     * - "ctime" - time of last change
 */

define("MAX_BYTES_READ", 5);
define("MAX_BYTES_WRITE", 1);

class SerialPort
{
    public static $openPorts = array();

    private $portName;
    private $baudrate;
    private $overrideBaudrate = null; // 'null' means no overriding. Otherwise specify baudrate.
    private $connection;

    public function __construct($name, $baudrate){
        $this->portName = $name;
        if(!is_null($this->overrideBaudrate))
            $this->baudrate = $this->overrideBaudrate;
        else
            $this->baudrate = $baudrate;
    }

    public function __destruct()
    {
        if($this->isOpen())
            $this->close();
    }

    public function __get($var){
        return $this->$var;
    }

    public function __toString()
    {
        return "Serialport ({$this->portName}) on baudrate {$this->baudrate}";
    }

    public function open(){
        try{
            exec("mode {$this->portName}: baud={$this->baudrate} data=8 stop=1 parity=n");
            $this->connection = dio_open($this->portName, 2 /* Or use 'O_RDWR' */);
            if($this->isOpen())
                self::$openPorts[$this->portName] = $this;
        }catch(Exception $e){
            throw new Error("Could not open COM port! ({$this->portName})");
        }
    }

    public function close(){
        try{
            dio_close($this->connection);
            unset($this->connection);
            unset(self::$openPorts[$this->portName]);
        }catch(Exception $e){
            throw new Error("Could not close COM port! ({$this->portName})");
        }
    }

    public function read($bytes = MAX_BYTES_READ){
        $result = array();
        for($i = 0; $i < $bytes; $i++){
            $result[] = dio_read($this->connection, 1);
        }
        return $result;
    }

    public function send($data){
        try{
            dio_write($this->connection, $data, MAX_BYTES_WRITE);
        }catch(Exception $e){
            throw new Error("Could not send data to COM port! ({$this->portName})");
        }
    }

    public function getInfo($key){
        return dio_stat($this->connection)[$key];
    }

    public function isOpen(){
        return (isset($this->connection) && !empty($this->connection)) ? true : false;
    }
}
