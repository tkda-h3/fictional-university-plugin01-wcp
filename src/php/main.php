<?php

/*
  Plugin Name: Test Plugin
  Description: Simple plugin for test only.
  Version: 1.0
  Author: tkda-h3
  Author URI: https://github.com/tkda-h3/
*/


class WordCountAndTimePlugin
{
	protected $section_name = 'wcp_first_section';
	protected $slug = 'wcp-setting';
	protected $settings_group_name = 'wordcountplugin';

	function __construct()
	{
		add_action('admin_menu', array($this, 'adminPage'));
		add_action('admin_init', array($this, 'settings'));
		add_filter('the_content', array($this, 'ifWrap'));
	}

	function ifWrap($content)
	{
		if (
			is_main_query() and is_single() and
			(get_option('wcp_wordcount', '1') or
				get_option('wcp_charactercount', '1') or
				get_option('wcp_readtime', '1'))
		) {
			return $this->createHTML($content);
		}
		return $content;
	}

	function createHTML($content)
	{
		$html = '<h3>' . esc_html(get_option('wcp_headline', 'Post Statistics')) . '</h3><p>';

		// get word count once because both wordcount and read time will need it.
		if (get_option('wcp_wordcount', '1') or get_option('wcp_readtime', '1')) {
			$wordCount = str_word_count(strip_tags($content));
		}

		if (get_option('wcp_wordcount', '1')) {
			$html .= 'This post has ' . $wordCount . ' words.<br>';
		}

		if (get_option('wcp_charactercount', '1')) {
			$html .= 'This post has ' . strlen(strip_tags($content)) . ' characters.<br>';
		}

		if (get_option('wcp_readtime', '1')) {
			$html .= 'This post will take about ' . round($wordCount / 225) . ' minute(s) to read.<br>';
		}

		$html .= '</p>';

		if (get_option('wcp_location', '0') == '0') {
			return $html . $content;
		}
		return $content . $html;
	}

	function settings()
	{
		add_settings_section(
			$this->section_name, // section name
			null, // 挿入したい subtitle
			null, // 挿入したい content
			$this->slug // page slug name
		);

		add_settings_field(
			'wcp_location', // option name
			'Display Location', // display name
			array($this, 'locationHTML'), // function outputting html
			$this->slug, // page slug
			$this->section_name // section
		);
		register_setting(
			$this->settings_group_name, // group belonging to
			'wcp_location', // option name
			array(
				'sanitize_callback' => array($this, 'sanitizeLocation'),
				'default' => '0'
			)
		);

		add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), $this->slug, $this->section_name);
		register_setting($this->settings_group_name, 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics'));

		add_settings_field('wcp_wordcount', 'Word Count', array($this, 'checkboxHTML'), $this->slug, $this->section_name, array('theName' => 'wcp_wordcount'));
		register_setting($this->settings_group_name, 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

		add_settings_field('wcp_charactercount', 'Character Count', array($this, 'checkboxHTML'), $this->slug, $this->section_name, array('theName' => 'wcp_charactercount'));
		register_setting($this->settings_group_name, 'wcp_charactercount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

		add_settings_field('wcp_readtime', 'Read Time', array($this, 'checkboxHTML'), $this->slug, $this->section_name, array('theName' => 'wcp_readtime'));
		register_setting($this->settings_group_name, 'wcp_readtime', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
	}

	function sanitizeLocation($input)
	{
		if ($input != '0' and $input != '1') {
			add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either beginning or end.');
			return get_option('wcp_location');
			return;
		}
		return $input;
	}

	// reusable checkbox function
	function checkboxHTML($args)
	{ ?>
		<input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked(get_option($args['theName']), '1') ?>>
	<?php }

	function headlineHTML()
	{ ?>
		<input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
	<?php }

	function locationHTML()
	{ ?>
		<select name="wcp_location"><!-- option name -->
			<option value="0" <?php selected(get_option('wcp_location'), '0') ?>>Beginning of post</option>
			<option value="1" <?php selected(get_option('wcp_location'), '1') ?>>End of post</option>
		</select>
	<?php }

	function adminPage()
	{
		add_options_page(
			'Word Count Settings', // title
			'Word Count', // メニューのアンカーテキスト
			'manage_options',  // 権限(capability)
			$this->slug, // slug
			array($this, 'ourHTML') // 中身
		);
	}

	function ourHTML()
	{ 
		// https://codex.wordpress.org/Creating_Options_Pages
		?>
		<div class="wrap">
			<h1>Word Count Settings</h1>
			<form action="options.php" method="POST"><!--  -->
				<?php
				settings_fields($this->settings_group_name); // group名。セキュリティやロールなどをよしなにやってくれる
				do_settings_sections($this->slug); // slug name
				submit_button();
				?>
			</form>
		</div>
<?php }
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();
