<?php


namespace BladeBTC\Commands;

use BladeBTC\Helpers\Btc;
use BladeBTC\Models\Investment;
use BladeBTC\Models\InvestmentPlan;
use BladeBTC\Models\Users;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class BalanceCommand extends Command
{
	/**
	 * @var string Command Name
	 */
	protected $name = "balance";

	/**
	 * @var string Command Description
	 */
	protected $description = "Display account balance.";

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


			/**
			 * Contract list
			 */
			$investment = Investment::getActiveInvestment($user->getTelegramId());
			if (count($investment) > 0) {
				$investment_data = "\n<b>|   Balance   |   Termina   |</b>\n";
				foreach ($investment as $row) {
					$investment_data .= "|" . $row->amount . "|" . $row->contract_end_date . "|\n";
				}
			} else {
				$investment_data = "No estás activo";
			}


			/**
			 * Response
			 */
			$this->replyWithMessage([
				'text'         => "Tu balance:
<b>" . Btc::Format($user->getBalance()) . "</b> BTC\n
Total invertido:
<b>" . Btc::Format($user->getInvested()) . "</b> BTC\n
Total para retirar:
<b>" . Btc::Format($user->getPayout()) . "</b> BTC\n
Comisión pendiente (Por referidos):
<b>" . Btc::Format($user->getCommission()) . "</b> BTC\n
Total depositado (confirmado):
<b>" . Btc::Format($user->getLastConfirmed()) . "</b> BTC\n
Total depositado (Menor de lo necesario):
<b>" . Btc::Format($user->getLastConfirmed() - $user->getInvested()) . "</b> BTC\n
<b>Tu inversión:</b>
" . $investment_data . "
\n
Para iniciar tu plan selecciona la opción \"Invertir\".",
				'reply_markup' => $reply_markup,
				'parse_mode'   => 'HTML',
			]);
		}
	}
}
