<?php
  class db{
    private $dbHost ='containers-us-west-203.railway.app';
    private $dbUser = 'root';
    private $dbPass = 'IpiAKS4aogDg9BTYMN32';
    private $dbName = 'railway';
    private $puert = '7113';
    //conección 
    public function conectDB(){
      $mysqlConnect = "mysql:host=$this->dbHost;port=$this->puert;dbname=$this->dbName";
      $dbConnecion = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
      $dbConnecion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   // $dbConnecion->exec("set names utf8");
      return $dbConnecion;
    }
  }
