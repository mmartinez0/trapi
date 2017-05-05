<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

final class MIGUEL_Admin {

	public static function load( $plugin ) {

		$admin = new MIGUEL_Admin( $plugin );
		$admin->plugins_loaded();
		return $admin;
	}

	public function __construct( $plugin ) {

		$this->plugin = $plugin;
		$this->browser_title = 'TRAPI Plugin';
		$this->menu_title = 'TRAPI';
		$this->menu_slug = 'trapi';
	}

	public function plugins_loaded(){

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	public function init() {
	}

	public function admin_menu(){

		add_menu_page(
			$this->browser_title,
			$this->menu_title,
			'administrator',
			$this->menu_slug,
			array( $this, 'admin_page'),
			'dashicons-smiley',
			3.959599);

		add_submenu_page(
			$this->menu_slug,
			$this->browser_title,
			'Dashboard',
			'administrator',
			$this->menu_slug,
			array( $this, 'admin_page'));

		add_action( 'load-toplevel_page_' . $this->menu_slug , array( $this, 'load_toplevel_page' ) );
		add_action( 'load-admin_page_' . $this->menu_slug , array( $this, 'load_admin_page' ) );
	}

	public function load_toplevel_page() {
	}

	public function load_admin_page() {
	}

	public function admin_init(){
	}

	public function admin_page(){

		$tab = empty( $_GET['tab'] ) ? '' : $_GET['tab'];

		$admin_url = admin_url('admin.php?page=') . $_GET[ 'page' ];

		$tabs = array(
			''     => (object)array( 'id' => '',     'title' => 'General', 'view' => 'tabs/general.php'),
			'authenticateuser' => (object)array( 'id' => 'authenticateuser', 'title' => 'AuthenticateUser', 'view' => 'tabs/authenticateuser.php'),
			'GetUserSubscriptions' => (object)array( 'id' => 'GetUserSubscriptions', 'title' => 'GetUserSubscriptions', 'view' => 'tabs/GetUserSubscriptions.php'),
			'api' => (object)array( 'id' => 'api', 'title' => 'API', 'view' => 'tabs/api.php')
		);?>

		<div class="wrap">

			<h2>Resources</h2>


			<div class="miguel-admin-page"><?php

				$selected_tab = $this->generate_tabs( $tabs, $admin_url, $tab );

				$action = $admin_url;

				if( ! empty( $tab ) )
					$action = $admin_url . '&tab=' . $tab;

				if( ! empty( $selected_tab ) ) {?>

					<form method="post" action="<?php echo $action; ?>"><?php
						require_once( dirname(__FILE__) . '/views/' . $selected_tab->view );?>
					</form><?php
				}?>

			</div>
		</div><?php
	}

	protected function generate_tabs( $tabs, $admin_url, $tab ) {

		$selected_tab = null;?>

		<h2 class="nav-tab-wrapper"><?php

			foreach ($tabs as $key => $value) {

				$href = $admin_url;
				if( ! empty($key) )
					 $href = $admin_url . '&tab=' . $key;

				$class = $key === $tab ? ' nav-tab-active' : '';

				if( ! empty( $class ) )
					$selected_tab = $value;?>

				<a href="<?php echo $href; ?>" class="nav-tab<?php echo $class; ?>"><?php _e( $value->title, MIGUEL_DOMAIN ); ?></a><?php
			}?>
		</h2><?php

		return $selected_tab;
	}
}
