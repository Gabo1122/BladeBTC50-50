<?php


namespace BladeBTC\Commands;

use BladeBTC\Helpers\Btc;
use BladeBTC\Helpers\Currency;
use BladeBTC\Models\Investment;
use BladeBTC\Models\InvestmentPlan;
use BladeBTC\Models\Users;
use Exception;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class ReinvestCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "reinvest";

    /**
     * @var string Command Description
     */
    protected $description = "Load reinvest menu.";

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
             * Check if balance is lower than the minimum reinvest
             */
            if ($user->getBalance() < Currency::GetBTCValueFromCurrency(InvestmentPlan::getValueByName("minimum_reinvest_usd"))) {


                $this->replyWithMessage([
                    'text' => "Sorry to tell you that, but your balance is not high enough for that!\n<b>Min: " . Currency::GetBTCValueFromCurrency(InvestmentPlan::getValueByName("minimum_reinvest_usd")) . " BTC</b>",
                    'reply_markup' => $reply_markup,
                    'parse_mode' => 'HTML',
                ]);

            }
            else {


                /**
                 * Check if user have an active investment
                 */
                $active_investment_count = Investment::getActiveInvestment($user->getTelegramId());
                if (count($active_investment_count) > 0){
                    $this->replyWithMessage([
                        'text' => "You can only have one active investment. You need to wait the end of your current investment before doing reinvestment.",
                        'reply_markup' => $reply_markup,
                        'parse_mode' => 'HTML',
                    ]);
                }
                else {

                    try {


                        /**
                         * Reinvest balance
                         */
                        $user->Reinvest();
                        $user->Refresh();


                        /**
                         * Response
                         */
                        $this->replyWithMessage([
                            'text' => "Congratulation your balance has been properly invested!",
                            'reply_markup' => $reply_markup,
                            'parse_mode' => 'HTML',
                        ]);


                        /**
                         * Show new balance
                         */
                        $this->triggerCommand("balance");

                    } catch (Exception $e) {

                        $this->replyWithMessage([
                            'text' => "An error occurred while generating your payment address.\n" . $e->getMessage() . ". \xF0\x9F\x98\x96",
                            'reply_markup' => $reply_markup,
                            'parse_mode' => 'HTML'
                        ]);
                    }
                }

            }
        }
    }
}
