<?php


$outputDir = getcwd();
$generator = new \Icamys\SitemapGenerator\SitemapGenerator('', $outputDir);

// will create also compressed (gzipped) sitemap
$generator->toggleGZipFileCreation();

// determine how many urls should be put into one file;
// this feature is useful in case if you have too large urls
// and your sitemap is out of allowed size (50Mb)
// according to the standard protocol 50000 is maximum value (see http://www.sitemaps.org/protocol.html)
$generator->setMaxURLsPerSitemap(50000);

// sitemap file name
$generator->setSitemapFileName("rootFiles/sitemap-index.xml");

// sitemap index file name
$generator->setSitemapIndexFileName("sitemap-index.xml");



$generator->addURL("https://".\efwTheme\engine::getCurrentDomain(false)."");
// generate internally a sitemap
$generator->createSitemap();

// write early generated sitemap to file(s)
$generator->writeSitemap();

// update robots.txt file in output directory or create a new one
$generator->updateRobots();

// submit your sitemaps to Google, Yahoo, Bing and Ask.com
$generator->submitSitemap();