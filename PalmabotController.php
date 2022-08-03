<?php

namespace App\Controllers;



class PalmabotController extends BaseController
{

    /*COSITAS:
        Setwebhook--> https://api.telegram.org/bot5250388873:AAFFjkX3lSAAjoYMPNkSTxMOxLMBFZF0z3w/setWebhook?url=https://tbot.comovas.es/palmabot


        Comprobarwebhook--> https://api.telegram.org/bot5250388873:AAFFjkX3lSAAjoYMPNkSTxMOxLMBFZF0z3w/getWebhookInfo 


        https://api.telegram.org/bot5250388873:AAFFjkX3lSAAjoYMPNkSTxMOxLMBFZF0z3w/setWebhook?url=https://tbot.comovas.es/palmabot

    */

    
    public function index(){
        echo "hola mundo";
    }

    public function request() {

            $token  = '';
            $link   = 'https://api.telegram.org:443/bot'.$token.'';
            
            $getupdate = file_get_contents('php://input'); // for webhook

    
                $update  = json_decode($getupdate, TRUE);
                
                $chat_id = isset($update['message']['chat']['id']) 
                        ? $update['message']['chat']['id'] : 8691410;

                $from_id = isset($update['message']['from']['id']) 
                        ? $update['message']['from']['id'] : 8691410;

                $text = isset($update['message']['text']) 
                    ? $update['message']['text'] : "0";   

                $nombre = isset($update['message']['from']['first_name']) 
                    ? $update['message']['from']['first_name'] : "0";

                $type = isset($update['message']['chat']['type']) 
                    ? $update['message']['chat']['type'] : "0";

                $username = isset($update['message']['from']['username']) 
                    ? $update['message']['from']['username'] : "@";

                /*PRIMER PASO: GUARDAR DATOS EN BASE*/
                $this->guardarDatos3($link, $chat_id, $from_id, $text, $nombre, $type, $username);

      

            switch ( substr(strtoupper($text),0,4) ) {
                case "/TOD":
                    $parameter = [
                        'chat_id'   => $chat_id, 
                        'text'      => "Hola ".$nombre."__\n\n\n ```".$getupdate."```"
                    ];            
                    $request_url = $link.'/sendMessage?'.http_build_query($parameter); $this->sendM3($request_url);
                    break;
                case "/AYU":
                    $this->ayudaAyuda($link, $chat_id);
                    return true;
                    break;
                case "/ROL":
                    $this->Rol($link,$nombre,$chat_id,$from_id,$type);
                    return true;
                    break;
                case "/SRO":
                    $this->SetRol($link,$nombre,$chat_id,$from_id,$text);
                    break;
                case "/MEG":
                    //$this->Mega($chat_id,$token);
                    $this->Mega2($link,$nombre,$chat_id,$from_id,$type);
                    break;
                case "/SME":
                    $this->setMega2($link,$nombre,$chat_id,$from_id,$text);
                default:
                    return true;
                    break;
            }
        return true;
    }

    
    public function Reset($chat_id,$token){
        $respuesta = "🤖 Escribe /AYUDA para ver todos los comandos.";
        $removeKeyboard = array('remove_keyboard' => true);
        $removeKeyboardEncoded = json_encode($removeKeyboard);
        $url = "https://api.telegram.org/bot$token/sendmessage?text=$respuesta&chat_id=$chat_id&reply_markup=$removeKeyboardEncoded";
        $this->sendM3($url);
    }

    public function Mega($chat_id, $token){
        $respuesta = "[MegaSport]: ¿Qué estás haciendo?";
        $keyboard = array(array("/sMega1💪\n\nEn la sala de máquinas","🤸/sMega2\n\nRealizado una actividad","🚿/sMega3\n\nRelajándome en el Spá","/CERRAR❌\n\nTECLADO")); 
        $resp = array("keyboard" => $keyboard,"resize_keyboard" => true,"one_time_keyboard" => true);
        $reply = json_encode($resp); 
        $url = "https://api.telegram.org/bot$token/sendmessage?chat_id=$chat_id&text=$respuesta&reply_markup=".$reply;
        $this->sendM3($url);
    }

