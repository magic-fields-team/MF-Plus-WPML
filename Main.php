<?php
/*
Plugin Name: MF Plus WPML
Plugin URI: http://magicfields.org
Description: This plugin provide a integration between magic fields and WPML
Versio: 1
Author: Magic Fields Team
Author URI: http://magicfields.org
Licence: GPL2
*/

/*  Copyright 2010 Magic Fields Team  (email : me@gnuget.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Magic Fields and WPML is already installed?
 *
 */
function checking_dependencies() {

  /**
   *  @todo If mf or wpml are not installed  kill this process
   */
  if(! method_exists('PanelFields','PanelFields') || ! defined('ICL_SITEPRESS_VERSION')) {
    add_action('admin_notices','mf_wpml_notices');
  }
}


/**
 * Display a message in function if all the dependencies of the plugins
 * are installed or not
 *
 *
 * @todo improve the desing of this message
 */
function mf_wpml_notices() {
  echo "You need install first Magic Fields and WPML for use this plugin";
}

add_action('init','checking_dependencies');


/**
 * Fixing the links into the "manage posts screen"
 */ 
add_action('admin_print_scripts','mfwpml_scripts');

/** 
 * In the manage posts screen WPML display a new column for add translation for the post
 * when the lists belongs to a writepanel the wplm links are broken, this function fix that
 */ 
function fixing_mf_url($column_name,$id) {
  if($column_name == "icl_translations") {
    print_r($id);
  }
}

/**
 * 
 */
function mfwpml_scripts() {
  global $parent_file;

  $types = array('edit.php','edit-pages.php','edit.php?post_type=page');

  if(in_array($parent_file,$types)) {
    //loading core of the datepicker
    wp_enqueue_script('mfwpml',plugins_url('js/mfpluswpml_edit.js',__FILE__,array('jquery')));
  }
 }
