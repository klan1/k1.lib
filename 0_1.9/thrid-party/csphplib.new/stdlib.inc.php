<?php

/* Funciones decraradas en este archivo
  void 	vd(mixed var, [string debug_name = '']);
  string	function get_ip_mask(string IP);
  string	function get_last_ip_num(string	IP);
  boolean	function validate_user_ip([string caller = 'app']);
  string	use_encoded_file(string file_name);
  string	html_serialize(string SQL_query, string var_name[,string obj_prefix = '',int tags = 0,int debug=0]);
 */

function carvac_err($errno, $sender = 'no sent', $file='', $line='') {
    global $carvac_err_msg;
    echo "<p>CARVAC Error Code ($errno) = " . $carvac_err_msg["$errno"] . " -> ($sender) $file $line</p>";
}

function app_err($errno, $sender = 'no sent') {
    global $carvac_err_msg;
    echo "<p>CSPHPLIB Error Code ($errno) = " . $carvac_err_msg["$errno"] . " -> ($sender)</p>";
}

function vd($var, $debug_name = '') {
    echo "\n<!--- INICIO Debug '$debug_name' --->\n<pre>";
    if (is_array($var)) {
        echo "$debug_name " . print_r($var);
    } else {
        echo "$debug_name " . $var;
    }
    echo "</pre>\n<!--- FIN Debug '$debug_name' --->\n";
    return false;
}

// Funciones de Seguridad de Licencia SEGURIDAD  SEGURIDAD  SEGURIDAD  SEGURIDAD  SEGURIDAD  SEGURIDAD  SEGURIDAD
//  SEGURIDAD  SEGURIDAD  SEGURIDAD SEGURIDAD  SEGURIDAD  SEGURIDAD  SEGURIDAD  SEGURIDAD  SEGURIDAD  SEGURIDAD

function get_ip_mask($IP) {
    $IP_len = strlen($IP);
    $last_dot_pos = strrpos($IP, '.');
    $IP_mask = substr_replace($IP, '', $last_dot_pos, $IP_len);
    return $IP_mask;
}

function get_last_ip_num($IP) {
    $IP_len = strlen($IP);
    $last_dot_pos = strrpos($IP, '.') + 1;
    $IP_num = substr($IP, $last_dot_pos);
    return $IP_num;
}

function validate_user_ip() {
    global $IP_auth, $mysql_db;
    $user_addr = $_SERVER['REMOTE_ADDR'];
    $num_mask = count($IP_auth);
    $user_mask = get_ip_mask($user_addr);

    // Ciclo por mascara
    for ($mask = 0; $mask <= $num_mask; $mask++) {
        if ($user_mask != $IP_auth[$mask][0]) {
            continue;
        }
        $num_addr = count($IP_auth[$mask]) - 1;

        // Ciclo por permisos de mascara
        for ($addr = 1; $addr <= $num_addr; $addr++) {
            // Verifica si toda la mascara esta autorizada
            if ($IP_auth[$mask][$addr] == 'all') {
                return true;
            }

            // Entra aqui si hay un maximo de IP's
            if (!(strpos($IP_auth[$mask][$addr], 'max') === false)) {
                $num_max = substr($IP_auth[$mask][$addr], 4);
                $user_mask = get_ip_mask($user_addr);
                $user_mask_len = strlen($user_mask);

                $SQL_text = "SELECT COUNT(*) FROM sessions WHERE ((last_in + INTERVAL 15 MINUTE) > NOW()) AND (SUBSTRING(IP,1,$user_mask_len) = '$user_mask')";
                //echo "<p>$SQL_text</p>";
                $mysql_db->query($SQL_text);

                if ($mysql_db->data[1][0] <= $num_max) {
                    return true;
                } else {
                    // Si la direccion de llamada esta dentro de las direcciones deja pasar
                    $SQL_text = "SELECT COUNT(*) FROM sessions WHERE ((last_in + INTERVAL 15 MINUTE) > NOW()) AND (IP = '$user_addr')";
                    if ($mysql_db->data[1][0] > 1) {
                        return true;
                    } else {
                        echo jsAlert("Se ha excedido el numero maximo ($num_max) de licencias para esta CLASE C\\nEspere 15 Minutos e intente de nuevo.");
                        return false;
                    }
                }
            }

            // Verifica un rango de IP's
            if (!(strpos($IP_auth[$mask][$addr], 'to') === false)) {
                $to_pos = strpos($IP_auth[$mask][$addr], 'to');
                $IP_range_1 = get_last_ip_num(substr($IP_auth[$mask][$addr], 0, $to_pos - 1));
                $IP_range_2 = get_last_ip_num(substr($IP_auth[$mask][$addr], $to_pos + 3));
                if ((get_last_ip_num($user_addr) >= $IP_range_1) && (get_last_ip_num($user_addr) <= $IP_range_2)) {
                    return true;
                }
            }

            // Si ninguna de las anterios valida verifica tan solo la dir IP del cliente
            if ($IP_auth[$mask][$addr] == $user_addr) {
                return true;
            }
        }
    }
    return false;
}

