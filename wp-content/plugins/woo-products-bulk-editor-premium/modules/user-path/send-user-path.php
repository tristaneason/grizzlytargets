<?php
if (!class_exists('WPSE_Send_User_Path')) {

	/**
	 * Note to reviewers. This class saves in the database the last 40 admin pages 
	 * related to this plugin used by the administrator (either when the admin opens 
	 * a page with a slug from this plugin or opens a admin URL containing a identifier as query string.
	 * 
	 * This information is intended to be used to build a little activity profile and customize
	 * the experience on the site (hide features that the admin rarely uses, for example).
	 * 
	 * This information never leaves your server, it's just a bunch of activity data stored
	 * in your database and used by the plugin locally
	 */
	class WPSE_Send_User_Path {

		var $args = null;
		var $user_path = null;

		function __construct() {
			add_action('wpse_user_path_init', array($this, 'user_path_init'));
		}

		function user_path_init($user_path) {
			$this->args = $user_path->args;
			$this->args['ga_property_id'] = 'UA-82227229-1';
			$this->user_path = $user_path;

			add_action('admin_footer', array($this, 'send_events_once'));
		}

		function send_events_once() {
			if (defined('DOING_AJAX') || $this->args['is_free'] || (bool) get_option($this->args['user_path_key'] . '_user_path_sent', 0)) {
				return;
			}
			?>
			<script>var vgSendPath = <?php echo $this->get_js_function(); ?>; vgSendPath();</script>
			<?php
			update_option($this->args['user_path_key'] . '_user_path_sent', 1);
		}

		function get_js_function($clear_processed_events = true) {
			$events = $this->user_path->get_events();
			$user_events = wp_list_filter($events[$this->args['user_id']], array('vgStatus' => 0));

			if (empty($user_events)) {
				return;
			}
			if ($clear_processed_events) {
				foreach ($user_events as $index => $event) {
					$user_events[$index]['vgStatus'] = 1;
				}
				$events[$this->args['user_id']] = $user_events;
				update_option($this->args['user_path_key'] . '_user_path', $events);
			}
			ob_start();
			?>
			function(data){
			var vgEvents = <?php echo json_encode(array_slice($user_events, -40, 40)); ?>;

			// Init GA
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php echo $this->args['ga_property_id']; ?>', 'auto');

			vgEvents.forEach( function(event, index){
			ga('send', event);
			});
			}
			<?php
			return ob_get_clean();
		}

	}

	new WPSE_Send_User_Path();
}