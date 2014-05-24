$(function(){
  $('#sidebar').affix({
      offset: {
        top: 201 - 10
      }
    });

  hljs.initHighlightingOnLoad();

  $('body').scrollspy({ target: '#leftCol' });

});
