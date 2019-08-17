<?php

require __DIR__ . '/bootstrap/app.php';

use BladeBTC\Helpers\Btc;
use BladeBTC\Helpers\Currency;
use BladeBTC\Helpers\Wallet;
use BladeBTC\Models\BotSetting;
use BladeBTC\Models\ErrorLogs;
use BladeBTC\Models\Investment;
use BladeBTC\Models\InvestmentPlan;
use BladeBTC\Models\Transactions;
use BladeBTC\Models\Users;

try {

    /**
     * Load .env file
     */
    $env = new Dotenv\Dotenv(__DIR__);
    $env->load();

    /**
     * Recover all address
     */
    $addresses = Wallet::listAddress();
    foreach ($addresses['addresses'] as $address) {


        /**
         * Check if address have balance
         */
        if ($address['total_received'] > 0) {


            /***
             * Validate Label User ID
             */
            if (Users::checkExistByInvestmentAddress($address['address'])) {


                /**
                 * Try go get user telegram ID from address
                 */
                $telegram_id = Users::getTelegramIDByInvestmentAddress($address['address']);


                /**
                 * Verify if we found telegram ID
                 */
                if (!is_null($telegram_id)) {


                    /**
                     * Build user object
                     */
                    $user = new Users($telegram_id);


                    /**
                     * Calculate BTC on this address
                     */
                    $userLastConfirmedInBTC = $user->getLastConfirmed();
                    $totalConfirmedForThisAddressInBTC = Btc::SatoshiToBitcoin(Wallet::getConfirmedReceivedByAddress($address['address']));
                    $confirmedNewDepositInBtc = $totalConfirmedForThisAddressInBTC - $userLastConfirmedInBTC;


                    /**
                     * Check if transaction have confirmation
                     */
                    if ($confirmedNewDepositInBtc > 0) {


                        /**
                         * Set last confirmed
                         */
                        $user->setLastConfirmed($totalConfirmedForThisAddressInBTC);
                        $user->refresh();

                        /**
                         * Check if new confirmed amount is higher to create an investment based on the investment plan active
                         */
                        $balanceConfirmed = $user->getLastConfirmed() - $user->getInvested();
                        if ($balanceConfirmed >= $user->getCurrentMinimumBTC()) {

                            /**
                             * Create investment
                             */
                            Investment::create($user->getTelegramId(), $balanceConfirmed);


                            /**
                             * Update invested
                             */
                            $newInvested = $user->getInvested() + $balanceConfirmed;
                            $user->setInvested($newInvested);


                            /**
                             * Give bonus to referent - First invest only
                             */


                            /**
                             * Get referent Id
                             */
                            $referent_id = $user->getReferentId();


                            /**
                             * Give commission
                             */
                            if (!is_null($referent_id)) {


                                /**
                                 * Only give commission if referent have an active investment
                                 */
                                $active_investment_count = Investment::getActiveInvestment($referent_id);
                                if (count($active_investment_count) > 0){
                                    Users::giveCommission($referent_id, Currency::GetBTCValueFromCurrency(InvestmentPlan::getValueByName('referral_bonus_usd')));
                                }

                            }


                            /**
                             * Set current minimum BTC value to Null
                             * This way the next investment of this user will
                             * be calculated with the current currency value
                             */
                            $user->setNullCurrentMinimumBTC();


                            /**
                             * Log transaction
                             */
                            Transactions::log([
                                "telegram_id" => $user->getTelegramId(),
                                "amount" => $confirmedNewDepositInBtc,
                                "withdraw_address" => "",
                                "message" => "",
                                "tx_hash" => "",
                                "tx_id" => "",
                                "status" => 1,
                                "type" => "deposit",
                            ]);


                            /**
                             * Send user message - Notification of deposit
                             */
                            $apiToken = BotSetting::getValueByName('app_id');
                            $data = [
                                'parse_mode' => 'HTML',
                                'chat_id' => $user->getTelegramId(),
                                'text' => 'Your deposit of <b>' . BTC::Format($confirmedNewDepositInBtc) . '</b> is now accepted and your balance of ' . BTC::Format($balanceConfirmed) . ' is invested.'
                            ];
                            $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data));
                        } else {

                            /**
                             * Log transaction
                             */
                            Transactions::log([
                                "telegram_id" => $user->getTelegramId(),
                                "amount" => $confirmedNewDepositInBtc,
                                "withdraw_address" => "",
                                "message" => "",
                                "tx_hash" => "",
                                "tx_id" => "",
                                "status" => 1,
                                "type" => "deposit",
                            ]);

                            /**
                             * Send user message - Notification of deposit
                             */
                            $apiToken = BotSetting::getValueByName('app_id');
                            $data = [
                                'parse_mode' => 'HTML',
                                'chat_id' => $user->getTelegramId(),
                                'text' => 'Your deposit of <b>' . BTC::Format($confirmedNewDepositInBtc) . '</b> is now accepted but is not higher to invest. You have now an amount of ' . BTC::Format($balanceConfirmed) . ' BTC. The minimum invest is ' . $user->getCurrentMinimumBTC() . ' BTC.'
                            ];
                            $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data));
                        }
                    }
                }
            }
        }
    }

} catch (Exception $e) {
    try {

        ErrorLogs::Log($e->getCode(), $e->getMessage(), $e->getLine(), 'CRON DEPOSIT', $e->getFile());
        error_log($e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getFile());

    } catch (Exception $q) {

        error_log($q->getMessage() . " on line " . $q->getLine() . " in file " . $q->getFile());
    }
}
