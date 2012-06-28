<?php
/**
 * Creates a settings page for the plugin
 * @author Elio Rivero
 * @since 1.0.0
 */
class ILCMQMAdmin {
	
	/**
	 * Get plugin global settings.
	 * @var Object $ilc_mqm_settings
	 * @since 1.0.0
	 */
	static $settings;
	static $settings_id;
	static $settings_page_id;
	static $filepath;
	
	function __construct() {
		self::$settings 		= get_option('ilc_mqm_settings');
		self::$settings_id 		= 'ilc_mqm_settings';
		self::$settings_page_id = 'ilc_mqm_options';
		self::$filepath 		= plugin_dir_path(__FILE__) . '/ilc-mqmonitor.php';
		add_action( 'admin_init', array(&$this, 'admin_init') );
		add_action( 'admin_menu', array(&$this, 'plugin_menu'));
		register_activation_hook(	self::$filepath, array(&$this, 'activate'));
		register_deactivation_hook(	self::$filepath, array(&$this, 'deactivate'));
	}
	
	/**
	 * Creates the contextual help for this plugin
	 * @param string
	 * @return string
	 * @since 1.0.0
	 */
	function help() {
		
		$screen = get_current_screen();
			
		$html = '<h5>' . __('Welcome!', 'ilc') . '</h5>';
		$html .= '<p>' . __('The ILC Media Queries Monitor plugin will show the current media query in use in the Admin Bar. You need to enter your media queries below', 'ilc') . '</p>';
		
		$html .= '<p><em>' . sprintf( __('ILC Media Queries Monitor created by Elio Rivero. Follow %s on Twitter for the latest updates.', 'ilc'), '<a href="http://twitter.com/eliorivero">@eliorivero</a>') . '</em></p>';
		$screen->add_help_tab( array(
			'id'      => 'ilc-main',
			'title'   => __('Introduction', 'ilc'),
			'content' => $html,
		));
		
	}
	
	/**
	 * Creates the options page for this plugin
	 * @since 1.0.0
	 */
	function options_page(){
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.', 'ilc') );
		}
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e('ILC Media Queries Monitor', 'ilc'); ?></h2>
			
