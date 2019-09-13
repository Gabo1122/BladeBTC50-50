<?php


namespace BladeBTC\Commands;

use BladeBTC\Helpers\Btc;
use BladeBTC\Helpers\Currency;
use BladeBTC\Helpers\Wallet;
use BladeBTC\Models\InvestmentPlan;
use BladeBTC\Models\Users;
use Exception;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class OutCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "out";

    /**
     * @var string Command Description
     */
    protected $description = "Withdraw";

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


            $out_amount = trim(explode(" ", $this->update->getMessage()->getText())[1]);


            try {


                /**
                 * Check if out amount is empty
                 */
                if (empty($out_amount)) {

                    $this->replyWithMessage([
                        'text' => "Para retirar necesitas poner el monto que deseas retirar luego de la opción /sacar. Aun no has indicado el monto que deseas retirar.",
                        'reply_markup' => $reply_markup,
                        'parse_mode' => 'HTML',
                    ]);

                }


                /**
                 * Validate payout amount requested
                 */
                elseif (!is_numeric($out_amount) || $out_amount <  Currency::GetBTCValueFromCurrency(InvestmentPlan::getValueByName("minimum_payout_usd"))) {

                    $this->replyWithMessage([
                        'text' => "Necesitas al menos " . Currency::GetBTCValueFromCurrency(InvestmentPlan::getValueByName("minimum_payout_usd")) . " BTC. para retirar e indicar el monto en números, también recuerda que para recibir fondos por tus referidos debes tener un plan activo.",
                        'reply_markup' => $reply_markup,
                        'parse_mode' => 'HTML',
                    ]);
                }


                /**
                 * Validate account balance
                 */
                elseif ($user->getBalance() < $out_amount) {

                    $this->replyWithMessage([
                        'text' => "Aún no tienes fondos suficientes.",
                        'reply_markup' => $reply_markup,
                        'parse_mode' => 'HTML',
                    ]);
                }


                /**
                 * Withdraw
                 */
                else {

                    /**
                     * Check if the withdraw is possible
                     */
                    if ($out_amount - Btc::SatoshiToBitcoin(InvestmentPlan::getValueByName('withdraw_fee')) <= 0) {

                        /**
                         * Response
                         */
                        $this->replyWithMessage([
                            'text' => "El monto mínimo para retirar es " . Btc::Format((Btc::SatoshiToBitcoin(InvestmentPlan::getValueByName('withdraw_fee')) + 0.00000100)) . "BTC. Esto es debido al costo de la transacción en la red de Bitcoin. El cual es " . BTC::Format(Btc::SatoshiToBitcoin(InvestmentPlan::getValueByName('withdraw_fee'))) . " BTC.",
                            'reply_markup' => $reply_markup,
                            'parse_mode' => 'HTML',
                        ]);

                    }
                    else {


                        $transaction = Wallet::makeOutgoingPayment($user->getWalletAddress(), Btc::BtcToSatoshi($out_amount));

                        if (!empty($transaction) && empty($transaction->error)) {


                            /**
                             * Update user balance
                             */
                            $user->updateBalance($out_amount, $transaction);


                            /**
                             * Response
                             */
                            $this->replyWithMessage([
                                'text' => "Mensaje :\n<b>" . $transaction->message . "</b>\n" . "ID de transacción:\n<b>" . $transaction->txid . "</b>\n" . "Hash:\n<b>" . $transaction->tx_hash . "</b>",
                                'reply_markup' => $reply_markup,
                                'parse_mode' => 'HTML',
                            ]);

                        }
                        else {

                            /**
                             * Response
                             */
                            $this->replyWithMessage([
                                'text' => "Ha ocurrido un error al retirar tus BTC.\n<b>[Error] " . $transaction->error . "</b>. \xF0\x9F\x98\x96",
                                'reply_markup' => $reply_markup,
                                'parse_mode' => 'HTML',
                            ]);
                        }
                    }
                }
            } catch (Exception $e) {

                $this->replyWithMessage([
                    'text' => "Ha ocurrido un error al retirar tus BTC.\n" . $e->getMessage() . ". \xF0\x9F\x98\x96",
                    'reply_markup' => $reply_markup,
                    'parse_mode' => 'HTML'
                ]);
            }
        }
    }
}
