<?php


namespace BladeBTC\Commands;

use BladeBTC\Helpers\AddressValidator;
use BladeBTC\Helpers\Btc;
use BladeBTC\Models\Users;
use Exception;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class UpdateWalletCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "update_wallet";

    /**
     * @var string Command Description
     */
    protected $description = "Update wallet address";

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
             * Get wallet address from message
             */
            $wallet_address = trim(substr($this->update->getMessage()->getText(), 10));

            try {

                /**
                 * Validate if address is empty
                 */
                if (empty($wallet_address)) {

                    $this->replyWithMessage([
                        'text' => "No has ingresado ninguna dirección. \xF0\x9F\x98\x96",
                        'reply_markup' => $reply_markup,
                        'parse_mode' => 'HTML',
                    ]);

                }


                /**
                 * Validate address validity
                 */
                elseif (!AddressValidator::isValid($wallet_address)) {

                    $this->replyWithMessage([
                        'text' => "La dirección (<b>$wallet_address</b>) no es una dirección de Bitcoin válida.\nPor favor intenta de nuevo con una direccion correcta y recuerda solo dejar un espacio después de /direccion. \xF0\x9F\x98\x96",
                        'reply_markup' => $reply_markup,
                        'parse_mode' => 'HTML',
                    ]);
                }


                /**
                 * Update account
                 */
                else {


                    /**
                     * Store investment_address
                     */
                    $user->setWalletAddress($wallet_address);


                    /**
                     * Response
                     */
                    $this->replyWithMessage([
                        'text' => "La dirección (<b>$wallet_address</b>) se ha configurado correctamente.",
                        'reply_markup' => $reply_markup,
                        'parse_mode' => 'HTML',
                    ]);
                }

            } catch (Exception $e) {
                $this->replyWithMessage([
                    'text' => "Ha ocurrido un error al guardar tu dirección. Error: " . $e->getMessage() . "\nPor favor contacta soporte. \xF0\x9F\x98\x96",
                    'reply_markup' => $reply_markup,
                    'parse_mode' => 'HTML',
                ]);
            }
        }
    }
}
