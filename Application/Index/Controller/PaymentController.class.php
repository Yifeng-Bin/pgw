<?php

namespace Index\Controller;

use Think\Controller;

class PaymentController extends Controller {

    public function notify() {
        $type = I('get.type');
        $payObj = new \Common\Library\Pay\Pay();
        $pay = $payObj->getDriver($type);
        if ($pay == false) {
            echo 'Access Denied';
            die();
        }
        $log_path = LOG_PATH . 'paylog/' . $type . '/' . date('y_m_d') . '.log';
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            unset($_GET[C('VAR_MODULE')], $_GET[C('VAR_CONTROLLER')], $_GET[C('VAR_ACTION')]);
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['type']);
        } else {
            exit('Access Denied');
        }
        \Think\Log::write(I('get.method'), \Think\Log::INFO, 'file', $log_path);
        \Think\Log::write(var_export($notify, true), \Think\Log::INFO, 'file', $log_path);
        //验证
        if ($pay->verifyNotify($notify)) {
            //获取订单信息
            $info = $pay->getInfo();
            \Think\Log::write('订单信息：' . var_export($info, true), \Think\Log::INFO, 'file', $log_path);
            if ($info['status']) {
                $payInfo = M("PayLog")->where(array('log_id' => $info['out_trade_no']))->find();
                \Think\Log::write('支付日志信息：' . var_export($payInfo, true), \Think\Log::INFO, 'file', $log_path);
                if (!empty($payInfo) && $payInfo['money'] == $info['money']) {
                    if ($payInfo['pay_status'] == 0) {
                        $result = M("PayLog")->where(array('log_id' => $info['out_trade_no'], 'pay_status' => 0))->save(array('pay_status' => 1, 'pay_time' => gmtime()));
                        if ($result !== false && $result > 0) {
                            if ($payInfo['log_type'] == 1) { //保证金
                                M('Users')->where(array('user_id' => $payInfo['user_id']))->save(array('money' => array('exp', 'money+' . $info['money'])));
                            } elseif ($payInfo['log_type'] == 2) { //充值金币
                                $money_to_gold = M('Config')->where(array('name' => 'money_to_gold'))->getField('value');
                                $value = intval($info['money']) * intval($money_to_gold);
                                M('Users')->where(array('user_id' => $payInfo['user_id']))->save(array('gold' => array('exp', 'gold+' . $value)));
                                //奖励积分 购买金币
                                $data = array(
                                    'user_id' => $payInfo['user_id'],
                                    'change_type' => 99,
                                    'change_desc' => '购买金币',
                                    'change_time' => gmtime(),
                                    'in_out' => 0,
                                    'id' => $info['out_trade_no'],
                                    'integral' => $value,
                                );
                                $log_id = M('IntegralLog')->add($data);
                                //奖励积分
                            } elseif ($payInfo['log_type'] == 3) { //需求支付
                                $tender_id = M("PayLog")->where(array('log_id' => $info['out_trade_no']))->getField('out_id');
                                M('Tender')->where(array('tender_id' => $tender_id,'status' => STATUS_WAIT_PAY ))->save(array('status' =>STATUS_WAIT_EXECUTE ));
                                M('Tender')->where(array('tender_id' => $tender_id))->save(array('pay_total' => array('exp', 'pay_total+' . $info['money'])));
                            }
                        }
                    }
                    if (I('get.method') == "return") {
                        if ($payInfo['log_type'] == 1) {
                            \redirect(U("User/service"));
                        } elseif ($payInfo['log_type'] == 2) {
                            \redirect(U("User/gold?type=pay_log"));
                        } elseif ($payInfo['log_type'] == 3) {
                            \redirect(U("User/requirement"));
                        }
                    } else {
                        $pay->notifySuccess();
                        die();
                    }
                } else {
                    $this->error("订单金额确认失败！", U("User/service"));
                }
            } else {
                $this->error("支付失败！", U("User/service"));
            }
        } else {
            \Think\Log::write('验证失败', \Think\Log::INFO, 'file', $log_path);
            $this->error("支付失败！", U("User/service"));
        }
    }

}
