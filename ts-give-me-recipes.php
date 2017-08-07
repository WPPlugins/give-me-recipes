<?php
/*
 * Plugin Name: TS Give Me Recipes
 * Plugin URI: https://wordpress.org/plugins/give-me-recipes
 * Description: Give Me Recipes is a plugin that allows your site visitor to search food recipes by ingredients and or search term. 
 * Version: 1.0.0
 * Author: Tero Sarparanta
 * Author URI: https://profiles.wordpress.org/tero2000
 * License: WTFPL
 *
*/

if ( ! defined( 'ABSPATH' ) ) exit; 
// Load Styles for the admin, not actually necessary.
function foodrecipes_backend_styles() {

 wp_enqueue_style( 'foodrecipes_backend_css', plugins_url( 'ts-give-me-recipes.css', __FILE__ ) );

}
add_action( 'admin_head', 'foodrecipes_backend_styles' );

// Ajax function when sending the form this and the js file are the ones that do the magic. 
function recipe_ajax_request() {
 $i = $_REQUEST["ingredients"];
 $q = $_REQUEST["query"];

 $recipeUrl = 'http://www.recipepuppy.com/api/?i='.$i.'&q='.$q.'&p=1';
 $json = file_get_contents($recipeUrl);
 die($json);
}

add_action( 'wp_ajax_recipe_ajax_request', 'recipe_ajax_request' );
add_action('wp_ajax_nopriv_recipe_ajax_request', 'recipe_ajax_request');


// Loading the js file and the css file.
function foodrecipes_frontend_scripts_and_styles() {

 wp_enqueue_style( 'foodrecipes_frontend_css', plugins_url( 'ts-give-me-recipes.css', __FILE__ ) );
 wp_enqueue_script( 'foodrecipes_frontend_js', plugins_url( 'ts-give-me-recipes.js', __FILE__ ), array('jquery'), '', true );
 // Localize the script with new data
$dir = basename(__DIR__) ;
$ts_js_array = array(
  'plugin_url'  => plugins_url() . '/' .  $dir,
  'site_url'    => get_site_url()
);
wp_localize_script( 'foodrecipes_frontend_js', 'wp_urls', $ts_js_array, admin_url( 'admin-ajax.php' ) );

}
add_action( 'wp_enqueue_scripts', 'foodrecipes_frontend_scripts_and_styles' );

/**
 * Adds foodrecipes widget.
 */
class foodrecipes extends WP_Widget {

 /**
  * Register widget with WordPress.
  */
 function __construct() {
  parent::__construct(
   'foodrecipes', // Base ID
   __( 'Give Me Recipes', 'food_recipes' ), // Name
   array( 'description' => __( 'Widget that shows a Give Me Recipes', 'food_recipes' ), ) // Args
  );
 }

 /**
  * Front-end display of widget. What will be put to the sidebar. 
  *
  * @see WP_Widget::widget()
  *
  * @param array $args     Widget arguments.
  * @param array $instance Saved values from database.
  */
 public function widget( $args, $instance ) {
  echo $args['before_widget'];
  if ( ! empty( $instance['title'] ) ) {
   echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
  }

$actionUrl = plugins_url( 'recipe-result.php', __FILE__ );
?>
<form id="recipes-form" action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>" method="post">
  Ingredients:<br>
  <input id="ingredients" type="text" name="ingredients">
  <br>
  Search:<br>
  <input id="query" type="text" name="query">
  <br><br>
  <input id="give-me-recipe" type="submit" value="Give Me Recipes">
</form>
<ul id="the-recipes"></ul>
<?php
  echo $args['after_widget'];
 }

 /**
  * Back-end widget form.
  *
  * @see WP_Widget::form()
  *
  * @param array $instance Previously saved values from database.
  */
 public function form( $instance ) {
  $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Give Me Recipes', 'food_recipes' );
 // $number = ! empty()
  ?>
  <p>
  <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label> 
  <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
  </p>
  <?php 
 }

 /**
  * Sanitize widget form values as they are saved.
  *
  * @see WP_Widget::update()
  *
  * @param array $new_instance Values just sent to be saved.
  * @param array $old_instance Previously saved values from database.
  *
  * @return array Updated safe values to be saved.
  */
 public function update( $new_instance, $old_instance ) {
  $instance = array();
  $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
  
  return $instance;
 }

} // class foodrecipes

// register foodrecipes widget
function register_foodrecipes() {
    register_widget( 'foodrecipes' );
}
add_action( 'widgets_init', 'register_foodrecipes' );


?>