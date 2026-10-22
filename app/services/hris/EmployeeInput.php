<?php

// Employee-local adapter: do not change the shared validator's legacy callers.
class EmployeeInput
{
    public static function employee(array $input, bool $editing = false): array
    {
        $rules = [
            'employee_no' => ['type' => 'string', 'required' => false],
            'firstname' => ['type' => 'string', 'required' => true],
            'middlename' => ['type' => 'string', 'required' => false],
            'lastname' => ['type' => 'string', 'required' => true],
            'email' => ['type' => 'email', 'required' => false],
            'contact_no' => ['type' => 'string', 'required' => false, 'regex' => '/^[0-9+()\-\s]{7,20}$/'],
            'branch_id' => ['type' => 'int', 'required' => true, 'min' => 1],
            'position_id' => ['type' => 'int', 'required' => false, 'min' => 1],
            'org_unit_id' => ['type' => 'int', 'required' => false, 'min' => 1],
            'employment_type_id' => ['type' => 'int', 'required' => true, 'min' => 1],
            'employment_status_id' => ['type' => 'int', 'required' => false, 'min' => 1],
            'date_hired' => ['type' => 'string', 'required' => true],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];
        if ($editing) {
            $rules['id'] = ['type' => 'int', 'required' => false, 'min' => 1];
            $rules['date_resigned'] = ['type' => 'string', 'required' => false];
        }
        $result = self::fields($input, $rules, ['date_hired', 'date_resigned']);
        // The number is generated on create and immutable on update, even if tampered.
        unset($result['data']['employee_no']);
        if (!empty($result['data']['date_resigned']) && !empty($result['data']['date_hired'])
            && $result['data']['date_resigned'] < $result['data']['date_hired']) {
            $result['errors']['date_resigned'] = 'Date resigned cannot be earlier than date hired.';
        }
        return $result;
    }

    public static function compensation(array $input): array
    {
        // The employee form has always serialized id (blank on create).
        // Keep the existing HRIS frequency choices; do not impose payroll's smaller list.
        return self::fields($input, [
            'id' => ['type' => 'int', 'required' => false, 'min' => 1],
            'employee_id' => ['type' => 'int', 'required' => true, 'min' => 1],
            'salary_type' => ['type' => 'string', 'required' => true, 'enum' => ['MONTHLY', 'DAILY', 'HOURLY']],
            'payroll_frequency' => ['type' => 'string', 'required' => true, 'enum' => ['DAILY', 'WEEKLY', 'BI_WEEKLY', 'SEMI_MONTHLY', 'MONTHLY']],
            // Validate without converting monetary values to binary floating point.
            'salary_rate' => ['type' => 'string', 'required' => true],
            'effective_from' => ['type' => 'string', 'required' => true],
            'effective_to' => ['type' => 'string', 'required' => false],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ], ['effective_from', 'effective_to']);
    }

    public static function csrf(array $input): array
    {
        return self::fields($input, ['csrf_token' => ['type' => 'string', 'required' => true]], []);
    }

    private static function fields(array $input, array $rules, array $dates): array
    {
        // HTTP form fields are strings. Reject arrays/objects/bools before legacy casts.
        foreach ($input as $field => $value) {
            if ($value !== null && !is_string($value) && !is_int($value) && !is_float($value)) {
                return ['data' => [], 'errors' => [$field => 'Invalid field value.']];
            }
            if (is_string($value) && str_contains($value, "\0")) {
                return ['data' => [], 'errors' => [$field => 'Invalid field value.']];
            }
        }
        $result = validate($input, $rules);
        foreach ($dates as $field) {
            $value = $result['data'][$field] ?? null;
            if ($value !== null && (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)
                || !checkdate((int) substr($value, 5, 2), (int) substr($value, 8, 2), (int) substr($value, 0, 4)))) {
                $result['errors'][$field] = "$field must be a valid date in Y-m-d format.";
            }
        }
        if (array_key_exists('salary_rate', $rules) && isset($result['data']['salary_rate'])
            && (!is_numeric($result['data']['salary_rate']) || !is_finite((float) $result['data']['salary_rate'])
                || (float) $result['data']['salary_rate'] <= 0)) {
            $result['errors']['salary_rate'] = 'Please enter a valid salary rate.';
        }
        if (!empty($result['data']['effective_from']) && !empty($result['data']['effective_to'])
            && $result['data']['effective_to'] < $result['data']['effective_from']) {
            $result['errors']['effective_to'] = 'Effective To cannot be earlier than Effective From.';
        }
        return $result;
    }
}
