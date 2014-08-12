$(function() {

	var texte_courant;
	var collerTexte = false;

	$('#redactor_content').redactor({
		focus: false,
		lang:"fr",
		buttons: ['bold', 'italic', 'underline'],
        plugins: ['appeldenote'],
		linebreaks: true,
		pastePlainText: true,
		observeLinks: false,
		initCallback: function() {
			/*texte_courant = this.get();
			if (texte_courant != "") {
				$('#redactor_content').parent().css('opacity', '1');
			}*/
		},
		changeCallback: function(e) {
			/*var texte_courant = this.get();
			if (texte_courant == "") {
				$('#redactor_content').parent().css('opacity', '');
			} else {
				$('#redactor_content').parent().css('opacity', '1');
			}*/
			collerTexte = false;
		},
		placeholder: '(Texte)'
	});
	






	$('#redactor_notes').redactor({
		focus: false,
		lang:"fr",
		buttons: ['bold', 'italic', 'underline'],
		observeLinks: false,
		initCallback: function() {
			/*if (texte_courant != "") {
				$('#redactor_notes').parent().css('opacity', '1');
			}*/
		},
		changeCallback: function(html) {
			//console.log(html);
			if (collerTexte == false) {
				if (html.substring(0,8) == "<ol>\n</o" || html.substring(0,7) == "<ol></o" || html.substring(0,4) != "<ol>") {
					//$('#redactor_notes').parent().css('opacity', '');
					this.set("<ol><li></li></ol>", false);
				} else {
					//$('#redactor_notes').parent().css('opacity', '1');
					var finString = html.indexOf("</ol>");
					if (finString+5 != html.length) {
						this.set(html.substring(0,finString+5));
					}
				}
			} else {
				collerTexte = false;
			}
		}
	});

	$('#redactor_content').parent().css('padding-bottom',10);

	$('[contenteditable]').on('paste', function (e) {
    	e.preventDefault();

    	var fenetre = prompt("Coller ici le texte à insérer", "");
		if (fenetre != null) {
			collerTexte = true;
		    document.execCommand('inserttext', false, fenetre);
		}
	});
 
});