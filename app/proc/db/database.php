<?php
    class database{

        private $connection_string;
        private static $conn;
        public function __construct($conn){
          $this->connection_string = $conn;
        }
        public function Connect(){
          try {
            if(is_null(self::$conn))
              return self::$conn = new mysqli($this->connection_string['host'],$this->connection_string['user'],$this->connection_string['password'],$this->connection_string['database']);
            else
              return self::$conn;
            } catch (\Exception $ex) {
              echo $ex->getMessage();
          }
          
        }
        public function Filter($string){
            return self::$conn->real_escape_string(strip_tags((trim($string))));
        }
        public function Close(){
          if(!is_null(self::$conn))
            self::$conn = null;
        }
    }