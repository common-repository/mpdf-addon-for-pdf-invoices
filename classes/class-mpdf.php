<?php
/**
 * mPDF library
 *
 * @link       
 * @since 1.0.0     
 * 
 */
if (!defined('ABSPATH')) {
    exit;
}
class Wt_Pklist_Mpdf
{
    public $mpdf=null;
    public $page_properties = array();
    public $mpdf_config = array();
    public function __construct($config = array(),$template_type='')
    {
        include_once(WT_PKLIST_MPDF_PLUGIN_PATH . 'vendor/autoload.php');
        $this->page_properties = array(
                'mode'          => 'utf-8',
                'margin_left'   => 5,
                'margin_right'  => 5,
                'margin_top'    => 5,
                'margin_bottom' => 5,
                'margin_header' => 0,
                'margin_footer' => 0,
                'orientation'   => 'P',
                'format'        => 'A4'
        );
        $this->mpdf_config = $config;
        $config = wp_parse_args(
            $config,
            $this->page_properties,
        );
        // initiate mpdf class
        $this->mpdf = new \Mpdf\Mpdf($config); 

    }

    /**
    *   @since 1.0.0 Generate PDF from HTML
    */
    public function generate($upload_dir, $html, $action, $is_preview, $file_path, $args=array())
    {
        $upload_loc=Wf_Woocommerce_Packing_List::get_temp_dir();
        $plugin_upload_folder=$upload_loc['path'];
        $template_type = str_replace($plugin_upload_folder,'',$upload_dir);
        $template_type = str_replace('/','',$template_type);
        $this->page_properties = apply_filters('wt_pklist_alter_page_properties_in_mpdf',$this->page_properties,$template_type);

        $new_config = wp_parse_args(
            $this->mpdf_config,
            $this->page_properties,
        );
        
        $this->mpdf = new \Mpdf\Mpdf($new_config); 
        $html_file_arr = explode('<!DOCTYPE html>', $html);
        $this->mpdf->tempDir = $upload_dir;
        $this->mpdf->autoScriptToLang = true;
        $this->mpdf->autoLangToFont = true;

        if(Wf_Woocommerce_Packing_List_Admin::is_enable_rtl_support()) /* checks the current language need RTL support */
        {
            $this->mpdf->SetDirectionality('rtl');
        }

        $mpdf_water_mark=array(
            'status'=>false,
            'text'=>__('Recieved', 'mpdf-addon-for-woocommerce-pdf-invoices')
        );
        $mpdf_water_mark=apply_filters('wt_pklist_mpdf_water_mark', $mpdf_water_mark);
        ob_start();
        for($i=0; $i<count($html_file_arr); $i++){
            if(trim($html_file_arr[$i]) != ""){
                $actual_html = '<!DOCTYPE html>'.$html_file_arr[$i];
                $this->mpdf->WriteHTML($actual_html);
                if(is_array($mpdf_water_mark) && isset($mpdf_water_mark['status']) && $mpdf_water_mark['status']===true)
                {
                    $text=(isset($mpdf_water_mark['text']) ? $mpdf_water_mark['text'] : __('Recieved', 'mpdf-addon-for-woocommerce-pdf-invoices'));

                    $this->mpdf->SetWatermarkText($text);
                    $this->mpdf->showWatermarkText = true;
                }
                if($i != (count($html_file_arr)-1)){
                    $this->mpdf->AddPage();
                }
            }
        }

        $output_type='F'; //save to file
        if($action=='download' || $action=='preview')
        {
            $output_type='I'; //inline output
            if($action=='download' && !$is_preview)
            {
                $output_type='D'; //force download
                $file_path=basename($file_path);
            }
        }
        ob_end_clean();
        $this->mpdf->Output($file_path, $output_type);
        return true;    
    }

    
    /**
     *  Generate PDF from HTML. Method for plugins other than invoice plugin
     * 
     *  @since 1.0.7
    */
    public function generate_pdf($args)
    {
        $args = wp_parse_args(
            $args,
            array(
                'html'      => '',
                'file_path' => '',
                'action'    => 'save', //save, download, preview
            )
        );

        if("" === $args['html'] || "" === $args['file_path'])
        {
            return false;
        }

        $this->mpdf->tempDir = dirname($args['file_path']);
        $this->mpdf->autoScriptToLang = true;
        $this->mpdf->autoLangToFont = true;

        if(isset($args['rtl']) && $args['rtl'])
        {
            $this->mpdf->SetDirectionality('rtl');
        }

        $html_file_arr = explode('<!DOCTYPE html>', $args['html']); //check for multiple docs        
        
        for($i = 0; $i<count($html_file_arr); $i++)
        {
            if("" !== trim($html_file_arr[$i]))
            {
                $this->mpdf->WriteHTML('<!DOCTYPE html>' . $html_file_arr[$i]);

                if($i !== (count($html_file_arr) - 1))
                {
                    $this->mpdf->AddPage();
                }
            }
        }

        switch($args['action'])
        {
            case 'save':
                $output_type = 'F'; //save to file
                break;

            case 'download':
                $output_type = 'D'; //force download
                $file_path = basename($args['file_path']);
                break;
            
            default:
                $output_type='I'; //inline output
                break;
        }

        $this->mpdf->Output($args['file_path'], $output_type);

        return true;    
    }
}