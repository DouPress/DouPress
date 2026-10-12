<?php header("Content-Type: application/xml; charset=UTF-8"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
  <url>
    <loc><?php app_site_link(); ?></loc>
    <priority>1.00</priority>
    <lastmod><?php echo date("Y-m-d H:i:s"); ?></lastmod>
    <changefreq>always</changefreq>
  </url>
  <?php while (app_next_post()) { ?>
    <url>
      <loc><?php app_post_url(); ?></loc>
      <priority>0.8</priority>
      <lastmod><?php echo !empty($app_post['mod_time']) ? $app_post['mod_time'] : app_post_date(false) . ' ' . app_post_time(false); ?></lastmod>
      <changefreq>daily</changefreq>
    </url>
  <?php } ?>
  <?php
  require PATH_ROOT . '/data/pages/index/publish.php';
  foreach ($app_pages as $app_page) {
    $page_lastmod = (isset($app_page['mod_time']) && $app_page['mod_time'] != '')
      ? $app_page['mod_time']
      : $app_page['date'] . ' ' . $app_page['time'];
  ?>
    <url>
      <loc><?php app_get_url('', $app_page['path']); ?></loc>
      <priority>0.6</priority>
      <lastmod><?php echo $page_lastmod; ?></lastmod>
      <changefreq>weekly</changefreq>
    </url>
  <?php } ?>

</urlset>