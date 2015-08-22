<?php

/**
 * Clase para el envio de email(s) via SMTP o mail()
 *
 * Clase para el evio multiple o simple de email(s) via SMTP o
 * por medio de la funcion mail().
 * Tambien comprueba si los email son validos o no y en
 * la conexion al servidor puede ser con o sin login de usuario.
 *
 * @version   2.0a
 * @author MSDark (MsDark_@hotmail.com)
 * @copyright MSDark 
 */
class email {

    /**
     * Contiene el asunto del email  enviar
     *
     * @var string
     * @acces private
     * 
     */
    var $asunto;

    /**
     * Contiene las direcciones de destino
     *
     * @var string
     * @acces private
     *
     */
    var $direcciones;

    /**
     * Remitente del email
     *
     * @var string
     * @acces private
     *
     */
    var $remitente;

    /**
     * Contiene el mensaje a enviar solo texto plano
     *
     * @var string
     * @acces private
     * 
     */
    var $mensaje;

    /**
     * Contiene si el email fue enviado o no
     *
     * Guarda un valor en caso de que el mail haya sido enviado.
     * @var booleano
     * @acces private
     *
     */
    var $enviado;

    /**
     * Contiene el errro ocurrido en ejecucion
     *
     * @var string
     * @acces private
     *
     */
    var $error;

    /**
     * Contiene un valor si los email son validos o no
     *
     * @var booleano
     * @acces private
     * 
     */
    var $comprobar;

    /**
     * Constructor de la clase
     *
     * Setea el valor de asunto, mensaje,remitente
     * y direcciones, mada a revisar los email o devuelve cierto error.
     *
     * @param asunto = asunto del email
     * @param direccion = direccion del email
     * @param mensaje =mensaje de email
     * @param remitente = remitente del email
     * @acces private 
     */
    function email($asunto, $direccion, $mensaje, $remitente) {
        if (empty($asunto) || empty($direccion) || empty($mensaje) || empty($remitente)) {
            return $this->error(1);
        } else {
            $this->asunto = $asunto;
            $this->direcciones = $direccion;
            $this->mensaje = $mensaje;
            $this->remitente = $remitente;

            $this->comprobar($direccion, $remitente);
            if (!$this->comprobar) {
                return $this->error(2);
            }//if
        }   //if-else
    }

//funcion

    /**
     * Setea el error que pueda ocurrir durante ejecucion
     *
     * @param e = error ocurrido durante ejecucion
     * @acces private
     *
     */
    function error($e) {
        switch ($e) {
            case 1:
                return $this->error = "No has completado todos los datos";
                break;
            case 2:
                return $this->error = "El/los email(s) de remitente y/o destino son invalidos";
                break;
            case 3:
                return $this->error = "No se han establecido los campos necesarios";
                break;
            case 4:
                return $this->error = "No se puede enviar el/los email(s)";
                break;
            case 5:
                return $this->error = "No se puede realizar conexion con el servidor SMTP <b>$errn: error</b>";
                break;
            case 6:
                return $this->error = "No se puede establecer comunicacion con el servidor";
                break;
            case 7:
                return $this->error = "No se puede cerrar conexion con el servidor";
                break;
            default:
                break;
        }//switch
    }

//funcion

    /**
     * Devuelve el error ocurrido
     *
     * @acces private
     *
     * @return string El error ocurrido
     */
    function errores() {
        return $this->error;
    }

//funcion

