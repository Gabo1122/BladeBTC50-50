<?php


namespace BladeBTC\Commands;

use BladeBTC\Helpers\Btc;
use BladeBTC\Models\BotSetting;
use BladeBTC\Models\Referrals;
use BladeBTC\Models\Users;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class ReferralCommand extends Command
{
	/**
	 * @var string Command Name
	 */
	protected $name = "referral";

	/**
	 * @var string Command Description
	 */
	protected $description = "Referral menu";

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
		$this->replyWithChatAction(['action' => Actions::TYPING]);


		/**
		 * Verify user
		 */
		$user = new Users($id);
		if ($user->exist() == false) {

			$this->triggerCommand('start');

		} else {

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
				'keyboard'          => $keyboard,
				'resize_keyboard'   => true,
				'one_time_keyboard' => false,
			]);


			$this->replyWithMessage([
				'text'         => "<b>Mis referidos:</b>

Envia este enlace a tus amigos y familia para empezar a recibir tu comisión.

<b>Este es tu enlace personal:</b>
https://t.me/" . BotSetting::getValueByName("app_name") . "?start=" . $user->getReferralLink() . "

<b>Mi equipo</b>

Total referidos : <b>" . Referrals::getTotalReferrals($user->getTelegramId()) . "</b>

Miembros | Activos | Inversión
" . Referrals::getTotalReferrals($user->getTelegramId()) . " | " . Referrals::getActiveReferrals($user->getTelegramId()) . " | " . Btc::Format(Referrals::getReferralsInvest($user->getTelegramId())) . " BTC
",
				'reply_markup' => $reply_markup,
				'parse_mode'   => 'HTML',
			]);


		}
	}
}
