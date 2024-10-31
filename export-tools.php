<?php

/**
 * Wrap given string in XML CDATA tag.
 *
 * @since 2.1.0
 *
 * @param string $str String to wrap in XML CDATA tag.
 * @return string
 */
function npxbe_cdata($str) {
    if (seems_utf8($str) == false)
        $str = utf8_encode($str);

    // $str = ent2ncr(esc_html($str));
    $str = '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $str) . ']]>';

    return $str;
}

/**
 * Return the URL of the site
 *
 * @since 2.5.0
 *
 * @return string Site URL.
 */
function npxbe_site_url() {
    // ms: the base url
    if (is_multisite())
        return network_home_url();
    // wp: the blog url
    else
        return get_bloginfo('url');
}

/**
 * Generates list of authors with posts
 *
 * @since 3.1.0
 */
function npxbe_authors_list() {
    global $wpdb;

    $authors = array();
    $results = $wpdb->get_results("SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != 'auto-draft'");
    foreach ((array) $results as $result)
        $authors[] = get_userdata($result->post_author);

    $authors = array_filter($authors);

    foreach ($authors as $author) {
        npxbe_open_tag('wp:author');
        npxbe_add_tag('wp:author_id', $author->ID);
        npxbe_add_tag('wp:author_login', $author->user_login);
        npxbe_add_tag('wp:author_email', $author->user_email);
        npxbe_add_tag('wp:author_display_name', npxbe_cdata($author->display_name));
        npxbe_add_tag('wp:author_first_name', npxbe_cdata($author->user_firstname));
        npxbe_add_tag('wp:author_last_name', npxbe_cdata($author->user_lastname));
        npxbe_close_tag('wp:author');
    }
}

/**
 * Generates list of taxonomy terms, in XML tag format, associated with a post
 *
 * @since 2.3.0
 */
function npxbe_post_taxonomy() {
    $post = get_post();

    $taxonomies = get_object_taxonomies($post->post_type);
    if (empty($taxonomies))
        return;
    $terms = wp_get_object_terms($post->ID, $taxonomies);

    foreach ((array) $terms as $term) {
        npxbe_open_tag_with_atributes('category');
        npxbe_add_attribute('domain', $term->taxonomy);
        npxbe_add_attribute('nicename', $term->slug, TRUE);
        npxbe_add_value(npxbe_cdata($term->name));
        npxbe_close_tag('category', FALSE);
    }
}

/**
 * Generates list of meta, in XML tag format, associated with a post
 */
function npxbe_post_meta() {
    global $wpdb;
    $post = get_post();

    $postmeta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID));
    foreach ($postmeta as $meta) {
        if (!apply_filters('wxr_export_skip_postmeta', false, $meta->meta_key, $meta)) {
            npxbe_open_tag('wp:postmeta');
            npxbe_add_tag('wp:meta_key', $meta->meta_key);
            npxbe_add_tag('wp:meta_value', npxbe_cdata($meta->meta_value));
            npxbe_close_tag('wp:postmeta');
        }
    }
}

/**
 * Generates list of comments, in XML tag format, associated with a post
 */
function npxbe_post_comments() {
    global $wpdb;
    $post = get_post();

    $comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved <> 'spam'", $post->ID));
    foreach ($comments as $c) {
        npxbe_open_tag('wp:comment');
        npxbe_add_tag('wp:comment_id', $c->comment_ID);
        npxbe_add_tag('wp:comment_author', npxbe_cdata($c->comment_author));
        npxbe_add_tag('wp:comment_author_email', $c->comment_author_email);
        npxbe_add_tag('wp:comment_author_url', esc_url_raw($c->comment_author_url));
        npxbe_add_tag('wp:comment_author_IP', $c->comment_author_IP);
        npxbe_add_tag('wp:comment_date', $c->comment_date);
        npxbe_add_tag('wp:comment_date_gmt', $c->comment_date_gmt);
        npxbe_add_tag('wp:comment_content', npxbe_cdata($c->comment_content));
        npxbe_add_tag('wp:comment_approved', $c->comment_approved);
        npxbe_add_tag('wp:comment_type', $c->comment_type);
        npxbe_add_tag('wp:comment_parent', $c->comment_parent);
        npxbe_add_tag('wp:comment_user_id', $c->user_id);

        $c_meta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->commentmeta WHERE comment_id = %d", $c->comment_ID));
        foreach ($c_meta as $meta) {
            npxbe_open_tag('wp:commentmeta');
            npxbe_add_tag('wp:meta_key', $meta->meta_key);
            npxbe_add_tag('wp:meta_value', npxbe_cdata($meta->meta_value));
            npxbe_close_tag('wp:commentmeta');
        }

        npxbe_close_tag('wp:comment');
    }
}

