<?php
/**
 * Uninstall Feedback
 *
 * @link       
 * @since 1.0.3     
 *
 */
if (!defined('ABSPATH')) {
    exit;
}
class Wt_Pklist_Mpdf_Uninstall_Feedback
{
	protected $api_url='https://feedback.webtoffee.com/wp-json/wtmpdf/v1/uninstall';
    protected $current_version=WT_PKLIST_MPDF_VERSION;
    protected $auth_key='wtmpdf_uninstall_1234#';
    protected $plugin_id='wtmpdf';
    public function __construct()
	{
        add_action('admin_footer', array($this,'deactivate_scripts'));
        add_action('wp_ajax_wtmpdf_submit_uninstall_reason', array($this,"send_uninstall_reason"));
        add_filter('plugin_action_links_'.plugin_basename(WT_PKLIST_MPDF_PLUGIN_FILENAME),array($this,'plugin_action_links'));
    }
    public function plugin_action_links($links) 
	{
		if(array_key_exists('deactivate',$links))
		{
            $links['deactivate']=str_replace('<a', '<a class="wtmpdf-deactivate-link"',$links['deactivate']);
        }
		return $links;
	}
    private function get_uninstall_reasons()
    {
        $reasons = array(
            array(
                'id' => 'no-language-support',
                'text' => __('Language support issues', 'mpdf-addon-for-woocommerce-pdf-invoices'),
                'type' => 'text',
                'placeholder' => __('Which language?', 'mpdf-addon-for-woocommerce-pdf-invoices')
            ),
            array(
                'id' => 'found-better-plugin',
                'text' => __('I found a better plugin', 'mpdf-addon-for-woocommerce-pdf-invoices'),
                'type' => 'text',
                'placeholder' => __('Which plugin?', 'mpdf-addon-for-woocommerce-pdf-invoices')
            ),
            array(
                'id' => 'not-have-that-feature',
                'text' => __('The plugin is great, but I need specific feature that you don\'t support', 'mpdf-addon-for-woocommerce-pdf-invoices'),
                'type' => 'textarea',
                'placeholder' => __('Could you tell us more about that feature?', 'mpdf-addon-for-woocommerce-pdf-invoices')
            ),
            array(
                'id' => 'did-not-work-as-expected',
                'text' => __('Plugin does not work as expected.', 'mpdf-addon-for-woocommerce-pdf-invoices').' <span style="display:inline-block;"> '.sprintf(__('%sWooCommerce PDF Invoices by WebToffee%s plugin is already active.', 'mpdf-addon-for-woocommerce-pdf-invoices'), '<a href="https://wordpress.org/plugins/print-invoices-packing-slip-labels-for-woocommerce/" target="_blank">', '</a>').'</span>',
                'type' => 'textarea',
                'placeholder' => __('The mPDF plugin is strictly an add-on for the WooCommerce PDF Invoices by WebToffee plugin. Assuming that this plugin is also active, please share more info regarding the issue.', 'mpdf-addon-for-woocommerce-pdf-invoices')
            ),
            array(
                'id' => 'other',
                'text' => __('Other', 'mpdf-addon-for-woocommerce-pdf-invoices'),
                'type' => 'textarea',
                'placeholder' => __('Could you tell us a bit more?', 'mpdf-addon-for-woocommerce-pdf-invoices')
            ),
        );

        return $reasons;
    }

