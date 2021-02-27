<?php
if(!defined('LIT_DIR')) define('LIT_DIR', get_stylesheet_directory());
if(!defined('LIT_URL')) define('LIT_URL', get_stylesheet_directory_uri());
class customCustom
{
    private static $instance;

    private $menu_names = [];

    private $submenu = [];

    public function __construct()
    {
        $this->register_hooks();
    }

    private function register_hooks()
    {
        /**
         * 取消 Wordpress 自動更新
         */
        add_filter('pre_site_transient_update_core', array($this, 'remove_core_updates'));
        add_filter('pre_site_transient_update_plugins', array($this, 'remove_core_updates'));
        add_filter('pre_site_transient_update_themes', array($this, 'remove_core_updates'));
        /**
         * 更改密碼強度限制
         */
        add_filter('woocommerce_min_password_strength', array($this, 'change_password_length'));
        /**
         * 載入後台CSS檔案
         */
        add_action('admin_head', array($this, 'load_admin_css_files'));
        /**
         * 從HTML的資源連結中移除網站網址資訊
         */
        add_action('get_header', array($this, 'remove_domain_from_html'));
        /**
         * 針對特定分類、靜態頁面、單一文章頁自動載入指定的CSS與JS檔案
         */
        add_action('wp_enqueue_scripts', array($this, 'load_type_css_js_files'), 102);
        /**
         * 自動載入全域CSS檔案
         */
        add_action('wp_enqueue_scripts', array($this, 'load_global_css_files'), 101);
        /**
         * 自動載入全域JS檔案
         */
        add_action('wp_enqueue_scripts', array($this, 'load_global_js_files'), 100);
        /**
         * 載入其他JS或CSS檔案
         */
        // add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        /**
         * 移除後台post列表頁不必要的欄位
         */
        add_action('admin_head', array($this, 'remove_unused_columns'));
        /**
         * 移除admin bar的wordpress logo
         */
        add_action('admin_bar_menu', array($this, 'remove_admin_bar_wordpress_logo'), 999);
        /**
         * 後台文章資料擴充
         */
        add_action( 'init', [$this,'customer_post_taxonomy'], 0 );//自定義分類
        add_action( 'add_meta_boxes', [$this, 'add_meta_boxes' ] ,20); //在後台編輯頁面增加設定的欄位
        add_action( 'save_post', [$this, 'save_data_for_custome_data'],1,2); //儲存post增加欄位

        /**
         * Other
         */

        add_action('yith_wcwl_before_wishlist',[$this,'add_export_form_wishlist']);//前台增加匯出按鈕
        add_action('init',[$this,'create_export_wishlist_file']);//產生匯出檔案

    }
    public function create_export_wishlist_file($wishlist){
        if (isset($_POST['export']) && $_POST['export'] == 'wishlist-true' && isset($_POST['product_ids']) && $_POST['product_ids'] != '') {
            $product_ids = json_decode($_POST['product_ids']);
            ob_start();
            $data_header = [
                'product_name'=>'產品名稱',
                'product_material'=>'材質',
                'product_diameter'=>'尺寸',
                'product_color'=>'顏色',
                'product_num'=>'數量(ex:100/月)',
            ];
            $data_set = array();
            $data_terms = array();
            foreach ( $product_ids as $product_id ) {
                global $product;
                $product = wc_get_product($product_id);
                $product_terms = get_the_terms($product->get_id(), 'filter-category');
                foreach ($product_terms as $term) {
                    $parent_term = get_term($term->parent);
                    $data_terms[$product->get_id()][$parent_term->slug] = $term->name;
                }
                ob_start();
                $data = [
                    'product_name' => $product->get_title(),
                    'product_material' => $data_terms[$product->get_id()]['material'],
                    'product_diameter' => $data_terms[$product->get_id()]['diameter'],
                    'product_color' => $data_terms[$product->get_id()]['color'],
                    'product_num' => ' ',
                ];
                ob_get_clean();
                array_push($data_set, $data);
            }
            ob_start();
            $stream = fopen('php://output' ,'w');
            fputs($stream, ( chr(0xEF) . chr(0xBB) . chr(0xBF) ) );
            fputcsv($stream, array_values($data_header));
            foreach ($data_set as $data ) {
                fputcsv($stream, array_values($data));
            }
            $csv = ob_get_clean();
            fclose($stream);
            ob_end_clean();

            $time = date('Y-m-d');
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename= 願望清單{$time}.csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            echo $csv;
            exit;
        }
    }
    public function customer_post_taxonomy(){
        register_taxonomy( 'filter-category', 'product', array(
            'label' => '詳細資訊分類', 'hierarchical' => true, 'meta_box_cb'=> false,
        ));
    }
    public function add_meta_boxes(){
        add_meta_box('product-filter-category','商品詳細資訊分類',[$this,'prodcut_filter_category'],'product','normal','core');
    }
    public function prodcut_filter_category(){
        global $post;
        $product_terms = wp_get_post_terms($post->ID,'filter-category',array( 'fields' => 'ids' ));
        $parent_terms = get_terms( 'filter-category', array( 'parent' => 0, 'orderby'  => 'term_order', 'order' => 'ASC', 'hide_empty' => false ) );
        foreach ($parent_terms as $parent){
    ?>
        <fieldset>
            <p class="form-field">
                <lable for="<?=$parent->slug?>"><?=$parent->name?></lable>
                <select id="<?=$parent->slug?>" name="filter_category[]">
                    <option value="">請選擇</option>
                    <?php
                        $children_terms = get_terms( 'filter-category', array( 'parent' => $parent->term_id, 'orderby'  => 'term_order', 'order' => 'ASC', 'hide_empty' => false ) );
                        foreach ($children_terms as $children){
                    ?>
                        <option value="<?=$children->term_id?>" <?=(in_array($children->term_id,$product_terms))?'selected':''?>><?=$children->name?></option>
                    <?php }?>
                </select>
            </p>
        </fieldset>
    <?php
        }
    }
    public function save_data_for_custome_data($post_id, $post ){
        if(isset($_POST['filter_category'])){
            wp_set_post_terms($post_id,$_POST['filter_category'],'filter-category');
        }
    }
    public function add_export_form_wishlist($wishlist){
        $wishlist_items = $wishlist->get_items();
        $product_ids = array();
        foreach ( $wishlist_items as $item ) {
            $product = $item->get_product();
            $product_ids []= $product->get_id();
        }
    ?>
        <form action="" method="post">
            <input type="hidden" name="export" value="wishlist-true">
            <input type="hidden" name="product_ids" value="<?=json_encode($product_ids);?>">
            <input type="submit" value="匯出">
        </form>
    <?php
    }



