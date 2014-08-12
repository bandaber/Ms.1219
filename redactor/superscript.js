if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.appeldenote = {
     init: function() {
        this.buttonAdd('appeldenote', 'Appel de note', this.boutonAppel);
        this.buttonAwesome('appeldenote', 'fa-superscript');
    },
    boutonAppel: function(buttonName, buttonDOM, buttonObj, e) {
        this.execCommand('superscript')
    }
}