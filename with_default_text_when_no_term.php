<?php
/*
Parent only	Uses parent term
Child only	Uses first child term
Both parent & child	Uses the parent term
No terms	Falls back to "zones" / "county"
*/
function custom_hotel_permalinks($post_link, $post) {
    if ($post->post_type == 'hotel') {
        // Get terms
        $zones = get_the_terms($post->ID, 'zones');
        $other_hotels = get_the_terms($post->ID, 'other_hotels');

        // Handle zones (same as before)
        $zone_slug = 'zones';
        if ($zones && !is_wp_error($zones)) {
            foreach ($zones as $term) {
                if ($term->parent == 0) {
                    $zone_slug = $term->slug;
                    break;
                }
            }
            if ($zone_slug === 'zones' && !empty($zones)) {
                $zone_slug = $zones[0]->slug;
            }
        }

        // Handle other_hotels with "county" fallback
        $hotel_slug = 'county'; // default fallback if no terms
        if ($other_hotels && !is_wp_error($other_hotels)) {
            foreach ($other_hotels as $term) {
                if ($term->parent == 0) {
                    $hotel_slug = $term->slug;
                    break;
                }
            }
            if ($hotel_slug === 'county' && !empty($other_hotels)) {
                $hotel_slug = $other_hotels[0]->slug;
            }
        }

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
