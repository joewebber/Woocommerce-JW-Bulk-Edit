<?php
/**
 * JW Bulk Edit Plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class JW_Bulk_Edit_Plugin {

  /**
   * Array of products
   *
   */
  protected $_products;


  /**
   * Constructor.
   *
   */
  function __construct() {

    // Get the HTML
    $this->_showHTML();

    // If we have post data, search products
    if (!empty($_POST)) {

      // Search products
      $this->_searchProducts($_POST['data']);

      // If we have products
      if (!empty($this->_products)) {

        // If we are just searching
        if ($_GET['action'] == 'search') {

          // Show search result
          $this->_showSearchResult();

        } else if ($_GET['action'] == 'update') { // We are updating

          // Update products
          $this->_updateProducts();

        }

      }

    }

  }

  /* Show the admin panel HTML */
  protected function _showHTML() {

    // Woocommerce global
    global $woocommerce;

    ?>
    <div class="wrap woocommerce">

      <div id="icon-woocommerce" class="icon32-woocommerce-settings icon32"><br /></div>

        <h2 class="nav-tab-wrapper woo-nav-tab-wrapper" style="padding-bottom: 10px">

          <?php

            // Show the page title
            echo  __( 'Bulk Edit Product Variations', 'woocommerce-bulkvariations' );

          ?>

        </h2>

        <form method="post" name="search" action="admin.php?page=woocommerce-jwbulkedit&action=search">

          <table style="margin-top: 10px">
            <tr>
              <td>Select filter method</td>
              <td></td>
              <td>
                <select class="filter_select">
                  <option>Select...</option>
                  <option value="sku_field">SKU</option>
                  <option value="brand_field">Brand</option>
                </select>
              </td>
            </tr>

            <tr class="sku_field" style="display: none">
              <td>SKU</td>
              <td></td>
              <td>
                <input type="text" name="data[sku]" value="">
              </td>
            </tr>

            <tr class="brand_field" style="display: none">
              <td>Brand</td>
              <td></td>
              <td>

              <select name="data[product_brand]" id="">
                <option value="">Select brand</option>
                <?php

                $terms = get_terms( array(
                    'taxonomy' => 'product_brand',
                    'hide_empty' => true,
                ) );

                foreach ($terms as $key => $value) { ?>

                  <option value="<?php echo $value->term_id; ?>"><?php echo $value->name; ?></option>

                <?php } ?>
              </select>

              </td>
            </tr>

          </table>

          <p class="search_submit" style="display: none"><button type="submit" class="button">View Products</button></p>

        </form>

    </form>

  </div>

  <script>

    jQuery(document).ready(function() {

      // Filter select
      jQuery('.filter_select').change(function() {

        jQuery('.sku_field, .brand_field').hide()

        jQuery('.' + jQuery(this).val()).show()

        jQuery('.search_submit').show()

      })

    })

   </script>

  <?php

  }

  /* Show search result */
  protected function _showSearchResult() { ?>

  <div class="wrap" style="border-top: 1px solid #ccc">

    <?php if (!empty($_POST['data']['product_brand'])) { ?>

      <br>

      <form name="update_price" method="post" action="admin.php?page=woocommerce-bulkvariations&action=update">

        <input type="hidden" name="data[product_brand]" value="<?php echo (isset($_POST['data']['product_brand'])) ? htmlentities($_POST['data']['product_brand']) : ''; ?>">

        <div>

          <label>Increase price by:</label>

          <input type="number" name="percent" step="0.01"> percent

          <button type="submit" class="button">Update Prices</button>

          <hr>

        </div>

      </form>

    <?php } ?>

    <p>Your search returned the following product(s):</p>

    <?php foreach ($this->_products as $product) { ?>

    <div><?php echo $product['data']['thumbnail']; ?></div>

    <h3><a href="javascript: window.open('/wp-admin/post.php?post=<?php echo $product['data']['id']; ?>&action=edit', '_blank', 'toolbar=yes,scrollbars=yes,resizable=yes,top=200,left=200,width=600,height=600');"><?php echo $product['data']['title']; ?></a></h3>

    <h4><?php echo count($product['variants']); ?> variations found</h4>

    <?php if (!empty($_POST['data']['sku'])) { ?>

    <p>Select the variation sizes to be updated, then enter the price and click 'Update Price'</p>

    <?php } ?>

    <?php

      // Sizes array
      $sizes = array();

      foreach ($product['variants'] as $variant) {

        $sizes[$variant['meta']['attribute_pa_size'][0]] = str_replace('-', '.', $variant['meta']['attribute_pa_size'][0]);

      }

      // De-dupe array
      $sizes = array_unique($sizes);

    ?>

    <?php if (!empty($_POST['data']['sku'])) { ?>

      <form name="update_price" method="post" action="admin.php?page=woocommerce-bulkvariations&action=update">

        <input type="hidden" name="data[sku]" value="<?php echo (isset($_POST['data']['sku'])) ? htmlentities($_POST['data']['sku']) : ''; ?>">

        <?php

            // Loop through sizes
            foreach ($sizes as $key => $size) { ?>

              <div style="margin: 10px 0">

                <input type="radio" name="data[pa_size]"  value="<?php echo $key; ?>">

                <label><?php echo $size; ?></label>

              </div>

      <?php } ?>

        <div>

          <label>Set price to:</label>

          <input type="number" name="price" step="0.01">

          <button type="submit" class="button">Update Price</button>

          <br><br>

          <hr>

          <br><br>

        </div>

      </form>

    <?php } ?>

    <?php } ?>

  </div>

  <script>

    jQuery(document).ready(function() {

      jQuery('.overlay').hide()

      jQuery('.button').removeAttr('disabled')

      jQuery('form[name="update_price"] button').click(function() {

        jQuery('.button').attr('disabled', 'disabled')

        jQuery('.overlay').show()

        jQuery('form[name="update_price"]').submit()

      })

    })

   </script>

  <style>

    .overlay {
      position: fixed;
      display: none;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0,0,0,0.5);
      z-index: 2;
    }

    .overlay > div {
      font-size: 20px;
      color: #fff;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
    }

    .overlay > div > div {
      text-align: center;
      margin-bottom: 10px;
    }

  </style>

  <div class="overlay">

    <div>

      <div><img src="/wp-includes/js/thickbox/loadingAnimation.gif"></div>

      Updating Variations

    </div>

  </div>

  <?php

  }

  /* Update products */
  protected function _updateProducts() {

    $count = 0;

    // Loop through products
    foreach ($this->_products as $product) {

      // Update Variants
      if ( !empty($product['variants'])) {

        // Loop through variants
        foreach ($product['variants'] as $variant) {

          if (isset($_POST['price'])) {

            // Get price to update to
            $price = (float) $_POST['price'];

            $regular_price = (float) $_POST['price'];

          } else {

            // get price
            $current_price = (float) get_post_meta($variant['id'], '_price', true);

            // Set new price
            $price = number_format($current_price * (1 + ((float) $_POST['percent'] / 100)), 2);

            // get regular price
            $current_regular_price = (float) get_post_meta($variant['id'], '_regular_price', true);

            // Set new price
            $regular_price = number_format($current_regular_price * (1 + ((float) $_POST['percent'] / 100)), 2);

          }

          // Update price
          update_post_meta( $variant['id'], '_price', $price);

          // Update regular price
          update_post_meta( $variant['id'], '_regular_price', $regular_price);

          // Remove variant transients
          wc_delete_product_transients( $variant['id'] );

          $count++;

        }

      }

      // Update simple product
      if ( empty($product['variants']) ) {

        // Simple product update
        $simple = $product['data'];

        // get price
        $current_price = (float) get_post_meta($simple['id'], '_price', true);

        // Set new price
        $price = number_format($current_price * (1 + ((float) $_POST['percent'] / 100)), 2);

        // get regular price
        $current_regular_price = (float) get_post_meta($simple['id'], '_regular_price', true);

        // Set new price
        $regular_price = number_format($current_regular_price * (1 + ((float) $_POST['percent'] / 100)), 2);

        // Update price
        update_post_meta( $simple['id'], '_price', $price);

        // Update regular price
        update_post_meta( $simple['id'], '_regular_price', $regular_price);

      }

      // Remove product transients
      wc_delete_product_transients( $product['data']['id'] );

    } ?>

    <div class="wrap" style="border-top: 1px solid #ccc">

      <h4><?php echo $count . ' variations updated'; ?></h4>

    </div>

<?php

  }

  /* Search products */
  protected function _searchProducts($data = array()) {

    // If we have an SKU
    if (!empty($data['sku'])) {

      // Get parent products that match search
      $parent_products = $this->_getProducts(
        array(
          'key' => '_sku',
          'value' => $data['sku'],
          'compare' => '='
        )
      );

      // Remove sku from data array
      unset($data['sku']);

    }

    if (!empty($data['product_brand'])) {

      // Add to parameters array
      $parameters[] = array(
        'taxonomy' => 'product_brand',
        'field' => 'term_id',
        'terms' => $data['product_brand'],
      );

      // Get parent products that match search
      $parent_products = $this->_getProducts($parameters, 'tax');

      // Remove product brand from data array
      unset($data['product_brand']);

    }

    // If we have parent products
    if (!empty($parent_products)) {

      // Search parameters
      $parameters = array();

      // If we have search data
      if (!empty($data)) {

        // Other parameters
        foreach ($data as $key => $val) {

          // If we have a value
          if ($val != '') {

            // Add to parameters array
            $parameters[] = array(
              'key' => 'attribute_' . $key,
              'value' => $val,
              'compare' => '='
            );

          }

        }

      }

      // Array to hold product data
      $products = array();

      // Get child products that match search
      foreach ($parent_products as $parent_product) {

        // Add product data to array
        $products[] = array(
          'data' => $parent_product,
          'variants' => array()

        );

      }

      // Counter
      $i = 0;

      // Loop through parent ids and get variants
      foreach ($products as $product) {

        // Get variants
        $products[$i]['variants'] = $this->_getChildProducts($parameters, $product['data']['id']);

        $i++;

      }


    }

    // Set the products
    $this->_products = $products;

  }

  /* Product loop */
  protected function _productLoop($query) {

    $products = array();

    // If we have posts
    if ( $query->have_posts() ) {

      // Loop through posts
      while ( $query->have_posts() ) {

        // Set the post object
        $query->the_post();

        // Add to products array
        $products[] = array(
          'id' => get_the_ID(),
          'title' => get_the_title(),
          'meta' => get_post_meta( get_the_ID() ),
          'thumbnail' => get_the_post_thumbnail( get_the_ID(), array(266, 266) )
        );

      }

      // Restore post data
      wp_reset_postdata();

    }

    // Return products
    return $products;

  }

  /* Get child products */
  protected function _getChildProducts($parameters = array(), $post_parent = 0) {

    $meta_query = array();

    if (!empty($parameters)) {

      $meta_query = array(
        'relation' => 'AND',
        $parameters
      );

    }

    // Get products
    $query = new WP_Query(
      array(
        'post_type' => 'product_variation',
        'posts_per_page' => -1,
        'post_parent' => $post_parent,
        'meta_query' => $meta_query
      )
    );

    return $this->_productLoop($query);

  }

  /* Get products */
  protected function _getProducts($parameters = array(), $type = 'meta') {

    // Get products
    $query = new WP_Query(
      array('post_type' => 'product',
        'posts_per_page' => -1,
        $type . '_query' => array(
          'relation' => 'AND',
          $parameters
        )
      )
    );

   return $this->_productLoop($query);

  }

  /* Get Product Attributes */
  protected function _getProductAttributes($exclude = array()) {

    // Get the product attributes
    $attributes = get_object_taxonomies('product', 'objects');

    // Array to hold data
    $data = array();

    // Loop through and remove exclusions
    foreach ($attributes as $attribute) {

      // If not in the exclusion array
      if (!in_array($attribute->name, $exclude)) {

        // Add to data array
        $data[] = $attribute;

      }

    }

    // Return the data
    return $data;

  }

}