(function() {
  tinymce.create('tinymce.plugins.eighttracksPlugin', {
    init: function(ed, url) {
      ed.addCommand('eighttracks_button', function() {
        ed.windowManager.open({
          file: url + '/8tracks_button.htm',
          width: 600 + parseInt(ed.getLang('eighttracks_button.delta_width', 0)),
          height: 480 + parseInt(ed.getLang('eighttracks_button.delta_height', 0)),
          inline: 1
        }, {
          plugin_url: url
        });
      });
      ed.addButton('eighttracks_button', {
        title: 'eighttracks_button',
        cmd: 'eighttracks_button',
        image: url + '/icon.jpg'
      });
    },
    getInfo: function() {
      return {
        longname: 'eighttracks_button',
        author: 'Jon Martin',
        authorurl: 'http://www.shh-listen.com',
        infourl: 'https://wordpress.org/extend/plugins/8tracks-shortcode/',
        version: tinymce.majorVersion + "." + tinymce.minorVersion
      };
    }
  });
  tinymce.PluginManager.add('eighttracks_button', tinymce.plugins.eighttracksPlugin);
}());
