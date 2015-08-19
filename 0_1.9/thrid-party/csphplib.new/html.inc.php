<?php

function back_button() {
    echo "<input type=\"button\" name=\"back\" value=\"&lt;-- Volver\" onClick=\"window.location = document.referrer\">";
}

function chose_input_value($form_value_name, $default_value = '', $post = true) {
    if ($post) {
        if (isset($_POST[$form_value_name])) {
            return $_POST[$form_value_name];
        } else {
            return $default_value;
        }
    } else {
        if (isset($_GET[$form_value_name])) {
            return $_GET[$form_value_name];
        } else {
            return $default_value;
        }
    }
}

class html_select {

    var $data_type;
    var $data_array;
    // Atributos de Conexion
    var $sql_object;
    var $db_table;
    var $field_ID;
    var $field_data;
    var $SQL_code;
    var $where_clause;
    // Atributos de SELECT (HTML)
    var $name;
    var $multiple;
    var $size;
    var $tabindex;
    var $onfocus;
    var $onclick;
    var $onblur;
    var $onchange;
    var $style_class;
    // Atributos varios
    var $order;
    var $blank_option;
    var $selected;
    var $blank_value;
    var $blank_index;
    // codigo HTML
    var $html_code;

    function html_select(&$main_data) {
        $this->where_clause = '';
        $this->name = 'default';
        $this->multiple = 0;
        $this->size = 1;
        $this->tabindex = '';
        $this->order = 0;
        $this->selected = '0';
        $this->blank_option = false;
        $this->blank_value = '';
        $this->blank_index = '';

        if (is_array($main_data)) {
            $this->data_type = 'array';
            $this->data_array = $main_data;
            reset($this->data_array);
            list($key, $value) = each($this->data_array);
            $this->name = $key;
        } elseif (is_object($main_data)) {
            if (!get_class($main_data) == 'cs_db_manager') {
                carvac_err('0208', 'html_select');
                return false;
            } else {
                $this->data_type = 'object';
                $this->sql_object = new sql($main_data);
            }
        } else {
            return false;
        }
    }

    function do_code($do_echo = 0) {
        if ($this->data_type == 'object') {
            if ($this->SQL_code == '') {
                $this->SQL_code = "SELECT {$this->field_ID},{$this->field_data} FROM {$this->db_table}";
                if ($this->where_clause != '') {
                    $this->SQL_code.= " WHERE {$this->where_clause}";
                }
                if ($this->order == 1) {
                    $this->SQL_code.= " ORDER BY {$this->field_data} ASC";
                }
            }

            $this->sql_object->query($this->SQL_code);
        }

        $this->html_code = "\n<!--- Comienzo de list generada por {$this->name} --->\n";
        $this->html_code.= "\t<SELECT TYPE=\"select\" NAME=\"$this->name\"";
        if ($this->multiple) {
            $this->html_code .= " multiple=1";
        }
        if ($this->size > 1) {
            $this->html_code .= " size=$this->size";
        }
        if ($this->tabindex != '') {
            $this->html_code .= " tabindex=$this->tabindex";
        }
        if ($this->style_class != '') {
            $this->html_code .= " CLASS='{$this->style_class}'";
        }
        if (@$this->sql_object->num_rows != 0) {
            if ($this->onfocus != '') {
                $this->html_code .= " onFocus=\"$this->onfocus\"";
            }
            if ($this->onchange != '') {
                $this->html_code .= " onChange=\"$this->onchange\"";
            }
            if ($this->onclick != '') {
                $this->html_code .= " onFocus=\"$this->onclick\"";
            }
        }
        $this->html_code.= ">\n";

        if (($this->blank_option) || ($this->blank_value != '')) {
            $this->html_code.= "\t\t<option value='{$this->blank_index}'>{$this->blank_value}</option>\n";
        }

        if (($this->data_type == 'object') && ($this->sql_object->num_rows != 0)) {
            for ($i = 1; $i <= $this->sql_object->num_rows; $i++) {
                if ($this->selected == $this->sql_object->data[$i][$this->field_ID]) { // si falla la selccion por defecto puede ser el ===
                    $selected = " SELECTED";
                } else {
                    $selected = "";
                }
                $this->html_code.= "\t\t<option value='{$this->sql_object->data[$i][$this->field_ID]}'{$selected}>{$this->sql_object->data[$i][$this->field_data]}</option>\n";
            }
        } elseif ($this->data_type == 'array') {
            while (list($key, $value) = each($this->data_array)) {
                if ($this->selected == $key) {
                    $selected = " SELECTED";
                } else {
                    $selected = "";
                }
                $this->html_code.= "\t\t<option value='{$key}'{$selected}>{$value}</option>\n";
            }
        }
        $this->html_code.="\t</SELECT>\n";
        $this->html_code.= "<!--- Fin de la lista generada por $this->name --->\n";
        if ($do_echo) {
            echo $this->html_code;
        }
        return true;
    }

}