    public function ayudaAyuda($link, $chat_id){
        $parameter = [
            'chat_id'   => $chat_id, 
            'text'      => "🤖𝚋𝚒𝚙 𝚋𝚒𝚙... 
_\n[[COMANDOS]] En esta versión están disponibles dos comandos:
\n 👉 El comando /ROL sirve para elegir un Emoji distintivo entre varios seleccionables.
---- Uso del comando: escribe en el chat la palabra /ROL o pulsa sobre ella para comenzar y sigue las instrucciones de @Palmabot.
\n 👉 El comando /MEGA sirve socializar con los otros miembros del chat indicando lo que estás haciendo en el gimnasio. Como resultado obtendrás una lista pública de todos los registros.
---- Uso del comando: escribe en el chat la palabra /MEGA o pulsa sobre ella para comenzar y sigue las instrucciones de @Palmabot.

_\n[[SEGURIDAD]] @Palmabot no almacena datos personales de los usuarios como el número de teléfono y el límite de registos en lista está limitado. 

_\n[[TELEFÓNO DEL SUICIDIO]] Existen vías de ayuda ¡¡ Llama al +34717003717 !!

_\n[[DISCLAIMER]] Ni @Palmabot, este bot, ni @Megasolteros, el canal de charlas de la plataforma Telegram, están afiliados con el gimnasio MegaSport.

_\n¿Necesitas ayuda humana? ¿Sugerencias? ¿Comisiones o encargos? 
PREGUNTA A @GranSuom"
        ];            
        $request_url = $link.'/sendMessage?'.http_build_query($parameter); 
        $this->sendM3($request_url);
    }
    
    public function SetRol($link,$nombre,$chat_id,$from_id,$text){
        $db = \Config\Database::connect();
        $builder = $db->table('bt_btusers');

        //echo substr("Hello world",6);
        $grupo_id = substr($text,6);
        $grupo_id = str_replace("__","-",$grupo_id);
        
        switch (substr($text,5,1)){
            case "1":
                $rol = "♂️";
                break;
            case "2":
                $rol = "♀️";
                break;
            case "3": 
                $rol = "🧘";
                break;
            case "4":
                $rol = "🎾"; 
                break;
            case "5":
                $rol = "💃";  
                break;
            case "6":
                $rol = "🏳️‍🌈"; 
                break;
            default:
                $rol = "🥷";
                break;
        }

        $data = [
            'bt_perso3' => $rol
        ];

        $builder->where('bt_from_id', $from_id);
        $builder->where('bt_chat_id', $grupo_id);
        $builder->update($data);

        //Escribir confirmaciones en chat privado:
        $parameter = [
            'chat_id'   => $chat_id, 
            'text'      => "🤖 Has cambiado correctamente tu rol a [[ ".$rol." ]]... 👏👏👏"
        ];            
        $request_url = $link.'/sendMessage?'.http_build_query($parameter); 
        $this->sendM3($request_url);        
        sleep(1);
        //en chat de grupo:
        $parameter = [
            'chat_id'   => $grupo_id, 
            'text'      => "🤖 NOVEDADES: El usuario ".$nombre." ha cambiado a [[ ".$rol." ]]\n Si quieres cambiar tu Emoji escribe 👉/ROL ⚙️⚙️⚙️"  
        ];            
        $request_url = $link.'/sendMessage?'.http_build_query($parameter); 
        $this->sendM3($request_url);        

    }

    public function mensajeError($link,$text,$chat_id){
        $parameter = [
            'chat_id'   => $chat_id, 
            'text'      => $text
        ];            
        $request_url = $link.'/sendMessage?'.http_build_query($parameter); 
        $this->sendM3($request_url);        
    }

