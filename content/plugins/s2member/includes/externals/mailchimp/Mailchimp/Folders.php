<?php

class Mailchimp_Folders {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Add a new folder to file campaigns, autoresponders, or templates in
     * @param string $name
     * @param string $type
     * @return associative_array with a single value:
     *     - folder_id int the folder_id of the newly created folder.
     */
    public function add($name, $type) {
        $_params = array("name" => $name, "type" => $type);
        return $this->master->call('folders/add', $_params);
    }

    /**
     * Delete a campaign, autoresponder, or template folder. Note that this will simply make whatever was in the folder appear unfiled, no other data is removed
     * @param int $fid
     * @param string $type
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function del($fid, $type) {
        $_params = array("fid" => $fid, "type" => $type);
        return $this->master->call('folders/del', $_params);
    }

    /**
     * List all the folders of a certain type
     * @param string $type
     * @return array structs for each folder, including:
     *     - folder_id int Folder Id for the given folder, this can be used in the campaigns/list() function to filter on.
     *     - name string Name of the given folder
     *     - date_created string The date/time the folder was created
     *     - type string The type of the folders being returned, just to make sure you know.
     *     - cnt int number of items in the folder.
     */
    public function getList($type) {
        $_params = array("type" => $type);
        return $this->master->call('folders/list', $_params);
    }

    /**
     * Update the name of a folder for campaigns, autoresponders, or templates
     * @param int $fid
     * @param string $name
     * @param string $type
     * @return associative_array with a single entry:
     *     - complete bool whether the call worked. reallistically this will always be true as errors will be thrown otherwise.
     */
    public function update($fid, $name, $type) {
        $_params = array("fid" => $fid, "name" => $name, "type" => $type);
        return $this->master->call('folders/update', $_params);
    }

}