    public function deactivate_scripts()
    {
        global $pagenow;
        if('plugins.php' != $pagenow)
        {
            return;
        }
        $reasons = $this->get_uninstall_reasons();
        ?>
        <div class="wtmpdf-modal" id="wtmpdf-wtmpdf-modal">
            <div class="wtmpdf-modal-wrap">
                <div class="wtmpdf-modal-header">
                    <h3><?php _e('If you have a moment, please let us know why you are deactivating:', 'mpdf-addon-for-woocommerce-pdf-invoices'); ?></h3>
                </div>
                <div class="wtmpdf-modal-body">
                    <ul class="reasons">
                        <?php 
                        foreach ($reasons as $reason) 
                        { 
                        ?>
                            <li data-type="<?php echo esc_attr($reason['type']); ?>" data-placeholder="<?php echo esc_attr(isset($reason['placeholder']) ? $reason['placeholder'] : ''); ?>">
                                <label><input type="radio" name="selected-reason" value="<?php echo esc_attr($reason['id']); ?>"><?php echo wp_kses_post($reason['text']); ?></label>
                            </li>
                        <?php 
                        } 
                        ?>
                    </ul>

                    <div class="wt_pklist_policy_infobox">
                        <?php _e("We do not collect any personal data when you submit this form. It's your feedback that we value.", "mpdf-addon-for-woocommerce-pdf-invoices");?>
                        <a href="https://www.webtoffee.com/privacy-policy/" target="_blank"><?php _e('Privacy Policy', 'mpdf-addon-for-woocommerce-pdf-invoices');?></a>        
                    </div>
                </div>
                <div class="wtmpdf-modal-footer">
                    <a class="button-primary" href="https://www.webtoffee.com/support/" target="_blank">
                        <span class="dashicons dashicons-external" style="margin-top:3px;"></span> 
                        <?php _e('Go to support', 'mpdf-addon-for-woocommerce-pdf-invoices'); ?></a>
                    <button class="button-primary wtmpdf-model-submit"><?php _e('Submit & Deactivate', 'mpdf-addon-for-woocommerce-pdf-invoices'); ?></button>
                    <button class="button-secondary wtmpdf-model-cancel"><?php _e('Cancel', 'mpdf-addon-for-woocommerce-pdf-invoices'); ?></button>
                    <a href="#" class="dont-bother-me"><?php _e('I rather wouldn\'t say', 'mpdf-addon-for-woocommerce-pdf-invoices'); ?></a>
                </div>
            </div>
        </div>
        <style type="text/css">
            .wtmpdf-modal {
                position: fixed;
                z-index: 99999;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background: rgba(0,0,0,0.5);
                display: none;
            }
            .wtmpdf-modal.modal-active {display: block;}
            .wtmpdf-modal-wrap {
                width: 50%;
                position: relative;
                margin: 10% auto;
                background: #fff;
            }
            .wtmpdf-modal-header {
                border-bottom: 1px solid #eee;
                padding: 8px 20px;
            }
            .wtmpdf-modal-header h3 {
                line-height: 150%;
                margin: 0;
            }
            .wtmpdf-modal-body {padding: 5px 20px 5px 20px;}
            .wfinvoice-modal-body .input-text,.wtmpdf-modal-body textarea {width:75%;}
            .wtmpdf-modal-body .input-text::placeholder,.wtmpdf-modal-body textarea::placeholder{ font-size:12px; }
            .wtmpdf-modal-body .reason-input {
                margin-top: 5px;
                margin-left: 20px;
            }
            .wtmpdf-modal-footer {
                border-top: 1px solid #eee;
                padding: 12px 20px;
                text-align: left;
            }
            .wt_pklist_policy_infobox{font-style:italic; text-align:left; font-size:12px; color:#aaa; line-height:14px; margin-top:35px;}
            .wt_pklist_policy_infobox a{ font-size:11px; color:#4b9cc3; text-decoration-color: #99c3d7; }
            .sub_reasons{ display:none; margin-left:15px; margin-top:10px; }
            a.dont-bother-me{ color:#939697; text-decoration-color:#d0d3d5; float:right; margin-top:7px; }
            .reasons li{ padding-top:5px; }
        </style>
        <script type="text/javascript">
            (function ($) {
                $(function () {
                    var modal = $('#wtmpdf-wtmpdf-modal');
                    var deactivateLink = '';
                    $('#the-list').on('click', 'a.wtmpdf-deactivate-link', function (e) {
                        e.preventDefault();
                        modal.addClass('modal-active');
                        deactivateLink = $(this).attr('href');
                        modal.find('a.dont-bother-me').attr('href', deactivateLink);
                        modal.find('input[type="radio"]:checked').prop('checked', false);
                    });
                    modal.on('click', 'button.wtmpdf-model-cancel', function (e) {
                        e.preventDefault();
                        modal.removeClass('modal-active');
                    });
                    modal.on('click', 'input[type="radio"]', function () {
                        var reason_id=$(this).val();
                        var parent = $(this).parents('li:first');
                        var inputType = parent.data('type');
                        modal.find('.reason-input').remove();
                        if($(this).attr('name')=='selected-reason')
                        {
                            modal.find('.sub_reasons').hide();
                        }
                        if(inputType=='main_reason')
                        {
                            modal.find('.sub_reasons[data-parent="'+reason_id+'"]').show();
                            modal.find('.sub_reasons[data-parent="'+reason_id+'"] input[type="radio"]:checked').trigger('click');
                        }else
                        {
                            var inputPlaceholder = parent.data('placeholder'),
                            reasonInputHtml = '<div class="reason-input">' + (('text' === inputType) ? '<input type="text" class="input-text" size="40" />' : '<textarea rows="5" cols="45"></textarea>') + '</div>';

                            if(inputType !== '')
                            {
                                parent.append($(reasonInputHtml));
                                parent.find('input, textarea').attr('placeholder', inputPlaceholder).focus();
                            }
                        }
                    });

                    modal.on('click', 'button.wtmpdf-model-submit', function (e) {
                        e.preventDefault();
                        var button = $(this);
                        if (button.hasClass('disabled')) {
                            return;
                        }
                        var reason_id='none';
                        var reason_info='';

                        var $radio = $('input[type="radio"][name="selected-reason"]:checked', modal);
                        if($radio.length>0)
                        {
                            reason_id=$radio.val();
                            var $selected_reason = $radio.parents('li:first');
                            if($selected_reason.attr('data-type')=="main_reason")
                            {
                                var sub_reason=$selected_reason.find('.sub_reasons');
                                var sub_reason_input=sub_reason.find('input[type="radio"][name="selected-sub-reason"]:checked');
                                if(sub_reason_input.length>0)
                                {
                                    reason_id+=' | '+sub_reason_input.val();
                                    var sub_reason_info_input = sub_reason_input.parents('li:first').find('textarea, input[type="text"]');
                                    if(sub_reason_info_input.length>0)
                                    {
                                        reason_info=$.trim(sub_reason_info_input.val());
                                    }
                                }
                            }else
                            {
                                var reason_info_input=$selected_reason.find('textarea, input[type="text"]');
                                if(reason_info_input.length>0)
                                {
                                    reason_info=$.trim(reason_info_input.val());
                                }
                            }  
                        }

                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'wtmpdf_submit_uninstall_reason',
                                _wpnonce: '<?php echo wp_create_nonce(WT_PKLIST_MPDF_PLUGIN_NAME);?>',
                                reason_id: reason_id,
                                reason_info: reason_info
                            },
                            beforeSend: function () {
                                button.addClass('disabled');
                                button.text('Processing...');
                            },
                            complete: function () {
                                window.location.href = deactivateLink;
                            }
                        });
                    });
                });
            }(jQuery));
        </script>
        <?php
    }

