<?php
    /**
     * Route Controller
     * 
     * @package     MagicPHP
     * @author      André Ferreira <andrehrf@gmail.com>
     * @link        https://github.com/magicphp/framework MagicPHP(tm)
     * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
     */

    class Routes{
        /**
         * Routes list
         * 
         * @access private
         * @var array 
         */
        private $aRoutes = array();
        
        /**
         * Sets the frontend overload
         * 
         * @access private
         * @var boolean 
         */
        private $bOverloadFrontend = false;
        
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
         * Function to set the overload frontend
         * 
         * @static
         * @access public
         * @param boolean $bStatus
         * @return void
         */
        public static function SetOverloadFrontend($bStatus = false){
            $oThis = self::CreateInstanceIfNotExists();
            
            if(is_bool($bStatus))
                $oThis->bOverloadFrontend = $bStatus;
        }
        
        /**
         * Function to parse route request
         * 
         * @static
         * @access public
         * @return void
         */
        public static function Parse(){
            $oThis = self::CreateInstanceIfNotExists();
            $sRoot = str_replace(array("index.php", " "), array("", "%20"), $_SERVER["SCRIPT_NAME"]);//Bugfix
            $sUri = ($sRoot != "/") ? str_replace($sRoot, "", $_SERVER["REQUEST_URI"]) : substr($_SERVER["REQUEST_URI"], 1, strlen($_SERVER["REQUEST_URI"])-1);
            $aParsedRoute = explode("/", $sUri);
            $mID = (array_key_exists(1, $aParsedRoute)) ? $aParsedRoute[1] : null;
            $sMethod = $oThis->Restful();  
            $oThis->RestParams();

            if(!$oThis->bOverloadFrontend){
                $bResult = Bootstrap::AutoLoad(((!empty($aParsedRoute[0]) && $aParsedRoute[0] != "/" ) ? strtolower(str_replace("@", "", $aParsedRoute[0])) : "main"));

                if(!$bResult)
                    Bootstrap::AutoLoad("main");
            }
            $sUri = preg_replace("/\?.*$/", "", $sUri);//Removendo path
            $sUri = preg_replace("/\#.*$/", "", $sUri);//Removendo path
            $bCall = false;


            $aIdent = array("int" => "\d+",
                            "str" => "\w+",
                            "flt" => "[\d.]+");

            foreach($oThis->aRoutes as $sRoute => $fFunc){  
                if (preg_match_all('/{(?P<field>\w+)(:((?P<type>\w{3,3})|regex\((?P<regex>.*)\)))?(:(?P<notnull>notnull))?}/i',$sRoute, $aMatches)){
                    if (preg_match('/(?P<pre>.*\w)\/\{/i',$sRoute, $aMatch)){
                        $sOk = str_replace('/', '\/', $aMatch['pre']);
                        $sRoute = str_replace($aMatch['pre'], $sOk, $sRoute);
                    } 
                    foreach ($aMatches['field'] as $iKey => $sValue) {
                        if ($aMatches['regex'][$iKey]){
                            if ($aMatches['notnull'][$iKey] == 'notnull'){
                                $sRoute = preg_replace('/\/{'.$aMatches['field'][$iKey].':regex('.$aMatches['regex'][$iKey].'):notnull}/i',"\/(".$aMatches['regex'][$iKey].")", $sRoute);
                            }else{
                                $sRoute = preg_replace('/\/{'.$aMatches['field'][$iKey].':regex('.$aMatches['regex'][$iKey].')}/i',"\/?(".$aMatches['regex'][$iKey].")?", $sRoute);                                                                                
                            }
                        }else{
                            if ($aMatches['type'][$iKey]){ 
                                if ($aMatches['notnull'][$iKey] == 'notnull'){
                                    $sRoute = preg_replace('/\/{'.$aMatches['field'][$iKey].':'.$aMatches['type'][$iKey].':notnull}/i',"\/(".$aIdent[$aMatches['type'][$iKey]].")", $sRoute);                                        
                                }else{
                                    $sRoute = preg_replace('/\/?{'.$aMatches['field'][$iKey].':'.$aMatches['type'][$iKey].'}/i',"\/?(".$aIdent[$aMatches['type'][$iKey]].")?", $sRoute);                                        
                                }
                            }else{
                                if ($aMatches['notnull'][$iKey] == 'notnull'){
                                    $sRoute = preg_replace('/\/{'.$aMatches['field'][$iKey].':notnull}/i',"\/([^\/]*)", $sRoute);                                        
                                }else{
                                    $sRoute = preg_replace("/\/?{".$aMatches['field'][$iKey]."}/", "\/?([^\/]*)?", $sRoute);                                                                                            
                                }
                            }
                        } 
                    }
                }else{
                    $sRoute = str_replace('/', '\/', $sRoute);
                }                    

                /*$sBaseFolder = substr($sRoot, 0,strlen($sRoot)-1);
                if ($sBaseFolder){
                    if (substr($sBaseFolder, 0,1) == '/'){
                        $sBaseFolder = substr($sBaseFolder, 1, strlen($sBaseFolder)-1);
                    }
                    $sBaseFolder = str_replace('/', '\/', $sBaseFolder);
                    preg_match('/^[^_]+/', $sRoute, $sMatch);
                    if ($sMatch){
                        $sRoute = str_replace($sMatch[0]."_", "", $sRoute);
                        $sRoute = $sMatch[0]."_".$sBaseFolder.$sRoute;
                    }
                }*/
                
                if(preg_match_all("/^".$sRoute."\/?$/i", $sMethod."_".$sUri, $aMatches)){
                    $aParams = array();
                    foreach($aMatches as $iKey => $aResult){
                        if($iKey > 0){
                            $aParams[] = $aResult[0];
                        }
                    }

                    $bCall = true; 
                    Storage::Set("route.request", $sRoute);

                    if(is_array($fFunc)){
                        if(is_array($fFunc[1]))
                            $aParams = array_merge($fFunc[1], $aParams);
                        else
                            $aParams = array_merge(array($fFunc[1]), $aParams);

                        call_user_func_array($fFunc[0], $aParams);
                    }
                    else{
                        call_user_func_array($fFunc, $aParams);
                    }

                    break;
                }              
            }        
            
            if(array_key_exists("__dynamicroute", $oThis->aRoutes) && !$bCall)
                call_user_func($oThis->aRoutes["__dynamicroute"]);  
            else
                Output::SendHTTPCode(404);
        }
        
        /**
         * Function to configure actions to dynamic routes
         * 
         * @static
         * @access public
         * @param function $fCallback Callback function for dynamic routes
         * @return void
         */
        public static function SetDynamicRoute($fCallback){
            $oThis = self::CreateInstanceIfNotExists();
            $oThis->aRoutes["__dynamicroute"] = $fCallback;
        }
        
        /**
         * Function to route configuration
         * 
         * @static
         * @access public
         * @param string $sRoute Route
         * @param string $sRequestType Request type (GET, POST, PUT, DELETE)
         * @param function $fCallback Callback function for route
         * @return void
         */
        public static function Set($sRoute, $sRequestType = "GET", $fCallback){
            $oThis = self::CreateInstanceIfNotExists();
            $oThis->aRoutes[$sRequestType."_".strtolower($sRoute)] = $fCallback;
        }
        
        /**
         * Function to check the type of request to support RESTful
         * 
         * @static
         * @access public
         * @return string
         */
        public static function Restful(){
            if($_SERVER["REQUEST_METHOD"] == "POST" && array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER))
                return strtoupper($_SERVER["HTTP_X_HTTP_METHOD"]);
            else
                return strtoupper($_SERVER["REQUEST_METHOD"]);
        }
        
        /**
         * Function to return parameters passed by PUT or DELETE methods
         * 
         * @static
         * @access public
         * @return void
         */
        public static function RestParams(){
            $sBuffer = file_get_contents("php://input");
            $aParams = explode("&", $sBuffer);
            $aReturn = array();
            
            foreach($aParams as $sParam){
                @list($mKey, $mValue) = @explode("=", $sParam);
                $mValue = urldecode($mValue);
                
                if(!empty($mKey) && !empty($mValue))
                    Storage::Set(strtolower(Routes::Restful()).".".$mKey, $mValue);
            }
        }
        
        /**
         * Function to check if the request is Ajax
         * 
         * @static
         * @access public
         * @return boolean
         */
        public static function IsAjaxRequest(){
           if(array_key_exists("HTTP_X_REQUESTED_WITH", $_SERVER))
               return (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
           else
               return false;
        }
        
        /**
         * Function to return the list of routes
         * 
         * @static
         * @return array
         */
        public static function GetRoutes(){
           $oThis = self::CreateInstanceIfNotExists();
           return  $oThis->aRoutes;
        }
        
        /**
         * Function to create default routes CRUD
         * 
         * @static
         * @access public
         * @param string $sRouteName
         * @param string $sController
         * @return void
         */
        public static function CRUD($sRouteName, $sController){
            if(method_exists($sController, "Index"))
                Routes::Set($sRouteName, "GET", $sController . "::Index");
            
            if(method_exists($sController, "Create"))
                Routes::Set($sRouteName . "/create", "GET", $sController . "::Create");
            
            if(method_exists($sController, "Insert"))
                Routes::Set($sRouteName, "POST", $sController . "::Insert");
            
            if(method_exists($sController, "Show"))
                Routes::Set($sRouteName . "/{id}", "GET", $sController . "::Show");
            
            if(method_exists($sController, "Edit"))
                Routes::Set($sRouteName . "/{id}/edit", "GET", $sController . "::Edit");
            
            if(method_exists($sController, "Update"))
                Routes::Set($sRouteName . "/{id}", "PUT", $sController . "::Update");
            
            if(method_exists($sController, "Destroy"))
                Routes::Set($sRouteName . "/{id}", "DELETE", $sController . "::Destroy");
        }
    }