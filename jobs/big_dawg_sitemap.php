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

			SELECT p.cID AS cID,
			       pp.cPath AS cPath,
			       c.cDateModified AS cDateModified,
			       ak.akHandle AS akHandle,
			       cav.avID AS avID,
			       atb.value AS booleanValue,
			       atd.value AS defaultValue,
			       atn.value AS numberValue
			FROM PagePermissionAssignments ppa
			JOIN PermissionKeys pk ON ppa.pkID = pk.pkID
			JOIN PermissionAccess pa ON pa.paID = ppa.paID
			JOIN PermissionAccessList pal ON pal.paID = ppa.paID
			JOIN PermissionAccessEntityGroups peg ON peg.peID = pal.peID
			JOIN Pages p ON p.cInheritPermissionsFromCID = ppa.cID
			JOIN CollectionVersions cv ON cv.cID = p.cID
			JOIN Collections c ON c.cID = cv.cID
			JOIN PagePaths pp ON pp.cID = cv.cID
			JOIN CollectionAttributeValues cav ON cav.cvID = cv.cvID
			JOIN AttributeValues av ON av.avID = cav.avID
			JOIN AttributeKeys ak ON ak.akID = cav.akID
			JOIN AttributeTypes at ON at.atID = av.atID
			LEFT JOIN atDefault atd ON atd.avID = cav.avID
			LEFT JOIN atBoolean atb ON atb.avID = cav.avID
			LEFT JOIN atNumber atn ON atn.avID = cav.avID
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
			AND ak.akHandle IN (
				"exclude_sitemapxml",
				"sitemap_priority",
				"sitemap_changefreq"
			)

			UNION

			SELECT p.cID AS cID,
			       pp.cPath AS cPath,
			       c.cDateModified AS cDateModified,
			       NULL AS akHandle,
			       NULL as avID,
			       NULL as booleanValue,
			       NULL AS defaultValue,
			       NULL AS numberValue
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

			UNION

			SELECT p.cID AS cID,
			       "" AS cPath,
			       c.cDateModified AS cDateModified,
			       ak.akHandle AS akHandle,
			       cav.avID AS avID,
			       atb.value AS booleanValue,
			       atd.value AS defaultValue,
			       atn.value AS numberValue
			FROM PagePermissionAssignments ppa
			JOIN PermissionKeys pk ON ppa.pkID = pk.pkID
			JOIN PermissionAccess pa ON pa.paID = ppa.paID
			JOIN PermissionAccessList pal ON pal.paID = ppa.paID
			JOIN PermissionAccessEntityGroups peg ON peg.peID = pal.peID
			JOIN Pages p ON p.cInheritPermissionsFromCID = ppa.cID
			JOIN CollectionVersions cv ON cv.cID = p.cID
			JOIN Collections c ON c.cID = cv.cID
			JOIN CollectionAttributeValues cav ON cav.cvID = cv.cvID
			JOIN AttributeValues av ON av.avID = cav.avID
			JOIN AttributeKeys ak ON ak.akID = cav.akID
			JOIN AttributeTypes at ON at.atID = av.atID
			LEFT JOIN atDefault atd ON atd.avID = cav.avID
			LEFT JOIN atBoolean atb ON atb.avID = cav.avID
			LEFT JOIN atNumber atn ON atn.avID = cav.avID
			WHERE pk.pkHandle = "view_page"
			AND pa.paIsInUse = 1
			AND pal.accessType = 10
			AND peg.gID = ?
			AND p.cIsActive = 1
			AND p.cPointerID = 0
			AND p.cIsSystemPage = 0
			AND cv.cvIsApproved = 1
			AND ak.akHandle IN (
				"exclude_sitemapxml",
				"sitemap_priority",
				"sitemap_changefreq"
			)
			AND p.cID = ?

			UNION

			SELECT p.cID AS cID,
			       "" AS cPath,
			       c.cDateModified AS cDateModified,
			       NULL AS akHandle,
			       NULL as avID,
			       NULL as booleanValue,
			       NULL AS defaultValue,
			       NULL AS numberValue
			FROM PagePermissionAssignments ppa
			JOIN PermissionKeys pk ON ppa.pkID = pk.pkID
			JOIN PermissionAccess pa ON pa.paID = ppa.paID
			JOIN PermissionAccessList pal ON pal.paID = ppa.paID
			JOIN PermissionAccessEntityGroups peg ON peg.peID = pal.peID
			JOIN Pages p ON p.cInheritPermissionsFromCID = ppa.cID
			JOIN CollectionVersions cv ON cv.cID = p.cID
			JOIN Collections c ON c.cID = cv.cID
			WHERE pk.pkHandle = "view_page"
			AND pa.paIsInUse = 1
			AND pal.accessType = 10
			AND peg.gID = ?
			AND p.cIsActive = 1
			AND p.cPointerID = 0
			AND p.cIsSystemPage = 0
			AND cv.cvIsApproved = 1
			AND p.cID = ?

			ORDER BY cID, akHandle DESC

		', array(GUEST_GROUP_ID, GUEST_GROUP_ID, GUEST_GROUP_ID, HOME_CID, GUEST_GROUP_ID, HOME_CID));

		$this->processRows($r, $sitemap);

		$sitemap->createSitemapIndex(rtrim(BASE_URL, '/') . '/');
	}

	protected function processRows(&$r, &$sitemap, $cID = false, $exclude = false, $frequency = false, $priority = false) {

		if (!$row = $r->FetchRow()) {
			return;
		}

		if ($row['cID'] != $cID) {
			$exclude = false;
			$priority = false;
			$frequency = false;
			$cID = $row['cID'];
		}

		if ($row['akHandle'] == 'exclude_sitemapxml') {
			$exclude = (bool) $row['booleanValue'];
			$this->processRows($r, $sitemap, $cID, $exclude, $frequency, $priority);
			return;
		}
		if ($row['akHandle'] == 'sitemap_priority') {
			$priority = $row['defaultValue'] !== null ? $row['defaultValue'] : (float) number_format((float) $row['numberValue'], 2);
			$this->processRows($r, $sitemap, $cID, $exclude, $frequency, $priority);
			return;
		}
		if ($row['akHandle'] == 'sitemap_changefreq') {
			$frequency = $row['defaultValue'];
			$this->processRows($r, $sitemap, $cID, $exclude, $frequency, $priority);
			return;
		}

		if (!$priority) {
			$priority = SITEMAPXML_DEFAULT_PRIORITY;
		}
		if (!$frequency) {
			$frequency = SITEMAPXML_DEFAULT_CHANGEFREQ;
		}

		if (!$exclude) {
			$sitemap->addItem($prettyURL . $row['cPath'], $priority, $frequency, $row['cDateModified']);
		}

		$this->processRows($r, $sitemap);
	}
}
