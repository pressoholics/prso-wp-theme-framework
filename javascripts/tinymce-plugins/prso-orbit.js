			(function() {
			   tinymce.create('tinymce.plugins.prso_banner_gallery', {
			      init : function(ed, url) {
			         ed.addButton('prso_banner_gallery', {
			            title : 'Banner Gallery',
			            image : url+'/prso-orbit-button.png',
			            
			            onclick : function() {
			            
			            			            			var name = prompt("Gallery Name", " ");
			            						            
			            			             
			             			             		ed.execCommand('mceInsertContent', false, '[prso_banner_gallery  name="'+name+'"]');
			             					               
			            }
			            
			         });
			      },
			      createControl : function(n, cm) {
			         return null;
			      },
			      getInfo : function() {
			         return {
			            longname : "Pressoholics Orbit Banner Gallery Plugin",
			            author : '',
			            authorurl : 'http://benjaminmoody.com',
			            infourl : 'http://pressoholics.com',
			            version : "1.0"
			         };
			      }
			   });
			   tinymce.PluginManager.add('prso_banner_gallery', tinymce.plugins.prso_banner_gallery);
			})();
			