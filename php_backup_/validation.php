<?php
// ============================================================
// VALIDATION.PHP — Clase para validar formularios
//
// Uso:
//   $v = new Validation($_POST);
//   $v->addRules('nombre', 'Nombre completo', array('required'=>true, 'length'=>array(3,50)));
//   $result = $v->validate();
//   if ($result['messages'] != '') { /* errores */ }
// ============================================================

class Validation
{
    protected $rules;
    protected $fields;

    public function __construct()
    {
        $args = func_get_args();
        $nargs = func_num_args();
        if ($nargs == 1) {
            $this->fields = $args[0];
        } else {
            $this->fields = array();
        }
    }

    function addRule($field, $rule, $params, $title = "")
    {
        $this->rules[$field][] = array("rule" => $rule, "params" => $params, "title" => $title);
    }

    function addRules($field, $title, $rules)
    {
        $args = func_get_args();
        $nargs = func_num_args();
        for ($i = 2; $i < $nargs; $i++) {
            foreach ($args[$i] as $rule => $params) {
                $this->addRule($field, $rule, $params, $title);
            }
        }
    }

    function validate()
    {
        $errors = array();
        $msg = "";
        $bad_fields = array();

        if (is_array($this->rules)) {
            foreach ($this->rules as $field => $rules) {
                foreach ($rules as $rule) {
                    $rule_name = $rule['rule'];
                    if (method_exists($this, $rule_name)) {
                        $rs = call_user_func(array($this, $rule_name), $field, $this->fields[$field] ?? '', $rule['params']);
                        if ($rs['result'] == false) {
                            $rs['msg'] = ($rule['title'] == "") ? $rs['msg'] : str_replace("{{$field}}", $rule['title'], $rs['msg']);
                            $errors[$field][$rule_name] = $rs['msg'];
                            $msg .= $rs['msg'] . "\n<br>";
                            $bad_fields[] = $field;
                        }
                    }
                }
            }
        }

        $bad_fields = array_unique($bad_fields);
        return array("messages" => $msg, "errors" => $errors, "bad_fields" => $bad_fields);
    }

    function required($field, $value, $params)
    {
        if ($params == true) {
            if ($value == "" || $value == "NULL") {
                return array("result" => false, "msg" => "El campo '{{$field}}' es obligatorio");
            }
        }
        return array("result" => true, "msg" => "");
    }

    function mail($field, $value, $params)
    {
        if ($params != true) return array("result" => true, "msg" => "");
        if ($value == "" || $value == "NULL") return array("result" => true, "msg" => "");
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return array("result" => true, "msg" => "");
        }
        return array("result" => false, "msg" => "El campo '{{$field}}' debe ser un correo electronico");
    }

    function integer($field, $value, $params)
    {
        if ($params != true) return array("result" => true, "msg" => "");
        if ($value == "" || $value == "NULL") return array("result" => true, "msg" => "");
        if (filter_var($value, FILTER_VALIDATE_INT)) {
            return array("result" => true, "msg" => "");
        }
        return array("result" => false, "msg" => "El campo '{{$field}}' debe ser un numero entero");
    }

    function maxLength($field, $value, $params)
    {
        if ($value == "" || $value == "NULL") return array("result" => true, "msg" => "");
        if (strlen($value) <= $params) {
            return array("result" => true, "msg" => "");
        }
        return array("result" => false, "msg" => "El campo '{{$field}}' debe tener maximo $params caracteres");
    }

    function minLength($field, $value, $params)
    {
        if ($value == "" || $value == "NULL") return array("result" => true, "msg" => "");
        if (strlen($value) >= $params) {
            return array("result" => true, "msg" => "");
        }
        return array("result" => false, "msg" => "El campo '{{$field}}' debe tener minimo $params caracteres");
    }

    function length($field, $value, $params)
    {
        if ($value == "" || $value == "NULL") return array("result" => true, "msg" => "");
        if (strlen($value) >= $params[0] && strlen($value) <= $params[1]) {
            return array("result" => true, "msg" => "");
        }
        return array("result" => false, "msg" => "El campo '{{$field}}' debe tener entre {$params[0]} y {$params[1]} caracteres");
    }

    function date($field, $value, $params)
    {
        if ($params != true) return array("result" => true, "msg" => "");
        if ($value == "" || $value == "NULL") return array("result" => true, "msg" => "");
        if ($this->validateDate($value)) {
            return array("result" => true, "msg" => "");
        }
        return array("result" => false, "msg" => "El campo '{{$field}}' debe ser una fecha valida");
    }

    protected function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
