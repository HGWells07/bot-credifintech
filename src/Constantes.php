<?php

namespace BotCredifintech;

class Constantes {

  public function __construct(){
    echo __CLASS__ . '<br />';
  }
  
  const CONFIG = [
    'facebook'=> [
        'token' => 'EAAFamui1cCoBAGbrNG3AX6g1q36eIwGfZAEg9AH3FZAD8pSFwVMpKXOPeUdg5dbcuytXf2fyHD3UyjlGEepSyoLJKPY8iKTaSPAlpxTl1nY31gop0ZChIoyaZCIatBY44SysqrHJbx3FYVamqeKshMKX6iiHBngrGK56C2fi3QZDZD',
        'app_secret' => 'c69036db967a97517be694473c59b8f2', 
        'verification'=>'23894sdf980sf9sdf9d2jaibaveracruzana2000',
    ]   
  ];

  //Mensajes para conversaciones

  const PEDIR_NOMBRE = "Escriba su nombre completo";
  const PEDIR_TELEFONO = "Escriba su número de teléfono";
  const PEDIR_NSS = "Escriba su NSS";
  const PEDIR_MATRICULA = "Escriba su número de matrícula";
  const PEDIR_INE = "Por favor tome una foto de su INE";
  const PEDIR_TALON_NOMINA = "Por favor tome una foto de sutalón de nómina";
  const PEDIR_INFORME_PAGO = "Por favor tome una foto de su Informe de pago";

  // Este mensaje se muestra cuando el usuario no coincide con el perfil de prospecto.
  const MENSAJE_NO = "Lo sentimos, aún no contamos con convenios para tu área, le agradecemos su interés y sugerimos que nos siga en nuestras redes para futuras actualizaciones.";

  // Se despliega para solicitar más datos al usuario que no coincidió con los prospectos.
  const MENSAJE_NO_DATOS = "Para notificarle de futuras actualizaciones en nuestros servicios, le solicitamos la siguiente información para mantenernos en contacto";
  
  // Preguntas
  const PREGUNTA_NOMBRE = "Escriba su nombre completo";
  const PREGUNTA_TELEFONO = "Proporcionenos su número telefónico o celular";

  // Preguntas Pensionados
  const PREGUNTA_PENSIONADO_MATRICULA = "Proporcionenos su matrícula de la institución";
  const PREGUNTA_PENSIONADO_DELEGACIÓN = "Escriba el nombre de la delegación donde se encuentra la estancia donde trabajó";

  const MENSAJE_NAVEGACION_BOTONES = "Por favor, conteste presionando los botones que aparecen en pantalla después de cada mensaje";
  const MENSAJE_AYUDA_ASESOR = "Actualmente se está comunicando a través de un sistema automatizado, pero parece requerir ayuda adicional, un asesor especializado se comunicará con usted";
  const MENSAJE_INSTRUCCIONES_LLAMAR_ASESOR = "Parece tener problemas con nuestro servicio automatizado, para llamar a un asesor especializado para que lo atienda personalmente, ingrese la palabra 'asesor' y espere a que el personal especializado se contacte con usted";

  //Antes de pedir los datos se verifica que el prospecto cuente con la documentación requerida
  const PREGUNTA_DOCUMENTACION = "¿Cuenta con los datos/documentos que se requieren?";
  
  //Estos dos mensajes conforman el enlistado y confirmación de que se cuenta con los datos requeridos
  const MENSAJE_DATOS_REQUERIDOS = "Para realizar esta solicitud de crédito se requerirán los siguientes datos/documentos, presione el boton de todo listo cuando esté seguro de tener toda la información a la mano";

  //Mensaje de finalizacion del registro
  const MENSAJE_SOLICITUD_TERMINADA = "¡Todo listo! en un momento un asesor especializado se contactará con usted";
}