<?php

class Mailchimp_Templates {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Create a new user template, <strong>NOT</strong> campaign content. These templates can then be applied while creating campaigns.
     * @param string $name
     * @param string $html
     * @param int $folder_id
     * @return associative_array with a single element:
     *     - template_id int the new template id, otherwise an error is thrown.
     */
    public function add($name, $html, $folder_id=null) {
        $_params = array("name" => $name, "html" => $html, "folder_id" => $folder_id);
        return $this->master->call('templates/add', $_params);
    }

    /**
     * Delete (deactivate) a user template
     * @param int $template_id
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function del($template_id) {
        $_params = array("template_id" => $template_id);
        return $this->master->call('templates/del', $_params);
    }

    /**
     * Pull details for a specific template to help support editing
     * @param int $template_id
     * @param string $type
     * @return associative_array info to be used when editing
     *     - default_content associative_array the default content broken down into the named editable sections for the template - dependant upon template, so not documented
     *     - sections associative_array the valid editable section names - dependant upon template, so not documented
     *     - source string the full source of the template as if you exported it via our template editor
     *     - preview string similar to the source, but the rendered version of the source from our popup preview
     */
    public function info($template_id, $type='user') {
        $_params = array("template_id" => $template_id, "type" => $type);
        return $this->master->call('templates/info', $_params);
    }

    /**
     * Retrieve various templates available in the system, allowing some thing similar to our template gallery to be created.
     * @param associative_array $types
     *     - user boolean Custom templates for this user account. Defaults to true.
     *     - gallery boolean Templates from our Gallery. Note that some templates that require extra configuration are withheld. (eg, the Etsy template). Defaults to false.
     *     - base boolean Our "start from scratch" extremely basic templates. Defaults to false. As of the 9.0 update, "base" templates are no longer available via the API because they are now all saved Drag & Drop templates.
     * @param associative_array $filters
     *     - category string optional for Gallery templates only, limit to a specific template category
     *     - folder_id string user templates, limit to this folder_id
     *     - include_inactive boolean user templates are not deleted, only set inactive. defaults to false.
     *     - inactive_only boolean only include inactive user templates. defaults to false.
     *     - include_drag_and_drop boolean Include templates created and saved using the new Drag & Drop editor. <strong>Note:</strong> You will not be able to edit or create new drag & drop templates via this API. This is useful only for creating a new campaign based on a drag & drop template.
     * @return associative_array for each type
     *     - user array matching user templates, if requested.
     *         - id int Id of the template
     *         - name string Name of the template
     *         - layout string General description of the layout of the template
     *         - category string The category for the template, if there is one.
     *         - preview_image string If we've generated it, the url of the preview image for the template. We do out best to keep these up to date, but Preview image urls are not guaranteed to be available
     *         - date_created string The date/time the template was created
     *         - active boolean whether or not the template is active and available for use.
     *         - edit_source boolean Whether or not you are able to edit the source of a template.
     *         - folder_id boolean if it's in one, the folder id
     *     - gallery array matching gallery templates, if requested.
     *         - id int Id of the template
     *         - name string Name of the template
     *         - layout string General description of the layout of the template
     *         - category string The category for the template, if there is one.
     *         - preview_image string If we've generated it, the url of the preview image for the template. We do out best to keep these up to date, but Preview image urls are not guaranteed to be available
     *         - date_created string The date/time the template was created
     *         - active boolean whether or not the template is active and available for use.
     *         - edit_source boolean Whether or not you are able to edit the source of a template.
     *     - base array matching base templates, if requested. (Will always be empty as of 9.0)
     */
    public function getList($types=array(), $filters=array()) {
        $_params = array("types" => $types, "filters" => $filters);
        return $this->master->call('templates/list', $_params);
    }

    /**
     * Undelete (reactivate) a user template
     * @param int $template_id
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function undel($template_id) {
        $_params = array("template_id" => $template_id);
        return $this->master->call('templates/undel', $_params);
    }

    /**
     * Replace the content of a user template, <strong>NOT</strong> campaign content.
     * @param int $template_id
     * @param associative_array $values
     *     - name string the name for the template - names must be unique and a max of 50 bytes
     *     - html string a string specifying the entire template to be created. This is <strong>NOT</strong> campaign content. They are intended to utilize our <a href="http://www.mailchimp.com/resources/email-template-language/" target="_blank">template language</a>.
     *     - folder_id int the folder to put this template in - 0 or a blank values will remove it from a folder.
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function update($template_id, $values) {
        $_params = array("template_id" => $template_id, "values" => $values);
        return $this->master->call('templates/update', $_params);
    }

}


