<?php
/**
 * Plugin Name: EGD Custom Registration
 * Description: Adds custom registration shortcode so user never sees the backend
 * Plugin URI: https://www.evilgeniusdevel.com
 * Version: 0.0.1
 * Author: Drew Wiltjer
 * Author URI: https://www.evilgeniusdvel.com
 * text-domain: egd-custom-registration
 */

 if( ! defined( 'ABSPATH') ) exit; //exit if accessed directly

 function egd_add_registration_scripts() { 
    wp_enqueue_style('egd-custom-registration', plugins_url( '/css/egd-custom-registration.css', __FILE__));
 }
 add_action( 'wp_enqueue_scripts', 'egd_add_registration_scripts');

 function egd_user_registration_shortcode() {
    $html = "";
    $errorMsg = "";
    $form_action = $_SERVER['PHP_SELF'];

    if(isset($_POST["new_user_submit"])) {
        if(empty($_POST["new_user_username"]) || empty($_POST["new_user_password"]) || empty($_POST["new_user_email"])) {
            $errorMsg = "Please complete all fields";
        } else {
            $username = $_POST["new_user_username"];
            $email = sanitize_email($_POST['new_user_email']);
            $userdata = array(
                'user_login'            => $username,
                'user_pass'             => $_POST['new_user_password'],
                'user_email'            => $email,
                'show_admin_bar_front'  => 'false',
                'role'                   => 'movie_watecher' 
            );
            $addUserAction = wp_insert_user($userdata);
            if (is_wp_error($addUserAction)) {
                $errorMsg = $addUserAction->get_error_message();
            } else {
                $errorMsg = "User created successfully, please login";
                $userSuccess = true;
            }
        }
    }

    
    $html .= '<form'. ($userSuccess || is_user_logged_in() ? ' class="hidden"' : '' ) .' name="oscar_registration_form" action="' . $form_action . '" method="post">';
    $html .= '<div class="elementor-form-fields-wrapper">';
    $html .= '<label for="new_user_username">Username</label>';
    $html .= '<input type="text" autocomplete="username" name="new_user_username" id="new_user_username" maxlength="50" placeholder="Username" />';
    $html .= '<label for="new_user_email">Email*</label>';
    $html .= '<input type="email" name="new_user_email" id="new_user_email" placeholder="Email" />';
    $html .= '<label for="new_user_password">Password*</label>';
    $html .= '<input type="password" autocomplete="new-password" name="new_user_password" id="new_user_password" placeholder="password" />';
    $html .= '<input type="submit" value="Register" id="new_user_submit" name="new_user_submit" />';
    $html .= '</div>';
    $html .= '</form>';
    $html .= '<div class="error">'. $errorMsg .'</div>';
    if (is_user_logged_in()) {
        $html .= '<script type="text/javascript">';
        $html .= 'jQuery(document).ready(function(){';
        $html .= 'jQuery(".hide-if-logged-in").addClass("hidden");});';
        $html .= '</script>';
    }
    
    return $html;
 }

 add_shortcode('egd_registration', 'egd_user_registration_shortcode');