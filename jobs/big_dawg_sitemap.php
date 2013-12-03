<?php
defined('C5_EXECUTE') or die('Access Denied.');

class BigDawgSitemap extends Job {

	public function getJobName() {
		return t('Generate sitemap.xml like a Big Dawg');
	}

	public function getJobDescription() {
		return t('Generate sitemap.zml like a Big Dawg. WOOF!');
	}

	public function run() {
		Loader::library('3rdparty/Sitemap', 'big_dawg_sitemap');
		$sitemap = new BigDawg_Sitemap(SITE);
		$sitemap->setPath(rtrim(DIR_BASE, '/') . '/');
		$sitemap->setDomain(rtrim(BASE_URL, '/'));

		$prettyURL = URL_REWRITING ? '' : '/index.php';

		$r = Loader::db()->Execute('
			SELECT pp.cPath, c.cDateModified
			FROM PagePermissionAssignments ppa
			JOIN PermissionKeys pk ON ppa.pkID = pk.pkID
			JOIN PermissionAccess pa ON pa.paID = ppa.paID
			JOIN PermissionAccessList pal ON pal.paID = ppa.paID
			JOIN PermissionAccessEntityGroups peg ON peg.peID = pal.peID
			JOIN Pages p ON p.cInheritPermissionsFromCID = ppa.cID
			JOIN CollectionVersions cv ON cv.cID = p.cID
			JOIN Collections c ON c.cID = cv.cID
			JOIN PagePaths pp ON pp.cID = cv.cID
			WHERE pk.pkHandle = "view_page"
			AND pa.paIsInUse = 1
			AND pal.accessType = 10
			AND peg.gID = ?
			AND p.cIsActive = 1
			AND p.cPointerID = 0
			AND p.cIsSystemPage = 0
			AND cv.cvIsApproved = 1
			AND pp.ppIsCanonical = 1
			AND pp.cPath NOT LIKE "/dashboard%"
		', array(GUEST_GROUP_ID));

		while ($row = $r->FetchRow()) {
			$sitemap->addItem($prettyURL . $row['cPath'], SITEMAPXML_DEFAULT_PRIORITY, SITEMAPXML_DEFAULT_CHANGEFREQ, $row['cDateModified']);
		}

		$sitemap->createSitemapIndex(rtrim(BASE_URL, '/') . '/');
	}
}
