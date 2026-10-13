<?php
/*
regex rules
    2 decimal only (non-negative numbers) // Matches: 0, 123, 45.6, 78.90, Rejects: 12.345, -5, abc
        'regex' => '/^\d+(\.\d{1,2})?$/'

    No special characters (letters & digits only, no - _ . , ! etc.) // Matches: JohnDoe, Farm123, House 45, Rejects: John_Doe, 123!, Name@Farm
        'regex' => '/^[A-Za-z0-9 ]+$/'
    
    Integers only (non-negative) // Matches: 0, 1, 42, Rejects: -1, 12.3, abc
        'regex' => '/^\d+$/'
    
    Positive integers only (>=1) // Matches: 1, 42, 999, Rejects: 0, 012, -3
        'regex' => '/^[1-9]\d*$/'
    
    Optional decimal (any precision, non-negative) // Matches: 0, 123, 12.345, Rejects: -5, abc
        'regex' => '/^[A-Za-z ]+$/'
    
    Letters only (no numbers, no special characters) // Matches: John, San Francisco, Rejects: John123, Name_!
        'regex' => '/^[A-Za-z ]+$/'

    Alphanumeric + spaces (no special chars) // Matches: Farm 45, Block12, Rejects: Block-12, Name@Farm
        'regex' => '/^[A-Za-z0-9 ]+$/'
    
    Email format // Matches standard emails: test@example.com, Rejects: abc, a@b, test@domain
        'regex' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/'
    
    No spaces allowed // Matches: Username123, Rejects: John Doe, 123
        'regex' => '/^\S+$/'
    
    Date format YYYY-MM-DD (basic check) // Matches: 2026-04-04, Rejects: 04-04-2026, 2026/04/04
        'regex' => '/^\d{4}-\d{2}-\d{2}$/'
    
    Basic 4-digit year (string) // Matches: 1900, 2026, 2050, Rejects: 123, 20265, 20a6, abcd
        'regex' => '/^\d{4}$/'
    
    Year within a realistic range (e.g., 1900–current year) // Matches: 1900–2099, Rejects: 1899, 2100, abcd, can combine with validator min/max
        'regex' => '/^(19\d{2}|20\d{2})$/'
*/
function validate(array $input, array $rules, bool $strict = true): array
{
    $data = [];
    $errors = [];

    // Strict mode: reject unknown fields
    if ($strict) {
        foreach ($input as $key => $_) {
            if (!array_key_exists($key, $rules)) {
                // Make field name readable: capitalize words and replace underscores
                $fieldName = ucwords(str_replace('_', ' ', $key));
                $errors[$key] = "$fieldName is an unexpected field.";
            }
        }
    }

    foreach ($rules as $field => $rule) {
        $value = $input[$field] ?? ($rule['default'] ?? null);

        // Trim early (fix whitespace issue)
        if (is_string($value)) {
            $value = trim($value);
        }

        // Required check
        if ($value === null || $value === '') {
            if (!empty($rule['required'])) {
                $errors[$field] = "$field is required.";
                continue;
            } else {
                $data[$field] = null;
                continue;
            }
        }

        $type = $rule['type'] ?? 'string';

        // STRICT type validation (no silent casting)
        switch ($type) {
            case 'int':
                //if (!filter_var($value, FILTER_VALIDATE_INT)) {
                if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $errors[$field] = "$field must be integer.";
                    continue 2;
                }
                $value = (int) $value;
                break;

            case 'float':
                if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                    $errors[$field] = "$field must be float.";
                    continue 2;
                }
                $value = (float) $value;
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "$field must be valid email.";
                    continue 2;
                }
                $value = strtolower($value);
                break;

            case 'year':
                if (!preg_match('/^\d{4}$/', $value)) {
                    $errors[$field] = "$field must be a 4-digit year.";
                    continue 2;
                }
                $year = (int) $value;
                $min = $rule['min'] ?? 1900;
                $max = $rule['max'] ?? (int) date('Y');

                if ($year < $min || $year > $max) {
                    $errors[$field] = "$field must be between $min and $max.";
                    continue 2;
                }

                $value = (string)$year; // keep as string for DB
                break;

            case 'date':
                try {
                    $format = $rule['format'] ?? 'Y-m-d'; // default full date
                    $dt = DateTime::createFromFormat($format, $value);
                    $errorsInDate = DateTime::getLastErrors();
                    if (!$dt || $errorsInDate['warning_count'] > 0 || $errorsInDate['error_count'] > 0) {
                        $errors[$field] = "$field must be a valid date in $format format.";
                        continue 2;
                    }

                    // Min/max date
                    if (isset($rule['min'])) {
                        $minDate = DateTime::createFromFormat($format, $rule['min']);
                        if ($dt < $minDate) {
                            $errors[$field] = "$field must be on or after {$rule['min']}.";
                            continue 2;
                        }
                    }
                    if (isset($rule['max'])) {
                        $maxDate = DateTime::createFromFormat($format, $rule['max']);
                        if ($dt > $maxDate) {
                            $errors[$field] = "$field must be on or before {$rule['max']}.";
                            continue 2;
                        }
                    }

                    $value = $dt->format($format); // store as string

                } catch (Exception $e) {
                    $errors[$field] = "$field must be a valid date.";
                    continue 2;
                }
                break;

            case 'yesno':
                $allowed = ['YES', 'NO'];
                if (!in_array($value, $allowed, true)) {
                    $errors[$field] = "$field must be YES or NO.";
                    continue 2;
                }
                break;

            case 'suffix':
                $allowed = ['', 'JR.', 'SR.', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
                if (!in_array(strtoupper($value), $allowed, true)) {
                    $errors[$field] = "$field has invalid value.";
                    continue 2;
                }
                break;

            case 'string':
            default:
                $value = (string) $value;
        }

        // Numeric min/max (NEW)
        if (in_array($type, ['int', 'float'], true)) {
            if (isset($rule['min']) && $value < $rule['min']) {
                $errors[$field] = "$field must be at least {$rule['min']}.";
                continue;
            }

            if (isset($rule['max']) && $value > $rule['max']) {
                $errors[$field] = "$field must not exceed {$rule['max']}.";
                continue;
            }
        }

        // Length checks
        if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
            $errors[$field] = "$field too short.";
            continue;
        }

        if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
            $errors[$field] = "$field too long.";
            continue;
        }

        // Enum (whitelist)
        if (isset($rule['enum']) && !in_array($value, $rule['enum'], true)) {
            $errors[$field] = "$field has invalid value.";
            continue;
        }

        // Regex
        if (isset($rule['regex']) && !preg_match($rule['regex'], $value)) {
            $errors[$field] = "$field format invalid.";
            continue;
        }

        $data[$field] = $value;
    }

    return ['data' => $data, 'errors' => $errors];
}

/* example usage
// $lastname  = (string) ($_POST['search-lastname'] ?? '');
// $firstname = (string) ($_POST['search-firstname'] ?? '');
// $middlename= (string) ($_POST['search-middlename'] ?? '');
// $suffix    = (string) ($_POST['search-suffix'] ?? '');

======================================================
$rules = [
    'search-lastname' => [
        'type' => 'string',
        'max_length' => 100
    ],
    'search-firstname' => [
        'type' => 'string',
        'max_length' => 100
    ],
    'search-middlename' => [
        'type' => 'string',
        'max_length' => 100
    ],
    'search-suffix' => [
        'type' => 'string',
        'enum' => ['', 'Jr', 'Sr', 'III', 'IV']
    ],
];

$result = validate($_POST, $rules);

if (!empty($result['errors'])) {
    // handle errors, e.g., return JSON or show alert
    je([
        'message' => $result['errors'],
        'type' => 'error'
    ], 'json');
    exit;
}

$data = $result['data'];

$lastname   = $data['search-lastname'];
$firstname  = $data['search-firstname'];
$middlename = $data['search-middlename'];
$suffix     = $data['search-suffix'];

// now pass variables/data to Model here....


*/