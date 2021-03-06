<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiBasePackagePackage extends Package {

	protected $pkgHandle = 'api_base_package';
	protected $appVersionRequired = '5.5.0';
	protected $pkgVersion = '1.1';

	public function getPackageName() {
		return t("Api:Base:Package");
	}

	public function getPackageDescription() {
		return t("API Package.");
	}

	public function install() {
		$installed = Package::getByHandle('api');
		if(!is_object($installed)) {
			throw new Exception(t('Please install the "API" package before installing %s', $this->getPackageName()));
		}
		
		$this->refreshRoutes();

		parent::install();
	}
	
	public function refreshRoutes() {
		$baseRoute = 'package';

/* Package Start */
		$pkg1 = array(); //List all packages
		$pkg1['pkgHandle'] = $this->pkgHandle;
		$pkg1['route'] = $baseRoute;
		$pkg1['routeName'] = t('List Packages');
		$pkg1['class'] = 'BasePackage';
		$pkg1['method'] = 'index';
		$pkg1['via'][] = 'get';
		
		$pkg2 = array(); //get info
		$pkg2['pkgHandle'] = $this->pkgHandle;
		$pkg2['route'] = $baseRoute.'/-/:handle';
		$pkg2['routeName'] = t('Package Information');
		$pkg2['class'] = 'BasePackage';
		$pkg2['method'] = 'info';
		$pkg2['via'][] = 'get';

		$pkg3 = array(); //updates
		$pkg3['pkgHandle'] = $this->pkgHandle;
		$pkg3['route'] = $baseRoute.'/updates';
		$pkg3['routeName'] = t('Package Updates Available');
		$pkg3['class'] = 'BasePackage';
		$pkg3['method'] = 'updates';
		$pkg3['via'][] = 'get';
		
		$pkg4 = array(); //update
		$pkg4['pkgHandle'] = $this->pkgHandle;
		$pkg4['route'] = $baseRoute.'/update';
		$pkg4['routeName'] = t('Package Update');
		$pkg4['class'] = 'BasePackage';
		$pkg4['method'] = 'update';
		$pkg4['via'][] = 'post';
		
		$pkg5 = array(); //uninstall
		$pkg5['pkgHandle'] = $this->pkgHandle;
		$pkg5['route'] = $baseRoute.'/destroy';
		$pkg5['routeName'] = t('Package Uninstall');
		$pkg5['class'] = 'BasePackage';
		$pkg5['method'] = 'destroy';
		$pkg5['via'][] = 'post';
		
		$pkg6 = array(); //uninstall
		$pkg6['pkgHandle'] = $this->pkgHandle;
		$pkg6['route'] = $baseRoute.'/install';
		$pkg6['routeName'] = t('Package Install');
		$pkg6['class'] = 'BasePackage';
		$pkg6['method'] = 'install';
		$pkg6['via'][] = 'post';
/* Package End */

/* Config Start */
		$config1 = array(); //get config keys for packages
		$config1['pkgHandle'] = $this->pkgHandle;
		$config1['route'] = $baseRoute.'/-/:handle/config/';
		$config1['routeName'] = t('Package Config Entries');
		$config1['class'] = 'PackageConfig';
		$config1['method'] = 'index';
		$config1['via'][] = 'get';

		$config2 = array(); //get config key info for packages
		$config2['pkgHandle'] = $this->pkgHandle;
		$config2['route'] = $baseRoute.'/-/:handle/config/:key';
		$config2['routeName'] = t('Package Config Entry Info');
		$config2['class'] = 'PackageConfig';
		$config2['method'] = 'entry';
		$config2['via'][] = 'get';
		
		$config3 = array(); //create config keys for packages
		$config3['pkgHandle'] = $this->pkgHandle;
		$config3['route'] = $baseRoute.'/-/:handle/config/-/create';
		$config3['routeName'] = t('Package Create Config Entries');
		$config3['class'] = 'PackageConfig';
		$config3['method'] = 'create';
		$config3['via'][] = 'post';

		$config4 = array(); //update config keys for packages
		$config4['pkgHandle'] = $this->pkgHandle;
		$config4['route'] = $baseRoute.'/-/:handle/config/-/update';
		$config4['routeName'] = t('Package Update Config Entries');
		$config4['class'] = 'PackageConfig';
		$config4['method'] = 'update';
		$config4['via'][] = 'post';

		$config5 = array(); //delete config keys for packages
		$config5['pkgHandle'] = $this->pkgHandle;
		$config5['route'] = $baseRoute.'/-/:handle/config/-/destroy';
		$config5['routeName'] = t('Package Delete Config Entries');
		$config5['class'] = 'PackageConfig';
		$config5['method'] = 'destroy';
		$config5['via'][] = 'post';
/* Config End */

		Loader::model('api_register', 'api');
		ApiRegister::add($pkg1);
		ApiRegister::add($pkg3);
		ApiRegister::add($pkg4);
		ApiRegister::add($pkg2);
		ApiRegister::add($pkg5);
		ApiRegister::add($pkg6);
		
		ApiRegister::add($config1);
		ApiRegister::add($config3);
		ApiRegister::add($config4);
		ApiRegister::add($config5);
		ApiRegister::add($config2);
	
	}
	
	public function uninstall() {
		Loader::model('api_register', 'api');
		ApiRegister::removeByPackage($this->pkgHandle);//remove all the apis
		parent::uninstall();
	}

}