<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( !class_exists( 'SPA_Public' ) )
{
    class SPA_Public
    {
        public $spa_block_css = "";
        public $headertitle = "{overallrating} Overall Satisfaction Rating";
        public $headersubtitle = "{numberofcustomer} Customer Ratings Ratings from Actual Customers";
        
        public function __construct()
        {
            add_action('wp_ajax_spa_load_reviews_ajax', array($this, 'spa_load_reviews'));
            add_action('wp_ajax_nopriv_spa_load_reviews_ajax', array($this, 'spa_load_reviews'));
            add_action( 'wp_enqueue_scripts',  array($this,'spa_enqueue_script_style_front') );
            add_shortcode('spa_shortcode',array($this,'spa_load_content')); //Plugin shortcode
        }

        /**
         * 
         * Include javascript and css for shortcode.
         * @since 1.0
         */
        public function spa_enqueue_script_style_front(){
            wp_enqueue_script('jquery');//Include jQuery for shortcode. @since 1.1
            wp_enqueue_style('style',SPA_PLUGIN_DIR.'public/css/style.css');
            wp_enqueue_style('font-awesome-min','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
            wp_enqueue_script('spa_javascript',SPA_PLUGIN_DIR.'public/js/spa_javascript.js', array( 'jquery' ));
            wp_localize_script( 'spa_javascript', 'spa_javascript_obj',
                array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'image_path' => SPA_PLUGIN_DIR.'images'
                )
            );
        }

        public function spa_load_header()
        {
            $url = SPA_API . '/sites/?siteid=' . SPA_SITE_ID . '&token=' . SPA_TOKEN . '';
               $comman_header = wp_remote_get($url);
               if(is_wp_error( $comman_header )){
                return false;
               }else{
                return json_decode($comman_header['body']);
               }
        }

        public function spa_load_reviews()
        {
            $page = $_REQUEST['page'];
            $sort = $_REQUEST['short'];
            $url = SPA_API . '/reviews/?siteid=' . SPA_SITE_ID . '&token=' . SPA_TOKEN . '&sort=' . $sort . '&page=' . $page . '';
            $reviews = wp_remote_get($url);
           	$result_objs = json_decode($reviews['body']);
            $result_arr = array();
            foreach ($result_objs as $key=>$result_obj){
                $result_arr[][$key] = (array) $result_obj;
            }
            echo json_encode($result_arr);
            die();
        }

        public function spa_load_content()
        {
            $reviews = $this->spa_load_header();
            ob_start();
            if(empty($reviews)){
                printf('<h1>%s</h1>', __('Invalid token or site id', 'shopperapproved'));
            }else {
                ?>
<style>
<?php echo $this->spa_block_css;
?>
</style>
<div class="spa_main_container">
    <div class="spa_header_container remove_padding">
        <div class="spa_image"><a href="https://www.shopperapproved.com/reviews/<?php echo SPA_SITE_ID; ?>"><img
                    src="<?php echo SPA_PLUGIN_DIR; ?>images/salogo.png" alt="salogo.png"></a></div>
        <div class="spa_header">
            <div class="spa_header_content">
                <div class="spa_full_width">
                    <div class="spa_half_width">
                        <?php for ($x = 1; $x <= 5; $x++) {
                                        if ($x <= $reviews->average) {
                                            echo "<span class='fa fa-star checked'></span>";
                                        } else {
                                            echo "<span class='fa fa-star'></span>";
                                        }
                                    } ?>
                        <h3><?php echo str_replace( '{overallrating}', '<span
                                class="bold" itemprop="ratingValue">'.$reviews->average.'</span>', $this->headertitle ); ?>
                        </h3>
                        <h5 class="white">
                            <?php echo str_replace( '{numberofcustomer}', ' <b style="color:orange;">'.$reviews->review_count.'</b>',
                            $this->headersubtitle ); ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="spa_dropdown_nav">
            <label for="review_short">Order:-</label><select class="dropbtn" id="review_short" name="short">
                <option value="newest">Newest</option>
                <option value="oldest">Oldest</option>
            </select>
            <label for="review_page">Page:-</label><select class="dropbtn" id="review_page" name="page">
                <?php
                            $review_counts = ceil($reviews->review_count / 100);
                            for ($x = 1; $x <= $review_counts; $x++) {
                                echo "<option value=" . ($x - 1) . ">" . $x . "</option>";
                            }
                            ?>
            </select>
        </div>
    </div>
    <div class="spa_content_container">
        <div id="loader"></div>

        <div id="spa_response"></div>
    </div>
</div>
<?php
            }
            $reviews = ob_get_clean();
            return $reviews;
        }
    }
}