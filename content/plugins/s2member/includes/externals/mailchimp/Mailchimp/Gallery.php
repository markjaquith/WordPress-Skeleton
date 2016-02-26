<?php

class Mailchimp_Gallery {
    public function __construct(Mailchimp $master) {
        $this->master = $master;
    }

    /**
     * Return a section of the image gallery
     * @param associative_array $opts
     *     - type string optional the gallery type to return - images or files - default to images
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     *     - sort_by string optional field to sort by - one of size, time, name - defaults to time
     *     - sort_dir string optional field to sort by - one of asc, desc - defaults to desc
     *     - search_term string optional a term to search for in names
     *     - folder_id int optional to return files that are in a specific folder.  id returned by the list-folders call
     * @return associative_array the matching gallery items
     *     - total int the total matching items
     *     - data array structs for each item included in the set, including:
     *         - id int the id of the file
     *         - name string the file name
     *         - time string the creation date for the item
     *         - size int the file size in bytes
     *         - full string the url to the actual item in the gallery
     *         - thumb string a url for a thumbnail that can be used to represent the item, generally an image thumbnail or an icon for a file type
     */
    public function getList($opts=array()) {
        $_params = array("opts" => $opts);
        return $this->master->call('gallery/list', $_params);
    }

    /**
     * Return a list of the folders available to the file gallery
     * @param associative_array $opts
     *     - start int optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
     *     - limit int optional for large data sets, the number of results to return - defaults to 25, upper limit set at 100
     *     - search_term string optional a term to search for in names
     * @return associative_array the matching gallery folders
     *     - total int the total matching folders
     *     - data array structs for each folder included in the set, including:
     *         - id int the id of the folder
     *         - name string the file name
     *         - file_count int the number of files in the folder
     */
    public function listFolders($opts=array()) {
        $_params = array("opts" => $opts);
        return $this->master->call('gallery/list-folders', $_params);
    }

    /**
     * Adds a folder to the file gallery
     * @param string $name
     * @return associative_array the new data for the created folder
     *     - data.id int the id of the new folder
     */
    public function addFolder($name) {
        $_params = array("name" => $name);
        return $this->master->call('gallery/add-folder', $_params);
    }

    /**
     * Remove a folder
     * @param int $folder_id
     * @return boolean true/false for success/failure
     */
    public function removeFolder($folder_id) {
        $_params = array("folder_id" => $folder_id);
        return $this->master->call('gallery/remove-folder', $_params);
    }

    /**
     * Add a file to a folder
     * @param int $file_id
     * @param int $folder_id
     * @return boolean true/false for success/failure
     */
    public function addFileToFolder($file_id, $folder_id) {
        $_params = array("file_id" => $file_id, "folder_id" => $folder_id);
        return $this->master->call('gallery/add-file-to-folder', $_params);
    }

    /**
     * Remove a file from a folder
     * @param int $file_id
     * @param int $folder_id
     * @return boolean true/false for success/failure
     */
    public function removeFileFromFolder($file_id, $folder_id) {
        $_params = array("file_id" => $file_id, "folder_id" => $folder_id);
        return $this->master->call('gallery/remove-file-from-folder', $_params);
    }

    /**
     * Remove all files from a folder (Note that the files are not deleted, they are only removed from the folder)
     * @param int $folder_id
     * @return boolean true/false for success/failure
     */
    public function removeAllFilesFromFolder($folder_id) {
        $_params = array("folder_id" => $folder_id);
        return $this->master->call('gallery/remove-all-files-from-folder', $_params);
    }

}