    /**
     * Comprueba los email de destino y remitente
     *
     * Comprueba si ambos campos son validos es decir contiene @, '.' y un dominio valido.
     * @param direccion = direccion del email
     * @param remitente = remitente del email{
     * @acces private
     *
     * @return booleano 
     */
    function comprobar($direccion, $remitente) {
        $this->comprobar = false;
        if ((@is_array($direccion)) && (@is_array($remitente))) {

            for ($i = 0; $i < @count($direccion); $i++) {
                if ((@strlen($direccion[$i]) >= 3) && (@substr_count($direccion[$i], "@") == 1) && (@substr($direccion[$i], 0, 1) != "@") && (@substr($direccion[$i], @strlen($direccion[$i]) - 1, 1) != "@") && (@strlen($remitente[$i]) >= 6) && (@substr_count($remitente[$i], "@") == 1) && (@substr($remitente[$i], 0, 1) != "@") && (@substr($direccion[$i], @strlen($remitente[$i]) - 1, 1) != "@")) {
                    if ((!@strstr($direccion[$i], "'")) && (!@strstr($direccion[$i], "\"")) && (!@strstr($direccion[$i], "\\")) && (!@strstr($direccion[$i], "\$")) && (!@strstr($direccion[$i], " ")) && (!@strstr($remitente[$i], "'")) && (!@strstr($remitente[$i], "\"")) && (!@strstr($remitente[$i], "\\")) && (!@strstr($remitente[$i], "\$")) && (!@strstr($remitente[$i], " "))) {
                        if ((@substr_count($direccion[$i], ".") >= 1) && (@substr_count($remitente[$i], ".") >= 1)) {
                            $term = @substr(@strrchr($direccion[$i], '.'), 1);
                            $term2 = @substr(@strrchr($remitente[$i], '.'), 1);
                            if (@strlen($term) > 1 && @strlen($term) < 5 && (!@strstr($term, "@")) && @strlen($term2) > 1 && @strlen($term2) < 5 && (!@strstr($term2, "@"))) {
                                $antes = @substr($direccion[$i], 0, @strlen($direccion[$i]) - @strlen($term) - 1);
                                $antes2 = @substr($remitente[$i], 0, @strlen($remitente[$i]) - @strlen($term) - 1);
                                $caracter = @substr($antes, @strlen($antes) - 1, 1);
                                $caracter2 = @substr($antes2, @strlen($antes2) - 1, 1);
                                if ($caracter != "@" && $caracter != "." && $caracter2 != "@" && $caracter2 != ".") {
                                    $this->comprobar = true;
                                }
                            }
                        }
                    }
                }
            }

            if ($this->comprobar) {
                return $this->comprobar = true;
            } else {
                return $this->comprobar = false;
            }
        } else {
            if ((@strlen($direccion) >= 3) && (@substr_count($direccion, "@") == 1) && (@substr($direccion, 0, 1) != "@") && (@substr($direccion, @strlen($direccion) - 1, 1) != "@") && (@strlen($remitente) >= 6) && (@substr_count($remitente, "@") == 1) && (@substr($remitente, 0, 1) != "@") && (@substr($direccion, @strlen($remitente) - 1, 1) != "@")) {
                if ((!@strstr($direccion, "'")) && (!@strstr($direccion, "\"")) && (!@strstr($direccion, "\\")) && (!@strstr($direccion, "\$")) && (!@strstr($direccion, " ")) && (!@strstr($remitente, "'")) && (!@strstr($remitente, "\"")) && (!@strstr($remitente, "\\")) && (!@strstr($remitente, "\$")) && (!@strstr($remitente, " "))) {
                    if ((@substr_count($direccion, ".") >= 1) && (@substr_count($remitente, ".") >= 1)) {
                        $term = @substr(@strrchr($direccion, '.'), 1);
                        $term2 = @substr(@strrchr($remitente, '.'), 1);
                        if (@strlen($term) > 1 && @strlen($term) < 5 && (!@strstr($term, "@")) && @strlen($term2) > 1 && @strlen($term2) < 5 && (!@strstr($term2, "@"))) {
                            $antes = @substr($direccion, 0, @strlen($direccion) - @strlen($term) - 1);
                            $antes2 = @substr($remitente, 0, @strlen($remitente) - @strlen($term) - 1);
                            $caracter = @substr($antes, @strlen($antes) - 1, 1);
                            $caracter2 = @substr($antes2, @strlen($antes2) - 1, 1);
                            if ($caracter != "@" && $caracter != "." && $caracter2 != "@" && $caracter2 != ".") {
                                $this->comprobar = true;
                            }
                        }
                    }
                }
            }
            if ($this->comprobar) {
                return $this->comprobar = true;
            } else {
                return $this->comprobar = false;
            }
        }
    }

//funcion

    /**
     * Envia el email
     *
     * Envia el email mediante funcion mail()
     * @acces private
     *
     */
    function enviar() {
        if (!$this->direcciones || !$this->asunto || !$this->mensaje || !$this->remitente) {
            return $this->error(3);
        } else {
            if (@is_array($this->direcciones) || @is_array($this->asunto) || @is_array($this->mensaje) || @is_array($this->remitente)) {
                for ($i = 0; $i < count($this->direcciones); $i++) {
                    if (!@mail($this->direcciones[$i], $this->asunto[$i], $this->mensaje[$i], "FROM : " . $this->remitente[$i] . "\r\nReply To: " . $this->remitente[$i] . "\r\n")) {
                        return $this->error(4);
                    } else {
                        return $this->enviado = true;
                    }
                }//fin for
            } else {
                if (!@mail($this->direcciones, $this->asunto, $this->mensaje, "FROM : " . $this->remitente . "\r\nReply To: " . $this->remitente . "\r\n")) {
                    return $this->error(4);
                } else {
                    return $this->enviado = true;
                }
            }
        }
    }

//funcion

    /**
     * Devuelve un mensaje de aviso del envio del mail
     *
     * @acces private
     *
     * @return string 
     */
    function enviado() {
        if (!$this->enviado) {
            return;
        } else {
            if ($this->enviado) {
                return "Email(s) enviado";
            } else {
                return "Email(s) no enviado";
            }
        }
    }

//funcion

