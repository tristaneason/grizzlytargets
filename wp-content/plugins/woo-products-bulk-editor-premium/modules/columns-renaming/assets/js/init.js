jQuery(document).ready(function(){function o(e,t){var n=jQuery('.modal-columns-visibility .vgse-sorter .js-column-key[value="'+e+'"]').parent(),o=n.find(".js-column-title").val(),i=prompt(vgse_editor_settings.texts.enter_column_name,o);if(null===i||i===o)return!0;if(jQuery.post(ajaxurl,{action:"vgse_rename_column",nonce:jQuery('.modal-columns-visibility input[name="wpsecv_nonce"]').val(),post_type:jQuery('.modal-columns-visibility input[name="wpsecv_post_type"]').val(),column_key:e,new_title:i},function(e){}),"undefined"!=typeof hot){var l=hot.propToCol(e),u=hot.getSettings().colHeaders;u[l]=i,hot.updateSettings({colHeaders:u})}return n.find(".js-column-title").val(i),n.find(".column-title").text(i),"function"==typeof t&&t(),!1}if(jQuery("body").on("click",".modal-columns-visibility   .rename-column",function(e){return e.preventDefault(),o(jQuery(this).parent().find(".js-column-key").val()),!1}),"undefined"==typeof hot||!jQuery(".modal-columns-visibility").length)return!0;var e=hot.getSettings().contextMenu;void 0===e.items&&(e.items={}),e.items.wpse_rename_column={name:vgse_editor_settings.texts.enter_column_name,hidden:function(){if(!hot.getSelected())return!0;var e=hot.colToProp(hot.getSelected()[0][1]),t=vgse_editor_settings.final_spreadsheet_columns_settings[e];return t&&!t.allow_to_rename},callback:function(e,t,n){o(hot.colToProp(t[0].start.col))}},hot.updateSettings({contextMenu:e})});