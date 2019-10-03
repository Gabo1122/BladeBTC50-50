<?php


namespace BladeBTC\Commands;

use BladeBTC\Helpers\Btc;
use BladeBTC\Models\BotSetting;
use BladeBTC\Models\Users;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class InfoCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "info";

    /**
     * @var string Command Description
     */
    protected $description = "Info menu.";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {

        /**
         * Chat data
         */
        $id = $this->update->getMessage()->getFrom()->getId();


        /**
         * Display Typing...
         */
        $this->replyWithChatAction([ 'action' => Actions::TYPING ]);


        /**
         * Verify user
         */
        $user = new Users($id);
        if ($user->exist() == false) {

            $this->triggerCommand('start');

        }
        else {

            /**
             * Keyboard
             */
             $keyboard = [
         			[ "Balance " . Btc::Format($user->getBalance()) . " \xF0\x9F\x92\xB0" ],
         			[ "Invertir \xF0\x9F\x92\xB5", "Retirar \xE2\x8C\x9B" ],
         			[ "Preguntas \xE2\x86\xA9", "Ayuda \xE2\x9D\x93" ],
         			[ "Mis referidos \xF0\x9F\x91\xAB","Importante \xE2\x80\xBC" ],
         			[ "Idioma-Language \xF0\x9F\x94\xA0" ],
         		];

            $reply_markup = $this->telegram->replyKeyboardMarkup([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
            ]);

            /**
             * Response
             */


            $this->replyWithMessage([
                'text' => "
<b>Como funciona?</b>\n
Para iniciar tu inversión necesitas hacer un deposito inicial de $100 equivalentes en Bitcoin, para hacerlo selecciona la opción Depositar, luego de que tu deposito sea confirmado se iniciará tu plan.\n
\nPuedes obtener tu enlace para referidos en la opción: 'Mis Referidos' pero para que puedas recibir este bono necesitas tener tu inversión mensual activa es decir hacer tu pago mes a mes.
\n<b>¿Necesitas ayuda?</b>\n" . BotSetting::getValueByName("support_chat_id"),
                'reply_markup' => $reply_markup,
                'parse_mode' => 'HTML',
            ]);

        }
    }
}