    /**
     * Envia el mail por medio de sockets
     *
     * Envia el mail por medio de sockets SMTP, con posibilidad de untentificar el usuario.
     * @param server = servidor SMTP
     * @param port = puerto SMTP
     * @param user = usuario del servidor por defecto ""
     * @param pass = password del servidor por defecto ""
     * @acces private
     * 
     *
     * @param string $user
     * @param string $pass
     */
    function smtp($server, $port, $user = "", $pass = "") {
        if (!$this->direcciones || !$this->asunto || !$this->mensaje || !$this->remitente) {
            return $this->error(3);
        } else {
            $fp = fsockopen($server, $port, $errn, $error, 30) or ( $this->error(5));
            echo fgets($fp, 1024);
            fputs($fp, "EHLO 71675615@globalcom\r\n");
            fgets($fp, 1024);
            if ($user != "" || $pass != "") {
                $auth = "AUTH LOGIN\r\n";
                fputs($fp, $auth . "\r\n") or ( $this->error(6));
                fgets($fp, 1024);
                $user = base64_encode($user);
                fputs($fp, $user . "\r\n") or ( $this->error(6));
                fgets($fp, 1024);
                $pass = base64_encode($pass);
                fputs($fp, $pass . "\r\n") or ( $this->error(6));
                fgets($fp, 1024);
            }
            if (@is_array($this->direcciones) || @is_array($this->asunto) || @is_array($this->mensaje) || @is_array($this->remitente)) {
                for ($i = 0; $i < count($this->direcciones); $i++) {
                    fputs($fp, "MAIL FROM:" . $this->remitente[$i] . "\r\n") or ( $this->error(6));
                    fgets($fp, 1024);

                    fputs($fp, "RCPT TO:" . $this->direcciones[$i] . "\r\n") or ( $this->error(6));
                    fgets($fp, 1024);

                    fputs($fp, "DATA\r\n") or ( $this->error(6));
                    fgets($fp, 1024);

                    fputs($fp, "SUBJECT:" . $this->asunto[$i] . "\r\n.\r\n") or ( $this->error(6));
                    fgets($fp, 1024);

                    fputs($fp, $this->mensaje[$i] . "\r\n.\r\n") or ( $this->error(6));
                    fgets($fp, 1024);
                }
            } else {
                fputs($fp, "MAIL FROM:" . $this->remitente . "\r\n") or ( $this->error(6));
                fgets($fp, 1024);
                fputs($fp, "RCPT TO:" . $this->direcciones . "\r\n") or ( $this->error(6));
                fgets($fp, 1024);
                fputs($fp, "DATA\r\n") or ( $this->error(6));
                fgets($fp, 1024);
                fputs($fp, "SUBJECT:" . $this->asunto . "\r\n.\r\n") or ( $this->error(6));
                fgets($fp, 1024);
                fputs($fp, $this->mensaje . "\r\n.\r\n") or ( $this->error(6));
                fgets($fp, 1024);
            }
            fputs($fp, "QUIT\r\n") or ( $this->error(6));
            fgets($fp, 1024);
            fclose($fp) or ( $this->error(7));
        }
    }

//funcion

    /*     * *   FIN CLASE ** */
}

/* * ************* Envio simple via funcion mail()************************* 
  $a = "Asunto";
  $d = "MsDark_@hotmail.com";
  $m = "Mensaje";
  $r = "remitente@alg.com";
  $mail = new email($a, $d, $m, $r);
  $mail->enviar();
  echo $mail->errores(); //Si exisiten errores los mostrara
  echo $mail->enviado(); //Muestra un mensaje al enviar el mail


  /* * ************* Envio multiple via funcion mail()*************************
  $a = array("Asunto", "Asunto2");
  $d = array("MsDark_@hotmail.com", "algo@dominio.com");
  $m = array("Mensaje", "Mensaje2");
  $r = array("remitente@algo.com", "remitente@algo.com");
  $mail = new email($a, $d, $m, $r);
  $mail->enviar();
  echo $mail->errores(); //Si exisiten errores los mostrara
  echo $mail->enviado(); //Muestra un mensaje al enviar el mail


  /* * ************* Envio simple via sockets    *************************
  $a = "Asunto";
  $d = "MsDark_@hotmail.com";
  $m = "Mensaje";
  $r = "remitente@alg.com";
  $server = "169.254.62.112"; //Ip o nombre, o direccion del servidor SMTP
  $port = "25"; //El puerto a usar
  $mail = new email($a, $d, $m, $r);
  $mail->smtp($server, $port);
  //$mail->smtp($server,$port); En caso de no existir login
  echo $mail->errores(); //Si exisiten errores los mostrara
  echo $mail->enviado(); //Muestra un mensaje al enviar el mail


  /* * ************* Envio multiple via sockets    *************************
  $a = array("Asunto", "Asunto2");
  $d = array("MsDark_@hotmail.com", "algo@dominio.com");
  $m = array("Mensaje", "Mensaje2");
  $r = array("remitente@algo.com", "remitente@algo.com");
  $server = "169.254.62.112"; //Ip o nombre, o direccion del servidor SMTP
  $port = "25"; //El puerto a usar
  $mail = new email($a, $d, $m, $r);
  $mail->smtp($server, $port);
  //$mail->smtp($server,$port); En caso de no existir login
  echo $mail->errores(); //Si exisiten errores los mostrara
  echo $mail->enviado(); //Muestra un mensaje al enviar el mail */
