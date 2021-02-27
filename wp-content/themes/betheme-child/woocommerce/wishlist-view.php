<?php
/**
 * Wishlist page template - Standard Layout
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist                      \YITH_WCWL_Wishlist Current wishlist
 * @var $wishlist_items                array Array of items to show for current page
 * @var $wishlist_token                string Current wishlist token
 * @var $wishlist_id                   int Current wishlist id
 * @var $users_wishlists               array Array of current user wishlists
 * @var $current_page                  int Current page
 * @var $page_links                    array Array of page links
 * @var $is_user_owner                 bool Whether current user is wishlist owner
 * @var $show_price                    bool Whether to show price column
 * @var $show_dateadded                bool Whether to show item date of addition
 * @var $show_stock_status             bool Whether to show product stock status
 * @var $show_add_to_cart              bool Whether to show Add to Cart button
 * @var $show_remove_product           bool Whether to show Remove button
 * @var $show_price_variations         bool Whether to show price variation over time
 * @var $show_variation                bool Whether to show variation attributes when possible
 * @var $show_cb                       bool Whether to show checkbox column
 * @var $show_quantity                 bool Whether to show input quantity or not
 * @var $show_ask_estimate_button      bool Whether to show Ask an Estimate form
 * @var $show_last_column              bool Whether to show last column (calculated basing on previous flags)
 * @var $move_to_another_wishlist      bool Whether to show Move to another wishlist select
 * @var $move_to_another_wishlist_type string Whether to show a select or a popup for wishlist change
 * @var $additional_info               bool Whether to show Additional info textarea in Ask an estimate form
 * @var $price_excl_tax                bool Whether to show price excluding taxes
 * @var $enable_drag_n_drop            bool Whether to enable drag n drop feature
 * @var $repeat_remove_button          bool Whether to repeat remove button in last column
 * @var $available_multi_wishlist      bool Whether multi wishlist is enabled and available
 * @var $no_interactions               bool
 */

if ( ! defined( 'YITH_WCWL' ) ) {
    exit;
} // Exit if accessed directly

if ( $wishlist && $wishlist->has_items() ) :
?>
<div class="wishlist-total-data">
    <div class="wishlist-product-section">
        <?php
            foreach ( $wishlist_items as $item ) :
            // phpcs:ignore Generic.Commenting.DocComment
            /**
             * @var $item \YITH_WCWL_Wishlist_Item
             */
            global $product;
            $product      = $item->get_product();
            $terms = array();
            $product_terms = get_the_terms($product->get_id(), 'filter-category');
            $product_ids []= $product->get_id();
            foreach ($product_terms as $term) {
                $parent_term = get_term($term->parent);
                $terms[$parent_term->slug] = $term->name;
            }
        ?>
        <div class="wishlist-product-list">
            <div class="wishlist-product-data">
                <div class="wishlist-product-img">
                    <img src="<?=get_the_post_thumbnail_url($product->get_id())?>">
                </div>
                <div class="wishlist-product-detail">
                    <span class="wishlist-product-text"><?=$product->get_sku()?></span><!--SKU-->
                    <span class="wishlist-product-text"><?=$product->get_title()?></span><!--品名-->
                    <span class="wishlist-product-text">材質：<?=$terms['material']?></span><!--材質-->
                    <span class="wishlist-product-text">顏色：<?=$terms['color']?></span><!--顏色-->
                    <span class="wishlist-product-text">口徑：<?=$terms['diameter']?></span><!--尺寸-->
                </div>
            </div>
            <div class="wishlist-product-delete">
                <a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item->get_product_id() ) ); ?>" class="remove remove_from_wishlist" title="<?php echo esc_html( apply_filters( 'yith_wcwl_remove_product_wishlist_message_title', __( 'Remove this product', 'yith-woocommerce-wishlist' ) ) ); ?>">
                    <img class="remove-wishlist-btn" src="<?=wp_get_attachment_image_src('375','thumbnail')[0]?>" >
                </a>
            </div>
        </div>
        <?php
            endforeach;
        ?>
    </div>
    <div class="wishlist-action-section">
        <div class="wishlist-action-btn">
            <form action="" method="post">
                <input type="hidden" name="clear" value="wishlist-clear">
                <input type="hidden" name="flag" value="true">
                <button id="wishlist-clear-btn" type="submit">全部清除</button>
            </form>
            <form action="" method="post">
                <input type="hidden" name="export" value="wishlist-true">
                <input type="hidden" name="product_ids" value="<?=json_encode($product_ids);?>">
                <button id="wishlist-export-btn" type="submit">匯出資料</button>
            </form>
        </div>
    </div>
</div>
<?php
    else:
?>
        <div class="wishlist-notice">
            <span class="wishlist-notice-txet"> 目前沒有產品哦! </span>
        </div>
<?php
    endif;
?>


