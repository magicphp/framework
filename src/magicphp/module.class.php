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
                Storage::Set("app.".$this->sModuleName.".".$sKey, $mValue);
            
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
                Storage::Set("app.".$this->sModuleName.".enabled", $bStatus);
            }
            else{
                $bStatus = true;
                file_put_contents($this->sModuleDiretory . SP . "status.txt", "1");
                Storage::Set("app.".$this->sModuleName.".enabled", true);
            }
            
            if($bStatus){
                Storage::SetArray("class.list", "module.".$this->sModuleName, $this->sModuleDiretory . "core" . SP);
<<<<<<< HEAD
                
                //Diretory Paths
                Storage::Set("app.".$this->sModuleName.".shell.css", $this->sModuleDiretory . "shell" . SP . "css" . SP);
                Storage::Set("app.".$this->sModuleName.".shell.tpl", $this->sModuleDiretory . "shell" . SP . "tpl" . SP);
                Storage::Set("app.".$this->sModuleName.".shell.js", $this->sModuleDiretory . "shell" . SP . "js" . SP);
                Storage::Set("app.".$this->sModuleName.".shell.img", $this->sModuleDiretory . "shell" . SP . "img" . SP);
                
                //Web Paths
                Storage::Set("virtual.".$this->sModuleName.".shell.img", Storage::Join("route.root", "app/" . $this->sModuleName . "/shell/img/"));
                Storage::Set("virtual.".$this->sModuleName.".shell.tpl", Storage::Join("route.root", "app/" . $this->sModuleName . "/shell/tpl/"));
                Storage::Set("virtual.".$this->sModuleName.".shell.js", Storage::Join("route.root", "app/" . $this->sModuleName . "/shell/js/"));
                Storage::Set("virtual.".$this->sModuleName.".shell.css", Storage::Join("route.root", "app/" . $this->sModuleName . "/shell/css/"));
            
                //Loading submodules
                if(is_dir($this->sModuleDiretory  . "modules")){
                    $aModulesDirectories = glob(($this->sModuleDiretory . "app" . SP . "*"), GLOB_ONLYDIR);
=======
                Storage::Set("module.".$this->sModuleName.".shell", $this->sModuleDiretory . "shell" . SP);
                Storage::Set("module.".$this->sModuleName.".shell.css", $this->sModuleDiretory . "shell" . SP . "css" . SP);
                Storage::Set("module.".$this->sModuleName.".shell.tpl", $this->sModuleDiretory . "shell" . SP . "tpl" . SP);
                Storage::Set("module.".$this->sModuleName.".shell.js", $this->sModuleDiretory . "shell" . SP . "js" . SP);
                Storage::Set("module.".$this->sModuleName.".shell.img", $this->sModuleDiretory . "shell" . SP . "img" . SP);
            
                //Loading submodules
                if(is_dir($this->sModuleDiretory  . "modules")){
                    $aModulesDirectories = glob(($this->sModuleDiretory . "modules" . SP . "*"), GLOB_ONLYDIR);
>>>>>>> origin/master

                    foreach($aModulesDirectories as $sModuleDiretory){
                        if(file_exists($sModuleDiretory . SP . "status.txt"))
                            $bStatus = (intval(file_get_contents($sModuleDiretory . SP . "status.txt")) == 1);
                        else
                            $bStatus = false;

                        if($bStatus){
                            if(file_exists($sModuleDiretory . SP . "settings.php") && $bStatus)
                                require_once($sModuleDiretory . SP . "settings.php");       

                            if(file_exists($sModuleDiretory . SP . "include.php") && $bStatus)
                                require_once($sModuleDiretory . SP . "include.php");  

                            if(file_exists($sModuleDiretory . SP . "routes.php") && $bStatus)
                                require_once($sModuleDiretory . SP . "routes.php");

                            if(file_exists($sModuleDiretory . SP . "events.php") && $bStatus)
                                require_once($sModuleDiretory . SP . "events.php");
                        }
                    }
                }
            }            
        }
    }
