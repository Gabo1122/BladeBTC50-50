<?php


namespace BladeBTC\Commands;

use BladeBTC\Helpers\Btc;
use BladeBTC\Helpers\Currency;
use BladeBTC\Models\InvestmentPlan;
use BladeBTC\Models\Users;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class WithdrawCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "withdraw";

    /**
     * @var string Command Description
     */
    protected $description = "Load withdraw menu";

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
             * Verify user wallet address
             */
            if (is_null($user->getWalletAddress())) {

                $this->replyWithMessage([
                    'text' => "Tu dirección de Bitcoin <b>aún ha sido configurada</b>\n
Para configurarla utiliza el comando /direccion seguido de tu dirección.\n
Ejemplo:\n
/direccion 1JrkEVAPaEnb48jeXgomFSgNqTNcvjgjTc\n
Puedes cambiar tu direccion en cualquier momento con esta opcion.",
                    'reply_markup' => $reply_markup,
                    'parse_mode' => 'HTML',
                ]);

            }
            else {

                $this->replyWithMessage([
                    'text' => "Tu dirección para retirar es :\n
<b>" . $user->getWalletAddress() . "</b>\n
Utiliza el comando /direccion para actualizar tu cuenta.\n\nPor ejemplo:\n/direccion 1JrkEVAPaEnb48jeXgomFSgNqTNcvjgjTc\n
Utiliza el comando /sacar para retirar fondos.\n\nPor ejemplo:\n/sacar 1.2\n
Y el monto deseado será transferido a la dirección configurada.
(Aproximadamente en una hora: " . Currency::GetBTCValueFromCurrency(InvestmentPlan::getValueByName("minimum_payout_usd")) . "BTC).",
                    'reply_markup' => $reply_markup,
                    'parse_mode' => 'HTML',
                ]);

            }
        }
    }
}
