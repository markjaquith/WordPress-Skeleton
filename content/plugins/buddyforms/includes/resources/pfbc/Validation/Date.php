<?php
class Validation_Date extends Validation {
    protected $message = "Error: %element% must contain a valid date.";

    public function isValid($value) {
        try {
            $date = new DateTime($value);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}
