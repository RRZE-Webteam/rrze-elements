(function() {

    var plugin_is_usable = tinymce.get('content').plugins.faurteshortcodes;
    console.log(plugin_is_usable);
    console.log(tinymce.get('content'));
    //console.log(tinymce.get('content').menuItems.InsertShortcodes.context);


    tinymce.PluginManager.add('rrzeelementsshortcodes', function(editor) {

        var menuItems = [];
        menuItems.push({
            type: 'menuitem',
            text: 'Accordion',
            onclick: function() {
                editor.insertContent('[collapsibles expand-all-link="true"]<br>[collapse title="Name" color="" name=""]<br>Hier der Text<br>[/collapse]<br>[collapse title="Name" color=""]<br>Hier der Text<br>[/collapse]<br>[/collapsibles]');
            }
        });
        menuItems.push({
            type: 'menuitem',
            text: 'Alert',
            onclick: function() {
                editor.insertContent('[alert]Hier der Text[/alert]<br>');
            }
        });
        menuItems.push({
            type: 'menuitem',
            text: 'Button',
            onclick: function() {
                editor.insertContent('[button link=""]Hier der Text[/button]<br>');
            }
        });
        menuItems.push({
            type: 'menuitem',
            text: 'Content-Slider',
            onclick: function() {
                editor.insertContent('[content-slider number="10"]');
            }
        });
        menuItems.push({
            type: 'menuitem',
            text: 'Custom News',
            onclick: function() {
                editor.insertContent('[custom-news number="5"]');
            }
        });
        menuItems.push({
            text: 'Hinweis / Absatzklasse',
            menu: [{
                    type: 'menuitem',
                    text: 'Hinweis',
                    onclick: function() {
                        editor.insertContent('[notice-hinweis title=""]Hier der Text[/notice-hinweis]');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Warnung',
                    onclick: function() {
                        editor.insertContent('[notice-attention title=""]Hier der Text[/notice-attention]');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Baustelle',
                    onclick: function() {
                        editor.insertContent('[notice-baustelle title=""]Hier der Text[/notice-baustelle]');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Fragezeichen',
                    onclick: function() {
                        editor.insertContent('[notice-question title=""]Hier der Text[/notice-question]');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Minus',
                    onclick: function() {
                        editor.insertContent('[notice-minus title=""]Hier der Text[/notice-minus]');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Plus',
                    onclick: function() {
                        editor.insertContent('[notice-plus title=""]Hier der Text[/notice-plus]');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Tipp',
                    onclick: function() {
                        editor.insertContent('[notice-tipp title=""]Hier der Text[/notice-tipp]');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Download',
                    onclick: function() {
                        editor.insertContent('[notice-download title=""]Hier der Text[/notice-download]');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'FAU-Box',
                    onclick: function() {
                        editor.insertContent('[notice-faubox title=""]Hier der Text[/notice-faubox]');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Audio',
                    onclick: function() {
                        editor.insertContent('[notice-audio title=""]Hier der Text[/notice-audio]');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Video',
                    onclick: function() {
                        editor.insertContent('[notice-video title=""]Hier der Text[/notice-video]');
                    }
                },
            ]
        });
        menuItems.push({
            text: 'Mehrspaltiger Text',
            menu: [{
                    type: 'menuitem',
                    text: 'Zwei Spalten: 1/2 - 1/2',
                    onclick: function() {
                        editor.insertContent('[two_columns_one]<br>Text der ersten Spalte<br>[/two_columns_one]<br>[two_columns_one_last]<br>Text der zweiten Spalte<br>[/two_columns_one_last]<br>[divider]<br>');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Zwei Spalten: 1/3 - 2/3',
                    onclick: function() {
                        editor.insertContent('[three_columns_one]<br>Text der ersten Spalte<br>[/three_columns_one]<br>[three_columns_two_last]<br>Text der zweiten (breiten)  Spalte<br>[/three_columns_two_last]<br>[divider]<br>');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Zwei Spalten: 2/3 - 1/3',
                    onclick: function() {
                        editor.insertContent('[three_columns_two]<br>Text der ersten (breiten)  Spalte<br>[/three_columns_two]<br>[three_columns_one_last]<br>Text der zweiten Spalte<br>[/three_columns_one_last]<br>[divider]<br>');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Zwei Spalten: 1/4 - 3/4',
                    onclick: function() {
                        editor.insertContent('[four_columns_one]<br>Text der ersten Spalte<br>[/four_columns_one]<br>[four_columns_three_last]<br>Text der zweiten (breiten) Spalte<br>[/four_columns_three_last]<br>[divider]<br>');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Zwei Spalten: 3/4 - 1/4',
                    onclick: function() {
                        editor.insertContent('[four_columns_three]<br>Text der ersten (breiten)  Spalte<br>[/four_columns_three]<br>[four_columns_one_last]<br>Text der zweiten Spalte<br>[/four_columns_one_last]<br>[divider]<br>');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Drei Spalten: 1/3 - 1/3 - 1/3',
                    onclick: function() {
                        editor.insertContent('[three_columns_one]<br>Text der ersten Spalte<br>[/three_columns_one]<br>[three_columns_one]<br>Text der zweiten Spalte<br>[/three_columns_one]<br>[three_columns_one_last]<br>Text der dritten Spalte<br>[/three_columns_one_last]<br>[divider]<br>');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Drei Spalten: 2/4 - 1/4 - 1/4',
                    onclick: function() {
                        editor.insertContent('[four_columns_two]<br>Text der ersten (breiten) Spalte<br>[/four_columns_two]<br>[four_columns_one]<br>Text der zweiten Spalte<br>[/four_columns_one]<br>[four_columns_one_last]<br>Text der dritten Spalte<br>[/four_columns_one_last]<br>[divider]<br>');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Drei Spalten: 1/4 - 2/4 - 1/4',
                    onclick: function() {
                        editor.insertContent('[four_columns_one]<br>Text der ersten Spalte<br>[/four_columns_one]<br>[four_columns_two]<br>Text der zweiten (breiten) Spalte<br>[/four_columns_two]<br>[four_columns_one_last]<br>Text der dritten Spalte<br>[/four_columns_one_last]<br>[divider]<br>');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Drei Spalten: 1/4 - 1/4 - 2/4',
                    onclick: function() {
                        editor.insertContent('[four_columns_one]<br>Text der ersten Spalte<br>[/four_columns_one]<br>[four_columns_one]<br>Text der zweiten Spalte<br>[/four_columns_one]<br>[four_columns_two_last]<br>Text der dritten (breiten) Spalte<br>[/four_columns_two_last]<br>[divider]<br>');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Vier Spalten: 1/4 - 1/4 - 1/4 - 1/4',
                    onclick: function() {
                        editor.insertContent('[four_columns_one]<br>Text der ersten Spalte<br>[/four_columns_one]<br>[four_columns_one]<br>Text der zweiten Spalte<br>[/four_columns_one]<br>[four_columns_one]<br>Text der dritten Spalte<br>[/four_columns_one]<br>[four_columns_one_last]<br>Text der vierten Spalte<br>[/four_columns_one_last]<br>[divider]<br>');
                    }
                }
            ]
        });
        menuItems.push({
            text: 'Pull-left / -right',
            menu: [{
                    type: 'menuitem',
                    text: 'Pull-left',
                    onclick: function() {
                        editor.insertContent('[pull-left]Hier der Text[/pull-left]<br>');
                    }
                },
                {
                    type: 'menuitem',
                    text: 'Pull-right',
                    onclick: function() {
                        editor.insertContent('[pull-right]Hier der Text[/pull-right]<br>');
                    }
                },
            ]
        });
        menuItems.push({
            type: 'menuitem',
            text: 'Timeline',
            onclick: function() {
                editor.insertContent('[timeline]<br>[timeline-item name="name1"]<br>Hier der Text<br>[/timeline-item]<br>[timeline-item name="name2"]<br>Hier der Text<br>[/timeline-item]<br>[timeline-item name="name3"]<br>Hier der Text<br>[/timeline-item]<br>[/timeline]');
            }
        });

        editor.addMenuItem('insertShortcodesRrzeElements', {
            icon: 'code',
            text: 'RRZE-Elements',
            menu: menuItems,
            context: 'insert',
        });
    });
})();