/* HTML 4.01 TABLE VER2 */

class html_table {

    //SQL
    var $sql_link;
    // Atributos de TABLE (HTML)
    var $width;
    var $border;
    var $align;
    var $onmouseover;
    var $onmouseout;
    var $onmousedown;
    // Atributos de STYLE por renglon
    var $style_code;
    var $style_titles;
    var $style_text;
    var $style_numeric;
    var $style_row_1;
    var $style_row_2;
    var $style_link;
    // Atributos varios
    var $numeric;
    var $input_object; // radio;checkbox
    var $dummy;
    var $key_column_alias;
    var $key_column_name;
    // codigo HTML
    var $html_code;

    function html_table(&$sql_link) {
        if (!get_class($sql_link) == 'sql') {
            carvac_err('0207', 'html_table');
            return false;
        } else {
            $this->sql_link = & $sql_link;
        }

        $this->width = '100%';
        $this->align = 'center';
        $this->border = 0;
        $this->style_titles = "carnet-nombre-dosis";
        $this->style_text = 'reporte-texto';
        $this->style_numeric = 'reporte-numeracion-de-resultado';
        $this->style_row_1 = "reporte-tipo-linea-1";
        $this->style_row_2 = "reporte-tipo-linea-2";
        $this->style_link = "reporte-vinculo";
        $this->numeric = 1;
        $this->js_color = 1;
        $this->dummy = 0;
    }

    function do_code($do_echo = 0) {
        $this->html_code = "\n<!--- Comienzo de TABLE --->\n";
        //<TABLE>
        if ($this->dummy == 1) {
            $this->html_code.= "<!-- DUMMY FORM FOR VerPaciente --><form name='VerPaciente' action='app.php?section=VerPaciente' method='post' target='_blank'><input type='hidden' name='userID'></form>\n";
        }
        $this->html_code.= "<TABLE ";
        if ($this->width != '') {
            $this->html_code.= "width=$this->width ";
        }
        if ($this->border != 0) {
            $this->html_code.= "border=$this->border ";
        }
        if ($this->align != '') {
            $this->html_code.= "align=$this->align ";
        }
        $this->html_code.= ">\n";
        // Primer renglon -- Titulos
        $this->html_code.= "\t<!--- INICIO TITULOS --->\n";
        $this->html_code.= "\t<TR CLASS='{$this->style_titles}'>\n";
        if ($this->numeric == 1) {
            $this->html_code.= "\t\t<TD width='1' class='{$this->style_numeric}'></TD>\n";
        }
        if (($this->input_object == 'radio') || ($this->input_object == 'checkbox')) {
            $this->html_code.= "\t\t<TD width='1' class='{$this->style_numeric}'></TD>\n";
        }
        for ($x = 0; $x < $this->sql_link->num_fields; $x++) {
            $this->html_code.= "\t\t<TD $this->style_code>" . $this->sql_link->data[0][$x] . "</TD>\n";
        }
        $this->html_code.= "\t</TR>\n";
        $this->html_code.= "\t<!--- FIN TITULOS --->\n";

        // Celdas de DATOS
        $this->html_code.= "\t<!--- INICIO DATOS --->\n";
        // Ciclo de RENGLONES
        for ($y = 1; $y < $this->sql_link->num_rows + 1; $y++) {
            if (($y % 2) == 0) {
                $type_row = 1;
            } else {
                $type_row = 2;
            }
            $this->html_code.= "\t<TR CLASS='{$this->{'style_row_' . $type_row}}' onMouseOver=\"cambiar_color(this,'over',$type_row);\" onMouseOut=\"cambiar_color(this,'out',$type_row);\" onMouseDown=\"cambiar_color(this,'down',$type_row);\" >\n";
            // NUMERIC
            if ($this->numeric == 1) {
                $this->html_code.= "\t\t<TD width='1' class='{$this->style_numeric}'>{$y}</TD>\n";
            }
            if ($this->input_object == 'radio') {
                $this->html_code.= "\t\t<TD width='1' class='{$this->style_numeric}'><input type='radio' name='{$this->key_column_name}' value='{$this->sql_link->data[$y][$this->key_column_alias]}'></TD>\n";
            }
            if ($this->input_object == 'checkbox') {
                $this->html_code.= "\t\t<TD width='1' class='{$this->style_numeric}'><input type='checkbox' name='{$this->key_column_name}[]' value='{$this->sql_link->data[$y][$this->key_column_alias]}'></TD>\n";
            }
            // Ciclo de CELDAS
            for ($x = 0; $x < $this->sql_link->num_fields; $x++) {
                // DATOS
                $this->html_code.= $this->chose_html($this->sql_link->data, $x, $y);
            }
            $this->html_code.= "\t</TR>\n";
        }
        $this->html_code.= "\t<!--- FIN DATOS --->\n";
        $this->html_code.="\t</TABLE>\n";
        $this->html_code.= "<!--- Fin de TABLE --->\n";

        if ($do_echo) {
            echo $this->html_code;
        }
        return true;
    }

