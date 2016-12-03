<?php
/**
 * Plugin Name: Netmkr
 * Plugin URI: http://netmkr.com
 * Description: This plugin pulls user/post info for use in d3 (REQUIRES wp-d3 plugin)
 * Version: 0.09.0
 * Author: Bruce Rinehart
 * Author URI: http://brucerinehart.com
 * License: GPL2
 */


// this is a hook, an action 
add_action( 'wp_head', 'netmkr1' );


// this is the function for the above action... puts header info in .. the header
function netmkr1() {
  if( is_single() ) {

 ?>
    <meta property="og:title" content="<?php the_title() ?>" />
    <meta property="og:site_name" content="<?php bloginfo( 'name' ) ?>" />
    <meta property="og:url" content="<?php the_permalink() ?>" />
    <meta property="og:description" content="<?php the_excerpt() ?>" />
    <meta property="og:type" content="article" />
  <?php
  }
}


// this does a WP guery or two to get users (and post and comments) dumpt to json that's written to the page for d3

function get_users_to_js_d3(){

// let's get some posts

// stub code
  // this one worked
$argss = array('post_type'   => 'post' );
$latest_posts = get_posts( $argss );


  $nodes = array ();
  $links = array ();

foreach ( $latest_posts as $post ) {

  array_push($nodes, array ( 'name' => $post->post_title, 'wp_pID' => $post->ID, 'postAuthor' => $post->post_author, 'group' => 'posts', 'nodeType' => 'post' ) );


    
// link between author and post
  // need to change the post author to a word .. now an index
  //therefore

 $user_info = get_userdata($post->post_author);

  array_push($links, array ( 'target' => $post->post_title, 'source' => $user_info->user_nicename ) );

  echo $post->post_title . "<br />" ;
}



// got users, working on posts...

// getting users by the fields

    $blogusers = get_users( array( 'fields' => array( 'user_nicename', 'user_login' , 'ID') ) ) ;


// this gets the roles and name of the user..just a test function 
    // should make an array and stuff into the json... but first... posts!!!

$usrIDsNnames = array ();

foreach ($blogusers as $varz) {
  $user = new WP_User($varz->ID);
  echo "<br />" . json_encode($user->user_nicename) . " is a " . json_encode($user->roles) . " ID number: " . json_encode($user->ID) . "<br />";


// this for use later... do this loop twice... not a big deal for few users... kinda dumb
// this unnecessary... duuuhhhhhhhhhhhhhhh
  array_push ( $usrIDsNnames, array ( $user->user_nicename => $user->ID));

}

// test ground for getting roles

// this just gets all available roles whether or not someone has it
// $wp_roles = new WP_Roles();
// $roles = $wp_roles->get_names();
// foreach($roles as $role) {
//   //echo $role;
// }


// echo "<br />" . json_encode($user->user_nicename) . " is a " . json_encode($user->roles) . "<br />";


//echo json_encode($blogusers);

  $strvals = "";
  $data =  array ();
  $datastr = '';
  $IDHolder = array ();

  foreach ($blogusers as $vals) {
    $userr = new WP_User($vals->ID);
    
    // just testing...
    echo "<br />" . json_encode($userr->user_nicename) . " is a " . json_encode($userr->roles) . "<br />";

    // risky.. but gonna bake in the group attribute to read the first item in the array.. usually(?) folks have one role
    array_push($nodes, array ( "name" => $vals->user_nicename, "group" => $userr->roles[0], "nodeType" => "user" ) );
    array_push($IDHolder, array ( "ID" => $vals->ID, "group" => "2" ) );


  // ok... links... gotta get author name from ID and put it in the source element of the link

  // if ( $user->ID == array_search( $user->ID , $usrIDsNnames )){


  //   array_push(links, var)



  // }

// fixing the links index 
      // foreach ($links as $lnkk){

      //   $links [ 'source' ] = array_search ( $lnkk->source, $usrIDsNnames );
      // }



// did this first... then added user_nicename (etc) field above    array_push($nodes, array ( "name" => $vals->user_nicename, "group" => "2" ) );
  }

echo json_encode($IDHolder);
  
// this is the dummy link data... one link
  // array_push($links, array ( "target" => 0, "source" => 1 ) );

// this is the main pre-json array
  $dadata = array ( "nodes" => $nodes, "links" => $links );

// this is the json
  $datastr = json_encode ( $dadata );

    $strtemp = '<script type="text/javascript">var dadata = ' . $datastr . '</script>';
    return $strtemp;

  //return  $results;

}



add_shortcode('userz', 'get_users_to_js_d3');


// ===================================================================================================================================
// ===================================================================================================================================
// ===================================================================================================================================
// ===================================================================================================================================
// ===================================================================================================================================
// =========================================================   WARNING: DUPLICATE NONSENSE BELOW   ====================================
// =========================================================   DO NOT RESUSITATE!!!                ====================================
// ===================================================================================================================================
// ===================================================================================================================================
// ===================================================================================================================================
// ===================================================================================================================================
// ===================================================================================================================================




