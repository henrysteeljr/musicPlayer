<?php

class eighttracks_metawidget extends WP_Widget {

  function __construct() {
        $widget_ops = array( 'classname' => '8tracks Post Meta', 'description' => __('Using a post\'s meta data, add an 8tracks collection to your sidebar.') );
        $control_ops = array('id_base' => 'eighttracks-metawidget');
        parent::__construct('eighttracks-metawidget', __('8tracks Meta'), $widget_ops, $control_ops);
  }

  function widget($args, $instance) {
    extract($args);
    $title      = apply_filters('widget_title',$instance['title']);
    $height     = trim($instance['eighttracks_height']);
    $width      = trim($instance['eighttracks_width']);
    $is_widget  = trim($instance['eighttracks_is_widget']);
    $usecat     = trim($instance['eighttracks_usecat']);
    $usetags    = trim($instance['eighttracks_usetags']);
    $recentcat  = trim($instance['eighttracks_recentcat']);
    $recenttags = trim($instance['eighttracks_recenttags']);
    $meta_url   = trim($instance['eighttracks_specmeta']);
    $sort       = trim($instance['eighttracks_sort']);

// Initializing the output code.
    echo ($args['before_widget']);
    echo ($args['before_title']);
    echo ($title);
    echo ($args['after_title']);
    echo '<div class="textwidget">';

  //Outputting the mixes.
    if (($usecat=='1') && ($usetags != '1') && (!isset($meta_url))) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" usecat="yes"  collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if (($usetags=='1') && ($usecat != '1') && (!isset($meta_url))) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" usetags="yes"  collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if (($usecat=='1') && ($usetags=='1') && (!isset($meta_url))) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" usecat="yes"  usetags="yes" collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if (($usecat=='1') && ($usetags != '1') && (isset($meta_url))) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" meta_url="'.($meta_url).'" usecat="yes"  collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if (($usetags=='1') && ($usecat != '1') && (isset($meta_url))) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" meta_url="'.($meta_url).'" usetags="yes"  collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if (($usecat=='1') && ($usetags=='1') && (isset($meta_url))) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" meta_url="'.($meta_url).'" usecat="yes"  usetags="yes" collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if (($recentcat=='1') && ($recenttags != '1')) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" usecat="yes"  collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if (($recenttags=='1') && ($recentcat != '1')) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" usetags="yes"  collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}
    else if (($recenttags=='1') && ($recentcat=='1')) {
        echo do_shortcode('[8tracks height="'.($height).'" width="'.($width).'" flash="no" usecat="yes"  usetags="yes" collection="yes" sort="' . ($sort) . '" is_widget="yes"]');
}

    echo '</div>';
    echo ($args['after_widget']);
}


  function update($new_instance, $old_instance) {
    $instance = $old_instance;

    $instance['title']                      = strip_tags($new_instance['title']);
    $instance['eighttracks_embed_type']     = strip_tags($new_instance['eighttracks_embed_type']);
    $instance['eighttracks_height']         = strip_tags($new_instance['eighttracks_height']);
    $instance['eighttracks_width']          = strip_tags($new_instance['eighttracks_width']);
    $instance['eighttracks_is_widget']      = strip_tags($new_instance['eighttracks_is_widget']);
    $instance['eighttracks_usecat']         = strip_tags($new_instance['eighttracks_usecat']);
    $instance['eighttracks_usetags']        = strip_tags($new_instance['eighttracks_usetags']);
    $instance['eighttracks_recentcat']      = strip_tags($new_instance['eighttracks_recentcat']);
    $instance['eighttracks_recenttags']     = strip_tags($new_instance['eighttracks_recenttags']);
    $instance['eighttracks_specmeta']       = strip_tags($new_instance['eighttracks_specmeta']);
    $instance['eighttracks_sort']           = strip_tags($new_instance['eighttracks_sort']);

    return $instance;
  }

  function form($instance){
    $defaults = array('eighttracks_embed_type' => 'tags', 'title' => '', 'is_widget' => 'yes', 'flash' => 'no', 'height' => '300', 'width' => '100%', 'meta_url' => NULL);
    $instance = wp_parse_args( (array) $instance, $defaults);
    $title      = strip_tags($instance['title']);
    $embed_type = strip_tags($instance['eighttracks_embed_type']);
    $width      = strip_tags($instance['eighttracks_width']);
    $height     = strip_tags($instance['eighttracks_height']);
    $is_widget  = strip_tags($instance['eighttracks_is_widget']);
    $usecat     = strip_tags($instance['eighttracks_usecat']);
    $usetags    = strip_tags($instance['eighttracks_usetags']);
    $recentcat  = strip_tags($instance['eighttracks_recentcat']);
    $recenttags = strip_tags($instance['eighttracks_recenttags']);
    $meta_url   = strip_tags($instance['eighttracks_specmeta']);
    $sort       = strip_tags($instance['eighttracks_sort']);

    ?>

    <p>
        Title:<br />
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" />
    </p>

    <p>Type:<br />
        <select class="eighttracks_embed_type" id="<?php echo $this->get_field_id('eighttracks_embed_type'); ?>" name="<?php echo $this->get_field_name('eighttracks_embed_type'); ?>" data-container="<?php echo $this->get_field_id('eighttracks_embed_options'); ?>">
            <option value="tags" <?php echo($embed_type == 'tags' ? 'selected' : '') ?>>Specific Post</option>
            <option value="recent" <?php echo($embed_type == 'recent' ? 'selected' : '') ?>>Latest Post</option>
        </select>
    </p>


    <div id="<?php echo $this->get_field_id('eighttracks_embed_options'); ?>">

        <!-- begin dynamic options -->
        <P>

        <div class="eighttracks_tags_options" style="display: none;">
            Use a specific post from this blog (Default is latest post):<br />
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('eighttracks_specmeta'); ?>" name="<?php echo $this->get_field_name('eighttracks_specmeta'); ?>" value="<?php echo $meta_url; ?>" /><br />
            <br />Use the post's categories as tags?&nbsp;
            <input id="<?php echo $this->get_field_id('eighttracks_usecat'); ?>" name="<?php echo $this->get_field_name('eighttracks_usecat'); ?>" type="checkbox" value="1" <?php checked( '1', $usecat ); ?> /><br />
            <br />Use the post's tags?&nbsp;
            <input id="<?php echo $this->get_field_id('eighttracks_usetags'); ?>" name="<?php echo $this->get_field_name('eighttracks_usetags'); ?>" type="checkbox" value="1" <?php checked( '1', $usetags ); ?> /><br />
        </div>

        <div class="eighttracks_recent_options" style="display: none;">
            Use the most recent post's categories as tags?&nbsp;
            <input id="<?php echo $this->get_field_id('eighttracks_recentcat'); ?>" name="<?php echo $this->get_field_name('eighttracks_recentcat'); ?>" type="checkbox" value="1" <?php checked( '1', $recentcat ); ?> /><br />
            <br />Use the most recent post's tags?&nbsp;
            <input id="<?php echo $this->get_field_id('eighttracks_recenttags'); ?>" name="<?php echo $this->get_field_name('eighttracks_recenttags'); ?>" type="checkbox" value="1" <?php checked( '1', $recenttags ); ?> /><br />
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


add_action('widgets_init', 'eighttracks_metawidgets_init');

function eighttracks_metawidgets_init() {
  register_widget('eighttracks_metawidget');
}
?>
