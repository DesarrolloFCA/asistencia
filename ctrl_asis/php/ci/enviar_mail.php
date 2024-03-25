<?php 

$parametros['correo_destino']= 'diegojaviermenendez@gmail.com';

//Definimos las propiedades y llamamos a los métodos
require_once('3ros/phpmailer/class.phpmailer.php');
$mail = new phpmailer();//instanciamos un objeto de la clase phpmailer
$mail->PluginDir    = "3ros/phpmailer/";
if(!empty($datos_correo['smtpsecure'])){ $mail->SMTPSecure   = $datos_correo['smtpsecure']; }
$mail->Mailer       = 'smtp';     //servidor smtp
$mail->SMTPAuth     = true;  //indicamos autenticación
$mail->Host         = 'smtp.uncu.edu.ar';      //nombre de nuestro servidor smtp  ej. "mail.diariosanrafael.com.ar"
$mail->Port         = '25'; // 25,587,465
$mail->Username     = 'citinformacion@uncu.edu.ar';  //nombre de usuario y password
$mail->Password     = 'infor123';
$mail->From         = 'citinformacion@uncu.edu.ar'; //nuestra dirección de correo y el nombre veran
$mail->FromName     = 'CIT';
$mail->Timeout      = 10;
$mail->IsHTML(true);

if(!empty($parametros['reply_email'])){
$mail->AddReplyTo($parametros['reply_email'], $parametros['reply_nombre']);
}

$mail->AddAddress($parametros['correo_destino']); //dirección destino

if(!empty($parametros['correo_copia'])){
$mail->AddCC($parametros['correo_copia']);
}

if(!empty($parametros['correo_copia_oculta'])){
$mail->AddBCC($parametros['correo_copia_oculta']);
}

$mail->Subject      = $parametros['asunto'];

$mail->Body         = '<div style="background-color: #F1F1F1; width: 100%; min-height: 400px; color: rgb(35, 31, 32); font-family: Calibri, Helvetica, Arial, sans-serif; font-size: 14px; background-position: 50% 0%; background-repeat: repeat no-repeat;">
  <div align="center" style="width: 100%; margin: 0px auto; padding-top: 30px;">
    <table style="padding: 0px; margin: 0px; border-collapse: collapse; empty-cells: hide; width: 100%;">
        <tbody>
            <tr>
              <td style="padding: 0px;">
                <div style="text-align: center; 
                min-height: 18px; padding-top: 0px; 
                padding-right: 0px; padding-bottom: 20px !important; 
                padding-left: 0px; width: 100%; margin: 0px; 
                font-weight: bold; 
                color: #4D4D4D; 
                font-size: 30px !important;">
                        <span style="margin-left: 4px; margin-right: 4px;">'.$parametros['encabezado_mensaje'].'</span>
                </div>

                <div style="vertical-align: top; width: 90%; margin: 0px auto; padding: 0px !important;">
                        <div style="padding: 15px 10px; margin: 0px; border: 1px solid rgb(188, 187, 193); background-color: rgb(255, 255, 255);">
                        '.$parametros['contenido_mensaje'].'
                        </div>
                </div>
              </td>
            </tr>
        </tbody>
    </table>
  </div>
</div>
<div style="font-size: 13px; height: 50px; margin: 0px; padding: 30px 0px 0px; width: 100%; background-color: #F1F1F1; color: #231f20; font-family: Calibri, Helvetica, Arial, sans-serif; position: relative; bottom: 0px;">
        <div style="background-color: #D8D8D8; height: 60px; margin: 0px; padding: 0px; text-align: center; color: #696969 !important; width: 100%; background-position: initial initial; background-repeat: initial initial;">
            <div style="width: 100%; margin: 0px; padding: 0px;">
                <div style="padding-top: 20px; border: 0px solid rgb(255, 255, 255);">
                Sistema desarrollado por el Centro Informático y Tecnol&oacute;gico de la UNCuyo - <a target="_blank" style="color:#337ab7; float:none; text-transform:none; border: none; padding-left: 0px" href="http://http://cit.uncu.edu.ar/">http://cit.uncu.edu.ar/</a>.
                </div>
            </div>
        </div>
</div>';

$mail->AltBody      = $parametros['encabezado_mensaje_txt'].'

'.$parametros['contenido_mensaje_txt'];


if(!empty($parametros['adjunto1'])){
    $mail->AddAttachment($parametros['adjunto1']);
}                        

//se envia el mensaje, si no ha habido problemas la variable tendra el valor true
$exito = $mail->Send();

if(!$exito){
    $error = "Error al enviar correo a ".$parametros['correo_destino']." al host ".$mail->Host.": ".$mail->ErrorInfo;
    toba::notificacion()->agregar($error, "error");
    return false;
}else{
    $mail->ClearAddresses();
    //toba::notificacion()->agregar("Este correo puede ingresar en la casilla de correo no deseado, por favor verificalo.", "info");
    return true;
}

?>