function app_session($do, $extra_info = '') {

    if ($do == 'init') {
        session_name("CARNETSID");
        session_start();
        return session_id();
    }

    global $mysql_db;

    if ($do == 'clean') {
        $last_id = session_id();
        if (!$mysql_db->query("DELETE FROM sessions WHERE CARNETSID='" . session_id() . "'")) {
            carvac_err("clean", __FUNCTION__, __FILE__, __LINE__);
        }
        @session_unset();
        @session_destroy();
        return $last_id;
    }

    if ($do == 'check') {
        if ($mysql_db->query("SELECT *  FROM sessions WHERE CARNETSID='" . session_id() . "'") && isset($_SESSION['app_session']) && $_SESSION['app_session']['CARNETSID'] == session_id() && ($_SESSION['app_session']["IP"] == $_SERVER['REMOTE_ADDR'])) {
            return true;
        } else {
            carvac_err("check", __FUNCTION__, __FILE__, __LINE__);
            return false;
        }
    }

    if ($do == 'load') {
        //se cargan los datos de session a una matriz
        if ($mysql_db->query("SELECT *  FROM sessions WHERE CARNETSID = '" . session_id() . "'")) {
            $_SESSION['app_session'] = $mysql_db->data[1];
        } else {
            carvac_err("load", __FUNCTION__, __FILE__, __LINE__);
        }
    }

    if ($do == 'create') {
        app_session('clean');
        $actual_id = app_session('init');
        $session_info = array(
            0 => array("CARNETSID", "logID", "IP", "datetime_in"),
            1 => array($actual_id, 'login_page', $_SERVER['REMOTE_ADDR'], $GLOBALS['G_now']));
        if (!$mysql_db->insert("sessions", $session_info)) {
            carvac_err("create", __FUNCTION__, __FILE__, __LINE__);
            return false;
        } else {
            app_session('load');
        }
    }

    if ($do == 'update') {
        $query = array(
            0 => array("logID", "last_action", "last_in"),
            1 => array($_SESSION['lu_permisos'][1]['logID'], $extra_info, $GLOBALS['G_now']));
        //1 => array ( $_SESSION['lu_permisos'][1]['logID']. " - " . $extra_info, $GLOBALS['G_now']));
        $conditions = "CARNETSID='" . $_SESSION['app_session']["CARNETSID"] . "'";
        if (!$mysql_db->update("sessions", $query, $conditions)) {
            carvac_err("update", __FUNCTION__, __FILE__, __LINE__);
        }
    }

    //print_r($_SESSION);
}

function use_encoded_file($file_name) {
    global $sessions;
    $file_size = filesize($file_name);
    $fp = fopen($file_name, 'r');
    $file_content = fread($fp, $file_size);
    fclose($fp);

    $unencoded_file_content = base64_decode($file_content);

    $tmp_file_name = $_ENV['TEMP'] . "\\tmp" . $sessions['CARNETSID'] . ".tmp";
    $tmp_file = fopen($tmp_file_name, 'a');
    fputs($tmp_file, $unencoded_file_content);
    fclose($tmp_file);

    return ($tmp_file_name);
}

function html_serialize($SQL_query, $array_name, $obj_prefix = '', $tags = 0, $debug=0) {
    global $mysql_db;
    global ${$array_name . '_args'};
    $js_code = '';
    $mysql_db->query($SQL_query);
    if ($tags != 0) {
        $js_code.= "\n<script>\n";
    }
    if ($debug != 0) {
        $js_code.= "\n//" . $mysql_db->text_query . "\n ";
    }
    $js_code.= "// COMIENZO de html_serialize\n";
    if ($mysql_db->num_rows >= 1) {
        $js_code.= "\t$array_name = new Array(" . $mysql_db->num_rows . ");\n";
        // Hacer la matriz de datos en Javascript
        for ($y = 1; $y <= $mysql_db->num_rows; $y++) {
            $js_code.= "\t" . $array_name . "[$y] = new Array(" . $mysql_db->num_fields . ");\n";
            for ($x = 0; $x <= $mysql_db->num_fields - 1; $x++) {
                $js_code.= "\t" . $array_name . "[$y][$x] = '{$mysql_db->data[$y][$x]}';\t\t// {$mysql_db->data[0][$x]}\n";
            }
        }
        // Guarda argunetos es forma de string para ser usados en la creacion de la funcion de evento
        ${$array_name . '_args'}.= "this.value,$array_name,";
        for ($x = 1; $x <= $mysql_db->num_fields - 1; $x++) {
            ${$array_name . '_args'}.= "this.form.$obj_prefix" . $mysql_db->data[0][$x];
            if ($x != $mysql_db->num_fields - 1) {
                ${$array_name . '_args'}.= ",";
            }
        }
    } else {
        $js_code.= "\t$array_name = '';\n";
    }
    $js_code.= "// FIN de html_serialize\n";
    if ($tags != 0) {
        $js_code.= "</script>\n";
    }
    return $js_code;
}

