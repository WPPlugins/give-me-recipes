/*
* Give me recipes JS
* Using ajax so there is no page refresh
*/
jQuery(document).ready(function(){

// When the search button is pressed.
 jQuery( "#give-me-recipe" ).click(function( event ) {

  // Prevent the default button action.
   event.preventDefault();

  // Get information from the form we're submitting.
   var form = jQuery('#recipes-form');
        formsAction = form.attr( 'action' );
   jQuery(form).fadeOut(300); 

   // Add the nice .gif thingy so it looks like we're loading :) 
    // Using timeout function here so it happens smoothly
     setTimeout(function(){  
      jQuery('#the-recipes').append('<li><img src="'+wp_urls.plugin_url+'/img/clock.gif"></li><li>Searching recipes ...</li>');
      }, 400);

  // Get values from the form fields.
    var ingredients = jQuery('#ingredients').val();
    var query = jQuery('#query').val();

    // Submit the information
    jQuery.ajax({
        type:"POST",
        dataType : "json",
        url: formsAction,
        data: {
            action: 'recipe_ajax_request',
            ingredients: ingredients,
            query: query
        },
        success:function(response){
        // Getting the response AKA the recipes.
          var obj = response.results; 
        // Set timeout funtion again here so the loading gif is displayed a bit longer. 
          setTimeout(function(){  
           jQuery('#the-recipes').empty();
           jQuery.each( obj, function( key, value ) {
            jQuery('#the-recipes').append('<a href="'+value.href+'" target="_blank" rel="nofollow"><li class="recipe-list-item"><img class="recipe-img" src="'+value.thumbnail+'"/>'+value.title+'</li></a>');
             
           });
          }, 2000);
            
        }
        }); 

  });
});