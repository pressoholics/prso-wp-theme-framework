			(function() {
			   tinymce.create('tinymce.plugins.accordion', {
			      init : function(ed, url) {
			         ed.addButton('accordion', {
			            title : 'Add Accordion Content',
			            image : url+'/prso-accordion-shortcode.png',
			            
			            onclick : function() {
			            
			            			            
			            		            			var content = prompt("Accordion - Click ok to add tags", "[accordion_row title='' active='']Add content between row tags. Keep rows within Accordion parent tag[/accordion_row]");
		            						             
			             			             		ed.execCommand('mceInsertContent', false, '[accordion ]'+content+'[/accordion]');
			             					               
			            }
			            
			         });
			      },
			      createControl : function(n, cm) {
			         return null;
			      },
			      getInfo : function() {
			         return {
			            longname : "Prso Accordion Content Shortcode",
			            author : '',
			            authorurl : 'http://benjaminmoody.com',
			            infourl : 'http://pressoholics.com',
			            version : "1.0"
			         };
			      }
			   });
			   tinymce.PluginManager.add('accordion', tinymce.plugins.accordion);
			})();
			