function post_serialize($form_name = '', $action = '') {
    $html = '<!-- Comienzo de post_serialize -->\n';
    if (count($_POST) > 0) {
        if ($form_name != '') {
            $html = "<form name='$form_name'  method='post' action='$action'>\n";
        }
        foreach ($_POST as $key => $value) {
            $html .= "\t<input name='$key' type='hidden' value='$value'>\n";
        }
    } else {
        $html .= "\t<!-- Nada q serializar en " . __FILE__ . " linea " . __LINE__ . "-->\n";
    }
    return $html .= '<!-- Fin de post_serialize -->\n';
}

function returnMacAddress() {
    $arpTable = `ipconfig -all`;

    $arpSplitted = split("\n", $arpTable);
    $remoteIp = $GLOBALS['REMOTE_ADDR'];
    $remoteIp = str_replace(".", "\\.", $remoteIp);

    foreach ($arpSplitted as $value) {
        $valueSplitted = split(" ", $value);

        foreach ($valueSplitted as $spLine) {
            if (preg_match("/[0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f][:-]" .
                            "[0-9a-f][0-9a-f][:-][0-9a-f][0-9a-f]/i", $spLine)) {
                return $spLine;
            }
        }

        $ipFound = false;
    }

    return false;
}

function returnZendId() {

    return false;
    
    $ZendID_Path = "c:\zendid.exe";
//	$ZendID_Path = "C:\\windows\\system32\\zendid.exe";
    if (!file_exists($ZendID_Path)) {
        echo "ZendID Imposible de ejecutar en {$ZendID_Path}";
        exit;
    } else {
        if (!isset($_SESSION['ZendID_MD5'])) {
            $zendid_exe_md5 = "6f83ccd4555a0e590c596e1571a5d606";
            $ZendID_MD5 = md5_file($ZendID_Path);
            if ($zendid_exe_md5 != $ZendID_MD5) {
                echo "Se ha alterado el archivo '$ZendID_Path', imposible de continuar.";
                exit;
            }
            $_SESSION['ZendID_MD5'] = $ZendID_MD5;
        }
    }

    if (isset($_SESSION['ZendID']) && $_SESSION['ZendID'] != '') {
        return $_SESSION['ZendID'];
    } else {
        $ZendIDReturn = `zendid.exe`;
        $ZendIDSplited = split("\n", $ZendIDReturn);

        foreach ($ZendIDSplited as $value) {
            $valueSplitted = split(":", $value);

            foreach ($valueSplitted as $spLine) {

                foreach ($valueSplitted as $spLine) {
                    if (preg_match("/[0-9a-z][0-9a-z][0-9a-z][0-9a-z][0-9a-z][:-][0-9a-z][0-9a-z][0-9a-z][0-9a-z][0-9a-z][:-][0-9a-z][0-9a-z][0-9a-z][0-9a-z][0-9a-z][:-][0-9a-z][0-9a-z][0-9a-z][0-9a-z][0-9a-z]/i", $spLine)) {
                        $_SESSION['ZendID'] = $spLine;
                        return $spLine;
                    }
                }
            }
        }

        return false;
    }
}

function getMagicValue() {
    $magicValue = md5(date("Y-m-d h:i") . "this is magic!!");
    return $magicValue;
}

function getBoleanValue() {

    $num_args = func_num_args();

    if ($num_args == 1) {
        return (func_get_arg(0) == "1") ? "1" : "0";
    } elseif ($num_args != 0) {
        $args = array();
        for ($i = 0; $i <= $num_args; $i++) {
            $arg[] = (func_get_arg($i) == '1') ? "1" : "0";
        }
        return $arg;
    } else {
        echo "Funcion getBoleanValue() llamada sin valores";
        exit;
    }
}

function getNullValue() {

    $num_args = func_num_args();

    if ($num_args == 1) {
        return (func_get_arg(0) != '') ? func_get_arg(0) : null;
    } elseif ($num_args != 0) {
        $args = array();
        for ($i = 0; $i <= $num_args; $i++) {
            $arg[] = (func_get_arg($i) != '') ? func_get_arg($i) : null;
        }
        return $arg;
    } else {
        echo "Funcion getNullValue() llamada sin valores";
        exit;
    }
}

function convertGetToPost() {
    if (isset($_GET)) {
        foreach ($_GET as $key => $value) {
            $_POST[$key] = $value;
        }
        unset($_GET);
        return true;
    } else {
        return false;
    }
}

?>