<?php
    /**
     * Global Storage
     * 
     * @package     MagicPHP
     * @author      André Ferreira <andrehrf@gmail.com>
     * @link        https://github.com/magicphp/framework MagicPHP(tm)
     * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
     */
    
    class Storage{
        /**
         * Storage list
         * 
         * @static
         * @access private
         * @var array 
         */
        private $aList = array();
        
        /**
         * Function to auto instance
         * 
         * @static
         * @access public
         * @return \self
         */
        public static function &CreateInstanceIfNotExists(){
            static $oInstance = null;

            if(!$oInstance instanceof self)
                $oInstance = new self();

            return $oInstance;
        }     
        
        /**
         * Function to check existence of data stored
         * 
         * @static
         * @access public
         * @param string $sKey Search key
         * @return boolean
         */
        public static function Has($sKey){
            $oThis = self::CreateInstanceIfNotExists();
            return (array_key_exists($sKey, $oThis->aList));
        }
        
        /**
         * Function to store data 
         * 
         * @static
         * @access public
         * @param string $sKey Search key
         * @param mixed $mValue Value Data to be stored
         * @return void
         */
        public static function Set($sKey, $mValue){
            $oThis = self::CreateInstanceIfNotExists();
            $oThis->aList[$sKey] = $mValue;  
        }
        
        /**
         * Function to store data in list
         * 
         * @static
         * @access public
         * @param string $sKey Search key
         * @param string $sKeyInArray Search key in the list
         * @param mixed $mValue Data to be stored
         * @return void
         */
        public static function SetArray($sKey, $sKeyInArray, $mValue){
            $oThis = self::CreateInstanceIfNotExists();
            
            if(!array_key_exists($sKey, $oThis->aList))
                $oThis->aList[$sKey] = array();
            
            $oThis->aList[$sKey][$sKeyInArray] = $mValue;
        }
             
        /**
         * Function to return data stored
         * 
         * @static
         * @access public
         * @param string $sKey Search key
         * @param mixed $mDefault Default value (returns if no storage)
         * @return mixed
         */
        public static function Get($sKey, $mDefault = false){
            $oThis = self::CreateInstanceIfNotExists();
            return (array_key_exists($sKey, $oThis->aList)) ? $oThis->aList[$sKey] : $mDefault;
        }
        
        /**
         * Function to return data stored in list
         * 
         * @static
         * @access public
         * @param string $sKey Search key
         * @param string $sKeyInArray Search key in the list
         * @param mixed $mDefault Default value (returns if no storage)
         * @return mixed
         */
        public static function GetArray($sKey, $sKeyInArray, $mDefault = false){
            $oThis = self::CreateInstanceIfNotExists();
            return (array_key_exists($sKey, $oThis->aList)) ? ((array_key_exists($sKeyInArray, $oThis->aList[$sKey]) ? $oThis->aList[$sKey][$sKeyInArray] : $mDefault)) : $mDefault;
        }
        
        /**
         * Function to concatenate data stored
         * 
         * @static
         * @access public
         * @param string $sKey Search key
         * @param string $sAppend Text to be concatenated
         * @return string|boolean
         */
        public static function Join($sKey, $sAppend){
            $oThis = self::CreateInstanceIfNotExists();
            return (array_key_exists($sKey, $oThis->aList)) ? ((is_string($oThis->aList[$sKey])) ? $oThis->aList[$sKey].$sAppend : false)  : false;
        }
        
        /**
         * Function to return all values ​​stored
         * 
         * @statis
         * @access public
         * @return array
         */
        public static function GetList(){
            $oThis = self::CreateInstanceIfNotExists();
            return $oThis->aList;
        }
        
        /**
         * Function to transform the variables Storage variables in Smarty Template Engine
         * 
         * @static
         * @access public
         * @param Smarty $oSmarty
         * @return void
         */
        public static function AssignSmarty(&$oSmarty){
            $oThis = self::CreateInstanceIfNotExists();
            $aStorage = Storage::GetList();
            
            $oSmartyVars = array();
            
            function ReturnSubKeys($sKeyRoot, $mValue){
                $aKey = explode(".", $sKeyRoot);
                $aArray = array();

                if(count($aKey) > 1){
                    $aSubKeys = ReturnSubKeys(str_replace($aKey[0].".", "", $sKeyRoot),$mValue);                                                   
                    $aArray[$aSubKeys["key"]] = @(is_array($aArray[$aSubKeys["key"]]) ? @array_merge_recursive($aArray[$aSubKeys["key"]], $aSubKeys["result"]) : $aSubKeys["result"]);
                    return array("result" => $aArray, "key" => $aKey[0]);
                }
                else{
                    return array("result" => $mValue, "key" => $sKeyRoot);
                }
            }
            
            foreach($aStorage as $sKey => $mValue){
                $aResult = ReturnSubKeys($sKey, $mValue);
                $oSmartyVars[$aResult["key"]] = @(is_array($oSmartyVars[$aResult["key"]]) ? @array_merge_recursive($oSmartyVars[$aResult["key"]], $aResult["result"]) : $aResult["result"]);
                $oSmarty->assign($aResult["key"], $oSmartyVars[$aResult["key"]]); 
            }
        }
    }