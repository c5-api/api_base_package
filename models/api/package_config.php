<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiPackageConfig extends ApiController {
	
	/**
	 * List of Package Config Entries
	 * @route /packages/:handle/config
	 * @method GET
	 */
	public function index() {
		//@todo
	}


	/**
	 * Get Package Config Entry
	 * @route /packages/:handle/config/:key
	 * @method GET
	 */	
	public function entry($handle, $key) {
		$pkg = self::validatePkg($handle);
		$conf = $pkg->config($key, true);
		if(is_object($conf)) {
			return $conf;
		}
		//@todo error
		
	}

	/**
	 * Create Package Config Entry
	 * @route /packages/:handle/config/create
	 * @method POST
	 */		
	public function create($handle) {
		$pkg = self::validatePkg($handle);
		$key = $_POST['key'];
		$value = $_POST['value'];
		if($key && $value) { //@todo validate they are strings
			return $pkg->saveConfig($key, $value);
		}
		//@todo error
	}

	/**
	 * Update Package Config Entry
	 * @route /packages/:handle/config/update
	 * @method POST
	 */		
	public function update($handle) {
		$pkg = self::validatePkg($handle);
		$key = $_POST['key'];
		$value = $_POST['value'];
		if($key && $value) { //@todo validate they are strings
			$val = $pkg->config($key, true);
			if(!is_object($val)) {
				return $pkg->saveConfig($key, $value);
			}
			//@todo error
		}
		//@todo error
	}

	/**
	 * Update Package Config Entry
	 * @route /packages/:handle/config/destroy
	 * @method POST
	 */		
	public function destroy($handle) {
		$pkg = self::validatePkg($handle);
		$key = $_POST['key'];
		if($key) { //@todo validate they are strings
			$val = $pkg->config($key, true);
			if(is_object($val)) {
				return $pkg->clearConfig($key);
			}
			//@todo error
		}
		//@todo error
	}
	
	private static function validatePkg($handle) {
		$pkg = Package::getByHandle($handle);
		if(is_object($pkg)) {
			return $pkg;
		}
		$resp = ApiResponse::getInstance();
		$resp->setError(true);
		//@todo finish
	
	}

}