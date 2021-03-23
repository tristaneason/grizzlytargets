<?php

if (!defined('ABSPATH')){
    exit; 
}

     /* ===========================================
                Includes VersionCompat class
        ===========================================
     */

if(! class_exists('VersionCompat')){
    Class VersionCompat{
        public $phpVersion;
        function __construct() {
            $this->phpVersion = PHP_VERSION;
        }

        function enArrayColumn($data,$key){
            $oldVersion = $this->phpVersion <= 5.4;
            $columns = (!$oldVersion && function_exists("array_column")) ? array_column($data, $key) : array();
            $arrLength = count($data);
            if(empty($arrLength) || !$oldVersion) return $columns;
            $indexArr = array_fill(0, $arrLength, $key);
            $columns = array_map(function($data, $index){$column = is_object($data) ? $data->$index : $data[$index] ; return $column;},$data,$indexArr);
            return $columns;
        }
    }
}