    public function Rol($link,$nombre,$chat_id,$from_id,$type){
        
        if ($type!=="supergroup")
        {
            $text = "🤖 Error: este comando solo puede ser usado desde un chat de grupo como @megasolteros ⚙️⚙️⚙️⚙️";
            $this->mensajeError($link,$text,$chat_id);
        }else{
        //echo str_replace("world","Peter","Hello world!");
        $text = "🤖 Hey, ".$nombre."\nPulsa sobre la palabra destacada para asignarte un Emoji (rol en grupo número".$chat_id."). Puedes elegir entre:";
        $text = $text." \n\n\n 1 [[ ♂️ ]] - Emoji Hombre Fit
👉👉 /sRol1".str_replace("-","__",$chat_id)." 
                        \n\n 2 [[ ♀️ ]] - Emoji Mujer Fit
👉👉 /sRol2".str_replace("-","__",$chat_id)." 
                        \n\n 3 [[ 🧘 ]] - Emoji Mente Zen
👉👉 /sRol3".str_replace("-","__",$chat_id)."                         
                        \n\n 4 [[ 🎾 ]] - Emoji Pistas Outdoor
👉👉 /sRol4".str_replace("-","__",$chat_id)."                         
                        \n\n 5 [[ 💃 ]] - Emoji Baile
👉👉 /sRol5".str_replace("-","__",$chat_id)."                         
                        \n\n 6 [[ 🏳️‍🌈 ]] - Emoji LGBT+
👉👉 /sRol6".str_replace("-","__",$chat_id);         
        $parameter = [
            'chat_id'   => $from_id, 
            'text'      => $text
        ];   
        $request_url = $link.'/sendMessage?'.http_build_query($parameter); 
        $this->sendM3($request_url);
        }//findelif_else
         
    }

    public function setMega2($link,$nombre,$chat_id,$from_id,$text){
        $db = \Config\Database::connect();
        $builder = $db->table('bt_mega');
        
        //PRIMER PASO: DELETE
        //https://codeigniter.com/user_guide/database/query_builder.html?highlight=insert#deleting-data
        $builder->where('me_from_id', $from_id);
        $builder->delete();

        //SEGUNDO PASO: AÑADIR REGISTRO

        //-----2A ¿conocer el grupo id?
        $grupo_id = substr($text,7);
        $grupo_id = str_replace("__","-",$grupo_id);

        //-----2B ¿conocer lo que están haciendo?
        switch (substr($text,6,1)){
            case "1":
                $haciendo = "está en la sala de fitness 🏋️";
                break;
            case "2":
                $haciendo = "está realizando una actividad 🤸";
                break;
            case "3": 
                $haciendo = "está jugando un partido 🎾";
                break;
            case "4":
                $haciendo = "está relajándose en el Spá 🚿"; 
                break;
        }

        $data = [
            'me_from_id' => $from_id,
            'me_chat_id' => $grupo_id,
            'me_perso1' => $haciendo
        ];
        
        $builder->insert($data);

        //TERCER PASO: MOSTRAR ÚLTIMOS 10 REGISTROS
        //https://codeigniter.com/user_guide/database/results.html
        //$query = $builder->getArray();
        //foreach ($query->getResult('array') as $row) {
        //    echo $row['title'];
        //}
        $resultado = "¿Quién está ahora en el gym? \n =========================== \n";
        $builder->orderBy('me_autodate', 'DESC');
        $query = $builder->get(10);
        foreach ($query->getResult('array') as $row) {
            
            //SACAR LA FECHA DIFERENCIA
            //https://www.w3schools.com/PHP/func_date_date_diff.asp
            //https://aarafacademy.com/calculate-difference-two-dates-php/
            //https://tecadmin.net/get-current-date-and-time-in-php/
            $date1=date_create($row['me_autodate']);
            $date2=date_create(date('Y-m-d H:i:s'));
            //$diff = $date1->diff($date2);
            $diff = date_diff($date1,$date2); 
            $diffc = ( ($diff->days * 24 ) * 60 ) + ( $diff->i );

            //SACAR RESULTADOS
            $sql = "SELECT * FROM `bt_btusers` WHERE `bt_from_id` = ".$row['me_from_id']." AND `bt_chat_id` =".$row['me_chat_id']." LIMIT 1";
            $query2 = $db->query($sql);
            $userRow = $query2->getRowArray();
           
            $resultado = $resultado."Hace ".$diffc." minutos:\n"."[[ ".$userRow['bt_perso3']." ]] ".$userRow['bt_first_name'].' @'.$userRow['bt_username']." ".$row['me_perso1']."\n --------------------------- \n";

            $resultado = str_replace("Hace 0 minutos","🔥🔥🔥AHORA",$resultado);
            $resultado = str_replace("Hace 1 minutos","🔥🔥🔥AHORA",$resultado);
            
   
        }//fin del foreach


        //MENSAJES DE CONFIRMACIONES
        //Escribir confirmaciones en chat privado:
        $parameter = [
        'chat_id'   => $grupo_id, 
        'text'      => "🤖 ".$resultado
        ];            
        $request_url = $link.'/sendMessage?'.http_build_query($parameter); 
        $this->sendM3($request_url);        
        sleep(3);

        $parameter = [
        'chat_id'   => $from_id, 
        'text'      => "🤖 Acción realizada correctamente ⚙️⚙️⚙️\n Mira lo que están haciendo los mienbros del chat del grupo en @MegaSolteros 😇"];            
        $request_url = $link.'/sendMessage?'.http_build_query($parameter); 
        $this->sendM3($request_url);        
        sleep(1);


    }

