<?php

function custom_hotel_permalinks($post_link, $post) {
    if ($post->post_type == 'hotel') {
        $zones = get_the_terms($post->ID, 'zones');
        $other_hotels = get_the_terms($post->ID, 'other_hotels');

        $zone_slug = ($zones && !is_wp_error($zones)) ? $zones[0]->slug : 'no-zone';
        $hotel_slug = ($other_hotels && !is_wp_error($other_hotels)) ? $other_hotels[0]->slug : 'no-hotel';

        return home_url("/hotel/{$zone_slug}/{$hotel_slug}/{$post->post_name}/");
    }
    return $post_link;
}
add_filter('post_type_link', 'custom_hotel_permalinks', 10, 2);

function custom_hotel_rewrite_rules() {
    add_rewrite_rule(
        '^hotel/([^/]+)/([^/]+)/([^/]+)/?$',
        'index.php?post_type=hotel&zones=$matches[1]&other_hotels=$matches[2]&name=$matches[3]',
        'top'
    );
}
add_action('init', 'custom_hotel_rewrite_rules');
