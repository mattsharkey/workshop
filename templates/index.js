document.querySelectorAll('.template-link').forEach(function(link){
    link.addEventListener('click', function(evt){
        if (document.getElementById('openInNewTab').checked) {
            evt.preventDefault();
            window.open(this.href);
        }
    })
});
