<?php header("Content-Type: application/xml; charset=UTF-8"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
  <url>
    <loc><?php dp_site_link(); ?></loc>
    <priority>1.00</priority>
    <lastmod><?php date("Y-m-d"); ?></lastmod>
    <changefreq>always</changefreq>
  </url>
  <?php while (dp_next_post()) { ?>
    <url>
      <loc><?php dp_post_url(); ?></loc>
      <priority>0.8</priority>
      <lastmod><?php dp_post_date(); ?> <?php dp_post_time(); ?></lastmod>
      <changefreq>daily</changefreq>
    </url>
  <?php } ?>

</urlset>