    public function Mega2($link,$nombre,$chat_id,$from_id,$type){
        
        if ($type!=="supergroup")
        {
            $text = "🤖 Error: este comando solo puede ser usado desde un chat de grupo como @megasolteros ⚙️⚙️⚙️⚙️";
            $this->mensajeError($link,$text,$chat_id);
        }else{
        //echo str_replace("world","Peter","Hello world!");
        $text = "🤖 Hey, ".$nombre."\nSocializa con tus amigos indicando publicamente lo que estás haciendo ahora en el gimnasio (Grupo".$chat_id."). Pulsa sobre la opción destacada:";
        $text = $text." \n\n\n - ¿Estás 🏋️ en la sala de fitness?
Usa el comando 1: 👉👉 /sMega1".str_replace("-","__",$chat_id)." 
                        \n\n - ¿Estás 🤸 realizando una actividad?
Usa el comando 2: 👉👉 /sMega2".str_replace("-","__",$chat_id)." 
                        \n\n - ¿Estás 🎾 jugando un partido?
Usa el comando 3: 👉👉 /sMega3".str_replace("-","__",$chat_id)."                         
                        \n\n - ¿Estás 🚿 relajándote en el Spá?
Usa el Comando 4: 👉👉 /sMega4".str_replace("-","__",$chat_id);

        $parameter = [
            'chat_id'   => $from_id, 
            'text'      => $text
        ];   
        $request_url = $link.'/sendMessage?'.http_build_query($parameter); 
        $this->sendM3($request_url);
        }//findelif_else
         
    }
    //$this->guardarDatos3($link, $chat_id, $from_id, $text, $nombre, $type, $username);
    public function guardarDatos3($link, $chat_id, $from_id, $text, $nombre, $type, $username){
        $db = \Config\Database::connect();
        $sql = 'SELECT COUNT(*) A FROM `bt_btusers` WHERE `bt_from_id` = "'.$from_id.'" AND `bt_chat_id` = "'.$chat_id.'" LIMIT 1';

        $query = $db->query($sql);
        $row  = $query->getRowArray();
        $builder = $db->table('bt_btusers');


        if ($row['A'] == 0){

            $this->bienvenida($link,$chat_id,$nombre,$row['A']);

            $data = [
                'bt_username'       => $username,
                'bt_first_name'     => $nombre,
                'bt_from_id'        => $from_id,
                'bt_chat_id'        => $chat_id,
                'bt_type'           => $type,
                'bt_perso1'         => $text,
                'bt_perso2'         => '',
                'bt_perso3'         => ''
            ];
            
            $builder->insert($data);
    
        }else{
            $data = [
                'bt_perso1'  => $text,
                'bt_perso2' => rand(0,100)." OK ".$row['A']
            ];

            $where = [  
                'bt_from_id' => $from_id,
                'bt_chat_id' => $chat_id
            ];

            $builder->update($data, $where);
        }


    }//fin guardarDatos3

