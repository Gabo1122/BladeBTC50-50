<?php


namespace BladeBTC\Commands;

use BladeBTC\Helpers\Btc;
use BladeBTC\Helpers\Currency;
use BladeBTC\Helpers\Wallet;
use BladeBTC\Models\Investment;
use BladeBTC\Models\InvestmentPlan;
use BladeBTC\Models\Users;
use Exception;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class InvestCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "invest";

    /**
     * @var string Command Description
     */
    protected $description = "Load invest menu.";

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
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
            ]);


            /**
             * Check if user have an active investment
             */
            $active_investment_count = Investment::getActiveInvestment($user->getTelegramId());
            if (count($active_investment_count) > 0) {
                $this->replyWithMessage([
                    'text' => "You can only have one active investment. You need to wait the end of your current investment before doing reinvestment.",
                    'reply_markup' => $reply_markup,
                    'parse_mode' => 'HTML',
                ]);
            } else {


                /**
                 * Generate payment address
                 */
                $payment_address = Wallet::generateAddress($user->getTelegramId());

                /**
                 * Validate payment address and reply
                 */
                if (isset($payment_address->address)) {

                    try {
                        /**
                         * Store investment_address
                         */
                        $user->setInvestmentAddress($payment_address->address);

                        /**
                         * Response
                         */
                        $this->replyWithMessage([
                            'text' => "Here is your personal BTC address for your investments:",
                            'reply_markup' => $reply_markup,
                            'parse_mode' => 'HTML'
                        ]);

                        $this->replyWithMessage([
                            'text' => "<b>$payment_address->address</b>",
                            'reply_markup' => $reply_markup,
                            'parse_mode' => 'HTML'
                        ]);


                        /**
                         * Set investment amount in BTC from current currency value
                         */
                        if ($user->getCurrentMinimumBTC() == null) {
                            $user->setCurrentMinimumBTC(Currency::GetBTCValueFromCurrency(InvestmentPlan::getValueByName("minimum_invest_usd")));
                            $user->Refresh();
                        }

                        $this->replyWithMessage([
                            'text' => "To start an investment and get the referral bonus send:\n\n<strong>" . $user->getCurrentMinimumBTC() . " BTC - aprox. " . InvestmentPlan::getValueByName("minimum_invest_usd") . " $ USD</strong>\n\nAfter correct transfer, your funds will be added to your account during an hour. Have fun and enjoy your referral bonus!",
                            'reply_markup' => $reply_markup,
                            'parse_mode' => 'HTML'
                        ]);

                    } catch (Exception $e) {
                        $this->replyWithMessage([
                            'text' => "An error occurred while generating your payment address.\n" . $e->getMessage() . ". \xF0\x9F\x98\x96",
                            'reply_markup' => $reply_markup,
                            'parse_mode' => 'HTML'
                        ]);
                    }


                } else {
                    $this->replyWithMessage([
                        'text' => "An error occurred while generating your payment address.\n" . $payment_address->error . ". \xF0\x9F\x98\x96",
                        'reply_markup' => $reply_markup,
                        'parse_mode' => 'HTML'
                    ]);
                }
            }
        }
    }
}