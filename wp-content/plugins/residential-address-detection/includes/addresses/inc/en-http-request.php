<?php

/**
 * Class to call curl request
 */
class EnRadHttpResponse {
  /**
   * Get Curl Response 
   * @param  $url curl hitting url
   * @param  $postData post data to get response
   * @return json
   */
  function en_rad_http_response($url, $postData) {
    if ( !empty( $url ) && !empty( $postData ) ) {
        $field_string = http_build_query($postData);
        
        $response = wp_remote_post($url,
            array(
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 5,
                'blocking' => true,
                'body' => $field_string,
            )
        );
        
        $output = wp_remote_retrieve_body($response);
        
        return $output;
      }    
  }
}
?>