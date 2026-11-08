<?php

/** Employee-profile payroll inputs; mirrors the existing Payroll Items forms. */
class EmployeeFinancials
{
    public static function definitions(): array
    {
        $amount = ['name' => 'amount', 'label' => 'Amount', 'input' => 'number', 'required' => true, 'min' => '0.01', 'step' => '0.01'];
        $dates = [
            ['name' => 'effective_from', 'label' => 'Effective From', 'input' => 'date', 'required' => true],
            ['name' => 'effective_to', 'label' => 'Effective To', 'input' => 'date', 'required' => false],
        ];
        $recurring = ['name' => 'is_recurring', 'label' => 'Recurring', 'input' => 'bool', 'required' => true, 'default' => '1'];
        $definitions = [
            'compensations' => [
                'title' => 'Compensations', 'singular' => 'Compensation', 'table' => 'payroll_employee_compensations',
                'fields' => array_merge([
                    ['name' => 'salary_type', 'label' => 'Salary Type', 'input' => 'select', 'required' => true, 'options' => ['MONTHLY', 'DAILY', 'HOURLY'], 'default' => 'MONTHLY'],
                    ['name' => 'payroll_frequency', 'label' => 'Payroll Frequency', 'input' => 'select', 'required' => true, 'options' => ['WEEKLY', 'SEMI_MONTHLY', 'MONTHLY'], 'default' => 'SEMI_MONTHLY'],
                    ['name' => 'salary_rate', 'label' => 'Salary Rate', 'input' => 'number', 'required' => true, 'min' => '0.01', 'step' => '0.01'],
                ], $dates),
            ],
        ];
        foreach (['allowances' => 'Allowance', 'commissions' => 'Commission', 'incentives' => 'Incentive', 'deductions' => 'Deduction'] as $key => $singular) {
            $typeField = strtolower($singular) . '_type_id';
            $recurring['default'] = $key === 'incentives' ? '0' : '1';
            $definitions[$key] = [
                'title' => ucfirst($key), 'singular' => $singular, 'table' => 'payroll_employee_' . $key,
                'typeTable' => 'payroll_' . strtolower($singular) . '_types', 'typeField' => $typeField,
                'fields' => array_merge([
                    ['name' => $typeField, 'label' => $singular . ' Type', 'input' => 'type', 'required' => true],
                    $amount,
                ], $key === 'commissions' ? [] : [$recurring], $dates),
            ];
        }
        $definitions['cash-advances'] = [
            'title' => 'Cash Advances', 'singular' => 'Cash Advance', 'table' => 'payroll_cash_advances',
            'fields' => [
                $amount,
                ['name' => 'deduction_type', 'label' => 'Deduction Type', 'input' => 'select', 'required' => true, 'options' => ['SINGLE', 'INSTALLMENT'], 'default' => 'INSTALLMENT'],
                ['name' => 'installment_amount', 'label' => 'Installment Amount', 'input' => 'number', 'required' => false, 'min' => '0', 'step' => '0.01'],
                ['name' => 'remaining_balance', 'label' => 'Remaining Balance', 'input' => 'number', 'required' => false, 'min' => '0', 'step' => '0.01'],
                ['name' => 'status', 'label' => 'Status', 'input' => 'select', 'required' => true, 'options' => ['ACTIVE', 'PAID', 'CANCELLED'], 'default' => 'ACTIVE'],
            ],
        ];
        return $definitions;
    }

    public static function clientDefinitions(): array
    {
        return array_map(static fn(array $definition): array => array_intersect_key($definition, array_flip(['title', 'singular', 'fields'])), self::definitions());
    }

    /** Keep Payroll Items numeric conversion, effective-date and cash-advance semantics. */
    public static function validate(array $definition, array $input): array
    {
        $data = [];
        foreach ($definition['fields'] as $field) {
            $value = $input[$field['name']] ?? null;
            if ($value !== null && !is_scalar($value)) {
                throw new InvalidArgumentException($field['label'] . ' has invalid value.');
            }
            $value = is_string($value) ? trim($value) : $value;
            if ($value === '' || $value === null) {
                if ($field['required']) {
                    throw new InvalidArgumentException($field['label'] . ' is required.');
                }
                $data[$field['name']] = null;
                continue;
            }
            if (in_array($field['input'], ['type', 'bool'], true)) {
                if (filter_var($value, FILTER_VALIDATE_INT) === false
                    || ($field['input'] === 'bool' && !in_array((int) $value, [0, 1], true))) {
                    throw new InvalidArgumentException($field['label'] . ' has invalid value.');
                }
                $value = (int) $value;
            } elseif ($field['input'] === 'number') {
                if (!is_numeric($value) || !is_finite((float) $value) || (float) $value < (float) $field['min']) {
                    throw new InvalidArgumentException($field['label'] . ' must be at least ' . $field['min'] . '.');
                }
                $value = (float) $value;
            } elseif ($field['input'] === 'date') {
                $date = DateTime::createFromFormat('Y-m-d', (string) $value);
                $errors = DateTime::getLastErrors();
                if (!$date || (is_array($errors) && ($errors['warning_count'] || $errors['error_count']))) {
                    throw new InvalidArgumentException($field['label'] . ' must be a valid date.');
                }
                $value = $date->format('Y-m-d');
            } else {
                $value = strtoupper((string) $value);
                if (!in_array($value, $field['options'], true)) {
                    throw new InvalidArgumentException($field['label'] . ' has invalid value.');
                }
            }
            $data[$field['name']] = $value;
        }
        if (!empty($data['effective_to']) && $data['effective_to'] < $data['effective_from']) {
            throw new InvalidArgumentException('Effective to must be on or after effective from.');
        }
        if (array_key_exists('remaining_balance', $data) && $data['remaining_balance'] === null) {
            $data['remaining_balance'] = $data['amount'];
        }
        if (($data['deduction_type'] ?? null) === 'SINGLE') {
            $data['installment_amount'] = null;
        }
        return $data;
    }
}
