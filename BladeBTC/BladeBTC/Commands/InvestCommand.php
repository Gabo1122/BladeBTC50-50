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
             * Check if user have an active investment
             */
            $active_investment_count = Investment::getActiveInvestment($user->getTelegramId());
            if (count($active_investment_count) > 0) {
                $this->replyWithMessage([
                    'text' => "Solo puedes depositar una vez al mes. Recuerda realizar tu pago el siguiente mes para poder recibir tu bono por referidos.",
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
                            'text' => "Esta es tu dirección para invertir:",
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
                            'text' => "Es necesario que inicies tu inversión para poder recibir el bono por referidos, la inversión es de:\n\n<strong>" . $user->getCurrentMinimumBTC() . " BTC - aproximadamente. " . InvestmentPlan::getValueByName("minimum_invest_usd") . " $ USD</strong>\n\nLuego de que realices tu primer pago debes esperar la confirmación que realiza la red de Bitcoin",
                            'reply_markup' => $reply_markup,
                            'parse_mode' => 'HTML'
                        ]);

                    } catch (Exception $e) {
                        $this->replyWithMessage([
                            'text' => "Ha ocurrido un error generando tu dirección.\n" . $e->getMessage() . ". \xF0\x9F\x98\x96",
                            'reply_markup' => $reply_markup,
                            'parse_mode' => 'HTML'
                        ]);
                    }


                } else {
                    $this->replyWithMessage([
                        'text' => "Ha ocurrido al generar tu dirección.\n" . $payment_address->error . ". \xF0\x9F\x98\x96",
                        'reply_markup' => $reply_markup,
                        'parse_mode' => 'HTML'
                    ]);
                }
            }
        }
    }
}
