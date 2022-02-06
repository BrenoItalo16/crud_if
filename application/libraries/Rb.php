<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function prepare_fields($fields,$post){
	$f = [];
	foreach($fields as $key){
		if ( isset($post[$key]) ){
			$f[$key] = $post[$key];
		} else
		if (strstr($key,"_id")){#caso seja uma chave estrangeira
			$f[$key] = null;
		}

	}
	return $f;
}


function val($arr,$key){
	
	if (isset($arr[$key])){
		return $arr[$key];
	} else {
		return "";
	}
	
}


function saveLogs(){
	$logs = R::getDatabaseAdapter()->getDatabase()->getLogger()->getLogs();
	$_SESSION["logs"] = $logs;
}


 
class Rb {
     
    function __construct() {
        // Include database configuration
        include(APPPATH.'/config/database.php');
         
        // Get Redbean
        include(APPPATH.'/third_party/rb/rb.php');
         
        // Database data
        $host 	= $db[$active_group]['hostname'];
        $user 	= $db[$active_group]['username'];
        $pass 	= $db[$active_group]['password'];
		$driver = $db[$active_group]['dbdriver'];
		$dsn 	= $db[$active_group]['dsn'];
        $db 	= $db[$active_group]['database'];
		
		
		if ($driver == "pdo"){
			// SQLITE
			R::setup( $dsn );
		} else
		if ($driver == "mysql"){
			// MYSQL
			R::setup("mysql:host=$host;dbname=$db", $user, $pass);
		} else {
			// POSTGRES
			R::setup("pgsql:host=$host;dbname=$db", $user, $pass);
		}


		if (ENVIRONMENT == "development"){
			R::fancyDebug();
			R::getDatabaseAdapter()->getDatabase()->getLogger()->setMode(1);
		}
        
    } //end __contruct()
} //end Rb
