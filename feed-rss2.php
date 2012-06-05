<?php
/**
 * RSS2 Feed Template for displaying RSS2 Posts feed.
 *
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

query_posts(array(
    'post_type'=>array('soe_eblog','soe_artist'),
    'posts_per_page'=>10));

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'); ?>
>

<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<image>
            <url><?php echo get_stylesheet_directory_uri();?>/img/soe-logo-menu.png</url>
            <title>Sounds of Europe</title>
            <link><?php echo site_url(); ?></link>
        </image>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
	<?php while( have_posts()) : the_post(); ?>
	<item>
	<?php
	$custom = get_post_custom($post->ID);
	$loc = GetLocation($custom['location'][0]);
// 	print_r($post);
	if($post->post_type == 'soe_artist')
        {
            echo '<title>'.get_the_title_rss().' — '.$loc->name.', '.GetCountryName($loc->country_code).'</title>';
        }
        else
        {
		echo '<title>'.get_the_title_rss().'</title>';
        }
		
	?>
		<link><?php the_permalink_rss() ?></link>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<?php the_category_rss('rss2') ?>

		<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php
        if($post->post_type == 'soe_artist')
        {
                $content = apply_filters('the_content', $custom['artist_bio'][0]);
                $content = str_replace(']]>', ']]&gt;', $content);
                echo '<description><![CDATA['.apply_filters('the_excerpt_rss', $custom['artist_use'][0]).']]></description>';
                echo '<content:encoded><![CDATA[' .apply_filters('the_content_feed', $content,'rss2') .']]></content:encoded>';
        }
        else
        {
                echo '<description><![CDATA['.apply_filters('the_excerpt_rss', get_the_excerpt()).']]></description>';
                echo '<content:encoded><![CDATA[' .get_the_content_feed('rss2') .']]></content:encoded>';
        }
		
	
?>
<?php rss_enclosure(); ?>
	<?php do_action('rss2_item'); ?>
	</item>
	<?php endwhile; ?>
</channel>
</rss>
