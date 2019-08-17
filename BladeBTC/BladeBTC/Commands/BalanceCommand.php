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
				["My balance " . Btc::Format($user->getBalance()) . " \xF0\x9F\x92\xB0"],
				["Invest \xF0\x9F\x92\xB5", "Withdraw \xE2\x8C\x9B"],
				["Reinvest \xE2\x86\xA9", "Help \xE2\x9D\x93"],
				["My Team \xF0\x9F\x91\xAB"],
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
				$investment_data = "\n<b>|   Amount   |   End   |</b>\n";
				foreach ($investment as $row) {
					$investment_data .= "|" . $row->amount . "|" . $row->contract_end_date . "|\n";
				}
			} else {
				$investment_data = "No active investment";
			}


			/**
			 * Response
			 */
			$this->replyWithMessage([
				'text'         => "Your account balance:
<b>" . Btc::Format($user->getBalance()) . "</b> BTC\n
Total invested:
<b>" . Btc::Format($user->getInvested()) . "</b> BTC\n
Total reinvested:
<b>" . Btc::Format($user->getReinvested()) . "</b> BTC\n
Total Payout:
<b>" . Btc::Format($user->getPayout()) . "</b> BTC\n
Pending commission (Referral):
<b>" . Btc::Format($user->getCommission()) . "</b> BTC\n
Total deposit (confirmed):
<b>" . Btc::Format($user->getLastConfirmed()) . "</b> BTC\n
Total balance of deposit (Lower than minimum invest):
<b>" . Btc::Format($user->getLastConfirmed() - $user->getInvested()) . "</b> BTC\n
<b>Your investment:</b>
" . $investment_data . "
\n
You may start another investment by pressing the \"Invest\" button.",
				'reply_markup' => $reply_markup,
				'parse_mode'   => 'HTML',
			]);
		}
	}
}

