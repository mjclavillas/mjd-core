<?php
namespace Mark\MjdCore\Http;

class Validator
{
    protected $errors = [];

    public function validate(array $data, array $rules)
    {
        foreach ($rules as $field => $fieldRules) {
            $rulesArray = explode('|', $fieldRules);
            $value = $data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    protected function applyRule($field, $value, $rule)
    {
        if ($rule === 'required' && empty($value)) {
            $this->errors[$field][] = "The {$field} field is required.";
        }

        if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "The {$field} must be a valid email address.";
        }

        if (str_starts_with($rule, 'min:')) {
            $min = (int) explode(':', $rule)[1];
            if (strlen($value) < $min) {
                $this->errors[$field][] = "The {$field} must be at least {$min} characters.";
            }
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}