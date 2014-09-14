<?php

class RGSignatureUpgrade{

    public static function set_version_info($version_info){
        if ( function_exists('set_site_transient') )
            set_site_transient("gforms_signature_version", $version_info, 60*60*12);
        else
            set_transient("gforms_signature_version", $version_info, 60*60*12);
    }

    public static function check_update($plugin_path, $plugin_slug, $plugin_url, $offering, $key, $version, $option){

        $version_info = function_exists('get_site_transient') ? get_site_transient("gforms_signature_version") : get_transient("gforms_signature_version");

        //making the remote request for version information
        if(!$version_info){
            //Getting version number
            $version_info = self::get_version_info($offering, $key, $version);
            self::set_version_info($version_info);
        }

        if ($version_info == -1)
            return $option;

        if(empty($option->response[$plugin_path]))
            $option->response[$plugin_path] = new stdClass();

        //Empty response means that the key is invalid. Do not queue for upgrade
        if(!$version_info["is_valid_key"] || version_compare($version, $version_info["version"], '>=')){
            unset($option->response[$plugin_path]);
        }
        else{
            $option->response[$plugin_path]->url = $plugin_url;
            $option->response[$plugin_path]->slug = $plugin_slug;
            $option->response[$plugin_path]->package = str_replace("{KEY}", $key, $version_info["url"]);
            $option->response[$plugin_path]->new_version = $version_info["version"];
            $option->response[$plugin_path]->id = "0";
        }

        return $option;

    }

    public static function display_plugin_message($message, $is_error = false){

        $style = $is_error ? 'style="background-color: #ffebe8;"' : "";

        echo '</tr><tr class="plugin-update-tr"><td colspan="5" class="plugin-update"><div class="update-message" ' . $style . '>' . $message . '</div></td>';
    }

    public static function display_upgrade_message($plugin_name, $plugin_title, $version, $message, $localization_namespace){
        $upgrade_message = $message .' <a class="thickbox" title="'. $plugin_title .'" href="plugin-install.php?tab=plugin-information&plugin=' . $plugin_name . '&TB_iframe=true&width=640&height=808">'. sprintf(__('View version %s Details', $localization_namespace), $version) . '</a>. ';
        self::display_plugin_message($upgrade_message);
    }

    //Displays current version details on Plugin's page
    public static function display_changelog($offering, $key, $version){

        $body = "key=$key";
        $options = array('method' => 'POST', 'timeout' => 3, 'body' => $body);
        $options['headers'] = array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
            'Content-Length' => strlen($body),
            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
            'Referer' => get_bloginfo("url")
        );

        $raw_response = wp_remote_request(GRAVITY_MANAGER_URL . "/changelog.php?" . self::get_remote_request_params($offering, $key, $version), $options);

        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
            $page_text = sprintf(__("Oops!! Something went wrong.%sPlease try again or %scontact us%s.", 'gravityformssignature'), "<br/>", "<a href='http://www.gravityforms.com'>", "</a>");
        }
        else{
            $page_text = $raw_response['body'];
            if(substr($page_text, 0, 10) != "<!--GFM-->")
                $page_text = "";
        }
        echo stripslashes($page_text);

        exit;
    }


    public static function get_version_info($offering, $key, $version){

        $body = "key=$key";
        $options = array('method' => 'POST', 'timeout' => 3, 'body' => $body);
        $options['headers'] = array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
            'Content-Length' => strlen($body),
            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
            'Referer' => get_bloginfo("url")
        );
        $url = GRAVITY_MANAGER_URL . "/version.php?" . self::get_remote_request_params($offering, $key, $version);
        $raw_response = wp_remote_request($url, $options);

        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code'])
            return -1;
        else
        {
            $ary = explode("||", $raw_response['body']);
            return array("is_valid_key" => $ary[0], "version" => $ary[1], "url" => $ary[2]);
        }
    }

    public static function get_remote_request_params($offering, $key, $version){
        global $wpdb;
        return sprintf("of=%s&key=%s&v=%s&wp=%s&php=%s&mysql=%s", urlencode($offering), urlencode($key), urlencode($version), urlencode(get_bloginfo("version")), urlencode(phpversion()), urlencode($wpdb->db_version()));
    }

}
?>
