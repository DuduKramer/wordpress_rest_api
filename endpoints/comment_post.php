<?php 
  function api_comment_post($request){
    $post_id = $request['id'];
    $user = wp_get_current_user();

    if($user === 0){ 
      $response = new WP_Error('error', 'Sem permissao', ['status' => 401]);
      return rest_ensure_response($response);
    }
    
    $comment = sanitize_text_field($request['comment']);
    $user_id = $user -> ID;

    if(empty($comment)){
      $response = new WP_Error('error', 'Dados incompletos.', ['status' => 400]);
      return rest_ensure_response($response);
    }

    $response = [
      'comment_author' =>$user->user_login,
      'comment' => $comment,
      'comment_post_ID' => $post_id,
      'user_id' => $user_id,
    ];

    $comment_id = wp_insert_comment($response);
    $comment = get_comment($comment_id);

    
    return rest_ensure_response($comment);
  }

  function register_api_comment_post(){ 
    register_rest_route('api', '/comment/(?P<id>[0-9]+)', [
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_comment_post',
    ]);
  }

  add_action('rest_api_init', 'register_api_comment_post');
?>
