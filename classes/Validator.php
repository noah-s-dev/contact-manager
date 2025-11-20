<?php
/**
 * Input Validation Class
 * Provides comprehensive validation for user inputs
 */

class Validator {
    private $errors = [];
    private $data = [];

    /**
     * Constructor
     * @param array $data
     */
    public function __construct($data = []) {
        $this->data = $data;
        $this->errors = [];
    }

    /**
     * Validate required field
     * @param string $field
     * @param string $message
     * @return self
     */
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || empty(trim($this->data[$field]))) {
            $this->errors[$field][] = $message ?? ucfirst($field) . ' is required.';
        }
        return $this;
    }

    /**
     * Validate minimum length
     * @param string $field
     * @param int $min
     * @param string $message
     * @return self
     */
    public function minLength($field, $min, $message = null) {
        if (isset($this->data[$field]) && strlen(trim($this->data[$field])) < $min) {
            $this->errors[$field][] = $message ?? ucfirst($field) . " must be at least {$min} characters long.";
        }
        return $this;
    }

    /**
     * Validate maximum length
     * @param string $field
     * @param int $max
     * @param string $message
     * @return self
     */
    public function maxLength($field, $max, $message = null) {
        if (isset($this->data[$field]) && strlen(trim($this->data[$field])) > $max) {
            $this->errors[$field][] = $message ?? ucfirst($field) . " must not exceed {$max} characters.";
        }
        return $this;
    }

    /**
     * Validate email format
     * @param string $field
     * @param string $message
     * @return self
     */
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field][] = $message ?? 'Please enter a valid email address.';
            }
        }
        return $this;
    }

    /**
     * Validate phone number
     * @param string $field
     * @param string $message
     * @return self
     */
    public function phone($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $phone = preg_replace('/\D/', '', $this->data[$field]);
            if (strlen($phone) < 10 || strlen($phone) > 15) {
                $this->errors[$field][] = $message ?? 'Please enter a valid phone number.';
            }
        }
        return $this;
    }

    /**
     * Validate that field matches another field
     * @param string $field
     * @param string $matchField
     * @param string $message
     * @return self
     */
    public function matches($field, $matchField, $message = null) {
        if (isset($this->data[$field]) && isset($this->data[$matchField])) {
            if ($this->data[$field] !== $this->data[$matchField]) {
                $this->errors[$field][] = $message ?? ucfirst($field) . ' must match ' . $matchField . '.';
            }
        }
        return $this;
    }

    /**
     * Validate alphanumeric characters only
     * @param string $field
     * @param string $message
     * @return self
     */
    public function alphanumeric($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!ctype_alnum($this->data[$field])) {
                $this->errors[$field][] = $message ?? ucfirst($field) . ' must contain only letters and numbers.';
            }
        }
        return $this;
    }

    /**
     * Validate against SQL injection patterns
     * @param string $field
     * @param string $message
     * @return self
     */
    public function noSqlInjection($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $dangerous_patterns = [
                '/(\bUNION\b|\bSELECT\b|\bINSERT\b|\bUPDATE\b|\bDELETE\b|\bDROP\b|\bCREATE\b|\bALTER\b)/i',
                '/(\-\-|\#|\/\*|\*\/)/i',
                '/(\bOR\b|\bAND\b)\s+\d+\s*=\s*\d+/i'
            ];
            
            foreach ($dangerous_patterns as $pattern) {
                if (preg_match($pattern, $this->data[$field])) {
                    $this->errors[$field][] = $message ?? 'Invalid characters detected in ' . $field . '.';
                    break;
                }
            }
        }
        return $this;
    }

    /**
     * Validate against XSS patterns
     * @param string $field
     * @param string $message
     * @return self
     */
    public function noXss($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $dangerous_patterns = [
                '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
                '/javascript:/i',
                '/on\w+\s*=/i'
            ];
            
            foreach ($dangerous_patterns as $pattern) {
                if (preg_match($pattern, $this->data[$field])) {
                    $this->errors[$field][] = $message ?? 'Invalid content detected in ' . $field . '.';
                    break;
                }
            }
        }
        return $this;
    }

    /**
     * Custom validation rule
     * @param string $field
     * @param callable $callback
     * @param string $message
     * @return self
     */
    public function custom($field, $callback, $message = null) {
        if (isset($this->data[$field])) {
            if (!call_user_func($callback, $this->data[$field])) {
                $this->errors[$field][] = $message ?? ucfirst($field) . ' is invalid.';
            }
        }
        return $this;
    }

    /**
     * Check if validation passed
     * @return bool
     */
    public function passes() {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     * @return bool
     */
    public function fails() {
        return !$this->passes();
    }

    /**
     * Get all errors
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get errors for a specific field
     * @param string $field
     * @return array
     */
    public function getFieldErrors($field) {
        return $this->errors[$field] ?? [];
    }

    /**
     * Get first error for a field
     * @param string $field
     * @return string|null
     */
    public function getFirstError($field) {
        $errors = $this->getFieldErrors($field);
        return !empty($errors) ? $errors[0] : null;
    }

    /**
     * Get all errors as flat array
     * @return array
     */
    public function getAllErrors() {
        $allErrors = [];
        foreach ($this->errors as $fieldErrors) {
            $allErrors = array_merge($allErrors, $fieldErrors);
        }
        return $allErrors;
    }

    /**
     * Sanitize data
     * @return array
     */
    public function getSanitizedData() {
        $sanitized = [];
        foreach ($this->data as $key => $value) {
            $sanitized[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
        }
        return $sanitized;
    }
}
?>

