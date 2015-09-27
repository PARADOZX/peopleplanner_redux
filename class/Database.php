<?php

class Database {
   protected $_conn;

   public function __construct($connection) {
       $this->_conn = $connection;
   }

   public function ExecuteObject($sql, $data) {
       // stuff
   }
}
