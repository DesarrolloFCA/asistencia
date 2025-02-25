<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

/**
 * Clase para crear un mail en texto plano o html. Encapsula a la librería PHPMailer.
 * @package Centrales
 */
class TobaMail
{
    protected $desde;
    protected $hacia;
    protected $asunto;
    protected $cuerpo;
    protected $desde_nombre;
    protected $html = true;
    protected $cc = array();
    protected $bcc = array();
    protected $datos_configuracion;
    protected $adjuntos = array();
    protected $debug = "SMTP::DEBUG_SERVER";
    protected $timeout = 30;
    protected $reply_to;
    protected $confirmacion;
    protected $nombre_conf = null;
    protected $config_file = '/usr/local/proyectos/comision/php/mail/formularios_asistencia.json';

    /**
     * Constructor de la clase
     * @param string $hacia  Direccion de email a la cual se enviará
     * @param string $asunto
     * @param string $cuerpo Contenido del email
     * @param string $desde Direccion de email desde la cual se envia (opcionalmente se obtiene desde los parametros)
     * @param string $config_file Ruta al archivo de configuración JSON
     */
    public function __construct($hacia, $asunto, $cuerpo, $desde = null, $ccopia)
    {
        $this->hacia = $hacia;
        $this->asunto = $asunto;
        $this->cuerpo = $cuerpo;
        $this->desde = $desde;
        $this->cc = $ccopia;
        //$this->config_file = $config_file;
    }

    /**
     * Permite modificar en runtime el nombre de la configuracion smtp a ser utilizada
     * @param string $nombre_conf  Nombre de la configuracion en el archivo smtp.ini
     */
    public function setConfiguracionSmtp($nombre_conf = null)
    {
        if (!is_null($nombre_conf)) {
            $this->nombre_conf = $nombre_conf;
        }
    }

    /**
     *  Método para obtener la configuración del servidor SMTP desde un archivo JSON
     */
    public function getDatosConfiguracionSmtp()
    {
        $config_path = $this->getAbsolutePath($this->config_file);
        if (!file_exists($config_path)) {
            throw new Exception("El archivo de configuración SMTP no existe: {$config_path}");
        }

        $config_data = file_get_contents($config_path);
        return json_decode($config_data, true);
    }


    /**
     * Servicio que dispara el envio del email
     */
    public function ejecutar()
    {
        $this->enviar();
    }

    /**
     * Realiza el envio del email propiamente dicho
     */
    public function enviar()
    {
        //SMTP needs accurate times, and the PHP time zone MUST be set
        //This should be done in your php.ini, but this is how to do it if you don't have access to that
        date_default_timezone_set('Etc/UTC');

        // Se obtiene la configuración del SMTP
        $this->datos_configuracion = $this->getDatosConfiguracionSmtp();
        if (!isset($this->desde)) {
            $this->desde = $this->datos_configuracion['from'];
        }

        // Construye y envia el mail
        $mail = new PHPMailer();
        try {
            $mail->isSMTP();
            //$mail->SMTPDebug = $this->datos_configuracion['debug'];
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Host = $this->datos_configuracion['host'];
            $mail->Port = $this->datos_configuracion['port'];
            //$mail->SMTPSecure = $this->datos_configuracion['security'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAuth = true;
            $mail->AuthType = 'XOAUTH2';
            //$mail->Timeout = $this->timeout;

            //if ($this->datos_configuracion['auto_tls']) {
            //    $mail->SMTPAutoTLS = true;
            //}

            $provider = new Google(
                [
                    'clientId' => $this->datos_configuracion['client_id'],
                    'clientSecret' => $this->datos_configuracion['client_secret'],
                ]
            );

            $mail->setOAuth(
                new OAuth(
                    [
                        'provider' => $provider,
                        'clientId' => $this->datos_configuracion['client_id'],
                        'clientSecret' => $this->datos_configuracion['client_secret'],
                        'refreshToken' => $this->datos_configuracion['refresh_token'],
                        'userName' => $this->datos_configuracion['username'],
                    ]
                )
            );
            $mail->CharSet = PHPMailer::CHARSET_UTF8;

            $mail->setFrom($this->desde, $this->datos_configuracion['from_name']);
            $mail->addAddress($this->hacia);

            // Agrego copias
            foreach ($this->cc as $copia) {
                $mail->addCC($copia);
            }

            // Agrego copias ocultas
            foreach ($this->bcc as $copia) {
                $mail->addBCC($copia);
            }

            if (isset($this->reply_to)) {
                $mail->addReplyTo($this->reply_to);
            }

            if (isset($this->confirmacion)) {
                $mail->ConfirmReadingTo = $this->confirmacion;
            }

            $mail->Subject = $this->asunto;
            $mail->Body = $this->cuerpo;
            $mail->isHTML($this->html);

            foreach ($this->adjuntos as $adjunto) {
                $mail->addAttachment($adjunto['path'], $adjunto['name'], $adjunto['encoding'], $adjunto['type']);
            }

            $mail->send();
            //echo "Message has been sent successfully";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function setCc($direcciones = array())
    {
        $this->cc = $direcciones;
    }

    public function setBcc($direcciones = array())
    {
        $this->bcc = $direcciones;
    }

    public function setHtml($html = true)
    {
        $this->html = $html;
    }

    public function setReply($reply)
    {
        $this->reply_to = $reply;
    }

    public function setRemitente($from_name)
    {
        $this->desde_nombre = $from_name;
    }

    public function setConfirmacion($confirm)
    {
        $this->confirmacion = $confirm;
    }

    public function agregarAdjunto($nombre, $path_archivo, $encoding = 'base64', $tipo = '')
    {
        $this->adjuntos[] = [
            'name' => $nombre,
            'path' => $path_archivo,
            'encoding' => $encoding,
            'type' => $tipo
        ];
    }
    /**
     * Obtiene la ruta absoluta del archivo de configuración
     * @param string $path Ruta relativa del archivo
     * @return string Ruta absoluta del archivo
     */
    private function getAbsolutePath($path)
    {
        if (realpath($path)) {
            return realpath($path);
        }

        return __DIR__ . DIRECTORY_SEPARATOR . $path;
    }
}
