<?php

namespace Exceedone\Exment\ColumnItems\CustomColumns;

use Exceedone\Exment\Validator;

class SelectValtext extends Select
{
    use ImportValueTrait;
    
    protected function getReturnsValue($select_options, $val, $label)
    {
        // switch column_type and get return value
        $returns = [];
        // loop keyvalue
        foreach ($val as $v) {
            // set whether $label
            if (is_null($v)) {
                $returns[] = null;
            } else {
                $returns[] = $label ? array_get($select_options, $v) : $v;
            }
        }
        return $returns;
    }
    
    protected function setValidates(&$validates)
    {
        $select_options = $this->custom_column->createSelectOptions();
        $validates[] = new Validator\SelectValTextRule($select_options);
    }

    public function saving()
    {
        $v = $this->getPureValue($this->value);
        if (!is_null($v)) {
            return $v;
        }
    }

    /**
     * replace value for import
     *
     * @return array
     */
    protected function getImportValueOption()
    {
        return $this->custom_column->createSelectOptions();
    }

    /**
     * Get pure value. If you want to change the search value, change it with this function.
     *
     * @param string $label
     * @return ?string string:matched, null:not matched
     */
    public function getPureValue($label)
    {
        foreach ($this->custom_column->createSelectOptions() as $key => $q) {
            if ($label == $q) {
                return $key;
            }
        }

        return null;
    }
    

    /**
     * Set Custom Column Option Form. Using laravel-admin form option
     * https://laravel-admin.org/docs/#/en/model-form-fields
     *
     * @param Form $form
     * @return void
     */
    public function setCustomColumnOptionForm(&$form)
    {
        // define select-item
        $form->textarea('select_item_valtext', exmtrans("custom_column.options.select_item"))
            ->required()
            ->help(exmtrans("custom_column.help.select_item_valtext"));

        // enable multiple
        $form->switchbool('multiple_enabled', exmtrans("custom_column.options.multiple_enabled"))
            ->attribute(['data-filtertrigger' =>true])
            ->help(exmtrans("custom_column.help.multiple_enabled"));

            $form->switchbool('radiobutton_enabled', exmtrans("custom_column.options.radiobutton_enabled"))
            ->attribute(['data-filter' => json_encode(['parent' => 1, 'key' => 'options_multiple_enabled', 'value' => '0'])])
            ->help(exmtrans("custom_column.help.radiobutton_enabled"));

        $form->switchbool('checkbox_enabled', exmtrans("custom_column.options.checkbox_enabled"))
            ->attribute(['data-filter' => json_encode(['parent' => 1, 'key' => 'options_multiple_enabled', 'value' => '1'])])
            ->help(exmtrans("custom_column.help.checkbox_enabled"));
    }
}
