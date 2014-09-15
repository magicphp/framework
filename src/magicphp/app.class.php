<?php
    /**
     * App Controller
     * 
     * @package     MagicPHP
     * @author      André Ferreira <andrehrf@gmail.com>
     * @link        https://github.com/magicphp/framework MagicPHP(tm)
     * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
     */

    class App{
        /**
         * List of modules
         * 
         * @access private
         * @var array 
         */
        private $aModules = array();
        
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
         * Function to append a module
         * 
         * @param string $sName Module name
         * @param string $sDirectory Path of module
         * @return \Module
         */
        public static function Append($sName, $sDirectory){
            $oThis = self::CreateInstanceIfNotExists();
            
            if(array_key_exists($sName, $oThis->aModules)){
                return $oThis->aModules[$sName];
            }
            else{
                $oModule = new Module($sName, $sDirectory);
                $oThis->aModules[$sName] = $oModule;
                return $oModule;
            }
        }
    }