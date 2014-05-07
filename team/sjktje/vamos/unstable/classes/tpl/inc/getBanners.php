<?
	//prepare x number of banners to show
	//only if no more banners are enqueue'ed reset them all
	$q = "select count(*) from cms_gallery_photos where pageid = -1 AND internal_stat < rating";
	$res = sql_query($q);
	if (sql_result($res,0)==0) {
		$q = "update cms_gallery_photos set internal_stat = 0 where pageid = -1";
		sql_query($q);
	}

	//first get all banners into memory that are listed to be shown
	//try to get banners not shown before
	$banner = array();
	$q = "select id from cms_gallery_photos where pageid = -1 AND internal_stat < rating";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$banner[]= $row["id"];
	}
	if (count($banner) < $count) {
		//we need more banners, all are shown already
		$q = sprintf("select id from cms_gallery_photos
			where pageid = -1 AND id NOT IN (%s) and internal_stat < rating", implode(",", $banner));
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$banner[]= $row["id"];
		}

		if (count($banner) < $count) {
			//we still need more banners to fill up the request
			$q = sprintf("select id from cms_gallery_photos where pageid = -1
				AND id NOT IN (%s)", implode(",", $banner));
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$banner[]= $row["id"];
			}
		}
	}
	/* create random values */
	$bannerlist = array();
	foreach ($banner as $k=>$v) {
		$bannerlist[$v] = rand(1,999999);
	}
	//now sort it
	asort($bannerlist);

	//limit to x banners
	$bannerlist = array_slice($bannerlist, 0, $count, TRUE);

	$banner = array();
	foreach ($bannerlist as $k=>$v) {
		$banner[$k] = $k;
	}
	$bannerlist = implode(",", $banner);

	$q = sprintf("select * from cms_gallery_photos where id IN (%s)", $bannerlist);
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$banner[$row["id"]] = array(
			"id" => $row["id"],
			"location" => sprintf("/showcms.php?dl=1&galleryid=%d&size=small", $row["id"]),
			"website"  => $row["url"],
			"rating"   => $row["rating"]
		);
	}

	$q = sprintf("update cms_gallery_photos set internal_stat = (internal_stat + 1)
		where pageid = -1 AND id IN (%s) ", $bannerlist);
	sql_query($q);

?>