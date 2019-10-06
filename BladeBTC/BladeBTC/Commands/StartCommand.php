<?php


namespace BladeBTC\Commands;

use BladeBTC\Helpers\Btc;
use BladeBTC\Models\BotSetting;
use BladeBTC\Models\Referrals;
use BladeBTC\Models\Users;
use Exception;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
	/**
	 * @var string Command Name
	 */
	protected $name = "start";

	/**
	 * @var string Command Description
	 */
	protected $description = "Start bot";

	/**
	 * @inheritdoc
	 */
	public function handle($arguments)
	{

	    error_log($arguments,0);

	    try {

            /**
             * Chat data
             */
            $username = $this->update->getMessage()->getFrom()->getUsername();
            $first_name = $this->update->getMessage()->getFrom()->getFirstName();
            $last_name = $this->update->getMessage()->getFrom()->getLastName();
            $id = $this->update->getMessage()->getFrom()->getId();


            /**
             * Display Typing...
             */
            $this->replyWithChatAction(['action' => Actions::TYPING]);


            /**
             * User model
             */
            $user = new Users($id);


            /**
             * Add user to our database
             */
            if ($user->exist() == false) {

                $user->create([
                    "username"   => isset($username) ? $username : "not set",
                    "first_name" => isset($first_name) ? $first_name : "not set",
                    "last_name"  => isset($last_name) ? $last_name : "not set",
                    "id"         => isset($id) ? $id : "not set",
                ]);

				/**
				 * Referral
				 */
				if (!empty($arguments)) {
					Referrals::BindAccount($arguments, $id);
				}

                /**
                 * Response
                 */
                $this->replyWithMessage([
                    'text'       => "Bienvenido <b>" . $first_name . "</b>. \xF0\x9F\x98\x84 \nSi necesitas más ayuda contacta a quien te ha referido " . BotSetting::getValueByName("telegram_id_referent"),
                    'parse_mode' => 'HTML',
                ]);

                /**
                 * Go to start
                 */
                $this->triggerCommand('start');

            } else {


				/**
				 * Referral
				 */
				if (!empty($arguments)) {
					Referrals::BindAccount($arguments, $id);
				}


				/**
				 * Teclado
				 */
		$keyboard = [
			[ "Balance " . Btc::Format($user->getBalance()) . " \xF0\x9F\x92\xB0" ],
			[ "Invertir \xF0\x9F\x92\xB5", "Retirar \xE2\x8C\x9B" ],
			[ "Preguntas \xE2\x86\xA9", "Ayuda \xE2\x9D\x93" ],
			[ "Mis referidos \xF0\x9F\x91\xAB","Importante \xE2\x80\xBC" ],
			[ "Idioma-Language \xF0\x9F\x94\xA0" ],
		];

				$reply_markup = $this->telegram->replyKeyboardMarkup([
						'keyboard'          => $keyboard,
						'resize_keyboard'   => true,
						'one_time_keyboard' => false,
				]);

                /**
                 * Response
                 */
                $this->replyWithMessage([
                    'text'         => "Hola de nuevo <b>" . $first_name . "</b>\nSeleciona una de las opciones en el menu para continuar. \xF0\x9F\x98\x84 \n Si necesitas más ayuda contacta a quien te ha referido " . BotSetting::getValueByName("support_chat_id"),
                    'reply_markup' => $reply_markup,
                    'parse_mode'   => 'HTML',
                ]);
            }
        }
        catch (Exception $e){

					$keyboard = [
						[ "Balance " . Btc::Format($user->getBalance()) . " \xF0\x9F\x92\xB0" ],
						[ "Invertir \xF0\x9F\x92\xB5", "Retirar \xE2\x8C\x9B" ],
						[ "Preguntas \xE2\x86\xA9", "Ayuda \xE2\x9D\x93" ],
						[ "Mis referidos \xF0\x9F\x91\xAB","Importante \xE2\x80\xBC" ],
						[ "Idioma-Language \xF0\x9F\x94\xA0" ],
					];

							$reply_markup = $this->telegram->replyKeyboardMarkup([
									'keyboard'          => $keyboard,
									'resize_keyboard'   => true,
									'one_time_keyboard' => false,
							]);

            $this->replyWithMessage([
                'text'         => "Ha ocurrido un error.\n" . $e->getMessage() . ". \xF0\x9F\x98\x96",
                'reply_markup' => $reply_markup,
                'parse_mode'   => 'HTML'
            ]);
        }
	}
}
