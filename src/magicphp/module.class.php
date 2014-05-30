<?php
    /**
     * Module Controller
     * 
     * @package     MagicPHP
     * @author      André Ferreira <andrehrf@gmail.com>
     * @link        https://github.com/magicphp/framework MagicPHP(tm)
     * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
     */

    class Module{
        /**
         * Module name
         * 
         * @access private
         * @var string
         */
        private $sModuleName = null;
        
        /**
         * Module diretory
         * 
         * @access private
         * @var string
         */
        private $sModuleDiretory = null;
                
        /**
         * Class constructor function
         * 
         * @access public
         * @param string $sName
         * @param string $sModuleDiretory
         * @return \Self
         */
        public function __construct($sModuleName, $sModuleDiretory) {
            $this->sModuleName = $sModuleName;
            $this->sModuleDiretory = $sModuleDiretory;
            return $this;
        }
        
        /**
         * Function to set module settings
         * 
         * @access public
         * @param string $sKey Search Key
         * @param mixed $mValue
         * @return \Module
         */
        public function Set($sKey, $mValue){
            if(!empty($this->sModuleName))
                Storage::Set("module.".$this->sModuleName.".".$sKey, $mValue);
            
            return $this;
        }
               
        /**
         * Function to start the module
         * 
         * @access public
         * @return void
         */
        public function Start(){
            if(file_exists($this->sModuleDiretory . SP . "status.txt")){
                $iStatus = intval(file_get_contents($this->sModuleDiretory . SP . "status.txt"));
                $bStatus = ($iStatus == 1);
                Storage::Set("module.".$this->sModuleName.".enabled", $bStatus);
            }
            else{
                $bStatus = true;
                file_put_contents($this->sModuleDiretory . SP . "status.txt", "1");
                Storage::Set("module.".$this->sModuleName.".enabled", true);
            }
            
            if($bStatus){
                Storage::SetArray("class.list", "module.".$this->sModuleName, $this->sModuleDiretory . "core" . SP);
                Storage::Set("module.".$this->sModuleName.".shell", $this->sModuleDiretory . "shell" . SP);
                Storage::Set("module.".$this->sModuleName.".shell.css", $this->sModuleDiretory . "shell" . SP . "css" . SP);
                Storage::Set("module.".$this->sModuleName.".shell.tpl", $this->sModuleDiretory . "shell" . SP . "tpl" . SP);
                Storage::Set("module.".$this->sModuleName.".shell.js", $this->sModuleDiretory . "shell" . SP . "js" . SP);
                Storage::Set("module.".$this->sModuleName.".shell.img", $this->sModuleDiretory . "shell" . SP . "img" . SP);
            }            
        }
    }
