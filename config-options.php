<?php
/**
 * @package Better-Email-Signature
 */

/*
 See readme.txt for more details
*/


if (!defined ('ABSPATH')) die ('No direct access allowed');


function better_email_signature_admin_scripts($page){
  wp_enqueue_style('bes-admin',  plugins_url( 'css/admin.css', __FILE__), array(), BETTER_EMAIL_SIGNATURE_VERSION);
}
add_action('admin_enqueue_scripts', 'better_email_signature_admin_scripts');

function better_email_signature_options_menu() {
  add_options_page('Better Email Signature', 'Better Email Signature', 'manage_options', 'better_email_signature', 'better_email_signature_options_printpage');
}
add_action('admin_menu', 'better_email_signature_options_menu');

function better_email_signature_options_init(){
  register_setting( 'better_email_signature_options', 'better_email_signature_options' , 'better_email_signature_options_validate' );
  add_settings_section ( 'better_email_signature_options', 'Better Email Signature Options', 'better_email_signature_options_header' , 'better_email_signature');
  add_settings_field ( 'better_email_signature_options_signature', 'Signature', 'better_email_signature_options_signature', 'better_email_signature' , 'better_email_signature_options' );
}
add_action('admin_init', 'better_email_signature_options_init' );

function better_email_signature_options_setdefaults() {
  $tmp = get_option('better_email_signature_options');
  if (!is_array($tmp)) {
    $arr = array( "signature" => "" );
    update_option('better_email_signature_options', $arr);
  }
}
register_activation_hook('better_email_signature', 'better_email_signature_options_setdefaults');

function better_email_signature_options_signature() {

  $options = get_option('better_email_signature_options');
  ?>
  <div id="bes_elements">
  <?php
  $signatures = $options['text'];
  if (is_array($signatures) && count($signatures)){
  foreach ($signatures as $idx => $signature) {
    $checked = $options['use'] == $idx ? 'checked="checked"' : '';
  ?>
    <div class="bes_element">
      <input type="radio" class="bes_use" name="better_email_signature_options[use]" value="<?php echo $idx ?>" id="better_email_signature_options_use_<?php echo $idx ?>" <?php echo $checked; ?> /> <label for="better_email_signature_options_use_<?php echo $idx ?>" ><?php echo _e('Use this') ?></label><br />
      <textarea rows="6" cols="60" name="better_email_signature_options[text][]" /><?php echo $signature; ?></textarea><br />
      <a class="bes_remove button button-small" href="javascript: void();" onclick="javascript: removeSignature(this); return false;"><?php echo _e('Remove') ?></a>
    </div>
  <?php }
  }
  else { ?>
    <div class="bes_element">
      <input type="radio" class="bes_use" name="better_email_signature_options[use]" value="0" id="better_email_signature_options_use_0" /> <label for="better_email_signature_options_use_0" ><?php echo _e('Use this') ?></label><br />
      <textarea rows="6" cols="60" name="better_email_signature_options[text][]" /></textarea><br />
      <a class="bes_remove button button-small" href="javascript: void();"><?php echo _e('Remove') ?></a>
    </div>
  <?php 
  }
  ?>  
  </div>
  <div><a id="bes_add" class="button-secondary" href="javascript: void();">Add Signature</a> </div>
  
  <script type="text/javascript">
    var bes_str = '<div class="bes_element">'+
                  '<input type="radio" class="bes_use" name="better_email_signature_options[use]" value="REPLACE" id="better_email_signature_options_use_REPLACE" /> <label for="better_email_signature_options_use_REPLACE" ><?php echo _e('Use this') ?></label><br />'+
                  '<textarea rows="6" cols="60" name="better_email_signature_options[text][]" /></textarea><br />'+
                  '<a class="bes_remove button button-small" href="javascript: void();" onclick="javascript: removeSignature(this); return false;"><?php echo _e('Remove') ?></a>'+
                  '</div>';
    
    jQuery(function($){
      $('#bes_add').click(function(e){
        e.stopPropagation();
        var addId = $(".bes_element").length;
        $("#bes_elements").append(bes_str.replace(/REPLACE/g, addId + ""));
        return false;
      });
      
      removeSignature = function(elm){
        $(elm).parent().remove();
        $(".bes_use").each(function(index, elm){
          if ($(elm).is(':checked')) $(elm).val(index);
        });
      }
      
      
    });
  </script>
  <?php
}

function better_email_signature_options_header() {
  settings_errors();
}

function better_email_signature_options_validate($input) {
  return $input;
}


function better_email_signature_options_printpage() {
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  $pver = BETTER_EMAIL_SIGNATURE_VERSION;

  echo <<<ENDHERE
  <div class="wrap">
  <h2>Better Email Signature <small>version <strong>$pver</strong></small></h2>

  <p>Enter the signatures which you wish to use here and choose one to be default signature. Do not add the &quot;--&quot;, as it will be added automatically.</p>
  <p>To turn off the adding of signatures just disable the plugin and your settings will not be deleted.</p>
  <form method="post" action="options.php">
ENDHERE;
  settings_fields('better_email_signature_options');
  do_settings_sections('better_email_signature');

  echo <<<ENDHERE
      <p class="submit">
      <input type="submit" class="button-primary" value="Save" />
      </p>
  </form>
ENDHERE;
  
  echo <<<ENDHERE
<hr>
  <h2>Want to find  more useful WordPress stuffs?</h2>
  <p>See <a href="http://www.joomportal.com/wordpress/" title="more usefull Wordpress articles">articles</a> here</p>
</div>


ENDHERE;
}


function better_email_signature_action_links($links, $file) {

  if ( $file == ADDEMAILSIG_SLUG."/".ADDEMAILSIG_SLUG.".php" ){
    array_unshift( $links, 
      '<a href="options-general.php?page=better_email_signature">Settings</a>'
    );
  }

  return $links;

}
add_filter( 'plugin_action_links', 'better_email_signature_action_links', 10, 2 );

?>
