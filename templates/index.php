<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<?php $tpl->drawHeadTitle(); ?>
<?php $tpl->drawMetaTags(); ?>
<?php $tpl->drawHeadCode(); ?>
<link rel="stylesheet" href="/styles.css" type="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
<link rel="home" href="/" title="Front page" />
<link rel="contents" href="/sitemap/" title="Site Map" />
<link rel="search" href="/search/" title="Search this site" />
<script type="text/javascript" src="/favorites.js"></script>
</head>
<body>
<div id="colleft">
<?php $tpl->drawInsertion(array('PlaceId' => 1)); ?>
</div>
<div id="colmiddle">
<?php $tpl->drawBodyTitle(); ?>
<?php $tpl->drawBodyContent(); ?>
</div>
</body>
</html>
<?php $tpl->drawDebug(); ?>
