<?php

namespace SendGrid;

class Email
{
    public
        $to,
        $toName,
        $from,
        $fromName,
        $replyTo,
        $cc,
        $ccName,
        $bcc,
        $bccName,
        $subject,
        $text,
        $html,
        $date,
        $content,
        $headers,
        $smtpapi,
        $attachments;

    public function __construct()
    {
        $this->fromName = false;
        $this->replyTo = false;
        $this->smtpapi = new \Smtpapi\Header();
    }

    /**
     * _removeFromList
     * Given a list of key/value pairs, removes the associated keys
     * where a value matches the given string ($item)
     *
     * @param Array $list - the list of key/value pairs
     * @param String $item - the value to be removed
     */
    private function _removeFromList(&$list, $item, $key_field = null)
    {
        foreach ($list as $key => $val) {
            if ($key_field) {
                if ($val[$key_field] == $item) {
                    unset($list[$key]);
                }
            } else {
                if ($val == $item) {
                    unset($list[$key]);
                }
            }
        }
        //repack the indices
        $list = array_values($list);
    }

    public function addTo($email, $name = null)
    {
        if ($this->to == null) {
            $this->to = array();
        }

        if (is_array($email)) {
            foreach ($email as $e) {
                $this->to[] = $e;
            }
        } else {
            $this->to[] = $email;
        }

        if (is_array($name)) {
            foreach ($name as $n) {
                $this->addToName($n);
            }
        } elseif ($name) {
            $this->addToName($name);
        }

        return $this;
    }

    public function addSmtpapiTo($email, $name = null)
    {
        $this->smtpapi->addTo($email, $name);

        return $this;
    }

    public function setTos(array $emails)
    {
        $this->to = $emails;

        return $this;
    }

    public function setSmtpapiTos(array $emails)
    {
        $this->smtpapi->setTos($emails);

        return $this;
    }

    public function addToName($name)
    {
        if ($this->toName == null) {
            $this->toName = array();
        }

        $this->toName[] = $name;

        return $this;
    }

    public function getToNames()
    {
        return $this->toName;
    }

    public function setFrom($email)
    {
        $this->from = $email;

        return $this;
    }

    public function getFrom($as_array = false)
    {
        if ($as_array && ($name = $this->getFromName())) {
            return array("$this->from" => $name);
        } else {
            return $this->from;
        }
    }

    public function setFromName($name)
    {
        $this->fromName = $name;

        return $this;
    }

    public function getFromName()
    {
        return $this->fromName;
    }

    public function setReplyTo($email)
    {
        $this->replyTo = $email;

        return $this;
    }

    public function getReplyTo()
    {
        return $this->replyTo;
    }

    public function setCc($email)
    {
        $this->cc = array($email);

        return $this;
    }

    public function setCcs(array $email_list)
    {
        $this->cc = $email_list;

        return $this;
    }

    public function addCc($email, $name = null)
    {
        if ($this->cc == null) {
            $this->cc = array();
        }

        if (is_array($email)) {
            foreach ($email as $e) {
                $this->cc[] = $e;
            }
        } else {
            $this->cc[] = $email;
        }

        if (is_array($name)) {
            foreach ($name as $n) {
                $this->addCcName($n);
            }
        } elseif ($name) {
            $this->addCcName($name);
        }

        return $this;
    }

    public function addCcName($name)
    {
        if ($this->ccName == null) {
            $this->ccName = array();
        }

        $this->ccName[] = $name;

        return $this;
    }

    public function removeCc($email)
    {
        $this->_removeFromList($this->cc, $email);

        return $this;
    }

    public function getCcs()
    {
        return $this->cc;
    }

    public function getCcNames()
    {
        return $this->ccName;
    }

    public function setBcc($email)
    {
        $this->bcc = array($email);

        return $this;
    }

    public function setBccs($email_list)
    {
        $this->bcc = $email_list;

        return $this;
    }