    public function send_uninstall_reason()
    {
        global $wpdb;
        $nonce=isset($_POST['_wpnonce']) ? sanitize_text_field($_POST['_wpnonce']) : ''; 
        if(!(wp_verify_nonce($nonce,WT_PKLIST_MPDF_PLUGIN_NAME)))
        {   
            wp_send_json_error();
        }
        if(!isset($_POST['reason_id']))
        {
            wp_send_json_error();
        }
        //$current_user = wp_get_current_user();
        $data = array(
            'reason_id'         => sanitize_text_field($_POST['reason_id']),
            'plugin'            => $this->plugin_id,
            'auth'              => $this->auth_key,
            'date'              => gmdate("M d, Y h:i:s A"),
            'url'               => '',
            'user_email'        => '',
            'reason_info'       => isset($_REQUEST['reason_info']) ? trim(stripslashes(sanitize_text_field($_REQUEST['reason_info']))) : '',
            'software'          => $_SERVER['SERVER_SOFTWARE'],
            'php_version'       => phpversion(),
            'mysql_version'     => $wpdb->db_version(),
            'wp_version'        => get_bloginfo('version'),
            'wc_version'        => (!defined('WC_VERSION')) ? '' : WC()->version,
            'locale'            => get_locale(),
            'multisite'         => is_multisite() ? 'Yes' : 'No',
            'wtmpdf_version'    =>$this->current_version,
        );
        // Write an action/hook here in webtoffe to recieve the data
        $resp = wp_remote_post($this->api_url, array(
            'method'        => 'POST',
            'timeout'       => 45,
            'redirection'   => 5,
            'httpversion'   => '1.0',
            'blocking'      => false,
            'body'          => $data,
            'cookies'       => array()
                )
        );
        wp_send_json_success();
    }
}
new Wt_Pklist_Mpdf_Uninstall_Feedback();