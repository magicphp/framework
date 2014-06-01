<?php
    /**
     * Events Controller
     * 
     * @package     MagicPHP
     * @author      André Ferreira <andrehrf@gmail.com>
     * @link        https://github.com/magicphp/framework MagicPHP(tm)
     * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
     */
    
    class Events{
        /**
         * List of events
         * 
         * @access private
         * @var array 
         */
        private $aEvents = array();
        
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
         * Function to call the event
         * 
         * @static
         * @access public
         * @param string $sName
         * @param array $aParams
         * @return mixed
         */
        public static function Call($sName, $aParams = null){
            $oThis = self::CreateInstanceIfNotExists();
            $sName = strtolower(str_replace(array("/", "\\", "--"), "-", $sName));//Bugfix
                        
            if(array_key_exists($sName, $oThis->aEvents)){
                switch($oThis->aEvents[$sName]["type"]){
                    case "perroute":
                        $sCurrentRoute = strtolower(str_replace(array("/", "\\", "--"), "-", Storage::Get("route")));//Bugfix
                        $sRoute = strtolower($oThis->aEvents[$sName]["method"]."_".$oThis->aEvents[$sName]["route"]);
                        
                        if($sRoute == $sCurrentRoute)
                            return call_user_func($oThis->aEvents[$sName]["func"], $aParams);
                    break;
                    case "default": return call_user_func($oThis->aEvents[$sName]["func"], $aParams); break;
                } 
            }
            else{
                return false;
            }
        }
                
        /**
         * Function to set the event
         * 
         * @static
         * @access public
         * @param string $sName
         * @param function $fCallback
         * @return void
         */
        public static function Set($sName, $fCallback){
            $oThis = self::CreateInstanceIfNotExists();
            $sName = strtolower(str_replace(array("/", "\\", "--"), "-", $sName));//Bugfix
            $oThis->aEvents[$sName] = array("type" => "default", "func" => $fCallback);
        }
        
        /**
         * Function to ser the evenet per route
         * 
         * @static
         * @access public
         * @param string $sName
         * @param string $sRoute
         * @param string $sMethod
         * @param function $fCallback
         * @return void
         */
        public static function SetPerRoute($sName, $sRoute, $sMethod, $fCallback){
            $oThis = self::CreateInstanceIfNotExists();
            $sRoute = strtolower(str_replace(array("/", "\\", "--"), "-", $sRoute));//Bugfix
            $oThis->aEvents[strtolower($sMethod."_".$sRoute."_".$sName)] = array("type" => "perroute", "func" => $fCallback, "route" => $sRoute, "method" => $sMethod);
        }
        
        /**
         * Function to check existence of event
         * 
         * @static
         * @access public
         * @param string $sName
         * @return boolean
         */
        public static function Has($sName){
            $oThis = self::CreateInstanceIfNotExists();
            $sName = strtolower(str_replace(array("/", "\\", "--"), "-", $sName));//Bugfix        
            return array_key_exists($sName, $oThis->aEvents);
        }
    }