    public function addBcc($email, $name = null)
    {
        if ($this->bcc == null) {
            $this->bcc = array();
        }

        if (is_array($email)) {
            foreach ($email as $e) {
                $this->bcc[] = $e;
            }
        } else {
            $this->bcc[] = $email;
        }

        if (is_array($name)) {
            foreach ($name as $n) {
                $this->addBccName($n);
            }
        } elseif ($name) {
            $this->addBccName($name);
        }

        return $this;
    }

    public function addBccName($name)
    {
        if ($this->bccName == null) {
            $this->bccName = array();
        }

        $this->bccName[] = $name;

        return $this;
    }

    public function getBccNames()
    {
        return $this->bccName;
    }

    public function removeBcc($email)
    {
        $this->_removeFromList($this->bcc, $email);

        return $this;
    }

    public function getBccs()
    {
        return $this->bcc;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function setSendAt($timestamp)
    {
        $this->smtpapi->setSendAt($timestamp);

        return $this;
    }

    public function setSendEachAt(array $timestamps)
    {
        $this->smtpapi->setSendEachAt($timestamps);

        return $this;
    }

    public function addSendEachAt($timestamp)
    {
        $this->smtpapi->addSendEachAt($timestamp);

        return $this;
    }

    /**
     * Convenience method to add template
     *
     * @param string The id of the template
     *
     * @return $this
     */
    public function setTemplateId($templateId)
    {
        $this->addFilter('templates', 'enabled', 1);
        $this->addFilter('templates', 'template_id', $templateId);

        return $this;
    }

    /** Convenience method to set asm group id
     *
     * @param string the group id
     *
     * @return $this
     */
    public function setAsmGroupId($groupId)
    {
        $this->smtpapi->setASMGroupID($groupId);

        return $this;
    }

    public function setAttachments(array $files)
    {
        $this->attachments = array();

        foreach ($files as $filename => $file) {
            if (is_string($filename)) {
                $this->addAttachment($file, $filename);
            } else {
                $this->addAttachment($file);
            }
        }

        return $this;
    }

    public function setAttachment($file, $custom_filename = null, $cid = null)
    {
        $this->attachments = array($this->getAttachmentInfo($file, $custom_filename, $cid));

        return $this;
    }

    public function addAttachment($file, $custom_filename = null, $cid = null)
    {
        $this->attachments[] = $this->getAttachmentInfo($file, $custom_filename, $cid);

        return $this;
    }

    public function getAttachments()
    {
        return $this->attachments;
    }

    public function removeAttachment($file)
    {
        $this->_removeFromList($this->attachments, $file, "file");

        return $this;
    }

    private function getAttachmentInfo($file, $custom_filename = null, $cid = null)
    {
        $info = pathinfo($file);
        $info['file'] = $file;
        if (!is_null($custom_filename)) {
            $info['custom_filename'] = $custom_filename;
        }
        if ($cid !== null) {
            $info['cid'] = $cid;
        }

        return $info;
    }

    public function setCategories($categories)
    {
        $this->smtpapi->setCategories($categories);

        return $this;
    }

    public function setCategory($category)
    {
        $this->smtpapi->setCategory($category);

        return $this;
    }

    public function addCategory($category)
    {
        $this->smtpapi->addCategory($category);

        return $this;
    }

    public function removeCategory($category)
    {
        $this->smtpapi->removeCategory($category);

        return $this;
    }

    public function setSubstitutions($key_value_pairs)
    {
        $this->smtpapi->setSubstitutions($key_value_pairs);

        return $this;
    }

    public function addSubstitution($from_value, array $to_values)
    {
        $this->smtpapi->addSubstitution($from_value, $to_values);

        return $this;
    }

    public function setSections(array $key_value_pairs)
    {
        $this->smtpapi->setSections($key_value_pairs);

        return $this;
    }

    public function addSection($from_value, $to_value)
    {
        $this->smtpapi->addSection($from_value, $to_value);

        return $this;
    }

    public function setUniqueArgs(array $key_value_pairs)
    {
        $this->smtpapi->setUniqueArgs($key_value_pairs);

        return $this;
    }

    ## synonym method
    public function setUniqueArguments(array $key_value_pairs)
    {
        $this->smtpapi->setUniqueArgs($key_value_pairs);

        return $this;
    }

    public function addUniqueArg($key, $value)
    {
        $this->smtpapi->addUniqueArg($key, $value);

        return $this;
    }

    ## synonym method
    public function addUniqueArgument($key, $value)
    {
        $this->smtpapi->addUniqueArg($key, $value);

        return $this;
    }

    public function setFilters($filter_settings)
    {
        $this->smtpapi->setFilters($filter_settings);

        return $this;
    }

    ## synonym method
    public function setFilterSettings($filter_settings)
    {
        $this->smtpapi->setFilters($filter_settings);

        return $this;
    }

    public function addFilter($filter_name, $parameter_name, $parameter_value)
    {
        $this->smtpapi->addFilter($filter_name, $parameter_name, $parameter_value);

        return $this;
    }

    ## synonym method
    public function addFilterSetting($filter_name, $parameter_name, $parameter_value)
    {
        $this->smtpapi->addFilter($filter_name, $parameter_name, $parameter_value);

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeadersJson()
    {
        if (count($this->getHeaders()) <= 0) {
            return "{}";
        }

        return json_encode($this->getHeaders(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    public function setHeaders($key_value_pairs)
    {
        $this->headers = $key_value_pairs;

        return $this;
    }

    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function removeHeader($key)
    {
        unset($this->headers[$key]);

        return $this;
    }

    public function getSmtpapi()
    {
        return $this->smtpapi;
    }

    public function toWebFormat()
    {
        $web = array(
            'to' => $this->to,
            'from' => $this->getFrom(),
            'x-smtpapi' => $this->smtpapi->jsonString(),
            'subject' => $this->getSubject(),
            'text' => $this->getText(),
            'html' => $this->getHtml(),
            'headers' => $this->getHeadersJson(),
        );

        if ($this->getToNames()) {
            $web['toname'] = $this->getToNames();
        }
        if ($this->getCcs()) {
            $web['cc'] = $this->getCcs();
        }
        if ($this->getCcNames()) {
            $web['ccname'] = $this->getCcNames();
        }
        if ($this->getBccs()) {
            $web['bcc'] = $this->getBccs();
        }
        if ($this->getBccNames()) {
            $web['bccname'] = $this->getBccNames();
        }
        if ($this->getFromName()) {
            $web['fromname'] = $this->getFromName();
        }
        if ($this->getReplyTo()) {
            $web['replyto'] = $this->getReplyTo();
        }
        if ($this->getDate()) {
            $web['date'] = $this->getDate();
        }
        if ($this->smtpapi->to && (count($this->smtpapi->to) > 0)) {
            $web['to'] = "";
        }

        $web = $this->updateMissingTo($web);

        if ($this->getAttachments()) {
            foreach ($this->getAttachments() as $f) {
                $file = $f['file'];
                $extension = null;
                if (array_key_exists('extension', $f)) {
                    $extension = $f['extension'];
                };
                $filename = $f['filename'];
                $full_filename = $filename;

                if (isset($extension)) {
                    $full_filename = $filename . '.' . $extension;
                }
                if (array_key_exists('custom_filename', $f)) {
                    $full_filename = $f['custom_filename'];
                }

                if (array_key_exists('cid', $f)) {
                    $web['content[' . $full_filename . ']'] = $f['cid'];
                }

                $contents = '@' . $file;

                // Guzzle handles this for us.
                // http://guzzle3.readthedocs.org/en/latest/http-client/request.html#post-requests
                // if (class_exists('CurlFile', false)) { // php >= 5.5
                // $contents = new \CurlFile($file, $extension, $filename);
                // }

                $web['files[' . $full_filename . ']'] = $contents;
            };
        }

        return $web;
    }

    /**
     * There needs to be at least 1 to address, or else the mail won't send.
     * This method modifies the data that will be sent via either Rest
     */
    public function updateMissingTo($data)
    {
        if ($this->smtpapi->to && (count($this->smtpapi->to) > 0)) {
            $data['to'] = $this->getFrom();
        }

        return $data;
    }
}