    public function guardarDatos2($link, $update){
        $db = \Config\Database::connect();
        
        ////existe bt_from_id?
        ////https://codeigniter.com/user_guide/database/examples.html#standard-query-with-single-result
        $sql = 'SELECT COUNT(*) A FROM `bt_btusers` WHERE `bt_from_id` = "'.$update['message']['from']['id'].'" AND `bt_chat_id` = "'.$update['message']['chat']['id'].'" LIMIT 1';
        $query = $db->query($sql);
        $row  = $query->getRowArray();
        $builder = $db->table('bt_btusers');


        if ($row['A'] == 0){
            $this->bienvenida($link, $update['message']['chat']['id'],$update['message']['from']['first_name'],$row['A']);

            $data = [
                'bt_username'       => 
isset($update['message']['from']['username']) ? $update['message']['from']['username'] : "@",
                'bt_first_name'     => $update['message']['from']['first_name'],
                'bt_from_id'        => $update['message']['from']['id'],
                'bt_chat_id'        => $update['message']['chat']['id'],
                'bt_type'           => $update['message']['chat']['type'],
                'bt_perso1'         => $update['message']['text'],
                'bt_perso2'         => '',
                'bt_perso3'         => ''
            ];
            
            $builder->insert($data);
    
        }else{
            $data = [
                'bt_perso1'  => $update['message']['text'],
                'bt_perso2' => rand(0,100)." OK ".$row['A']
            ];

            $where = [  
                'bt_from_id' => $update['message']['from']['id'],
                'bt_chat_id' => $update['message']['chat']['id']
            ];

            $builder->update($data, $where);
        }
    }

   

    public function bienvenida($link, $chat_id, $nombre, $rowA)
    {
        $parameter = [
            'chat_id'   => $chat_id, 
            'text'      => "🤖 Hola ".$nombre."¿eres la primera vez que escribes? ¡Gracias por venir! Preséntante y usa /ayuda para ver todo lo que este bot puede hacer por ti 💪💪💪"
        ];            
        $request_url = $link.'/sendMessage?'.http_build_query($parameter); 
        $this->sendM3($request_url);
    }


    public function apiRequestWebhook($request_url) {
            $ch = curl_init();
            $optArray = array(
                    CURLOPT_URL => $request_url,
                    CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array($ch, $optArray);
            $result = curl_exec($ch);
            curl_close($ch);
            //return $this->respond($data, 200);
            return $result;
    }

    public function sendMenssage2($link, $chat_id, $text){
        //global $URL;
        $json = ['chat_id'      => $chat_id,
                'text'          => $text,
                'parse_mode'    => 'HTML'];
                
        $client = \Config\Services::curlrequest();
        return $client->request('POST', $link.'/sendMessage', $json);
        //return http_post($link.'/sendMessage', $json);
    }

    public function sendM3($request_url){
        file_get_contents($request_url);
        return true;
    }

    public function base(){
        //$db = \Config\Database::connect();

        $db = db_connect();
        $query   = $db->query('SELECT pr_id, pr_texto FROM prueba');
        $results = $query->getResultArray();

        foreach ($results as $row) {
            echo $row['pr_id'];
            echo " ------------------ ";
            echo $row['pr_texto'];
            echo "<br>";
        }
    }//fin de base
      

}
