			(function() {
			   tinymce.create('tinymce.plugins.video', {
			      init : function(ed, url) {
			         ed.addButton('video', {
			            title : 'Add Video',
			            image : url+'/prso-video-shortcode.png',
			            
			            onclick : function() {
			            
			            			            			var service = prompt("Type: (youtube, vimeo)", "youtube");
			            						            			var aspect = prompt("Type: (widescreen, normal)", "widescreen");
			            						            			var width = prompt("Video Width", "560");
			            						            			var height = prompt("Video Height", "315");
			            						            
			            		            			var content = prompt("Video - Click ok to add tags", "Video ID Here");
		            						             
			             			             		ed.execCommand('mceInsertContent', false, '[video  service="'+service+'" aspect="'+aspect+'" width="'+width+'" height="'+height+'"]'+content+'[/video]');
			             					               
			            }
			            
			         });
			      },
			      createControl : function(n, cm) {
			         return null;
			      },
			      getInfo : function() {
			         return {
			            longname : "Prso Video Shortcode",
			            author : '',
			            authorurl : 'http://benjaminmoody.com',
			            infourl : 'http://pressoholics.com',
			            version : "1.0"
			         };
			      }
			   });
			   tinymce.PluginManager.add('video', tinymce.plugins.video);
			})();
			