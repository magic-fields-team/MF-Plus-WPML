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
  if(isNaN(panel_id)) {
    panel_id =   $('input[name=rc-cwp-custom-write-panel-id]').val();
  }

  //is a write panel post list?
  if(!isNaN(panel_id)) {
    //putting this on the translation links
    $('a[href*="trid="]').each(function() {    
      var href = $(this).attr('href');
      $(this).attr('href',href +='&custom-write-panel-id='+panel_id);
    });
  }
});
