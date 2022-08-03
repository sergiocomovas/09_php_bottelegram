<?php

namespace App\Controllers;

class PalmabotController3 extends BaseController
{
    public function index()
    {
        return 1;
    }

    public function request(){
        $token  = '';
        $link   = 'https://api.telegram.org:443/bot'.$token.'';
        
        $getupdate = file_get_contents('php://input'); // for webhook

        if ($getupdate){
            $update  = json_decode($getupdate, TRUE);
            $chat_id = $update['message']['chat']['id'];
            $from_id = $update['message']['from']['id'];
            $text = $update['message']['text'];   // message from user
            $nombre = $update['message']['from']['first_name'];
            $type = $update['message']['chat']['type'];

            /*¡¡GUARDAR DATOS EN BASE!!*/
            $this->guardarDatos($link, $update);

        }else{
            $text = "--";
            $req_message    = "--";          
            $chat_id = "8691410";
            $nombre = "puto";
        }

        switch ( substr(strtoupper($text),0,4) ) {
            case "/TOD":
                $parameter = [
                    'chat_id'   => $chat_id, 
                    'text'      => "Hola ".$nombre."__\n\n\n ```".$getupdate."```"
                ];            
                $request_url = $link.'/sendMessage?'.http_build_query($parameter); $this->sendM3($request_url);
                break;
            default:
                return true;
                break;
        }
    }

    public function sendM3($request_url){
        file_get_contents($request_url);
        return true;
    }

    /*--------------------------------*/

    public function guardarDatos($link,$update)
    {
        $db = \Config\Database::connect();
        
        ////existe bt_from_id?
        ////https://codeigniter.com/user_guide/database/examples.html#standard-query-with-single-result
        $query = $db->query('SELECT COUNT(*) A FROM `bt_btusers` WHERE `bt_from_id` = '.$update['message']['from']['id'].' AND `bt_chat_id` = '.$update['message']['chat']['id'].' LIMIT 1');
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
            'text'      => "👋👋👋 Hola ".$nombre.". ¿Eres la primera vez que escribes? ¡¡Me alegra de conocerte!!. \n\n\n⚠️Importante: escribe el comando 👉 /ayuda 👈 para recibir una explicación del funcionamiento del chat. \n\n\n ¡No olvides presentarte al resto de los miembros y pasarlo bien!! ".$rowA
        ];            
        $request_url = $link.'/sendMessage?'.http_build_query($parameter); 
        $this->sendM3($request_url);
    }

}