    public function remove_admin_bar_wordpress_logo( $wp_admin_bar ) {
        /** @var WP_Admin_Bar $wp_admin_bar */
        $wp_admin_bar->remove_node('wp-logo');

    }

    public static function get_instance()
    {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function rm_column_heading($columns)
    {
        $rm_column = array('search_weight', 'wpseo-links', 'wpseo-score', 'wpseo-score-readability', 'wpseo-title', 'wpseo-metadesc', 'wpseo-focuskw');
        foreach ($rm_column as $key => $value) {
            unset($columns[$value]);
        }
        return $columns;
    }

    public function remove_unused_columns()
    {
        foreach ( get_post_types() as $key ) {
            add_filter( 'manage_' . $key . '_posts_columns',
                array($this,'rm_column_heading'), 999, 1 );
        }
    }

    public function remove_core_updates(){
        global $wp_version;
        return(object) array('last_checked'=> time(),'version_checked'=> $wp_version);
    }


    public function change_password_length() {
        /**
         * 0: Disable
         * 1: Very weak
         * 2: Weak
         * 3: Strong (Default)
         */
        return 1;
    }

    public function load_admin_css_files()
    {
        $style_uri = get_stylesheet_directory_uri();
        $css_file = '/assets/css/admin-css.css';
        $min_css_file = '/assets/css/admin-css.css';
        if(file_exists(get_stylesheet_directory() . $min_css_file))
            wp_enqueue_style("custom_admin_css", $style_uri . $min_css_file);
        elseif(file_exists(get_stylesheet_directory() . $css_file))
            wp_enqueue_style("custom_admin_css", $style_uri . $css_file);
    }

    public function remove_domain_from_html()
    {
        global $CONFIG;
        if(isset($CONFIG['domains_to_be_removed'])) {
            ob_start(function($html) use($CONFIG){
                foreach($CONFIG['domains_to_be_removed'] as $url) {
                    $url = str_replace('.', '\.', $url);
                    $patterns = [
                        "/https?:\/\/$url\/*/",
                        "/\/\/$url\/*/",
                    ];
                    $html = preg_replace($patterns, '/', $html);
                }
                return $html;
            });
        }
    }

    /**
     * 針對特定分類、靜態頁面、單一文章頁自動載入指定的CSS與JS檔案
     * @hooked wp_enqueue_scripts
     */
    public function load_type_css_js_files()
    {
        global $post;
        $css_dir = '/assets/css';
        $js_dir = '/assets/js';
        $file_names = [];
        if(is_page() || is_404()) {
            $type = 'page';
            $css_dir = $css_dir . '/page/';
            $js_dir = $js_dir . '/page/';
            if(is_front_page()) {
                $file_names[] = 'home';
            } elseif(is_404()) {
                $file_names[] = '404';
            } else {
                $file_names[] = $post->post_name;
                $temp_page = $post;
                while($temp_page->post_parent) {
                    $temp_page = get_post($temp_page->post_parent);
                    $file_names[] = $temp_page->post_name;
                }
            }
        } elseif(is_archive() || (!is_front_page() && is_home())) {
            $type = 'archive';
            $css_dir = $css_dir . '/archive/';
            $js_dir = $js_dir . '/archive/';
            $file_names[] = get_post_type();
            if(!$file_names[0]) {
                global $wp_taxonomies;
                $term = get_queried_object();
                $post_types = (isset($wp_taxonomies[$term->taxonomy])) ? $wp_taxonomies[$term->taxonomy]->object_type : [];
                $file_names[0] = $post_types[0];
            }
        } elseif(is_single()) {
            $type = 'single';
            $css_dir = $css_dir . '/single/';
            $js_dir = $js_dir . '/single/';
            $file_names[] = get_post_type();
        } else
            return;

        foreach ($file_names as $file_name) {
            $min_css = $css_dir . "$file_name.min.css";
            $css = $css_dir . "$file_name.css";
            $min_js = $js_dir . "$file_name.min.js";
            $js = $js_dir . "$file_name.js";

            if(file_exists(LIT_DIR . $min_css)) {
                wp_enqueue_style("custom-{$type}-css-{$file_name}", LIT_URL . $min_css);
            } elseif(file_exists(LIT_DIR . $css)) {
                wp_enqueue_style("custom-{$type}-css-{$file_name}", LIT_URL . $css);
            }

            if(file_exists(LIT_DIR . $min_js)) {
                wp_enqueue_script("custom-{$type}-js-{$file_name}", LIT_URL . $min_js, ['jquery'], false, true);
            } elseif(file_exists(LIT_DIR . $js)) {
                wp_enqueue_script("custom-{$type}-js-{$file_name}", LIT_URL . $js, ['jquery'], false, true);
            }
        }
    }

    /**
     * 載入全域CSS檔案
     * @hooked wp_enqueue_scripts
     */
    public function load_global_css_files()
    {
        $directory = '/assets/css/global/';
        $files = glob(LIT_DIR . $directory . '*.css');
        foreach ($files as $key => $file) {
            $file_name = pathinfo($file)['filename'];
            $file_base_name = str_replace('.min', '', $file_name);
            $files[$key] = $file_base_name;
        }
        foreach (array_unique($files) as $css_filename) {
            $min_css = "$css_filename.min.css";
            $css = "$css_filename.css";
            if(file_exists(LIT_DIR . $directory . $min_css))
                wp_enqueue_style("custom-global-css-{$min_css}", LIT_URL . $directory . $min_css);
            elseif(file_exists(LIT_DIR . $directory . $css))
                wp_enqueue_style("custom-global-css-{$css}", LIT_URL . $directory . $css);
        }
    }
    /**
     * 載入全域CSS檔案
     * @hooked wp_enqueue_scripts
     */
    public function load_global_js_files()
    {
        $directory = '/assets/js/global/';
        $files = glob(LIT_DIR . $directory . '*.js');
        foreach ($files as $key => $file) {
            $file_name = pathinfo($file)['filename'];
            $file_base_name = str_replace('.min', '', $file_name);
            $files[$key] = $file_base_name;
        }
        foreach (array_unique($files) as $js_filename) {
            $min_js = "$js_filename.min.js";
            $js = "$js_filename.js";
            if(file_exists(LIT_DIR . $directory . $min_js))
                wp_enqueue_script("custom-global-js-{$min_js}", LIT_URL . $directory . $min_js, ['jquery'], false, true);
            elseif(file_exists(LIT_DIR . $directory . $js))
                wp_enqueue_script("custom-global-js-{$js}", LIT_URL . $directory . $js, ['jquery'], false, true);
        }
    }

    // /**
    //  * 載入其他JS或CSS檔案
    //  * @hooked wp_enqueue_scripts
    //  */
    // public function enqueue_scripts()
    // {
    //     wp_register_script('custom-lib-js-lodash', LIT_URL . '/assets/js/libs/lodash.js');
    //     wp_enqueue_script('custom-lib-js-lodash');
    // }

}
$GLOBALS['customCustom'] = customCustom::get_instance();
