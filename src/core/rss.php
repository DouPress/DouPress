<?php header("Content-Type: application/rss+xml; charset=UTF-8"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:wfw="http://wellformedweb.org/CommentAPI/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
  xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>
<channel>
  <title><?php app_site_name(); ?></title>
  <link><?php app_site_link(); ?></link>
  <description><?php app_site_desc(); ?></description>
  <language>zh_CN</language>
  <sy:updatePeriod>hourly</sy:updatePeriod>
  <sy:updateFrequency>1</sy:updateFrequency>
  <generator>DouPress</generator>
<?php while (app_next_post()) { ?>
    <item>
      <title><?php app_post_title(); ?></title>
      <link><?php app_post_url(); ?></link>
      <guid><?php app_post_url(); ?></guid>
      <dc:creator><?php app_nick_name(); ?></dc:creator>
      <pubDate><?php app_post_date(); ?> <?php app_post_time(); ?></pubDate>
<?php app_post_tags("      <category><![CDATA[", "\n", "]]></category>"); echo "\n"; ?>
      <content:encoded><![CDATA[<?php app_post_content();?>]]></content:encoded>
    </item>
<?php } ?>

</channel>
</rss>
