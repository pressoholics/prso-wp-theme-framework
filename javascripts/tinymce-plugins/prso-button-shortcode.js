			(function() {
			   tinymce.create('tinymce.plugins.button', {
			      init : function(ed, url) {
			         ed.addButton('button', {
			            title : 'Add Button',
			            image : url+'/prso-button-shortcode.png',
			            
			            onclick : function() {
			            
			            			            			var type = prompt("Type: (default, secondary, success, alert)", "default");
			            						            			var size = prompt("Type: (tiny, small, medium, large)", "medium");
			            						            			var style = prompt("Type: (square, radius, round)", "square");
			            						            			var url = prompt("Link URL", "http://");
			            						            
			            		            			var content = prompt("Button - Click ok to add tags", "Button Text Here");
		            						             
			             			             		ed.execCommand('mceInsertContent', false, '[button  type="'+type+'" size="'+size+'" style="'+style+'" url="'+url+'"]'+content+'[/button]');
			             					               
			            }
			            
			         });
			      },
			      createControl : function(n, cm) {
			         return null;
			      },
			      getInfo : function() {
			         return {
			            longname : "Prso Call to Action Button Shortcode",
			            author : '',
			            authorurl : 'http://benjaminmoody.com',
			            infourl : 'http://pressoholics.com',
			            version : "1.0"
			         };
			      }
			   });
			   tinymce.PluginManager.add('button', tinymce.plugins.button);
			})();
			