    function chose_html(&$data_matrix, $x, $y) {
        //Celdas de DATOS Especificos
        if ((($data_matrix[0][$x] == 'userID') || (strtolower(substr($data_matrix[0][$x], 0, 4)) == 'hist'))) {
            $html_code = "\t\t<td width='40' class='{$this->style_link}'><a href=\"javascript:ir_a_usuario_rpt('{$data_matrix[$y][$x]}')\">{$data_matrix[$y][$x]}</a></td>\n";
            return $html_code;
        }
        if ((($data_matrix[0][$x] == 'rvID') || ($data_matrix[0][$x] == 'VID')) && ($y != 0)) {
            $html_code = "\t\t<td width='50' class='{$this->style_link}'><a href=\"javascript:ventana_reaccion_reporte('app.php?section=ReportarReaccion&rvID={$data_matrix[$y][$x]}')\">{$data_matrix[$y][$x]}</a></td>\n";
            return $html_code;
        }
        if ((strtolower($data_matrix[0][$x]) == 'edad') && ($y != 0)) {
            if ($data_matrix[$y][$x] != '') {
                $html_code = "\t\t<td width='60' class='{$this->style_link}'>{$data_matrix[$y][$x]}</td>\n";
            } else {
                $html_code = "\t\t<td width='60' class='{$this->style_link}'>" . calcular_edad($data_matrix[$y][$x - 1], 2) . "</td>\n";
            }
            return $html_code;
        }
        if ((strtolower(substr($data_matrix[0][$x], 0, 5)) == 'fecha') && ($y != 0)) {
            $html_code = "\t\t<td width='70' class='{$this->style_text}'>{$data_matrix[$y][$x]}</td>\n";
            return $html_code;
        }
        // Celdas de DATOS Comunes
        $html_code = "\t\t<td class='{$this->style_text}'>{$data_matrix[$y][$x]}</td>\n";
        return $html_code;
    }

}

function html_list(&$db, $tabla, $campoID, $campo_datos, $nombre_lista, $selected = '', $size_list = 0, $jscode_array = "", $order = 0) {
    if ($order == 1) {
        $order_code = "ORDER BY $campo_datos ASC";
    } else {
        $order_code = '';
    }
    $db->query("select $campoID,$campo_datos from $tabla $order_code");
    $html_code = "<!--- Comienzo de list generada por html_list --->\n";
    $html_code.= "<select name='$nombre_lista' size='$size_list' onChange='{$jscode_array['onChange']}'>\n";
    if ($size_list <= 1) {
        $html_code.= "\t<option value='0'></option>\n";
    }
    for ($i = 1; $i <= $db->num_rows; $i++) {
        if ($selected == $db->data[$i][$campoID]) {
            $html_code.= "\t<option value='" . $db->data[$i][$campoID] . "' selected>" . $db->data[$i][$campo_datos] . "</option>\n";
        } else {
            $html_code.= "\t<option value='" . $db->data[$i][$campoID] . "'>" . $db->data[$i][$campo_datos] . "</option>\n";
        }
    }
    $html_code.="</select>\n";
    $html_code.= "<!--- Fin de la lista generada por html_list --->\n";
    return $html_code;
}

?>