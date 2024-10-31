<?php

/*
  Plugin Name: New Post Automatic Xml Backup by Email
  Plugin URI: http://www.konnichiwamundo.com
  Description: This plugin sends an email to the admin account with the published post in the same xml format thaw WordPress uses to export the blog contents. Emails are also send when a published post is updated.
  Author: Xosen
  Author URI: http://www.konnichiwamundo.com
  Version: 1.0.3
  License: GPLv2 or later
 */

define( 'NPXBE_VERSION', '1.2' );
define( 'THIS_PLUGIN_DIR', '/new-post-automatic-xml-backup-by-email/');

include('xml-tools.php');
include('export-tools.php');

function npxbe_send_xml_backup_email() {
    global $post;

    function npxbe_create_post_xml_file() {
        global $post;

        setup_postdata($post);
        $is_sticky = is_sticky($post->ID) ? 1 : 0;
        
        npxbe_initialize_xml("1.0", get_bloginfo('charset'));
        npxbe_open_tag_with_atributes('rss', TRUE);
        npxbe_add_attribute('version', '2.0');
        npxbe_add_attribute('xmlns:excerpt', 'http://wordpress.org/export/'.NPXBE_VERSION.'/excerpt/');
        npxbe_add_attribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
        npxbe_add_attribute('xmlns:wfw', 'http://wellformedweb.org/CommentAPI/');
        npxbe_add_attribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        npxbe_add_attribute('xmlns:wp', 'http://wordpress.org/export/'.NPXBE_VERSION.'/', TRUE);
        
        npxbe_open_tag('channel');
        npxbe_add_tag('title', get_bloginfo( 'name' ));
        npxbe_add_tag('link', get_bloginfo( 'url' ));
        npxbe_add_tag('description', get_bloginfo( 'description' ));
        npxbe_add_tag('pubDate', date( 'D, d M Y H:i:s +0000' ));
        npxbe_add_tag('language', get_bloginfo( 'language' ));
        npxbe_add_tag('wp:wxr_version', NPXBE_VERSION);
        npxbe_add_tag('wp:base_site_url', npxbe_site_url());
        npxbe_add_tag('wp:base_blog_url', get_bloginfo( 'url' ));
        
        npxbe_authors_list();
        
        npxbe_open_tag('item');
        npxbe_add_tag('title', apply_filters('the_title_rss', $post->post_title));
        npxbe_add_tag('link', esc_url(get_permalink()));
        npxbe_add_tag('pubDate', mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false));
        npxbe_add_tag('dc:creator', npxbe_cdata(get_the_author_meta('login')));
        npxbe_open_tag_with_atributes('guid');
        npxbe_add_attribute('isPermaLink', 'false', TRUE);
        npxbe_add_value(esc_url(get_the_guid()));
        npxbe_close_tag('guid', FALSE);
        npxbe_add_tag('description', '');
        npxbe_add_tag('content:encoded', npxbe_cdata(apply_filters('the_content_export', $post->post_content)));
        npxbe_add_tag('excerpt:encoded', npxbe_cdata(apply_filters('the_excerpt_export', $post->post_excerpt)));
        npxbe_add_tag('wp:post_id', $post->ID);
        npxbe_add_tag('wp:post_date', $post->post_date);
        npxbe_add_tag('wp:post_date_gmt', $post->post_date_gmt);
        npxbe_add_tag('wp:comment_status', $post->comment_status);
        npxbe_add_tag('wp:ping_status', $post->comment_status);
        npxbe_add_tag('wp:post_name', $post->post_name);
        npxbe_add_tag('wp:status', $post->post_status);
        npxbe_add_tag('wp:post_parent', $post->post_parent);
        npxbe_add_tag('wp:menu_order', $post->menu_order);
        npxbe_add_tag('wp:post_type', $post->post_type);
        npxbe_add_tag('wp:post_password', $post->post_password);
        npxbe_add_tag('wp:is_sticky', $is_sticky);
        
        if($post->post_type == 'attachment'){
            npxbe_add_tag('wp:attachment_url', wp_get_attachment_url( $post->ID ));
        }
        npxbe_post_taxonomy();
        
        npxbe_post_meta();
        
        npxbe_post_comments();
        
        npxbe_close_tag('item');
        npxbe_close_tag('channel');
        npxbe_close_tag('rss');
        
        $xml_file_name = $post->post_name;
        
        if(empty($xml_file_name)){
            $xml_file_name = $post->ID;
        }
        
        $xml_file_path = WP_PLUGIN_DIR.THIS_PLUGIN_DIR.$xml_file_name.'.xml';
        npxbe_save_to_xml_file($xml_file_path);
        
        return $xml_file_path;
    }
    
    $xml_file_path = npxbe_create_post_xml_file();

    $admin_email = get_bloginfo('admin_email');
    $subject = 'Published Post XML Backup: ' . $post->post_title;
    $body = 'Post Title: ' . $post->post_title . "\n"
            . 'Author: ' . get_the_author_meta('login') . "\n"
            . 'Last modified by: ' . get_the_modified_author() . "\n"
            . 'Last Update: ' .  get_the_modified_date() . ' ' . get_the_modified_time();
    
    $attachments = array($xml_file_path);

    wp_mail($admin_email, $subject, $body, '', $attachments);
    
    unlink($xml_file_path);
}

add_action('publish_post', 'npxbe_send_xml_backup_email');