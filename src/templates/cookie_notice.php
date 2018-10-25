<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
<script>
window.addEventListener("load", function(){
window.cookieconsent.initialise({
  "palette": {
    "popup": {
      "background": "#eb6c44",
      "text": "#ffffff"
    },
    "button": {
      "background": "#f5d948"
    }
  },
  "content": {
    "href": "<?= $src ?>doc/Cookies.md"
  }
})});
</script>