			<form action="options.php" method="post">
				<?php settings_fields(self::$settings_id); ?>
				<?php do_settings_sections('ilc_mqm_options'); ?>
				<input class="button-primary" name="<?php _e('Submit','ilc'); ?>" type="submit" value="<?php esc_attr_e('Save Changes', 'ilc'); ?>" />
			</form>
		</div>
		<?php
	}
	
	/**
	 * Defines the settings field for the plugin options page.
	 * @since 1.0.0
	 */
	
	function admin_init(){
		
		register_setting( self::$settings_id, self::$settings_id, array(&$this, 'validate_options') );
		
		add_settings_section( 'ilc_mqm_main_section', __('Posts settings', 'ilc'), array(&$this, 'main_desc'), 'ilc_mqm_options' );
		
		$sections['main_section'] = array(
			array(
				'id' => 'queries_int',
				'label' => __('Media Queries', 'ilc'),
				'type' => 'textarea',
				'default' => '900,768,650,480,320',
				'class' => 'large-text',
				'help' => __('Enter each media query in use, separated by commas.', 'ilc')
			)
		);
		
		foreach ($sections as $key => $fields) {
			foreach($fields as $field){
				add_settings_field(
					'ilc_mqm_' . $field['id'],
					$field['label'],
					array( &$this, $field['type']),
					'ilc_mqm_options',
					'ilc_mqm_' . $key,
					array(
						'field_id' => $field['id'],
						'field_default' => $field['default'],
						'field_class' => isset($field['class'])? $field['class'] : null,
						'field_help' => isset($field['help'])? $field['help'] : null,
						'field_ops' => isset($field['options'])? $field['options'] : null
					)
				);
			}
		}
	}

	/**
	 * Creates a checkbox control
	 * @param array
	 * @since 1.0.0
	 */
	function checkbox($args) {
		extract($args);
		$options = get_option(self::$settings_id);
		$options[$field_id] = isset($options[$field_id])? $options[$field_id] : $field_default;
		if( isset ($options[$field_id]) ){
			$checked = 'checked="checked"';
		}
		else {
			$checked = '';
		}
		echo "<label for='ilc_mqm_$field_id'><input $checked id='ilc_mqm_$field_id' name='ilc_mqm_settings[$field_id]' type='checkbox' />";
		if( isset($field_help) ) echo " $field_help";
		echo '</label>';
	}
	
	/**
	 * Validates options trying to be saved. Specific sentences are required for each value.
	 * @param array
	 * @since 1.0.0
	 */
	function validate_options($input){
		$options = get_option(self::$settings_id);
		
		//Validate colors
		foreach ($input as $key => $value) {
			if(strpos($key,'_int')){
				$options[$key] = $value;
				if(!preg_match('/^([0-9]{3,4} *, *)*[0-9]{3,4}$/i', $options[$key])) {
					switch($key){
						case 'queries_int':
							$options[$key] = '900,768,650,480,320';
							break;
					}
				}
			}
		}
		
		return $options;
	}
	
	/**
	 * Callback for settings section
	 */
	function main_desc(){
		echo '<p>'.__('Set default values to retrieve related posts.', 'ilc') . '</p>';
	}
	
	/**
	 * Creates a textarea
	 * @param array
	 * @since 1.0.0
	 */
	function textarea($args) {
		extract($args);
		$options = get_option(self::$settings_id);
		$options[$field_id] = isset($options[$field_id])? $options[$field_id] : $field_default;
		$class = ( isset($field_class) )? "class='$field_class'" : "";
		echo "<textarea id='ilc_mqm_$field_id' $class rows='5' name='ilc_mqm_settings[$field_id]'>{$options[$field_id]}</textarea>";
		if( isset($field_help) ){
			echo "<br/><span class='description'>$field_help</span>";
		}
	}
	
	/**
	 * Creates a text input field
	 * @param array
	 * @since 1.0.0
	 */
	function text($args) {
		extract($args);
		$options = get_option(self::$settings_id);
		$options[$field_id] = isset($options[$field_id])? $options[$field_id] : $field_default;
		$class = ( isset($field_class) )? "class='$field_class'" : "";
		echo "<input id='ilc_mqm_$field_id' $class name='ilc_mqm_settings[$field_id]' type='text' value='{$options[$field_id]}' />";
		if( isset($field_help) ){
			echo "<br/><span class='description'>$field_help</span>";
		}
	}
	
	/**
	 * Creates Settings link on plugins list page.
	 * @param array
	 * @param string
	 * @return array
	 * @since 1.0.0
	 */
	function settings_link($links, $file) {
		if ($file == plugin_basename( self::$filepath )) {
			foreach($links as $k=>$v) {
				if (strpos($v, 'plugin-editor.php?file=') !== false)
					unset($links[$k]);
			}
			$links[] = "<a href='options-general.php?page=".self::$settings_page_id."'><b>" . __('Settings', 'ilc') . "</b></a>";
		}
		return $links;
	}
	
	/**
	 * Adds Settings link on plugins page. Create options page on wp-admin.
	 * @since 1.0.0
	 */
	function plugin_menu() {
		$plugindata = get_plugin_data( self::$filepath );
		add_filter( 'plugin_action_links', array(&$this, 'settings_link'), -10, 2);
		$op = add_options_page($plugindata['Name'], $plugindata['Name'], 'manage_options', self::$settings_page_id, array(&$this, 'options_page'));
		add_action('load-' . $op, array(&$this, 'help'));
	}
	
	
	/**
	 * Get plugin setting
	 * @param string
	 * @return mixed
	 * @since 1.0.0
	 */
	function get($key) {
		return self::$settings[$key];
	}
	
	/**
	 * Get and echo plugin setting
	 * @param string
	 * @since 1.0.0
	 */
	function gecho($key) {
		echo $this->get($key);
	}
	
	/**
	 * When the plugin is activated, we will setup some options on the database
	 * @since 1.0.0
	 */
	function activate(){
		$defaults = array(
			'queries_int'	=> '900,768,650,480,320'
		);
		add_option(self::$settings_id, $defaults);
	}

	/**
	 * When the plugin is deactivated, erase all options from database.
	 * @since 1.0.0
	 */
	function deactivate(){
		delete_option(self::$settings_id);
	}
}

?>