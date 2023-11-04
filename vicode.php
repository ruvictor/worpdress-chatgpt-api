<?php
/*
Plugin Name: ChatGPT API Plugin
Description: A WordPress plugin to interact with the ChatGPT API.
Version: 1.0
Author: Victor
*/

// Function to add a menu item for the plugin
function chatgpt_api_menu() {
    add_menu_page('ChatGPT API', 'ChatGPT API', 'manage_options', 'chatgpt-api', 'chatgpt_api_page');
}

add_action('admin_menu', 'chatgpt_api_menu');

// Create the plugin page
function chatgpt_api_page() {
    ?>
    <div class="wrap">
        <h2>Chat with ChatGPT</h2>
        <form method="post" action="">
            <label for="user_message">Your Message:</label>
            <input type="text" id="user_message" name="user_message" required>
            <input type="submit" name="send_message" value="Send">
        </form>
        
        <?php
        if ($_POST['send_message']) {
            $api_key = ''; // Replace with your actual API key
            $user_message = sanitize_text_field($_POST['user_message']);

            $response = chatgpt_send_message($api_key, $user_message);
            echo "<p><strong>Response:</strong> $response</p>";
        }
        ?>
    </div>
    <?php
}

// Function to send a message to the ChatGPT API
function chatgpt_send_message($api_key, $message) {
    $url = 'https://api.openai.com/v1/chat/completions';
    
    $data = json_encode(array(
        'max_tokens' => 100, // Adjust this based on your needs
        'model' => 'gpt-3.5-turbo',
        "messages" => array(
            array(
                "role" => "user",
                "content" => $message
            )
        ),
        "temperature" => 0.7
    ));

    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $api_key",
        ),
        'body' => $data,
        'timeout' => 50, // Set the timeout to 10 seconds
        
    );

    $response = wp_safe_remote_post($url, $args);

    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message();
    } else {
        $body = wp_remote_retrieve_body($response);
        
        $data = json_decode($body, true);
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';

        return $data['choices'][0]['message']['content'];
    }
}
?>
