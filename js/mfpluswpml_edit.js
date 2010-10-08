/**
 *  This could be more useful in a external file for 
 *  be used by anothers files  
 */
jQuery.extend({
  getUrlVars: function(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  },
  getUrlVar: function(name){
    return jQuery.getUrlVars()[name];
  }
});


jQuery(document).ready(function($) {
  //Getting the write panel id
  panel_id = parseInt($.getUrlVar('custom-write-panel-id'));

  //is a write panel post list?
  if(!isNaN(panel_id)) {
    //putting this on the translation links
    $.each($('td.column-icl_translations').has('a[href*=post-new.php]'),function(index,item) {
      anchor  = $(item).children();
      href =  $(anchor).attr('href');
      $(anchor).attr('href',href+'&custom-write-panel-id='+panel_id); 
    });
  }
});
