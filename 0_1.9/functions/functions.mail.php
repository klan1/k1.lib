<?php

function k1_mail($to, $subject, $message, $from = "", $data = null) {
   // require_once "Mail.php";

   $from = "$from <" . SMTP_ACCOUT . ">";

   $headers = array('From' => $from,
       'To' => $to,
       'Subject' => $subject);
   $smtp = Mail::factory('smtp', array('host' => SMTP_SERVER,
               'auth' => true,
               'username' => SMTP_ACCOUT,
               'password' => SMTP_PASSWORD));

   $mail = $smtp->send($to, $headers, $message);

   if (PEAR::isError($mail)) {
      k1_show_error($mail->getMessage());
   } else {
      return true;
   }
}

function k1_array_to_text($data, $html = false) {
   if (!is_array($data)) {
      d($data);
      die("Solo arreglos!");
   }

   $text_to_return = "";

   foreach ($data as $key => $datos) {
      if (!is_array($datos)) {
         if (!$html) {
            $text_to_return .= "$key  :  {$datos} \n";
         } else {
            $text_to_return .= "<div> $key  :  {$datos} </div>";
         }
      } else {
         $text_to_return .= k1_array_to_text($datos);
      }
   }

   return $text_to_return;
}

function k1_build_urlce($index_to_create,$form){
   $ws_url_parameters = "";
      foreach ($index_to_create as $key => $value) {
      if (!is_array($value)) {
         if (isset($form[$value])) {
            $ws_url_parameters .= "$value=" . urlencode($form[$value]) . "&";
         }
      } else {
         if (isset($form[$key])) {
            $ws_url_parameters .= "$key=" . urlencode(json_encode($form[$key])) . "&";
         }
      }
   }
   return $ws_url_parameters;
}

