<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getCensoRegistraduria($cedula, $source = "full") {
    $fetch_init_time = microtime(true);

    global $db;
    $return = false;

    if (!is_numeric($cedula)) {
        trigger_error("La cedula recibida no es un numero valido");
        return false;
    }


    $result_local_array = getFromLocalCenso($cedula);
    if (!isset($result_local_array['bajas'])) {
        $result_cache_array = getFromLocalCache($cedula);
        if (isset($result_cache_array['censo'])) {
            $result_local_array['censo'] = $result_cache_array['censo'];
        }
        if (isset($result_cache_array['inscripcion'])) {
            $result_local_array['inscripcion'] = $result_cache_array['inscripcion'];
        }
    }

    if (!empty($result_cache_array) || (!empty($result_local_array))) {
        $return = true;
    }
    /**
     * LOG BUILT 
     */
    $fetch_run_time = round((microtime(true) - $fetch_init_time), 5);

    $censo_log = array(
        'cedula' => $cedula,
        'http_response' => (isset($content1_info['http_code'])) ? $content1_info['http_code'] : -1,
//        'proxy' => $actual_proxy,
        'script_time' => $fetch_run_time,
        'IP' => (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1",
        'agent' => (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : "PHP (CLI)",
        'datetime' => date("Y-m-d H:i:s"),
    );
    \k1lib\sql\sql_insert($db, "CENSO2015_LOG", $censo_log);
    if ($return) {
        return $result_local_array;
    } else {
        return false;
    }
}

function getFromLocalCache($cedula) {
    global $db;
    $return = false;

    $return_array = array();

    /**
     * Primero consultamos desde LOCAL
     */
    $local_sql_query = "SELECT * FROM CENSO2015_JULIO WHERE cedula={$cedula}";
    $local_sql_query_result = \k1lib\sql\sql_query($db, $local_sql_query, false);
    if ($local_sql_query_result !== null) {
        $return_array['censo'] = $local_sql_query_result;
        $return = true;
    }
    $local_sql_query_insc = "SELECT * FROM CENSO2015_INSCRIPCIONES WHERE cedula={$cedula}";
    $local_sql_query_result_insc = \k1lib\sql\sql_query($db, $local_sql_query_insc, true);
    if ($local_sql_query_result_insc !== null) {
        $return_array['inscripcion'] = $local_sql_query_result_insc;
        $return = true;
    }
    if ($return) {
        return $return_array;
    } else {
        return false;
    }
}

function getFromLocalCenso($cedula) {
    global $db;
    $return = false;

    $return_array = array();

    /**
     * CONSULTA DE CENSO 2014
     */
    $local_sql_query_censo = "SELECT * FROM consultaCenso2014 WHERE NUIP={$cedula}";
    $local_sql_query_censo_result = \k1lib\sql\sql_query($db, $local_sql_query_censo, false);
    if ($local_sql_query_censo_result !== null) {
        $return_array['censo'] = $local_sql_query_censo_result;
        $return = true;
    }
    /**
     * CONSULTA DE BAJAS
     */
    $local_sql_query_baja = "SELECT * FROM `CENSO.BAJAS` WHERE NUIP={$cedula}";
    $local_sql_query_baja_result = \k1lib\sql\sql_query($db, $local_sql_query_baja, false);
    if ($local_sql_query_baja_result !== null) {
        $return_array['bajas'] = $local_sql_query_baja_result;
        $return = true;
    }
    /**
     * CONSULTA DE NOVEDADES
     */
    $local_sql_query_novedad = "SELECT * FROM consultaNovedad2014 WHERE NUIP={$cedula}";
    $local_sql_query_novedad_result = \k1lib\sql\sql_query($db, $local_sql_query_novedad, false);
    if ($local_sql_query_novedad_result !== null) {
        $return_array['novedad'] = $local_sql_query_novedad_result;
        $return = true;
    }
    /**
     * TRANSHUMANCIAN 
     */
    $local_sql_query_transhumancia = "SELECT * FROM `CENSO.TRASHUMANCIAN` WHERE NUIP={$cedula}";
    $local_sql_query_transhumancia_result = \k1lib\sql\sql_query($db, $local_sql_query_transhumancia, false);
    if ($local_sql_query_transhumancia_result !== null) {
        $return_array['transhumancia'] = $local_sql_query_transhumancia_result;
        $return = true;
    }
    /**
     * TRANSHUMANCIAN - punto de final
     */
    if (isset($return_array['transhumancia']['IDDIVIPOLI']) && !empty($return_array['transhumancia']['IDDIVIPOLI'])) {
        $local_sql_query_transhumancia_i = "SELECT * FROM `CENSO.DIVIPOLNOMBRES` WHERE IDDIVIPOL={$return_array['transhumancia']['IDDIVIPOLI']}";
        $local_sql_query_transhumancia_i_result = \k1lib\sql\sql_query($db, $local_sql_query_transhumancia_i, false);
        if ($local_sql_query_transhumancia_i_result !== null) {
            $return_array['transhumancia']['IDDIVIPOLI'] = $local_sql_query_transhumancia_i_result;
            $return = true;
        }
    }
    /**
     * TRANSHUMANCIAN - piunto de partida
     */
    if (isset($return_array['transhumancia']['IDDIVIPOLT']) && !empty($return_array['transhumancia']['IDDIVIPOLT'])) {
        $local_sql_query_transhumancia_t = "SELECT * FROM `CENSO.DIVIPOLNOMBRES` WHERE IDDIVIPOL={$return_array['transhumancia']['IDDIVIPOLT']}";
        $local_sql_query_transhumancia_t_result = \k1lib\sql\sql_query($db, $local_sql_query_transhumancia_t, false);
        if ($local_sql_query_transhumancia_t_result !== null) {
            $return_array['transhumancia']['IDDIVIPOLT'] = $local_sql_query_transhumancia_t_result;
            $return = true;
        }
    }
//    $local_sql_query_insc = "SELECT * FROM CENSO2015_INSCRIPCIONES WHERE cedula={$cedula}";
//    $local_sql_query_result_insc = \k1lib\sql\sql_query($db, $local_sql_query_insc, true);
//    if ($local_sql_query_result_insc !== null) {
//        $return_array['inscripcion'] = $local_sql_query_result_insc;
//        $return = true;
//    }
    if ($return) {
        return $return_array;
    } else {
        return false;
    }
}

function getFromCarvajal($cedula) {
    static $i = 0;

    static $user_agents = array(
        0 => "Mozilla/5.0 ;Windows NT 6.1; WOW64; Trident/7.0; rv:11.0; like Gecko",
        1 => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
        2 => 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.1.13) Gecko/20080310 Firefox/2.0.0.13',
        3 => 'Mozilla/5.0 ;Windows NT 6.2; WOW64; rv:27.0; Gecko/20100101 Firefox/27.0',
        4 => 'Mozilla/5.0 ;Windows NT 6.1; WOW64; rv:26.0; Gecko/20100101 Firefox/27.0',
    );
    $user_agents_index = rand(0, (count($user_agents) - 1));
//    if (count($proxy_list_array) === 0) {
    $proxy_file_content = file_get_contents(APP_RESOURCES_PATH . "/shell-scripts/proxy-list.txt");
    $proxy_list_array = explode("\n", $proxy_file_content);
//    }

    $return = false;
    $actual_proxy = null;
    $return_array['censo'] = array();
    $return_array['inscripcion'] = array();

    /**
     * FORM LOAD TO CAPTURE IMPUT DATA
     */
    $registraduria_url1 = "http://inscripcionelectoral.carvajal.com:8280/srvidc-webcon/";
    $registraduria_url2 = "http://inscripcionelectoral.carvajal.com:8280/srvidc-webcon/Home.xhtml";
    //$registraduria_url = "http://www3.registraduria.gov.co/censo/_censoresultado.php?nCedula=";

    $rnd_cookie = md5(rand(100, 9999));

    do {
        $ch1 = curl_init($registraduria_url1);
        curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch1, CURLOPT_TIMEOUT, 4); //timeout in seconds
        if (!empty($proxy_list_array[$i])) {
            $actual_proxy = $proxy_list_array[$i];
            \k1lib\common\d("Using proxy: " . $actual_proxy);
            curl_setopt($ch1, CURLOPT_PROXY, $actual_proxy);
        } else {
            \k1lib\common\d("Using local IP");
        }
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch1, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch1, CURLOPT_HEADER, true);
        curl_setopt($ch1, CURLOPT_USERAGENT, $user_agents[$user_agents_index]);
        curl_setopt($ch1, CURLOPT_COOKIEJAR, "/tmp/{$rnd_cookie}.txt");
        curl_setopt($ch1, CURLOPT_COOKIEFILE, "/tmp/{$rnd_cookie}.txt");
        $content1 = curl_exec($ch1);
        $content1_info = curl_getinfo($ch1);
        curl_close($ch1);
        /**
         * FORM NAME
         */
        $pattern_form_name = '/<form(.*)name="(.*?)" /i';
        preg_match_all($pattern_form_name, $content1, $matches_form);
        if (isset($matches_form[2][0]) && !empty($matches_form[2][0])) {
            $form_name = $matches_form[2][0];
        } else {
            $form_name = false;
            if (!isset($proxy_list_array[$i + 1])) {
                $i = 0;
            } else {
                $i++;
            }
            \k1lib\common\d($content1, true);
            \k1lib\common\d($content1_info);
        }
    } while ($form_name == false);

    if ($content1_info['http_code'] == 200) {

//\k1lib\common\d($form_name);
        /**
         * J_ID
         */
        $pattern_j_id = '/<input id="f1:j_idt(.*?)"/i';
        preg_match_all($pattern_j_id, $content1, $matches_j_id);
        $j_id = $matches_j_id[1][0];
//\k1lib\common\d($j_id);
//die();
        /**
         * javax.faces.ViewState
         */
        $pattern_seguridad = '/<input(.*)name=\"javax.faces.ViewState\"(.*)value=\"(.*?)\"/i';
        preg_match_all($pattern_seguridad, $content1, $matches0);
        $verify_value = $matches0[3][0];

        /**
         * FORM SEND
         */
        $ch2 = curl_init($registraduria_url2);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch2, CURLOPT_REFERER, $registraduria_url1);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_USERAGENT, $user_agents[$user_agents_index]);
        curl_setopt($ch2, CURLOPT_COOKIEJAR, "/tmp/{$rnd_cookie}.txt");
        curl_setopt($ch2, CURLOPT_COOKIEFILE, "/tmp/{$rnd_cookie}.txt");

        $data = array(
            "{$form_name}" => "{$form_name}",
            "{$form_name}:tipoDocto" => "C",
            "{$form_name}:nroDocto" => $cedula,
            "javax.faces.ViewState" => $verify_value,
            "javax.faces.source" => "{$form_name}:j_idt{$j_id}",
            "javax.faces.partial.event" => "click",
            "javax.faces.partial.execute" => "{$form_name}:j_idt{$j_id} @component",
            "javax.faces.partial.render" => "@component",
            "org.richfaces.ajax.component" => "{$form_name}:j_idt{$j_id}",
            "{$form_name}:j_idt{$j_id}" => "{$form_name}:j_idt{$j_id}",
            "rfExt" => "null",
            "AJAX:EVENTS_COUNT" => "1",
            "javax.faces.partial.ajax" => "true"
        );

        curl_setopt($ch2, CURLOPT_POSTFIELDS, $data);


        $content2 = curl_exec($ch2);
        $content2_info = curl_getinfo($ch2);
