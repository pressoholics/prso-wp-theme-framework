			(function() {
			   tinymce.create('tinymce.plugins.panel', {
			      init : function(ed, url) {
			         ed.addButton('panel', {
			            title : 'Add content panel',
			            image : url+'/prso-panel-shortcode.png',
			            
			            onclick : function() {
			            
			            			            			var type = prompt("Type: (default, callout)", "default");
			            						            			var radius = prompt("Type: (default, round)", "default");
			            						            
			            		            			var content = prompt("Panel Content - Click ok to add tags", "Add content between panel tags");
		            						             
			             			             		ed.execCommand('mceInsertContent', false, '[panel  type="'+type+'" radius="'+radius+'"]'+content+'[/panel]');
			             					               
			            }
			            
			         });
			      },
			      createControl : function(n, cm) {
			         return null;
			      },
			      getInfo : function() {
			         return {
			            longname : "Prso Content Box Shortcode",
			            author : '',
			            authorurl : 'http://benjaminmoody.com',
			            infourl : 'http://pressoholics.com',
			            version : "1.0"
			         };
			      }
			   });
			   tinymce.PluginManager.add('panel', tinymce.plugins.panel);
			})();
			