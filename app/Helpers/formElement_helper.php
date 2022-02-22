<?php

function set_val(string $val, array $data = null)
{
  return set_value($val) != '' ? set_value($val) : ($data != null ? $data[$val] : '');
}

function set_sel(string $key, string $val, array $value, array $is_edit = null)
{
  return set_select($val, $value['id']) != '' ? set_select($key, $value['id']) : ($is_edit != null ? ($is_edit[$key] == $value['id'] ? 'selected' : '') : '');
}

function makeFormElement(array $elements, $validation = null, array $is_edit = null)
{
  $string = '';

  foreach ($elements as $key => $element) {

    $errorChecker = $validation != null && isset($validation) && $validation->getError($key);

    $string .= '<div class="' . $element['column'] . '">';
    $string .= '<label for="' . $key . '" class="form-label">' . $element['label'] . '</label>';

    // if element type is string, date or email
    if ($element['type'] == 'text' || $element['type'] == 'date' || $element['type'] == 'email') {
      $string .= '<input type="' . $element['type'] . '" class="form-control ' . ($errorChecker ? 'is-invalid' : '') . '" name="' . $key . '" id="' . $key . '" value="' . set_val($key, $is_edit) . '" style="' . (array_key_exists('style', $element) ? $element['style'] : '') . '" ' . (array_key_exists('placeholder', $element) ? 'placeholder="' . $element['placeholder'] . '"' : '') . '>';
    }


    // if element type is string, date or email
    if ($element['type'] == 'password') {
      $string .= '<input type="password" class="form-control ' . ($errorChecker ? 'is-invalid' : '') . '" name="' . $key . '" id="' . $key . '" value="' . set_value($key, '') . '" style="' . (array_key_exists('style', $element) ? $element['style'] : '') . '" placeholder="●●●●●●●●">';
    }

    // if element type is textarea
    if ($element['type'] == 'textarea') {
      $string .= '<textarea name="' . $key . '" class="form-control" rows="' . $element['rows'] . '" ' . (array_key_exists('placeholder', $element) ? 'placeholder="' . $element['placeholder'] . '"' : '') . '>' . set_val($key, $is_edit) . '</textarea>';
    }

    if (!isset($element['is_jsselect']) && $element['type'] == 'select') {
      $string .= '<select class="form-control" name="' . $key . '">';
      $string .= '<option value="">' . $element['empty_label'] . '</option>';
      foreach ($element['data'] as $k => $v) {
        $string .= '<option value="' . $v['id'] . '" ' . set_sel($key, $k, $v, $is_edit) . '>' . $v[$element['show_value']] . '</option>';
      }
      $string .= '</select>';
    }

    if (isset($element['is_jsselect']) && $element['type'] == 'select') {
      $string .= '<select class="form control" name="' . $key . '">';
      if (isset($element['data']['id']) && $element['data']['show_value'])
        $string .= '<option value="' . $element['data']['id'] . '" selected>' . $element['data']['show_value'] . '</option>';
      $string .= '</select>';
    }

    // add custom html element
    if (isset($element['custom_html'])) {
      $string .= $element['custom_html'][0];
    }

    // check validation
    if ($errorChecker) {
      $string .= '<div class="alert alert-danger small mt-2 p-2 text-center">';
      $string .= $validation->getError($key);
      $string .= '</div>';
    }
    $string .= '</div>';
  }
  return $string;
}