//\k1lib\common\d(curl_getinfo($ch2));
        curl_close($ch2);
        if ($content2_info['http_code'] == 200) {

            /**
             * INFO BASICA CENSO CON NOMBRE
             */
            $pattern_datos_censo = "/<tr>\s<td>Fecha ingreso<\/td>\s<td>(.*)<\/td>\s<\/tr>\s<tr>\s<td>Numero de documento:<\/td>\s<td>(.*)<\/td>\s<\/tr>\s<tr>\s<td>Nombres: <\/td>\s<td>(.*)<\/td>\s<\/tr>\s<tr>\s<td>Apellidos: <\/td>\s<td>(.*)<\/td>\s<\/tr>\s<tr>\s<td>Departamento:<\/td>\s<td>(.*)<\/td>\s<\/tr>\s<tr>\s<td>Municipio:<\/td>\s<td>(.*)<\/td>\s<\/tr>\s<tr>\s<td>Nombre puesto:<\/td>\s<td>(.*)<\/td>\s<\/tr>\s<tr>\s<td>Direccion puesto:<\/td>\s<td>(.*)<\/td>\s<\/tr>/i";
//$pattern = '/<td>Fecha ingreso<\/td>\s<td>(.*?)<\/td>/i';
//$pattern = '/<td>(.*)<\/td>/i';
            preg_match_all($pattern_datos_censo, $content2, $matches1);

            if (isset($matches1[2][0])) {
//        \k1lib\common\d($matches1);
                $return_array['censo']['cedula'] = $matches1[2][0];
                $return_array['censo']['fecha_ingreso'] = $matches1[1][0];
                $return_array['censo']['nombres'] = $matches1[3][0];
                $return_array['censo']['apellidos'] = $matches1[4][0];
                $return_array['censo']['dpto'] = $matches1[5][0];
                $return_array['censo']['mun'] = $matches1[6][0];
                $return_array['censo']['puesto'] = $matches1[7][0];
                $return_array['censo']['dir_puesto'] = $matches1[8][0];
                /**
                 * SQL Insert for "cache" :P
                 */
                \k1lib\sql\sql_insert($db, "CENSO2015_JULIO", $return_array['censo']);
            }
            /**
             * J_ID
             */
            $pattern2_j_id = '/<thead id="j_idt(.*):th"/i';
            if (preg_match_all($pattern2_j_id, $content2, $matches2_j_id)) {

                $j_id2 = $matches2_j_id[1][0];
                if (!empty($j_id2)) {
                    /**
                     * INFO ACTUALIZACION DE DATOS
                     */
                    $pattern_datos_actualizacion = "/<td id=\"j_idt{$j_id2}:0:j_idt[0-9]+\" class=\"rf-dt-c\">(.*)<\/td><td id=\"j_idt{$j_id2}:0:j_idt[0-9]+\" class=\"rf-dt-c\">(.*)<\/td><td id=\"j_idt{$j_id2}:0:j_idt[0-9]+\" class=\"rf-dt-c\">(.*)<\/td><td id=\"j_idt{$j_id2}:0:j_idt[0-9]+\" class=\"rf-dt-c\">(.*)<\/td><td id=\"j_idt{$j_id2}:0:j_idt[0-9]+\" class=\"rf-dt-c\">(.*)<\/td><td id=\"j_idt{$j_id2}:0:j_idt[0-9]+\" class=\"rf-dt-c\">(.*)<\/td><td id=\"j_idt{$j_id2}:0:j_idt[0-9]+\" class=\"rf-dt-c\">(.*)<\/td><td id=\"j_idt{$j_id2}:0:j_idt[0-9]+\" class=\"rf-dt-c\">(.*)<\/td><td id=\"j_idt{$j_id2}:0:j_idt[0-9]+\" class=\"rf-dt-c\">(.*?)<\/td><\/tr><\/tbody>/i";
//$pattern = '/<td>Fecha ingreso<\/td>\s<td>(.*?)<\/td>/i';
//$pattern = '/<td>(.*)<\/td>/i';
                    if (preg_match_all($pattern_datos_actualizacion, $content2, $matches2)) {
                        $return_array['inscripcion']['cedula'] = $cedula;
                        $return_array['inscripcion']['dpto'] = $matches2[1][0];
                        $return_array['inscripcion']['mun'] = $matches2[2][0];
                        $return_array['inscripcion']['codzona'] = $matches2[3][0];
                        $return_array['inscripcion']['codpuesto'] = $matches2[4][0];
                        $return_array['inscripcion']['puesto'] = $matches2[5][0];
                        $return_array['inscripcion']['dir_puesto'] = $matches2[6][0];
                        $return_array['inscripcion']['fecha_update'] = $matches2[7][0];
                        $return_array['inscripcion']['clase'] = $matches2[8][0];
                        $return_array['inscripcion']['estado'] = $matches2[9][0];
                    }
//                            \k1lib\common\d($matches2);
                }
                if (!empty($matches2[2][0])) {
                    \k1lib\sql\sql_insert($db, "CENSO2015_INSCRIPCIONES", $return_array['inscripcion']);
                }
            }
            unlink("/tmp/{$rnd_cookie}.txt");
            $return = TRUE;
        } else {
            trigger_error("La peticion de la pagina de fuente ha retornado error: " . $content1_info["http_code"]);
            \k1lib\common\d($content1_info);
        }
// http_code $content1 
    } else {
        trigger_error("La peticion de la pagina de fuente ha retornado error: " . $content1_info["http_code"]);
        \k1lib\common\d($content2_info);
    }
    if ($return) {
        return $return_array;
    } else {
        return false;
    }
}
