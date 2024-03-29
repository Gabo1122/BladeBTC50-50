<?php
require $_SERVER['DOCUMENT_ROOT'] . '/gui/views/partials/header.php';

use BladeBTC\GUI\Controllers\ManageInvestmentPlan;
use BladeBTC\GUI\Helpers\Form;
use BladeBTC\GUI\Helpers\Messages;
use BladeBTC\GUI\Helpers\Path;
use BladeBTC\GUI\Helpers\Request;
use BladeBTC\GUI\Helpers\Session;
use BladeBTC\GUI\Models\InvestmentPlansModel;

?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1 id="module">Investment Plans (Bot)</h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo Path::module(); ?>/telegram-investment-plan.php"><i class="fa fa-dashboard"></i>Investment
                        Plans (Bot)</a>
                </li>

            </ol>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">


            <div class="row">
                <div class="col-md-12">
                    <?php
                    if (Request::get('action') && Session::getFormId('manage-link') == Request::get('token')) {
                        try {
                            $message = ManageInvestmentPlan::action();
                            Messages::success($message);
                        } catch (Exception $e) {
                            Messages::error($e->getMessage());
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php
                    if (!is_null(Request::post('save-plan')) && Session::getFormId('mng-plan') == Request::post('DBLP')) {
                        try {

                            if (Form::getReturn('edit_mode') == 1) {

                                //EDIT
                                if (ManageInvestmentPlan::editInvestmentPlan()) {
                                    Messages::success("The Investment Plan has been modified.");
                                }
                            }
                            else {

                                //ADD
                                if (ManageInvestmentPlan::addInvestmentPlan()) {
                                    Messages::success("The Investment Plan has been created.");
                                }
                            }

                        } catch (Exception $e) {
                            Messages::error($e->getMessage());
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Add an Investment Plan</h3>
                        </div>

                        <div class="box-body">



                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <form action="<?php echo Path::module() ?>/telegram-investment-plan.php"
                                                  method="post">
                                                <input type="hidden" name="DBLP"
                                                       value="<?php echo Session::setFormId('mng-plan'); ?>">
                                                <div class="row">
                                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                                        <label>Minimum Invest (USD)&nbsp;&nbsp;<i
                                                                    style="color: red;" class="fa fa-question-circle"
                                                                    title="Indicate the minimum amount of BTC that a user need to invest."
                                                                    data-toggle="tooltip"></i></label>
                                                        <div class="input-group"><span class="input-group-addon"><i
                                                                        class="fa fa-user"></i></span>
                                                            <input class="form-control" name="minimum_invest_usd"
                                                                   type="text"
                                                                   value="<?php Form::get('minimum_invest_usd') ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                                        <label>Minimum Reinvest (USD)&nbsp;&nbsp;<i
                                                                    style="color: red;" class="fa fa-question-circle"
                                                                    title="Indicate the minimum amount of BTC that a user need to reinvest."
                                                                    data-toggle="tooltip"></i></label>
                                                        <div class="input-group"><span class="input-group-addon"><i
                                                                        class="fa fa-user"></i></span>
                                                            <input class="form-control pointer" name="minimum_reinvest_usd"
                                                                   type="text"
                                                                   value="<?php Form::get('minimum_reinvest_usd') ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="row">
                                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                                        <label>Minimum Payout (USD)&nbsp;&nbsp;<i
                                                                    style="color: red;" class="fa fa-question-circle"
                                                                    title="Indicate the minimum amount of BTC that a user should have in is balance to withdraw."
                                                                    data-toggle="tooltip"></i></label>
                                                        <div class="input-group"><span class="input-group-addon"><i
                                                                        class="fa fa-user"></i></span>
                                                            <input class="form-control" name="minimum_payout_usd"
                                                                   type="text"
                                                                   value="<?php Form::get('minimum_payout_usd') ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                                        <label>Referral Bonus (USD)&nbsp;&nbsp;<i
                                                                    style="color: red;" class="fa fa-question-circle"
                                                                    title="Indicate the amount that a user should have in bonus for a referral investment."
                                                                    data-toggle="tooltip"></i></label>
                                                        <div class="input-group"><span class="input-group-addon"><i
                                                                        class="fa fa-user"></i></span>
                                                            <input class="form-control" name="referral_bonus_usd"
                                                                   type="text"
                                                                   value="<?php Form::get('referral_bonus_usd') ?>">
                                                        </div>
                                                    </div>

                                                </div>
                                                <br/>
                                                <div class="row">
                                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                                        <label>Contract Time (Days)&nbsp;&nbsp;<i
                                                                    style="color: red;" class="fa fa-question-circle"
                                                                    title="Indicate the number of days that an investment will be under contract to get interest. During this time the user cannot withdraw."
                                                                    data-toggle="tooltip"></i></label>
                                                        <div class="input-group"><span class="input-group-addon"><i
                                                                        class="fa fa-user"></i></span>
                                                            <input class="form-control" name="contract_day" type="text"
                                                                   value="<?php Form::get('contract_day') ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                                        <label>Required Confirmations&nbsp;&nbsp;<i
                                                                    style="color: red;" class="fa fa-question-circle"
                                                                    title="Indicate the number of confirmation we need from the blockchain network to credit a user account on a deposit. Default: 3"
                                                                    data-toggle="tooltip"></i></label>
                                                        <div class="input-group"><span class="input-group-addon"><i
                                                                        class="fa fa-user"></i></span>
                                                            <input class="form-control pointer"
                                                                   name="required_confirmations"
                                                                   type="text"
                                                                   value="<?php Form::get('required_confirmations') ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="row">

                                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                                        <label>Withdraw Fee (Satoshi)&nbsp;&nbsp;<i
                                                                    style="color: red;" class="fa fa-question-circle"
                                                                    title="Indicate the fee used to process the transaction on blockchain network on a withdraw."
                                                                    data-toggle="tooltip"></i></label>
                                                        <div class="input-group"><span class="input-group-addon"><i
                                                                        class="fa fa-user"></i></span>
                                                            <select name="withdraw_fee" class="form-control">
                                                                <option value="null" selected>Select an option</option>
                                                                <option value="10000" <?php echo(Form::getReturn('withdraw_fee') == 10000 ? 'selected' : null) ?>>
                                                                    10 000
                                                                </option>
                                                                <option value="20000" <?php echo(Form::getReturn('withdraw_fee') == 20000 ? 'selected' : null) ?>>
                                                                    20 000
                                                                </option>
                                                                <option value="30000" <?php echo(Form::getReturn('withdraw_fee') == 30000 ? 'selected' : null) ?>>
                                                                    30 000
                                                                </option>
                                                                <option value="40000" <?php echo(Form::getReturn('withdraw_fee') == 40000 ? 'selected' : null) ?>>
                                                                    40 000
                                                                </option>0.001
                                                                <option value="50000" <?php echo(Form::getReturn('withdraw_fee') == 50000 ? 'selected' : null) ?>>
                                                                    50 000
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input class="btn btn-success btn-block" type="submit" name="save-plan"
                                                               value="Save">
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Investment Plans</h3>
                        </div>

                        <div class="box-body">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert bg-black-gradient"><i class="fa fa-check-circle fa-fw"></i> Please note that modifying the investment plans only affects the contracts that will be contracted after this change. All active contracts remain unchanged. To modify an active contract you must consult the user section.</div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">

                                    <!-- Tableau -->
                                    <div class="table-responsive">
                                        <table id="mng_investment_plan" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Minimum Invest (USD)</th>
                                                <th>Minimum Reinvest (USD)</th>
                                                <th>Minimum Payout (USD)</th>
                                                <th>Contract Days</th>
                                                <th>Referral Bonus (USD)</th>
                                                <th>Required Confirmations</th>
                                                <th>Withdraw Fee</th>
                                                <th>Active</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php

                                            $investment_plans = InvestmentPlansModel::getAll();
                                            $token = Session::setFormId('manage-link');
                                            while ($investment_plan = $investment_plans->fetchObject()) {

                                                $rm = '?action=delete_plan&id=' . $investment_plan->id . '&token=' . $token;
                                                $vim = '?action=edit_plan&id=' . $investment_plan->id . '&token=' . $token;
                                                $unl = '?action=activate_plan&id=' . $investment_plan->id . '&token=' . $token;

                                                echo '<tr>
                                                        <td>' . $investment_plan->minimum_invest_usd . '</td>
                                                        <td>' . $investment_plan->minimum_reinvest_usd . '</td>
                                                        <td>' . $investment_plan->minimum_payout_usd . '</td>
                                                        <td>' . $investment_plan->contract_day . '</td>
                                                        <td>' . $investment_plan->referral_bonus_usd . '</td>
                                                        <td>' . $investment_plan->required_confirmations . '</td>
                                                        <td>' . $investment_plan->withdraw_fee . '</td>
                                                        <td>' . ($investment_plan->active ? '<label style="color: darkgreen;" class="fa fa-check-circle"></label>' : '<label style="color: red;" class="fa fa-times-circle"></label>') . '</td>
                                                        <td>
                                                            <a  class="btn btn-success btn-xs" 
                                                                title="Edit Investment Plan" 
                                                                data-toggle="tooltip"
                                                                href="' . $vim . '">
                                                                <i class="fa fa-pencil fa-fw"></i>
                                                            </a>
                                                            
                                                            <a  class="btn btn-bitbucket btn-xs" 
                                                                title="Activate Investment Plan" 
                                                                data-toggle="tooltip"
                                                                onclick="return iconfirm(\'Attention!\',\'Are you sure you want to activate this investment plan?\',this.href)" 
                                                                href="' . $unl . '">
                                                                <i class="fa fa-check-circle fa-fw"></i>
                                                            </a>
                                                            <a  class="btn btn-danger btn-xs" 
                                                                title="Delete Investment Plan" 
                                                                data-toggle="tooltip"
                                                                onclick="return iconfirm(\'Attention!\',\'Are you sure you want to delete this investment plan?\',this.href)" 
                                                                href="' . $rm . '">
                                                                <i class="fa fa-trash-o fa-fw"></i>
                                                            </a>
                                                        </td>
                                                        </tr>';
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
        <!-- /.content -->
    </div>

    <script>
        $(function () {
            $('#mng_investment_plan').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': false,
                'autoWidth': true,
                "lengthMenu": [[50, 100, -1], [50, 100, "All"]]
            })
        })
    </script>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/gui/views/partials/footer.php';