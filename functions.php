////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Μετατροπή των ελληνικών σε Greeklish στα slugs των σελιδών, των προϊόντων και των posts.
function convert_greek_to_greeklish($text) {
    $greek  = array('ά','έ','ή','ί','ύ','ό','ώ','ϊ','ϋ','ΐ','ΰ','α','β','γ','δ','ε','ζ','η','θ','ι','κ','λ','μ','ν','ξ','ο','π','ρ','σ','τ','υ','φ','χ','ψ','ω','ς', 'Ά', 'Έ', 'Ή', 'Ί', 'Ύ', 'Ό', 'Ώ', 'Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω');
    $greeklish = array('a','e','i','i','u','o','o','i','u','i','i','a','b','g','d','e','z','i','th','i','k','l','m','n','ks','o','p','r','s','t','u','f','x','ps','o','s', 'a', 'e', 'i', 'i', 'u', 'o', 'o', 'a', 'b', 'g', 'd', 'e', 'z', 'i', 'th', 'i', 'k', 'l', 'm', 'n', 'ks', 'o', 'p', 'r', 's', 't', 'u', 'f', 'x', 'ps', 'o');

    $text = str_replace($greek, $greeklish, $text);
    $text = strtolower($text); // Convert to lowercase
    $text = preg_replace("/[^a-zA-Z0-9\-]/", "", $text); // Remove symbols except for hyphen
    $text = str_replace(' ', '-', $text); // Convert spaces to hyphens

    return $text;
}

function greeklish_slugs_on_save($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
        return $post_id;

    // Get the post type
    $post_type = get_post_type($post_id);
    $allowed_post_types = array('post', 'page', 'product');

    // Check if this is a taxonomy (which could be a product attribute) and exclude it
    if (taxonomy_exists($post_type) && strpos($post_type, 'pa_') !== false) {
        return $post_id;
    }

    // Convert slug only for the desired post types and only if it hasn't been converted before
    if (in_array($post_type, $allowed_post_types) && !get_post_meta($post_id, '_greeklish_converted', true)) {
        $post = get_post($post_id);
        $new_slug = convert_greek_to_greeklish($post->post_name);

        if ($post->post_name != $new_slug) {
            wp_update_post(array(
                'ID' => $post_id,
                'post_name' => $new_slug
            ));
        }

        // Mark this post as converted
        add_post_meta($post_id, '_greeklish_converted', '1', true);
    }

    return $post_id;
}

add_action('save_post', 'greeklish_slugs_on_save');
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
