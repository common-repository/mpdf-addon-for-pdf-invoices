<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              https://www.webtoffee.com/
 *
 * @wordpress-plugin
 * Plugin Name:       mPDF addon for PDF Invoices
 * Plugin URI:        https://wordpress.org/plugins/mpdf-addon-for-pdf-invoices/
 * Requires Plugins:  woocommerce
 * Description:       mPDF add-on for WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels
 * Version:           1.2.3
 * Author:            WebToffee
 * Author URI:        https://www.webtoffee.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mpdf-addon-for-pdf-invoices
 * Domain Path:       /languages
 * WC tested up to:   8.9
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 *  Declare compatibility with custom order tables for WooCommerce.
 * 
 *  @since 1.0.3
 *  
 */
add_action(
    'before_woocommerce_init',
    function () {
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }
);

if(!function_exists('activate_wt_woocommerce_packing_list_mpdf'))
{
	register_activation_hook( __FILE__, 'activate_wt_woocommerce_packing_list_mpdf' );
	function activate_wt_woocommerce_packing_list_mpdf()
	{
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		/**
		*	Check PDF, Gift card, Request quote plugin is installed
		* 
		* 	@since 	1.0.7 	Checking added for Gift cards plugin. 
		*/
		if((!is_plugin_active('wt-woocommerce-packing-list/wf-woocommerce-packing-list.php')  
			&& !is_plugin_active('print-invoices-packing-slip-labels-for-woocommerce/print-invoices-packing-slip-labels-for-woocommerce.php') 
			&& !is_plugin_active('wt-woocommerce-gift-cards/wt-woocommerce-gift-cards.php')
			&& !is_plugin_active('wt-gift-cards-woocommerce/wt-gift-cards-woocommerce.php')
			&& !is_plugin_active('wt-woo-request-quote/wt-woo-request-quote.php')) 
			&& !isset($_GET['wt_pklist_mpdf_force_activate']))
		{
			$get_arr=array_map('sanitize_text_field', $_GET);
			$continue_url=admin_url("plugins.php?".http_build_query($get_arr).'&wt_pklist_mpdf_force_activate=1');
			$skip_url=admin_url("plugins.php");
			$download_url='https://wordpress.org/plugins/print-invoices-packing-slip-labels-for-woocommerce/';
			$gc_download_url='https://www.webtoffee.com/product/woocommerce-gift-cards/';
			$wtwraq_download_url='https://www.webtoffee.com/product/woocommerce-request-a-quote/';
			

			$str=sprintf(__("%s The plugin is an addon for the `WooCommerce PDF Invoice by WebToffee`, `WebToffee WooCommerce Gift Cards`, `WebToffee WooCommerce Request a Quote` and currently works only with these plugins. %s", "mpdf-addon-for-woocommerce-pdf-invoices"), '<span style="font-weight:bold; font-size:16px; display:inline-block; margin-bottom:15px;">', '</span>');
			$str.='<br />';
			$str.=sprintf(__('%s Continue activation %s', "mpdf-addon-for-woocommerce-pdf-invoices"), '<a href="'.esc_attr($continue_url).'">', '</a>');
			$str.='&nbsp; | &nbsp;';
			$str.=sprintf(__('%s Skip activation %s', "mpdf-addon-for-woocommerce-pdf-invoices"), '<a href="'.esc_attr($skip_url).'">', '</a>');
			$str.='&nbsp; | &nbsp;';
			$str.=sprintf(__('%s Download WooCommerce PDF Invoice by WebToffee %s', "mpdf-addon-for-woocommerce-pdf-invoices"), '<a href="'.esc_attr($download_url).'" target="_blank">', '</a>');
			$str.='&nbsp; | &nbsp;';
			$str.=sprintf(__('%s Download WebToffee WooCommerce Gift Cards %s', "mpdf-addon-for-woocommerce-pdf-invoices"), '<a href="'.esc_attr($gc_download_url).'" target="_blank">', '</a>');
			$str.='&nbsp; | &nbsp;';
			$str.=sprintf(__('%s Download WebToffee WooCommerce Request a Quote %s', "mpdf-addon-for-woocommerce-pdf-invoices"), '<a href="'.esc_attr($wtwraq_download_url).'" target="_blank">', '</a>');
			wp_die($str);
		}
	}
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(!is_plugin_active('wt-woocommerce-packing-list/wf-woocommerce-packing-list.php')  
	&& !is_plugin_active('print-invoices-packing-slip-labels-for-woocommerce/print-invoices-packing-slip-labels-for-woocommerce.php') 
	&& !is_plugin_active('wt-woocommerce-gift-cards/wt-woocommerce-gift-cards.php')
	&& !is_plugin_active('wt-gift-cards-woocommerce/wt-gift-cards-woocommerce.php')
	&& !is_plugin_active('wt-woo-request-quote/wt-woo-request-quote.php')) /* checking PDF, Gift card, request quote plugin is installed */
{
	return;
}

if(!defined('WT_PKLIST_MPDF_VERSION')) //check plugin file already included
{
    define('WT_PKLIST_MPDF_PLUGIN_DEVELOPMENT_MODE', false );
    define('WT_PKLIST_MPDF_PLUGIN_BASENAME', plugin_basename(__FILE__) );
    define('WT_PKLIST_MPDF_PLUGIN_PATH', plugin_dir_path(__FILE__) );
    define('WT_PKLIST_MPDF_PLUGIN_URL', plugin_dir_url(__FILE__));
    define('WT_PKLIST_MPDF_PLUGIN_FILENAME',__FILE__);
    define('WT_PKLIST_MPDF_SETTINGS_FIELD','Wt_Woocommerce_Packing_List_Mpdf');
    define('WT_PKLIST_MPDF_PLUGIN_NAME','wt-woocommerce-packing-list-mpdf');
    define('WT_PKLIST_MPDF_PLUGIN_DESCRIPTION','WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels MPDF add-on');
    /**
     * Currently plugin version.
     */
    define( 'WT_PKLIST_MPDF_VERSION', '1.2.3' );
}else
{
	return;
}

/**
 * Collect uninstall feedback
 * @since 1.0.3 
 */

require_once plugin_dir_path( __FILE__ ) . 'includes/class-wt-mpdf-uninstall-feedback.php';

/**
 * Review seeking banner
 * @since 1.0.3 
 */

require_once plugin_dir_path(__FILE__) . 'includes/class-wt-mpdf-review_request.php';

if(!class_exists('Wt_Pklist_Mpdf_Addon'))
{
	class Wt_Pklist_Mpdf_Addon
	{
		public function __construct()
		{
			/**
			 * Invoice plugin related functionalities. Checks invoice plugins (Pro, Basic) are active
			 */
			if(is_plugin_active('wt-woocommerce-packing-list/wf-woocommerce-packing-list.php')  
			|| is_plugin_active('print-invoices-packing-slip-labels-for-woocommerce/print-invoices-packing-slip-labels-for-woocommerce.php'))
			{
				add_action('plugins_loaded', array($this, 'init'));
			}

			
			/**
			 * 	To return mPDF library info. 
			 * 	Dependent plugins can call `apply_filters` function for this hook to get library info
			 * 	Return format: 	array(
	         *      				'file'	=> Library main file path,
	         *      				'class'	=> Library main file class name,
	         *     					'title'	=> Library title,
		     *					);
			 * 		
			 * 	
			 * 	@since 1.0.7
			 */
			add_filter('wt_pklist_mpdf_get_lib_info', array($this, 'get_mpdf_info'));
		}

		public function init()
		{
			/* Pro v4.0.9 or above, Basic v2.6.7 or above */
			$min_required_version=(is_plugin_active('wt-woocommerce-packing-list/wf-woocommerce-packing-list.php') ? '4.0.9' : '2.6.7');

			/**
			*	If minimum required version of Invoice plugin is not installed then return.
			*/
			if(!version_compare(WF_PKLIST_VERSION, $min_required_version, '>='))
			{
			   return;
			}

			
			/* add MPDF to PDF libraries */
			add_filter('wt_pklist_alter_pdf_libraries', array($this, 'alter_pdf_libraries'));

			$active_pdf_lib=Wf_Woocommerce_Packing_List::get_option('active_pdf_library');
			if('mpdf' === $active_pdf_lib)
			{
				/* disable product table column reverse option */
				add_filter('wf_pklist_enable_product_table_columns_reverse', '__return_false');

				add_filter('wf_pklist_is_rtl_for_pdf', '__return_false');
			
				/* hiding of table elements via css will not work in MPDF. Position absolute for inner blocks have no support in MPDF */
				add_filter('wt_pklist_alter_final_order_template_html', array($this, 'alter_final_order_template_html'), 10, 6);
			}
			
		}

		/**
		*	@since 1.0.0
		*	Remove hidden elements. MPDF will not support hidden table elements
		*	Position absolute for inner blocks have no support in MPDF. So we have to remove the `Recieved stamp` HTML
		* 	@since 1.0.4 [Bug fix] Table body style not applying to inner td elements
		*/
		public function alter_final_order_template_html($html, $template_type, $order, $box_packing, $order_package, $template_for_pdf)
		{
			include_once "classes/simple_html_dom.php"; /* include simple HTML dom library */
			$html_dom=Wt_Pklist_Mpdf_Addon\str_get_html($html);
			
			if( empty( $html_dom ) ){
				return $html;
			}
			
			$html_dom=Wt_Pklist_Mpdf_Addon\str_get_html($html);
			if(strpos($html, 'wfte_hidden')!==false) /* some hidden elements are there */
			{				
				foreach($html_dom->find('.wfte_hidden') as $elm) 
				{
				    $elm->remove();
				}				
			}
		
			if($template_for_pdf && !empty( $html_dom ) )
			{
				/**
				*	Remove recieved seal element when preparing PDF
				*/
				$received_seal_elm=$html_dom->find('.wfte_received_seal', 0);
				if($received_seal_elm)
				{
					//$recieved_text=$received_seal_elm->innertext;
					$received_seal_elm->remove();
				}

				/**
				*	Convert invoice data div to table
				*/
				$table_html_arr=array();
				$multi_column=false;
				$single_column=false;
				$invoice_data_elm=$html_dom->find('.wfte_invoice_data, .wfte_order_data');
				if(is_rtl()){
					foreach($invoice_data_elm as $main_elm)
					{
						if($main_elm->tag=='div') //only div element.
						{
							foreach($main_elm->children() as $elm)
							{
								if($elm->tag=='div') /* only if div */
								{
									$this->prepare_table_html_array($elm, $multi_column, $single_column, $table_html_arr);

									$table_html=$this->prepare_table_html_from_array($table_html_arr, $elm, ($multi_column && $single_column));
									$elm->outertext=$table_html;
									$table_html_arr=array(); //reset table html arr
									$multi_column=false;
									$single_column=false;
								}
							}
						}
					}
				}

				$footer_elem = $html_dom->find('.template_footer');
				if(is_rtl()){
					foreach($footer_elem as $main_elm)
					{
						if($main_elm->tag=='div') //only div element.
						{
							foreach($main_elm->children() as $elm)
							{
								if($elm->tag=='div') /* only if div */
								{
									$this->prepare_table_html_array($elm, $multi_column, $single_column, $table_html_arr);

									$table_html=$this->prepare_table_html_from_array($table_html_arr, $elm, ($multi_column && $single_column));
									$elm->outertext=$table_html;
									$table_html_arr=array(); //reset table html arr
									$multi_column=false;
									$single_column=false;
								}
							}
						}
					}
				}
				/**
				*	Convert extra data div to table
				*/
				$extra_fields_elm=$html_dom->find('.wfte_extra_fields');
				if(is_rtl()){
					foreach($extra_fields_elm as $elm)
					{
						if($elm->tag=='div') /* only if div */
						{
							$this->prepare_table_html_array($elm, $multi_column, $single_column, $table_html_arr);

							$table_html=$this->prepare_table_html_from_array($table_html_arr, $elm, ($multi_column && $single_column));
							$elm->outertext=$table_html;
							$table_html_arr=array(); //reset table html arr
							$multi_column=false;
							$single_column=false;
						}
					}
				}

				/**
				 * 	Applying style of tbody to inner td elements
				 */
				$product_table_body_elm=$html_dom->find('.wfte_product_table_body', 0);
				if($product_table_body_elm)
				{
					$product_table_body_style=$product_table_body_elm->style;
					if($product_table_body_style)
					{	
						$product_table_body_elm_children=$product_table_body_elm->children(0)->children();
						if ( $product_table_body_elm_children ) {
							foreach($product_table_body_elm_children as $td_elm)
							{
								if( 'td' === $td_elm->tag )
								{
									$td_elm->style=$product_table_body_style;
								}
							}
						}
					}
				}				
			}

			if( !empty( $html_dom ) ){
				$html=$html_dom->outertext;
				$html_dom->clear();
			}
			
			return $html;
		}

		/**
		*	@since 1.0.0 Prepare array for table
		*/
		private function prepare_table_html_array($elm, &$multi_column, &$single_column, &$table_html_arr)
		{
			$div_child=$elm->find('div');
			$span_child=$elm->find('span');
			$elm_html=trim($elm->innertext);

			/* sometimes HTML dom failed to get actual count */
			$span_child_count=substr_count($elm_html, '<span');
			$div_child_count=substr_count($elm_html, '<div');


			if($span_child_count>0 || $div_child_count>0) /* div or span */
			{
				$child_count=$span_child_count+$div_child_count;
				foreach($elm->children() as $child_elm)
				{
					$child_tag=$child_elm->tag;
					if($child_tag=='div' || $child_tag=='span') //wait to get div, span
					{
						$pos=strpos($elm_html, '<'.$child_tag);
						if($pos===0) //on first pos
						{
							$child_outer_text=$child_elm->outertext;
							$rest_text=str_replace($child_outer_text, '', $elm_html);
							$table_html_arr[]=array($child_outer_text, $rest_text);							
						}else
						{
							if($child_count==1)
							{
								$innertext_arr=explode('<'.$child_tag, $elm_html); 
								$table_html_arr[]=array($innertext_arr[0], '<'.$child_tag.$innertext_arr[1]);
							}else
							{
								$label=substr($elm_html, 0, ($pos+1)).$child_elm->outertext;
								$rest_text=str_replace($label, '', $elm_html);
								$table_html_arr[]=array($label, $rest_text);
							}
						}
						break;
					}
				}				

				$multi_column=true; /* for colspan need checking */
			}else
			{
				$table_html_arr[]=array($elm_html);
				$single_column=true; /* for colspan need checking */
			}

		}

		/**
		*	@since 1.0.0 Prepare table HTML from array
		*/
		private function prepare_table_html_from_array($table_html_arr, $elm, $colspan_needed=false)
		{
			$class_attr=($elm->class ? ' class="'.$elm->class.'"' : '');
			$table_html='<table '.$class_attr.'>';
			foreach ($table_html_arr as $tr_data)
			{
				$colspan_attr=((count($tr_data)==1 && $colspan_needed) ? ' colspan="2"' : '');
				$table_html.='<tr>';
				$table_html.='<td'.$colspan_attr.' class="wfte_invoice-header_color">'.implode('</td><td class="wfte_invoice-header_color">', $tr_data).'</td>';
				$table_html.='</tr>';
			}
			$table_html.='</table>';

			return $table_html;
		}

		/**
		*	@since 1.0.0 Add MPDF to PDF generating libraries
		*/
		public function alter_pdf_libraries($pdf_libs)
		{
			$pdf_libs['mpdf'] = $this->get_mpdf_info();
			return $pdf_libs;
		}
		
		
		/**
		 * 	Details of mPDF library
		 * 	This function is also used as a callback for `wt_pklist_mpdf_get_lib_info` filter
		 * 	
		 * 	@since 	1.0.7
		 * 	@return array 	mPDF details	
		 */
		public function get_mpdf_info($arr = array())
		{
			return array(
                'file'	=> WT_PKLIST_MPDF_PLUGIN_PATH.'classes/class-mpdf.php', //library main file
                'class'	=> 'Wt_Pklist_Mpdf', //class name
                'title'	=> 'Mpdf', //This is for settings section
	        );
		}
	}
	new Wt_Pklist_Mpdf_Addon();
}