// below here is a second version of the above function (c2/28/16)
// trying to add comments.. all needs much cleaning... ugh


// this does a WP guery or two to get users (and post and comments) dumpt to json that's written to the page for d3

function get_users_to_js_d3c(){

// let's get some posts

// stub code
  // this one worked
$argss = array('post_type'   => 'post' );
$latest_posts = get_posts( $argss );


  $nodes = array ();
  $links = array ();

foreach ( $latest_posts as $post ) {

  array_push($nodes, array ( 'name' => $post->post_title, 'wp_pID' => $post->ID, 'postAuthor' => $post->post_author, 'group' => 'posts', 'nodeType' => 'post' ) );


    
// link between author and post
  // need to change the post author to a word .. now an index
  //therefore

 $user_info = get_userdata($post->post_author);

  array_push($links, array ( 'target' => $post->post_title, 'source' => $user_info->user_nicename ) );

  echo $post->post_title . "<br />" ;
}


// let's do the comment getting here
//	$blogComments = get_comments(array( 'status' => 'approve'));
	$blogComments = get_comments();
  // loop and make nodes and links for the  comments
	foreach($blogComments as $comment){

    // look up the post name from comment_post_ID in 
    foreach ( $latest_posts as $latepost ){
      // if the nodeID equals the comment_post_ID, then that's the one to link to!!!
      if ($latepost->ID == $comment->comment_post_ID){
        // make the link here?
               // oh yeah.. the link from the post to the comment...!!!

        array_push($links, array ( 'target' => $comment->comment_ID, 'source' => $latepost->post_title ) );
        
      } else {}
    }


      // its a visiting commentor
      // make a node for the commenter and a node for the comment, then a link to the page (pageID comes with comment!!! wooohooo!)
      // commenter node

//        array_push($nodes, array ( 'name' => $comment->comment_author, 'wp_pID' => $comment->comment_ID, 'postAuthor' => $comment_author, 'group' => 'commenter', 'nodeType' => 'commenter' ) );
        array_push($nodes, array ( 'name' => $comment->comment_author, 'postAuthor' => $comment_author, 'group' => 'commenter', 'nodeType' => 'commenter' ) );

      // commenter link
//        array_push($links, array ( 'target' => $post->post_title, 'source' => $user_info->user_nicename ) );
        array_push($links, array ( 'target' => $comment->comment_ID, 'source' => $comment->comment_author ) );
      // commented post  
        // gonna put the comment_ID in the node name for now... should do a lookup or something...


  //        array_push($nodes, array ( 'name' => $comment->comment_ID, 'wp_pID' => $comment->comment_ID, 'postAuthor' => $comment->$comment_author, 'group' => 'comment', 'nodeType' => 'comment' ) );
        array_push($nodes, array ( 'name' => $comment->comment_ID, 'wp_pID' => $comment->comment_ID, 'postAuthor' => $comment->$comment_author, 'group' => 'comment', 'nodeType' => 'comment' ) );

}
// ok, so if the userID is 0, then it's a visitor commentor (make new nodes)

		// get all approved comments with empty number arg
		$all_comments=get_comments( array('status' => 'approve' ) );




// got users, working on posts...

// getting users by the fields

    $blogusers = get_users( array( 'fields' => array( 'user_nicename', 'user_login' , 'ID') ) ) ;


// this gets the roles and name of the user..just a test function 
    // should make an array and stuff into the json... but first... posts!!!

// test ground for getting roles

// this just gets all available roles whether or not someone has it
// $wp_roles = new WP_Roles();
// $roles = $wp_roles->get_names();
// foreach($roles as $role) {
//   //echo $role;
// }


  $strvals = "";
  $data =  array ();
  $datastr = '';

  foreach ($blogusers as $vals) {
    $userr = new WP_User($vals->ID);
    
    // just testing...
    echo "<br />" . json_encode($userr->user_nicename) . " is a " . json_encode($userr->roles) . "<br />";

    // risky.. but gonna bake in the group attribute to read the first item in the array.. usually(?) folks have one role
    array_push($nodes, array ( "name" => $vals->user_nicename, "group" => $userr->roles[0], "nodeType" => "user" ) );

    }
  
// this is the dummy link data... one link
  // array_push($links, array ( "target" => 0, "source" => 1 ) );

// this is the main pre-json array
  $dadata = array ( "nodes" => $nodes, "links" => $links );

// this is the json
  $datastr = json_encode ( $dadata );

    $strtemp = '<script type="text/javascript">var dadata = ' . $datastr . '</script>';
    return $strtemp;

  //return  $results;

}



add_shortcode('userzc', 'get_users_to_js_d3c');


?>