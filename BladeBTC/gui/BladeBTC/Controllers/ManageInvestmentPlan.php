<?php

namespace BladeBTC\GUI\Controllers;

use BladeBTC\GUI\Helpers\Form;
use BladeBTC\GUI\Helpers\Request;
use BladeBTC\GUI\Models\InvestmentPlansModel;
use Exception;

class ManageInvestmentPlan
{

    /**
     * Handle multiples actions in Investment Plan management.
     *
     * @return null|string
     * @throws Exception
     */
    public static function action()
    {

        $action = Request::get('action');

        $msg = null;
        switch ($action) {

            case "edit_plan" :

                $plan_data = InvestmentPlansModel::getById(Request::get('id'), true);
                Form::save($plan_data, true);
                $msg = "The Investment Plan has been loaded.";

                break;

            case "delete_plan":

                InvestmentPlansModel::delete(Request::get('id'));

                $msg = "The Investment Plan has been deleted.";

                break;


            case "activate_plan":

                InvestmentPlansModel::activatePlan(Request::get('id'));

                $msg = "The Investment Plan has been activated.";

                break;

        }

        return $msg;
    }


    /**
     * Add new account
     *
     * @return bool
     * @throws Exception
     */
    public static function addInvestmentPlan()
    {

        /**
         * Save form data
         */
        Form::save(Request::post());

        /**
         * Form value
         */
        $minimum_invest = Request::post('minimum_invest_usd');
        $minimum_reinvest = Request::post('minimum_reinvest_usd');
        $minimum_payout = Request::post('minimum_payout_usd');
        $referral_bonus = Request::post('referral_bonus_usd');
        $contract_day = Request::post('contract_day');
        $required_confirmations = Request::post('required_confirmations');
        $withdraw_fee = Request::post('withdraw_fee');

        /**
         * Validate minimum invest
         */
        if (empty($minimum_invest)) {
            Form::remove('minimum_invest_usd');
            throw new Exception("You must enter a minimum invest amount.");
        }

        if ($minimum_invest <= 0) {
            Form::remove('minimum_invest_usd');
            throw new Exception("You must enter a minimum invest greater than 0.");
        }

        /**
         * Validate minimum reinvest
         */
        if (empty($minimum_reinvest)) {
            Form::remove('minimum_reinvest_usd');
            throw new Exception("You must enter a minimum reinvest amount.");
        }

        if ($minimum_reinvest <= 0) {
            Form::remove('minimum_invest_usd');
            throw new Exception("You must enter a minimum reinvest greater than 0.");
        }

        /**
         * Validate minimum payout
         */
        if (empty($minimum_payout)) {
            Form::remove('minimum_payout_usd');
            throw new Exception("You must enter a minimum payout amount.");
        }

        if ($minimum_payout <= 0) {
            Form::remove('minimum_payout_usd');
            throw new Exception("You must enter a minimum payout greater than 0.");
        }

        /**
         * Validate minimum payout
         */
        if (empty($referral_bonus)) {
            Form::remove('referral_bonus_usd');
            throw new Exception("You must enter a minimum referral bonus amount.");
        }

        if ($referral_bonus <= 0) {
            Form::remove('referral_bonus_usd');
            throw new Exception("You must enter a minimum referral bonus amount greater than 0.");
        }

        /**
         * Validate contract day
         */
        if (empty($contract_day)) {
            Form::remove('contract_day');
            throw new Exception("You must enter a contract time in days.");
        }


        /**
         * Validate Confirmation Required
         */
        if (empty($required_confirmations)) {
            Form::remove('required_confirmations');
            throw new Exception("You must enter a number of confirmation required.");
        }


        /**
         * Validate Withdraw fee
         */
        if ($withdraw_fee == -1) {
            Form::remove('withdraw_fee');
            throw new Exception("You must select the withdraw fee.");
        }

        /**
         * Prepare data
         */
        $plan = [
            "minimum_invest" => $minimum_invest,
            "minimum_reinvest" => $minimum_reinvest,
            "minimum_payout" => $minimum_payout,
            "referral_bonus" => $referral_bonus,
            "contract_day" => $contract_day,
            "required_confirmations" => $required_confirmations,
            "withdraw_fee" => $withdraw_fee,
        ];

        try {

            InvestmentPlansModel::create($plan);

            Form::destroy();

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Edit account
     *
     * @return bool
     * @throws Exception
     */
    public static function editInvestmentPlan()
    {

        /**
         * Save form data
         */
        Form::update(Request::post());

        /**
         * Form value
         */
        $minimum_invest = Request::post('minimum_invest_usd');
        $minimum_reinvest = Request::post('minimum_reinvest_usd');
        $minimum_payout = Request::post('minimum_payout_usd');
        $referral_bonus = Request::post('referral_bonus_usd');
        $contract_day = Request::post('contract_day');
        $required_confirmations = Request::post('required_confirmations');
        $withdraw_fee = Request::post('withdraw_fee');
        $plan_id = Form::getReturn('id');

        /**
         * Validate minimum invest
         */
        if (empty($minimum_invest)) {
            Form::remove('minimum_invest');
            throw new Exception("You must enter a minimum invest amount.");
        }

        if ($minimum_invest <= 0) {
            Form::remove('minimum_invest');
            throw new Exception("You must enter a minimum invest greater than 0.");
        }

        /**
         * Validate minimum reinvest
         */
        if (empty($minimum_reinvest)) {
            Form::remove('minimum_reinvest');
            throw new Exception("You must enter a minimum reinvest amount.");
        }

        if ($minimum_reinvest <= 0) {
            Form::remove('minimum_invest');
            throw new Exception("You must enter a minimum reinvest greater than 0.");
        }

        /**
         * Validate minimum payout
         */
        if (empty($minimum_payout)) {
            Form::remove('minimum_payout');
            throw new Exception("You must enter a minimum payout amount.");
        }

        if ($minimum_payout <= 0) {
            Form::remove('minimum_payout');
            throw new Exception("You must enter a minimum payout greater than 0.");
        }

        /**
         * Validate minimum payout
         */
        if (empty($referral_bonus)) {
            Form::remove('referral_bonus_usd');
            throw new Exception("You must enter a minimum referral bonus amount.");
        }

        if ($referral_bonus <= 0) {
            Form::remove('referral_bonus_usd');
            throw new Exception("You must enter a minimum referral bonus amount greater than 0.");
        }


        /**
         * Validate contract day
         */
        if (empty($contract_day)) {
            Form::remove('contract_day');
            throw new Exception("You must enter a contract time in days.");
        }


        /**
         * Validate Confirmation Required
         */
        if (empty($required_confirmations)) {
            Form::remove('required_confirmations');
            throw new Exception("You must enter a number of confirmation required.");
        }


        /**
         * Validate Withdraw fee
         */
        if ($withdraw_fee == "null") {
            Form::remove('withdraw_fee');
            throw new Exception("You must select the withdraw fee.");
        }


        /**
         * Prepare data
         */
        $plan = [
            "id" => $plan_id,
            "minimum_invest" => $minimum_invest,
            "minimum_reinvest" => $minimum_reinvest,
            "minimum_payout" => $minimum_payout,
            "referral_bonus" => $referral_bonus,
            "contract_day" => $contract_day,
            "required_confirmations" => $required_confirmations,
            "withdraw_fee" => $withdraw_fee,
        ];

        try {

            InvestmentPlansModel::update($plan);

            Form::destroy();

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

