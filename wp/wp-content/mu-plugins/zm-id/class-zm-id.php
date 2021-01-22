<?php
require_once('vendor/autoload.php');
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
//
require_once ('soapclient/SforceEnterpriseClient.php');//it would be nice to put this in composer ... but i haven't found the right package, and this library does work, evn if it is >2 years old. it's compatable w php5.6 at least.
ini_set('soap.wsdl_cache_enabled', '0');

/*


defined in zm-id

define( 'ZMID_URL',  trailingslashit( plugins_url('', __FILE__) ));
define( 'ZMID_PATH', trailingslashit( plugin_dir_path( __FILE__) ) );

define("ZMID_SF_USERNAME", "crm@zoomermedia.ca");
define("ZMID_SF_PASSWORD", "gus3ufr_k5s4*Hek");
define("ZMID_SF_SECURITY_TOKEN", "dfZZaKj7eaAcT6ZM2LdNklo5");
*/
class ZMID {
	
	
	/**
	* JWT Parser Settings
	*
	* @var array
	*/
	
	
	public $section_settings = array();
	public $queryStringJWT = '';
	//public $parser;
	
	
	function __construct(){
		//$parser = new Parser();
	}
	//?access_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJlbWFpbCI6ImEuY29ybWFja0B6b29tZXJtZWRpYS5jYSIsImV4cCI6MTQ2MDEzMzcwMi44LCJpc3MiOiJodHRwOi8vaWQuem9vbWVyLmNhIn0.c4xbXZV9PtJgW1RcPRB7d24IrOY1Z1uP84xgPGyf3j5i1d8O9905_cwIi6H2jqzN_XgwQkgQvLciarB3gWG30A#
	public function getJWTFromGET () {
		//returns false if no JWT is there.
		if (isset($_GET["access_token"]) && $_GET["access_token"] != "" ) {
			return $_GET["access_token"];
		} 
		return false;
	}
	public function getToken () {
		$someTokenString = $this->getJWTFromGET();
		if ($someTokenString) {
			//return $parser
			$token = (new Parser())->parse((string) $someTokenString); // Parses from a string
			//how to check if is legit or not?
			//we should also validate ... what do we need to do a proper validation here?
			/*
			
			s55yTTedf
			
			ZMID_ID_AUTHORITY_ISSUER_URL is defined, proibably, as id.carp.ca
			*/
			return $token;
			
			/*$validationData = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
			$validationData->setIssuer(ZMID_ID_AUTHORITY_ISSUER_URL);
			$validationData->setAudience(ZMID_ID_AUDIENCE);//the url of this site.
			$validationData->setId(ZMID_ID_APIKEY);
			$isValid = $token->validate($validationData);
			if ($isValid) {
				return $token;
			} else {
				return $isValid;
			}*/
			
		}
		return false;
	}
	public function getTokenClaims () {
		$someTokenString = $this->getJWTFromGET();
		if ($someTokenString) {
			//return $parser
			$token = (new Parser())->parse((string) $someToken); // Parses from a string
			$tokenClaims = $token->getClaims();
			return $tokenClaims;
		}
		return false;
	}
	public function getTokenClaimsEmail () {
		$someTokenString = $this->getJWTFromGET();
		if ($someTokenString) {
			//return $parser
			$token = (new Parser())->parse((string) $someToken); // Parses from a string
			$tokenClaimsEmail = $token->getClaim('email');
			return $tokenClaimsEmail;
		}
		return false;
	}
	//Salesforce functions
	//Site_Preferences__c has been added to Salesforce ... this is where we will store json for user preferences
	//
	
	public function sfEmailCheck($email) {
		//echo("sfEmailCheck({$email})");
		$rtrn = (object) array("exists"=>"false","id"=>"","error"=>"true","message"=>"not run");
		//echo("<p>\$rtrn=={$rtrn}</p>");
		try {
			$mySforceConnection = new SforceEnterpriseClient();
			//echo("\$mySforceConnection==...");
			//$mySoapClient = $mySforceConnection->createConnection('soapclient/enterprise.wsdl.xml');//<--can't load?! try full file system path
			$mySoapClient = $mySforceConnection->createConnection(ZMID_PATH . '/zm-id/soapclient/enterprise.wsdl.xml');//<--can't load?! try full file system path
			$mylogin = $mySforceConnection->login(ZMID_SF_USERNAME, ZMID_SF_PASSWORD . ZMID_SF_SECURITY_TOKEN);
			$query = "SELECT Id, Site_Preferences__c, FirstName, LastName, PersonEmail, CARP_Active_Member__pc from Account WHERE PersonEmail = '".$email."'";
			//if they are a member, then we will have an entry in salesforce. if they are not a member, they could still have an entry in salesforce (expired, or not member)
			//echo("\$query=={$query}");
			$response = $mySforceConnection->query($query);
			if(count($response->records)>0) {
				//var_dump($response);
				$rtrn = (object) array("exists"=>"true","id"=>$response->records[0]->Id,"error"=>"false","message"=>"", "FirstName"=>$response->records[0]->FirstName, "LastName"=>$response->records[0]->LastName, "Site_Preferences__c"=>$response->records[0]->Site_Preferences__c);
			} else {
				$rtrn = (object) array("exists"=>"false","id"=>"","error"=>"false","message"=>"");
			}
		} catch (Exception $e) {
			//echo $mySforceConnection->getLastRequest();
			//$rtrn = (object)array("exists"=>"false","id"=>"","error"=>"true","message"=>$e->faultstring);
			$rtrn = (object) array("exists"=>"false","id"=>"","error"=>"true","message"=>$e->getMessage());
		}
		//var_dump($rtrn);
		//echo("\$rtrn==$rtrn");
		//die("ok");
		return $rtrn;
	}
	
	
	
	
	//id is the user's sf id
	//user is??
	public function sfUpdateSitePrefs($sfid, $sitePrefs) {
		$rtrn = (object)array("exists"=>"true","id"=>"","error"=>"true","message"=>"not run");

		try {
			$mySforceConnection = new SforceEnterpriseClient();
			$mySoapClient = $mySforceConnection->createConnection(ZMID_PATH . '/zm-id/soapclient/enterprise.wsdl.xml');//<--can't load?! try full file system path
			$mylogin = $mySforceConnection->login(ZMID_SF_USERNAME, ZMID_SF_PASSWORD . ZMID_SF_SECURITY_TOKEN);

			$records = array();

			$records[0] = new stdclass();
			$records[0]->Id = $sfid;
			$records[0]->Site_Preferences__c = $sitePrefs;
			
			$response = $mySforceConnection->update($records,'Account');

			if($response[0]->success==1) {
				$rtrn->error = false;
				$rtrn->message = '';//$user;
				$rtrn->id = $response[0]->id;
				$rtrn->Site_Preferences__c = $response[0]->Site_Preferences__c;
			}
			else {
				$rtrn = $response[0];
			}
		}
		catch (Exception $e) {
			$rtrn->error = true;
			$rtrn->message = $e->faultstring;
		}

		return $rtrn;
	}
	
	
	
	
	
	public function sayHello() {
		return "Hello World!";
	}
	
	
	
	
	
	
	
	
	
	
}