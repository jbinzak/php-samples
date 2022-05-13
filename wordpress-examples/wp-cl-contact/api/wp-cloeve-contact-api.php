<?php
/*
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');




class WP_Cloeve_Contact_API {

    // /wp-json/NAMESPACE/ENDPOINT
    const CC_API_NAMESPACE = 'cloeve-contact/v1';

    static function register_endpoints() {
        register_rest_route( self::CC_API_NAMESPACE, '/submit', array(
            'methods' => 'POST',
            'callback' => [__CLASS__, 'submit'],
        ) );
    }

    /**
     *  submit
     */
    static function submit() {

        // get args
        $contact_name = key_exists('contact_name', $_POST) ? sanitize_text_field($_POST['contact_name']) : '';
        $contact_email = key_exists('contact_email', $_POST) ? sanitize_email($_POST['contact_email']) : '';
        $contact_topics = key_exists('contact_topics', $_POST) ? ($_POST['contact_topics']) : '';
        $contact_message = key_exists('contact_message', $_POST) ? sanitize_text_field($_POST['contact_message']) : '';

        // get args
        if(empty($contact_name)){
            $post = json_decode(file_get_contents('php://input'), true);

            // get args
            $contact_name = key_exists('contact_name', $post) ? sanitize_text_field($post['contact_name']) : '';
            $contact_email = key_exists('contact_email', $post) ? sanitize_email($post['contact_email']) : '';
            $contact_topics = key_exists('contact_topics', $post) ? ($post['contact_topics']) : '';
            $contact_message = key_exists('contact_message', $post) ? sanitize_text_field($post['contact_message']) : '';

        }

        // save
        WP_Cloeve_Contact_List::insert_new_record([
            'name' => $contact_name,
            'email' => $contact_email,
            'message' => json_encode(['topics'=> $contact_topics, 'message'=>$contact_message])
        ]);


        // send to contact
        $body = 'New contact from ' . $contact_name . ' (' . $contact_email .') ' .  json_encode(['topics'=> $contact_topics, 'message'=>$contact_message]);
        $to = 'contact@cloeve.com';
        $subject = 'Website Contact';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $to, $subject, $body, $headers );


        // send to user
        $body = 'Hello ' . $contact_name . '! Thank you for reaching out to us. We will contact you back shortly. Thanks, The Cloeve Team.';
        $to = $contact_email;
        $subject = 'Cloeve Website Contact Confirmation';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $to, $subject, $body, $headers );

        return ['Message' => 'Successfully contacted!'];
    }

}
