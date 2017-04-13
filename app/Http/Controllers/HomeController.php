<?php

namespace App\Http\Controllers;


use App\Flow;
use App\Member;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     */
    public function index()
    {
        return view('home');
    }

    /**
     * 显示流量排名
     * @param Request $request
     * @return string
     */
    public function show_rank(Request $request)
    {
        $type = $request->get('type');
        if ($type == 'hour') {
            $startTime = date('Y-m-d H:i:s', time() - 3600);
            $endTime = date('Y-m-d H:i:s', time());
        } else if ($type == 'day') {
            $startTime = date('Y-m-d H:i:s', time() - 86400);
            $endTime = date('Y-m-d H:i:s', time());
        } else {
            $startTime = date('Y-m-d H:i:s', time() - 604800);
            $endTime = date('Y-m-d H:i:s', time());
        }
        return json_encode($this->getRankFlows($startTime, $endTime));
//        return parent::appResponse($this->getRankFlows($startTime,$endTime));
    }

    /**
     * 获取流量排名数据
     * @param $startTime
     * @param $endTime
     * @return array
     */
    private function getRankFlows($startTime, $endTime)
    {
        $num = 0;
        $bgArray = ['#FF6384', '#4BC0C0', '#FFCE56', '#E7E9ED', '#36A2EB', '#8A2BE2', '#FF00FF',
            '#32CD32', '#6A5ACD', '#4169E1', '#00BFFF', '#FF4500', '	#D2691E', '#696969'];
        $data = [];
        $array = [];
        $labels = [];
        $bg_colors = [];
        $testFlow = Flow::getAllFlowRank($startTime, $endTime);
        foreach ($testFlow as $flowItem) {
            $data[$flowItem->port] = round($flowItem->flow / 1024, 2);
        }
        arsort($data);
        foreach ($data as $key => $value) {
            $array[$num] = $value;
            $labels[$num] = $key;
            $bg_colors[$num] = $bgArray[array_rand($bgArray, 1)];
            $num++;
        }
        $result = array(
            'flows' => $array,
            'labels' => $labels,
            'bg_colors' => $bg_colors
        );
        return $result;
    }

    /**
     * 显示用户管理页面
     */
    public function show_member()
    {
        $userArray = Member::orderBy('id', 'desc')->get();;
        return view('user_manage')->with('userArray', $userArray);
    }

    /**
     * 显示用户详情
     * @param Request $request
     * @return $this
     */
    public function user_detail(Request $request)
    {
        $port = $request->get('port');
        $user = Member::where('port', $port)->first();
        $temp = 'aes-256-cfb:' . $user->password . \Config::get('app.ss_domain') . $user->port;
        $qr_url = 'ss://' . base64_encode($temp);
        $total = Flow::getTotalFlow($port);
        $day_flow = $this->getThisDayFlow($port);
        $week_flow = $this->getThisWeekFlow($port);
        $month_flow = $this->getThisMonthFlow($port);
        return view('user_detail', array(
            'user' => $user,
            'qr_url' => $qr_url,
            'total' => $total,
            'day_flow' => $day_flow,
            'week_flow' => $week_flow,
            'month_flow' => $month_flow,
        ));
    }

    private function getThisDayFlow($port)
    {
        //获取当前开始的时刻和最后的时刻
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        return Flow::getFlowFromStartToEnd($port, $startTime, $endTime);
    }

    private function getThisWeekFlow($port)
    {
        //获取一周的第一天和最后一天
        $date = new \DateTime();
        $date->modify('this week');
        $first_day_of_week = $date->format('Y-m-d 00:00:00');
        $date->modify('this week +6 days');
        $end_day_of_week = $date->format('Y-m-d 23:59:59');
        return Flow::getFlowFromStartToEnd($port, $first_day_of_week, $end_day_of_week);
    }

    private function getThisMonthFlow($port)
    {
        //获取当月第一天和最后一天
        $first_date = date('Y-m-01 00:00:00', strtotime(date("Y-m-d")));
        $last_date = date('Y-m-d 23:59:59', strtotime("$first_date +1 month -1 day"));
        return Flow::getFlowFromStartToEnd($port, $first_date, $last_date);
    }


    /**
     * 按小时流量统计
     * @param Request $request
     */
    public function hour_flow(Request $request)
    {
        $port = $request->get('port');
        $distance = $request->get('hour');
        $startTime = date('Y-m-d H:00:00', time() + $distance);
        $hour = date('H', time() + $distance);
        $endTime = date('Y-m-d H:59:59', time() + $distance);
        echo json_encode(Flow::getHourFlow($port, $startTime, $endTime, $hour));
    }

    /**
     * 按天流量统计
     * @param Request $request
     */
    public function day_flow(Request $request)
    {
        $distance = $request->get('day');
        $port = $request->get('port');
        $startTime = date('Y-m-d 00:00:00', time() + $distance);
        $endTime = date('Y-m-d 23:59:59', time() + $distance);
        echo json_encode(Flow::getDayFlow($port, $startTime, $endTime));
    }

    /**
     * 按周流量统计
     * @param Request $request
     */
    public function week_flow(Request $request)
    {
        $distance = $request->get('week');
        $port = $request->get('port');
        //本周的第一天和最后一天
        $date = new \DateTime();
        $date->modify('this week');
        $first_day_of_week = $date->format('Y-m-d 00:00:00');
        $date->modify('this week +6 days');
        $end_day_of_week = $date->format('Y-m-d 23:59:59');
        //退或前进到某一周的第一天和最后一天
        $startTime = date('Y-m-d 00:00:00', strtotime($first_day_of_week) + $distance);
        $endTime = date('Y-m-d 23:59:59', strtotime($end_day_of_week) + $distance);
        echo json_encode(Flow::getWeekFlow($port, $startTime, $endTime));
    }

    /**
     * 向数据库中添加一个添加用户
     * @param Request $request
     */
    public function add_user(Request $request)
    {
        $array = array(
            'code' => 0,
            'msg' => ''
        );
        $port = $request->get('port');
        $name = $request->get('name');
        $password = $request->get('password');
        $user = Member::getMemberByPort($port);
        if (empty($user)) {
            Member::addNewMember($port, $password, $name);
            $array['msg'] = '添加成功';
        } else {
            $array['code'] = 1;
            $array['msg'] = '端口号重复';
        }
        echo json_encode($array);
    }

    /**
     * 编辑一个用户的信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function user_edit(Request $request)
    {
        $port = $request->get('port');
        $user = Member::getMemberByPort($port);
        return view('user_edit', [
            'port' => $port,
            'password' => $user->password,
            'name' => $user->name
        ]);
    }

    /**
     * 删除用户
     * @param Request $request
     * @return string
     */
    public function delete_member(Request $request)
    {
        $port = $request->get('port');
        $result = ['code' => 0, 'msg' => ''];
        if (!empty($port)) {
            $code = $this->deletePortFromSSServer($port);
            if ($code == 'ok') {
                Member::deleteByPort($port);
                $result['msg'] = '删除用户成功';
            } else {
                $result['code'] = 1;
                $result['msg'] = '删除用户失败';
            }
        } else {
            $result['code'] = 1;
            $result['msg'] = '端口号为空';
        }
        return json_encode($result);
    }

    /**
     * 更新用户信息
     * @param Request $request
     * @return string
     */
    public function update_member(Request $request)
    {
        $port = $request->get('port');
        $name = $request->get('name');
        $password = $request->get('password');
        $originName = $request->get('originName');
        $originPass = $request->get('originPass');
        Member::updateByPort($port, $password, $name);
        $result = $this->deletePortFromSSServer($port);
        $array = ['code' => 0, 'msg' => '更新用户信息成功'];
        if ($result != 'ok') {
            Member::updateByPort($port, $originPass, $originName);
            $array['code'] = 1;
            $array['msg'] = '更新用户信息失败';
        }
        return json_encode($array);
    }

    /**
     * 从SSServer删除端口
     * @param $port
     */
    public function deletePortFromSSServer($port)
    {
        error_reporting(E_ALL);
        $service_port = 6001;
        $address = '127.0.0.1';

        $client = new \swoole_client(SWOOLE_SOCK_UDP);
        if (!$client->connect($address, $service_port, -1)) {
            $result['code'] = 1;
            $result['msg'] = '删除用户失败，从服务器移除端口失败';
            echo json_encode($result);
            return;
        }
        $attr = array(
            'server_port' => (int)$port
        );
        $jsonAttr = 'remove:' . json_encode($attr);
        $client->send($jsonAttr);
        $result = $client->recv();
        $client->close();
        return $result;
    }


}
