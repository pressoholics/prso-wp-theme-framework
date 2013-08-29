			(function() {
			   tinymce.create('tinymce.plugins.two_columns', {
			      init : function(ed, url) {
			         ed.addButton('two_columns', {
			            title : 'Add Content Column',
			            image : url+'/prso-two-column-shortcode.png',
			            
			            onclick : function() {
			            
			            			            
			            		            			var content = prompt("Column Content - Click ok to add tags", "Add content between column tags");
		            						             
			             			             		ed.execCommand('mceInsertContent', false, '[two_columns ]'+content+'[/two_columns]');
			             					               
			            }
			            
			         });
			      },
			      createControl : function(n, cm) {
			         return null;
			      },
			      getInfo : function() {
			         return {
			            longname : "Prso Two Column Content Shortcode",
			            author : '',
			            authorurl : 'http://benjaminmoody.com',
			            infourl : 'http://pressoholics.com',
			            version : "1.0"
			         };
			      }
			   });
			   tinymce.PluginManager.add('two_columns', tinymce.plugins.two_columns);
			})();
			