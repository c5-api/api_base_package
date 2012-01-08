<?php defined('C5_EXECUTE') or die("Access Denied.");

class ApiBasePackagePackage extends Package {

	protected $pkgHandle = 'api_base_package';
	protected $appVersionRequired = '5.5.0';
	protected $pkgVersion = '1.0';

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

/* Package Start */
		$pkg1 = array(); //List all packages
		$pkg1['pkgHandle'] = $this->pkgHandle;
		$pkg1['route'] = 'package';
		$pkg1['routeName'] = t('List Packages');
		$pkg1['class'] = 'package';
		$pkg1['method'] = 'index';
		$pkg1['via'][] = 'get';
		
		$pkg2 = array(); //get info
		$pkg2['pkgHandle'] = $this->pkgHandle;
		$pkg2['route'] = 'package/:handle';
		$pkg2['routeName'] = t('Package Information');
		$pkg2['class'] = 'package';
		$pkg2['method'] = 'info';
		$pkg2['via'][] = 'get';
		
		$pkg3 = array(); //update
		$pkg3['pkgHandle'] = $this->pkgHandle;
		$pkg3['route'] = 'package/update';
		$pkg3['routeName'] = t('Package Update');
		$pkg3['class'] = 'package';
		$pkg3['method'] = 'update';
		$pkg3['via'][] = 'post';
		
		$pkg4 = array(); //uninstall
		$pkg4['pkgHandle'] = $this->pkgHandle;
		$pkg4['route'] = 'package/destroy';
		$pkg4['routeName'] = t('Package Uninstall');
		$pkg4['class'] = 'package';
		$pkg4['method'] = 'destroy';
		$pkg4['via'][] = 'post';
/* Package End */

/* Config Start */
		$config1 = array(); //get config keys for packages
		$config1['pkgHandle'] = $this->pkgHandle;
		$config1['route'] = 'package/:handle/config/';
		$config1['routeName'] = t('Package Config Entries');
		$config1['class'] = 'package_config';
		$config1['method'] = 'index';
		$config1['via'][] = 'get';

		$config2 = array(); //get config key info for packages
		$config2['pkgHandle'] = $this->pkgHandle;
		$config2['route'] = 'package/:handle/config/:key';
		$config2['routeName'] = t('Package Config Entry Info');
		$config2['class'] = 'package_config';
		$config2['method'] = 'entry';
		$config2['via'][] = 'get';
		
		$config3 = array(); //create config keys for packages
		$config3['pkgHandle'] = $this->pkgHandle;
		$config3['route'] = 'package/:handle/config/create';
		$config3['routeName'] = t('Package Create Config Entries');
		$config3['class'] = 'package_config';
		$config3['method'] = 'create';
		$config3['via'][] = 'post';

		$config4 = array(); //update config keys for packages
		$config4['pkgHandle'] = $this->pkgHandle;
		$config4['route'] = 'package/:handle/config/update';
		$config4['routeName'] = t('Package Update Config Entries');
		$config4['class'] = 'package_config';
		$config4['method'] = 'update';
		$config4['via'][] = 'post';

		$config5 = array(); //delete config keys for packages
		$config5['pkgHandle'] = $this->pkgHandle;
		$config5['route'] = 'package/:handle/config/destroy';
		$config5['routeName'] = t('Package Delete Config Entries');
		$config5['class'] = 'package_config';
		$config5['method'] = 'destroy';
		$config5['via'][] = 'post';
/* Config End */

		Loader::model('api_register', 'api');
		ApiRegister::add($pkg1);
		ApiRegister::add($pkg2);
		ApiRegister::add($pkg3);
		ApiRegister::add($pkg4);
		
		ApiRegister::add($config1);
		ApiRegister::add($config2);
		ApiRegister::add($config3);
		ApiRegister::add($config4);
		ApiRegister::add($config5);

		parent::install();
	}
	
	public function uninstall() {
		Loader::model('api_register', 'api');
		ApiRegister::removeByPackage($this->pkgHandle);//remove all the apis
		parent::uninstall();
	}

}