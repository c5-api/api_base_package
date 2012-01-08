<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiPackage extends ApiController {
	
	public function index() {
		return PackageList::get()->getPackages();	
	}
	
	public function config($handle, $key) {
		$pkg = Package::getByHandle($handle);
		
	}
	
	public function info($handle) {
		$pkg = Package::getByHandle($handle);
	
	}

}