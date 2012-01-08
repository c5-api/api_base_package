<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Package Config Class
 * 
 * Retrieve and set information about package config entries
 * @author Michael Krasnow <mnkras@gmail.com>
 */
class ApiPackageConfig extends ApiController {
	
	/**
	 * List of Package Config Entries
	 * @route /packages/:handle/config
	 * @method GET
	 */
	public function index($handle) {
		$pkg = self::validatePkg($handle);
		$db = Loader::db();
		$r = $db->Execute('SELECT * FROM Config where pkgID = ?', array($pkg->getPackageID()));
		$objs = array();
		while($row = $r->FetchRow()) {
			$objs[] = $row['cfKey'];
		}
		return $objs;
	}


	/**
	 * Get Package Config Entry
	 * @route /packages/:handle/config/:key
	 * @method GET
	 * @errors ERROR_NOT_FOUND
	 */	
	public function entry($handle, $key) {
		$pkg = self::validatePkg($handle);
		$conf = $pkg->config($key, true);
		if(is_object($conf)) {
			unset($conf->error);
			return $conf;
		}
		$resp = ApiResponse::getInstance();
		$resp->setError(true);
		$resp->setCode(404);
		$resp->setMessage('ERROR_NOT_FOUND');
		$resp->send();	
		
	}

	/**
	 * Create Package Config Entry
	 * @route /packages/:handle/config/create
	 * @method POST
	 * @errors ERROR_ALREADY_EXISTS | ERROR_BAD_REQUEST
	 */		
	public function create($handle) {
		$pkg = self::validatePkg($handle);
		$key = $_POST['key'];
		$value = $_POST['value'];
		if($key && $value) { //@todo validate they are strings
			$val = $pkg->config($key, true);
			if(!is_object($val)) {
				$resp = ApiResponse::getInstance();
				$resp->setCode(201);
				return $pkg->saveConfig($key, $value);
			}
			$resp = ApiResponse::getInstance();
			$resp->setError(true);
			$resp->setCode(409);
			$resp->setMessage('ERROR_ALREADY_EXISTS');
			$resp->send();	
		}
		$resp = ApiResponse::getInstance();
		$resp->setError(true);
		$resp->setCode(400);
		$resp->setMessage('ERROR_BAD_REQUEST');
		$resp->send();	
	}

	/**
	 * Update Package Config Entry
	 * @route /packages/:handle/config/update
	 * @method POST
	 * @errors ERROR_NOT_FOUND | ERROR_BAD_REQUEST
	 */		
	public function update($handle) {
		$pkg = self::validatePkg($handle);
		$key = $_POST['key'];
		$value = $_POST['value'];
		if($key && $value) { //@todo validate they are strings
			$val = $pkg->config($key, true);
			if(is_object($val)) {
				return $pkg->saveConfig($key, $value);
			}
			$resp = ApiResponse::getInstance();
			$resp->setError(true);
			$resp->setCode(404);
			$resp->setMessage('ERROR_NOT_FOUND');
			$resp->send();	
		}
		$resp = ApiResponse::getInstance();
		$resp->setError(true);
		$resp->setCode(400);
		$resp->setMessage('ERROR_BAD_REQUEST');
		$resp->send();	
	}

	/**
	 * Update Package Config Entry
	 * @route /packages/:handle/config/destroy
	 * @method POST
	 * @errors ERROR_NOT_FOUND | ERROR_BAD_REQUEST
	 */		
	public function destroy($handle) {
		$pkg = self::validatePkg($handle);
		$key = $_POST['key'];
		if($key) { //@todo validate they are strings
			$val = $pkg->config($key, true);
			if(is_object($val)) {
				return $pkg->clearConfig($key);
			}
			$resp = ApiResponse::getInstance();
			$resp->setError(true);
			$resp->setCode(404);
			$resp->setMessage('ERROR_NOT_FOUND');
			$resp->send();	
		}
		$resp = ApiResponse::getInstance();
		$resp->setError(true);
		$resp->setCode(400);
		$resp->setMessage('ERROR_BAD_REQUEST');
		$resp->send();		}
	
	private static function validatePkg($handle) {
		$pkg = Package::getByHandle($handle);
		if(is_object($pkg)) {
			return $pkg;
		}
		$resp = ApiResponse::getInstance();
		$resp->setError(true);
		$resp->setCode(404);
		$resp->setMessage('ERROR_PACKAGE_NOT_FOUND');
		$resp->send();	
	}

}