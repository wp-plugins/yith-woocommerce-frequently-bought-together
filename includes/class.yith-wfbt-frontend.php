<?php
/**
 * Frontend class
 *
 * @author Yithemes
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WFBT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WFBT_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WFBT_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WFBT_Frontend
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WFBT_VERSION;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WFBT_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			// enqueue scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'woocommerce_after_single_product_summary', array( $this, 'add_bought_together_form' ), 1 );
		}

		/**
		 * Enqueue scripts
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function enqueue_scripts(){

			wp_enqueue_style( 'yith-wfbt-style', YITH_WFBT_ASSETS_URL . '/css/yith-wfbt.css' );

			$background         = get_option( "yith-wfbt-button-color" );
			$background_hover   = get_option( "yith-wfbt-button-color-hover" );
			$text_color         = get_option( "yith-wfbt-button-text-color" );
			$text_color_hover   = get_option( "yith-wfbt-button-text-color-hover" );

			$inline_css = "
                .yith-wfbt-submit-block .yith-wfbt-submit-button {
                        background: {$background};
                        color: {$text_color};
                }
                .yith-wfbt-submit-block .yith-wfbt-submit-button:hover {
                        background: {$background_hover};
                        color: {$text_color_hover};
                }";

			wp_add_inline_style( 'yith-wfbt-style', $inline_css );
		}

		/**
		 * Form Template
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function add_bought_together_form(){

			global $product;

			// get meta for current product
			$group  = get_post_meta( $product->id, YITH_WFBT_META, true );

			if( empty( $group ) || $product->product_type == 'grouped' || $product->product_type == 'external' ) {
				return;
			}

			$product_id = $product->id;

			if( $product->product_type == 'variable' ) {

				$variations = $product->get_children();

				if( empty( $variations ) ) {
					return;
				}
				// get first product variation
				$product_id = array_shift( $variations );
			}

			// merge array
			$group = array_merge( array( $product_id ), $group );

			wc_get_template( 'yith-wfbt-form.php', $group, '', YITH_WFBT_DIR . 'templates/' );
		}
	}
}
/**
 * Unique access to instance of YITH_WFBT_Frontend class
 *
 * @return \YITH_WFBT_Frontend
 * @since 1.0.0
 */
function YITH_WFBT_Frontend(){
	return YITH_WFBT_Frontend::get_instance();
}