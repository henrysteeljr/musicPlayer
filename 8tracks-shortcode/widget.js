jQuery(document).ready(function() {
  jQuery(document).delegate('select.eighttracks_embed_type', 'change load', function() {
    //debugger;
    var embed_type;
    var container_id = '#' + jQuery(this).data('container');
    if (this instanceof Element) {
      embed_type = jQuery(this).val(); //get div name from select dropdown value
    }

    console.log('binding behaviors on ' + container_id);

    jQuery(container_id + '>div').hide(); // hide irrelevant options

    jQuery(container_id + ' .eighttracks_' + embed_type + '_options').show(); //show relevant options

    if (embed_type == 'artist' || embed_type == 'tags' || embed_type == 'recent') { //show shared options if applicable
      jQuery(container_id + ' .eighttracks_sort_options').show();
    }

    var defaultWidth, defaultHeight;
    if (embed_type == 'mix') { //update default height/width appropriately
      defaultWidth = '100%';
      defaultHeight = 300;
    } else {
      defaultWidth = '100%';
      defaultHeight = 500;
    }
    jQuery(container_id + ' .eighttracks_width').attr('placeholder', defaultWidth);
    jQuery(container_id + ' .eighttracks_height').attr('placeholder', defaultHeight);
  });

  jQuery('select.eighttracks_embed_type').trigger('change'); //initialize to saved values on load
});
