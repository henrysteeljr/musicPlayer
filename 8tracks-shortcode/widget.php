<?php

class eighttracks_basic_widget extends WP_Widget {

  function __construct() {
        $widget_ops = array( 'classname' => '8tracks', 'description' => __('Add an 8tracks mix or collection to your sidebar.') );
        $control_ops = array('id_base' => 'eighttracks-basicwidget');
        parent::__construct('eighttracks-basicwidget', __('8tracks'), $widget_ops, $control_ops);
  }

  function widget($args, $instance) {
    extract($args);
    $title      = apply_filters('widget_title',$instance['title']);
    $url        = trim($instance['eighttracks_url']);
    $height     = trim($instance['eighttracks_height']);
    $width      = trim($instance['eighttracks_width']);
    $flash      = trim($instance['eighttracks_flash']);
    $tags       = trim($instance['eighttracks_tags']);
    $artist     = trim($instance['eighttracks_artist']);
    $dj         = trim($instance['eighttracks_dj']);
    $collection = trim($instance['eighttracks_collection']);
    $list       = trim($instance['eighttracks_list']);
    $sort       = trim($instance['eighttracks_sort']);
    $smart_id   = trim($instance['eighttracks_smartid']);
    $is_widget  = trim($instance['eighttracks_is_widget']);
    $similar    = trim($instance['eighttracks_similar']);

// Initializing the output code.
    echo ($args['before_widget']);
    echo ($args['before_title']);
    echo ($title);
    echo ($args['after_title']);
    echo '<div class="textwidget">';

  //Outputting the mixes.
    if ($url != '') {
        echo do_shortcode('[8tracks url="'.($url).'" height="'.($height).'" width="'.($width).'" flash="no"  collection="no" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if ($similar != '') {
        echo do_shortcode('[8tracks similar="'.($similar).'" height="'.($height).'" width="'.($width).'" flash="no"  collection="no" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if ((empty($url)) && (!empty($dj))) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" dj="'.($dj).'" lists="'.($list).'" collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if ((empty($url)) && (!empty($tags))) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" tags="'.($tags).'"  collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if ((empty($url)) && (!empty($artist))) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" artist="'.($artist).'"  collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if ( (empty($url)) && (!empty($smart_id))) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" smart_id="'.($smart_id).'" collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if (!empty($lastfmuser))  {
        echo do_shortcode('[8tracks lastfm_user="'.($lastfmuser).'" lastfm_type="'.($lastfmtype).'" height="'.($height).'" width="'.($width).'" flash="no" sort="' . ($sort) . '" is_widget="yes"]');
}
    echo '</div>';
    echo ($args['after_widget']);
 }

  function update($new_instance, $old_instance) {
    $instance = $old_instance;

    $instance['title']                      = strip_tags($new_instance['title']);
    $instance['eighttracks_embed_type']     = strip_tags($new_instance['eighttracks_embed_type']);
    $instance['eighttracks_url']            = strip_tags($new_instance['eighttracks_url']);
    $instance['eighttracks_flash']          = strip_tags($new_instance['eighttracks_flash']);
    $instance['eighttracks_height']         = strip_tags($new_instance['eighttracks_height']);
    $instance['eighttracks_width']          = strip_tags($new_instance['eighttracks_width']);
    $instance['eighttracks_tags']           = strip_tags($new_instance['eighttracks_tags']);
    $instance['eighttracks_artist']         = strip_tags($new_instance['eighttracks_artist']);
    $instance['eighttracks_dj']             = strip_tags($new_instance['eighttracks_dj']);
    $instance['eighttracks_collection']     = strip_tags($new_instance['eighttracks_collection']);
    $instance['eighttracks_sort']           = strip_tags($new_instance['eighttracks_sort']);
    $instance['eighttracks_smartid']        = strip_tags($new_instance['eighttracks_smartid']);
    $instance['eighttracks_is_widget']      = strip_tags($new_instance['eighttracks_is_widget']);
    $instance['eighttracks_similar']        = strip_tags($new_instance['eighttracks_similar']);
    $instance['eighttracks_list']           = strip_tags($new_instance['eighttracks_list']);

    return $instance;
  }

  function form($instance){
    $defaults = array('eighttracks_embed_type' => 'mix', 'title' => '', 'is_widget' => 'yes', 'flash' => 'no', 'height' => '300', 'width' => '100%');
    $instance = wp_parse_args( (array) $instance, $defaults);

    $title      = strip_tags($instance['title']);
    $embed_type = strip_tags($instance['eighttracks_embed_type']);
    $width      = strip_tags($instance['eighttracks_width']);
    $height     = strip_tags($instance['eighttracks_height']);
    $url        = strip_tags($instance['eighttracks_url']);
    $flash      = strip_tags($instance['eighttracks_flash']);
    $tags       = strip_tags($instance['eighttracks_tags']);
    $artist     = strip_tags($instance['eighttracks_artist']);
    $dj         = strip_tags($instance['eighttracks_dj']);
    $collection = strip_tags($instance['eighttracks_collection']);
    $sort       = strip_tags($instance['eighttracks_sort']);
    $list       = strip_tags($instance['eighttracks_list']);
    $smart_id   = strip_tags($instance['eighttracks_smartid']);
    $is_widget  = strip_tags($instance['eighttracks_is_widget']);
    $similar    = strip_tags($instance['eighttracks_similar']);

    ?>

    <p>
        Title:<br />
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" />
    </p>

    <p>Type:<br />
    <select class="eighttracks_embed_type" id="<?php echo $this->get_field_id('eighttracks_embed_type'); ?>" name="<?php echo $this->get_field_name('eighttracks_embed_type'); ?>" data-container="<?php echo $this->get_field_id('eighttracks_embed_options'); ?>">
        <option value="mix" <?php echo($embed_type == 'mix' ? 'selected' : '') ?>>Mix</option>
        <option value="collection" <?php echo($embed_type == 'collection' ? 'selected' : '') ?>>Collection</option>
        <option value="similar" <?php echo($embed_type == 'similar' ? 'selected' : '') ?>>Similar Mixes</option>
        <option value="dj" <?php echo($embed_type == 'dj' ? 'selected' : '') ?>>DJ's latest mixes</option>
        <option value="artist" <?php echo($embed_type == 'artist' ? 'selected' : '') ?>>Artist search</option>
        <option value="tags" <?php echo($embed_type == 'tags' ? 'selected' : '') ?>>Tag Search</option>
    </select>
    </p>


    <div id="<?php echo $this->get_field_id('eighttracks_embed_options'); ?>">

        <!-- begin dynamic options -->
        <P>
        <div class="eighttracks_mix_options">
            Mix URL:<br />
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_url'); ?>" name="<?php echo $this->get_field_name('eighttracks_url'); ?>" value="<?php echo $url; ?>" />
        </div>

        <div class="eighttracks_collection_options" style="display: none;">
            Collection URL:<br />
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_smartid'); ?>" name="<?php echo $this->get_field_name('eighttracks_smartid'); ?>" value="<?php echo esc_attr($smart_id); ?>" /><br />
        </div>

        <div class="eighttracks_similar_options" style="display: none;">
            Insert a single mix URL:<br />
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_similar'); ?>" name="<?php echo $this->get_field_name('eighttracks_similar'); ?>" value="<?php echo esc_attr($similar); ?>" /><br />
        </div>

        <div class="eighttracks_dj_options" style="display: none;">
            Specific DJ:<br />
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_dj'); ?>" name="<?php echo $this->get_field_name('eighttracks_dj'); ?>" value="<?php echo esc_attr($dj); ?>" /><br /><br />
            <label for="<?php echo $this->get_field_id('eighttracks_list'); ?>">What type of collection?
            <select class='widefat' id="<?php echo $this->get_field_id('eighttracks_list'); ?>"
                name="<?php echo $this->get_field_name('eighttracks_list'); ?>" type="text">
                <option <?php selected( $instance['eighttracks_list'], ''); ?> value="mostrecent">DJ's latest mixes</option>
                <option <?php selected( $instance['eighttracks_list'], 'liked'); ?> value="liked">DJ's liked mixes</option>
                <option <?php selected( $instance['eighttracks_list'], 'listen_later'); ?> value="listen_later">DJ's 'Listen Later' mixes</option>
                <option <?php selected( $instance['eighttracks_list'], 'listened'); ?> value="listened">DJ's history</option>
                <option <?php selected( $instance['eighttracks_list'], 'recommended'); ?> value="recommended">Recommendations</option>
            </select>
        </div>

        <div class="eighttracks_tags_options" style="display: none;">
            Tag(s):<br />
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_tags'); ?>" name="<?php echo $this->get_field_name('eighttracks_tags'); ?>" value="<?php echo esc_attr($tags); ?>" />
        </div>

        <div class="eighttracks_artist_options" style="display: none;">
            Artist:
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_artist'); ?>" name="<?php echo $this->get_field_name('eighttracks_artist'); ?>" value="<?php echo esc_attr($artist); ?>" />
        </div>

        <div class="eighttracks_sort_options" style="display: none;">
            <br />
            Sort: <span class="color: #ccc;">(optional): hot, new, popular
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_sort'); ?>" name="<?php echo $this->get_field_name('eighttracks_sort'); ?>" value="<?php echo esc_attr($sort); ?>" />
        </div>
        </p>

        <!-- end dynamic options -->

        <p>
            Width:<br />
            <input id="<?php echo $this->get_field_id('eighttracks_width'); ?>" name="<?php echo $this->get_field_name('eighttracks_width'); ?>" type="text" value="<?php echo $width; ?>" class="eighttracks_width" placeholder="100%"/>
        </p>

        <p>
            Height:<br />
           <input id="<?php echo $this->get_field_id('eighttracks_height'); ?>" name="<?php echo $this->get_field_name('eighttracks_height'); ?>" type="text" value="<?php echo $height; ?>" class="eighttracks_height" placeholder="300"/>
        </p>

        <input type="hidden" name="submitted" value="1" />
    </div>

    <script type="text/javascript">
        jQuery('select.eighttracks_embed_type').trigger('change'); //initialize to saved values on load
    </script>

    <?php
  }
}


add_action('widgets_init', 'eighttracks_widgets_init');

function eighttracks_widgets_init() {
  register_widget('eighttracks_basic_widget');
}
?>
