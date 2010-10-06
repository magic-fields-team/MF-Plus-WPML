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
  if(! method_exists('PanelFields','PanelFields') || ! defined('ICL_SITEPRESS_VERSION')) {
    add_action('admin_notices','mf_wpml_notices');
  }
}


/**
 * Display a message in function if all the dependencies of the plugins
 * are installed or not
 *
 *
 */
function mf_wpml_notices() {
  echo "You need install first Magic Fields and WPML for use this plugin";
}

add_action('init','checking_dependencies');
