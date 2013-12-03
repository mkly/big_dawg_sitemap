<?php
defined('C5_EXECUTE') or die('Access Denied.');

class BigDawgSitemapPackage extends Package {

	protected $pkgHandle = "big_dawg_sitemap";
	protected $appVersionRequired = "5.6";
	protected $pkgVersion = "0.1";

	public function getPackageName() {
		return t('Big Dawg Sitemap');
	}

	public function getPackageDescription() {
		return t('A sitemap.xml generator for Big Dawgs');
	}

	public function install() {
		Job::installByPackage('big_dawg_sitemap', parent::install());
	}
}
