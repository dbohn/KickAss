$(function(){

  $('#sidebar').affix({
      offset: {
        top: 191
      }
    });

  $('body').scrollspy({ target: '#leftCol' });

  // init highlight.js
  hljs.initHighlightingOnLoad();

});
