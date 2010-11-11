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
  echo "<div class=\"mf_message error\">You need install first Magic Fields and WPML Multilingual CMS for use this plugin</div>";
}

add_action('init','checking_dependencies');


/**
 * Fixing the links into the "manage posts screen"
 */ 
add_action('admin_print_scripts','mfwpml_scripts');

/**
 * 
 */
function mfwpml_scripts() {
  global $parent_file;

  $types = array('edit.php','edit-pages.php','edit.php?post_type=page');

  if(in_array($parent_file,$types)) {
    wp_enqueue_script('mfwpml',plugins_url('js/mfpluswpml_edit.js',__FILE__,array('jquery')));
  }
}


/**
 * Magic Fields Api Implementation
 */

// Filter for alter the textbox

/** 
 * using the same values of the original post 
 * for the translated one
 */
add_filter('mf_source_post_data','get_info');
function get_info($post_id) {
  global $wpdb;

  //Getting the source of the post_id
  if(isset($_GET['trid']) && is_numeric($_GET['trid']) && empty($_GET['post'])) {
    $post_id = $wpdb->get_var($wpdb->prepare("SELECT  element_id FROM  {$wpdb->prefix}icl_translations WHERE trid={$_GET['trid']}"));
  }
  return $post_id;
}

//Keeping update the custom fields between translated posts
add_action('mf_presave','mfplus_update_values',10,7);

function mfplus_update_values($field_meta_id,$name,$group_index,$field_index,$post_id,$value_field,$writepanel_id){
  global $wpdb;

  //looking for the translated version of the post
  $trid = $wpdb->get_var($wpdb->prepare("SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = {$post_id} AND (element_type = 'post_post' OR element_type = 'post_page')"));

  //Getting the ID's of the translatable fields
  $ids = $wpdb->get_results("SELECT element_id FROM {$wpdb->prefix}icl_translations  WHERE trid = {$trid} AND element_id != {$post_id}");

  if(empty($ids)){
    return true;
  }

  /** getting the translatables fields  **/
  if(!$translatables = get_option("mfplus_translatables")){
    $translatables = array(); 
  }

  //getting the meta_id  of the translated field
  foreach($ids as $value) {

    if(in_array($writepanel_id."_".$name,$translatables)){
      //@todo use the GetMetaID for get the meta_id instead of this query 
      $meta_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".MF_TABLE_POST_META." WHERE post_id = {$value->element_id} AND field_name = '{$name}' AND group_count = {$group_index} AND field_count = {$field_index} AND order_id = {$group_index}"));
      //updating the field value
      update_post_meta($value->element_id,$name,$value_field); 
    }
  }
}

/**
 * Admin Menu
 */
add_action('admin_menu','mf_translatable_admin',20);


function mfplus_translatable_fields_admin() {
  global $wpdb;
  

  if(!$translatables = get_option("mfplus_translatables")) {
    $translatables = array();
  }
  print "<h2>Custom Fields Translatables</h2>";
  print "<div class='mf_plus'>";
  print "<p>Select which Custom Fields will be keep sync between translated posts</p>";
  print "</div>";

  if($_GET['saved_translatable_fields'] == "true"): ?>
      <div id="message" class="updated">
        <strong>Translatable fields saved</strong>
      </div>
  <?php endif; 


  //getting the write panels
  $writepanels = RCCWP_CustomWritePanel::GetCustomWritePanels();
  print "<form method=\"POST\">";
  print "<div class='custom_fields'>";
    foreach($writepanels as $writepanel) {
      print "<h2>{$writepanel->name}</h2>";
      //getting the custom fields of this write panel
      $fields =  $wpdb->get_results(" SELECT  cf.id,cf.name,cf.description FROM  ".MF_TABLE_GROUP_FIELDS." as cf LEFT JOIN ".MF_TABLE_PANEL_GROUPS." as mg  ON (cf.group_id = mg.id) WHERE  mg.panel_id = {$writepanel->id}");
      ?>
      <table class="widefat" width="100%">
        <thead>
          <tr>
            <th>Custom Field Name</th>
            <th>Description</th>
            <th>Keep sync</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($fields as $field):?>
          <tr>
            <td><?php print $field->name;?></td>
            <td><?php print $field->description;?></td>
            <td>
              <?php 
                $checked = "";
                if(in_array($writepanel->id."_".$field->name,$translatables)){
                  $checked = "checked=checked";
                }
              ?>
              <input type="checkbox" name="translatable_field[<?php print $writepanel->id; ?>][<?php print $field->name;?>]" <?php print $checked;?>>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <?php
    }

  print "</div>";
  print "<input type=\"submit\" name=\"save_submit\" id=\"save_sync\">";
  print "</form>";
}

function mf_translatable_admin(){
    add_submenu_page('MagicFieldsMenu','Translatable Fields','Translatable Fields',10,'translatablefields','mfplus_translatable_fields_admin');
}


/** Saving translatable fields **/
add_action('init','save_translatable_fields');
function save_translatable_fields(){
  if(empty($_POST['translatable_field'])){
    return false;
  }

  $translatables = array();
  foreach($_POST['translatable_field'] as $writepanel_id => $fields) {
    foreach($fields as $field => $value){
      $translatables[] = $writepanel_id."_".$field;
    }
  }

  update_option("mfplus_translatables",$translatables);

  wp_safe_redirect(
    add_query_arg(
      'saved_translatable_fields',
      'true',
      wp_get_referer()
    )
  );
}
